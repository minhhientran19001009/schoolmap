"""Read-only comparison of a source workbook with public VPS school profiles."""
from __future__ import annotations

import argparse
import csv
import json
import re
import unicodedata
from concurrent.futures import ThreadPoolExecutor
from datetime import datetime
from pathlib import Path
from urllib.request import urlopen
import difflib

import cleanGdnnMapSources as source

ROOT = Path(__file__).resolve().parents[1]
BASE = "https://ninhbinh.poly-hna.com"
# These are audit candidates, not identity mappings to import or write to DB.
CANDIDATES = {
    6: (["truong-cao-dang-van-hoa-nghe-thuat-va-du-470"], "Cùng trường"),
    20: (["truong-cao-dang-cong-nghe-kinh-te-va-che-629"], "Cùng trường"),
    30: (["truong-cao-dang-cong-nghiep-nam-dinh-192"], "Cùng trường"),
    90: (["truong-cao-dang-co-dien-nong-nghiep-va-m-218"], "Cùng trường; kiểm tra riêng cơ sở con"),
    138: (["HN-CD-004"], "Cùng trường; kiểm tra riêng bản ghi Cơ sở 1"),
    151: (["truong-cao-dang-nghe-so-20-bqp-vi-khe-509", "truong-cao-dang-nghe-so-20-bqp-truong-th-471"], "File cấp trường; web chia Vị Khê/Trường Thi, chưa phân bổ ngành theo cơ sở"),
    190: (["truong-cao-dang-xay-dung-nam-dinh-204"], "Cùng trường; kiểm tra riêng cơ sở con"),
    214: (["truong-cao-dang-co-dien-va-thuy-loi-bac--458"], "Ứng viên phân hiệu Ninh Bình; cần xác nhận cùng đơn vị"),
    240: (["truong-cao-dang-kinh-te-va-cong-nghe-nam-934"], "Ứng viên Khu A thuộc nhóm NCET; cần xác nhận phạm vi sau sáp nhập"),
    285: (["truong-trung-cap-dai-lam-800"], "Cùng trường"),
    290: (["truong-trung-cap-ky-thuat-du-lich-cong-d-724"], "Cùng trường"),
    299: (["truong-trung-cap-cong-nghe-so-8-nam-dinh-193"], "Cùng trường"),
    305: (["truong-trung-cap-nghe-quoc-te-ivs-235"], "Cùng trường, web thêm (IVS)"),
    315: (["truong-trung-cap-giao-thong-van-tai-tinh-811"], "GTVT được mở rộng thành Giao thông vận tải"),
    324: (["trung-tam-gdnn-nam-tam-diep-802"], "Cùng trung tâm"),
}


def key(name):
    # Keep Vietnamese tone marks: removing them could conflate different names.
    text = unicodedata.normalize("NFC", source.clean_major_name(name)).casefold()
    # Old/new Vietnamese tone placement conventions are the same spelling.
    for old, new in {"hoà": "hòa", "hoá": "hóa", "hoạ": "họa", "thuỷ": "thủy"}.items():
        text = text.replace(old, new)
    text = text.replace("c.nghệ", "công nghệ")
    text = re.sub(r"\bcông nghệ kt\b", "công nghệ kỹ thuật", text)
    return re.sub(r"[\W_]+", " ", text, flags=re.UNICODE).strip()


def fetch(path):
    with urlopen(BASE + path, timeout=35) as response:
        return json.load(response)


def cell(text):
    return str(text).replace("|", "\\|").replace("\n", " ")


def main():
    parser = argparse.ArgumentParser()
    parser.add_argument("workbook", type=Path)
    args = parser.parse_args()
    institutions, _ = source.parse_excel(args.workbook)
    listing = fetch("/api/schools")
    ids = set()
    for source_row, (targets, _) in CANDIDATES.items():
        ids.update(targets)
        for target in targets:
            root = next((s for s in listing if s["id"] == target), None)
            if root is None:
                raise RuntimeError(f"Audit candidate no longer exists: {target}")
    # Fetch additional profiles for school-group comparisons, without merging them.
    for school in listing:
        if any(school["id"].startswith(prefix) for prefix in [
            "truong-cao-dang-co-dien-nong-nghiep-va-m-",
            "truong-cao-dang-kinh-te-va-cong-nghe-",
            "truong-cao-dang-phat-thanh-truyen-hinh-i-",
            "truong-cao-dang-xay-dung-nam-dinh-co-so-",
        ]):
            ids.add(school["id"])
    with ThreadPoolExecutor(max_workers=5) as pool:
        profiles = dict(zip(sorted(ids), pool.map(lambda i: fetch("/api/schools/" + i), sorted(ids))))
    results = []
    csv_rows = []
    for institution in institutions:
        targets, scope = CANDIDATES[institution["source_row"]]
        for target in targets:
            profile = profiles[target]
            catalog = {key(p["name"]): p["name"] for p in profile.get("training_majors", [])}
            report = {key(p["name"]): p["name"] for p in (profile.get("training_source_report") or {}).get("programs", [])}
            expected = {key(p["name"]): p for p in institution["programs"]}
            missing = [p for k, p in expected.items() if k not in catalog]
            extras = [name for k, name in catalog.items() if k not in expected]
            variants = [{"source": p["source_names"], "web": catalog[k], "rows": p["source_rows"]}
                        for k, p in expected.items() if k in catalog and any(n != catalog[k] for n in p["source_names"])]
            result = {
                "source_name": institution["source_name"], "source_row": institution["source_row"],
                "source_ref": institution["source_ref"], "web_name": profile["name"], "web_id": target,
                "scope": scope, "source_count": len(expected), "catalog_count": len(catalog),
                "matched": len(expected.keys() & catalog.keys()), "missing": missing, "extras": extras,
                "variants": variants, "has_training_report": profile.get("training_source_report") is not None,
                "report_count": len(report), "report_matched": len(expected.keys() & report.keys()),
                "report_missing": [p["name"] for k, p in expected.items() if k not in report],
                "report_extras": [n for k, n in report.items() if k not in expected],
            }
            results.append(result)
            for k, program in expected.items():
                suggestions = difflib.get_close_matches(k, catalog.keys(), n=2, cutoff=.78) if k not in catalog else []
                csv_rows.append([institution["source_name"], profile["name"], target, scope,
                                 ", ".join(map(str, program["source_rows"])), " / ".join(program["source_names"]),
                                 program["name"], catalog.get(k, ""), "Khớp sau chuẩn hóa" if k in catalog else "Chưa có tên tương ứng trong danh mục",
                                 "Có" if k in report else "Không", "; ".join(catalog[s] for s in suggestions)])
            for extra in extras:
                csv_rows.append([institution["source_name"], profile["name"], target, scope, "", "", "", extra, "Chỉ có trong danh mục web", "", ""])
    out = ROOT / "docs" / "doi-chieu-ten-nganh-vps-2026-11-17"
    payload = {"checked_at": datetime.now().astimezone().isoformat(), "workbook": str(args.workbook),
               "source_sha256": source.source_hash(args.workbook), "source_schools": len(institutions),
               "source_rows": sum(len(p["source_rows"]) for s in institutions for p in s["programs"]),
               "source_school_major_pairs": sum(len(s["programs"]) for s in institutions), "results": results,
               "additional_profiles": [{"id": p["id"], "name": p["name"], "parent_school_id": p.get("parent_school_id"),
                   "catalog": sorted({x["name"] for x in p.get("training_majors", [])}),
                   "report": sorted({x["name"] for x in (p.get("training_source_report") or {}).get("programs", [])})}
                   for p in profiles.values()]}
    out.with_suffix(".json").write_text(json.dumps(payload, ensure_ascii=False, indent=2), encoding="utf-8")
    with out.with_suffix(".csv").open("w", encoding="utf-8-sig", newline="") as handle:
        writer = csv.writer(handle)
        writer.writerow(["Trường trong file", "Trường trên web", "ID VPS", "Phạm vi đối chiếu", "Dòng Excel", "Tên nguyên văn trong file", "Tên chuẩn hóa", "Tên trên web", "Kết quả danh mục", "Có trong báo cáo đào tạo web", "Tên gần giống để xem xét, chưa coi là khớp"])
        writer.writerows(csv_rows)
    md = ["# Đối chiếu tên ngành/nghề trong Excel và website ngày 17/11/2026", "",
          f"Nguồn: `{args.workbook}`. Sheet `Mau 1 - KQTS`. Website: {BASE}/map.", "",
          f"Đọc trực tiếp {len(institutions)} trường, {payload['source_rows']} dòng ngành, gộp thành {payload['source_school_major_pairs']} cặp trường–tên ngành. Chỉ so sánh tên; không so sánh số liệu, chỉ tiêu hoặc trình độ.", "",
          "Đọc API công khai `/api/schools/{id}`. Tách hai nơi: danh mục `training_majors` và báo cáo `training_source_report.programs`. Một ngành có trong báo cáo nhưng thiếu trong danh mục được ghi riêng. Các dòng cùng ngành khác trình độ được gộp một tên trong phép đếm. Không chỉnh sửa dữ liệu.", "",
          "Chuẩn hóa Unicode, hoa/thường, khoảng trắng, dấu câu, &/và, ĐHKK/điều hòa không khí, C.nghệ/Công nghệ, Công nghệ KT/Công nghệ kỹ thuật, cách đặt dấu hoà/hòa, hoá/hóa, hoạ/họa, thuỷ/thủy và các lỗi chính tả rõ ràng đã có trong bộ làm sạch. Không tự coi các tên gần giống hoặc cùng mã là một ngành. Những gợi ý gần giống trong CSV cần người kiểm tra, không được tính là khớp.", "",
          "Tên chỉ có trên web không đồng nghĩa sai hoặc phải xóa: file có thể chưa liệt kê hết, hoặc web giữ chương trình cũ. Với trường chia cơ sở/sáp nhập, số đếm chỉ áp dụng cho bản ghi ứng viên đã ghi rõ, không suy rộng toàn trường.", "",
          "## Tổng hợp theo bản ghi web", "",
          "| Trường trong file | Bản ghi web / phạm vi | Tên trong file | Tên danh mục web | Khớp | Chưa có tên tương ứng trong danh mục | Chỉ có trên web | Báo cáo khớp/file |",
          "| --- | --- | ---: | ---: | ---: | ---: | ---: | --- |"]
    for r in results:
        report = f"{r['report_matched']}/{r['source_count']}" if r["has_training_report"] else "Chưa có báo cáo"
        md.append("| " + " | ".join(map(cell, [r["source_name"], r["web_name"] + " — " + r["scope"], r["source_count"], r["catalog_count"], r["matched"], len(r["missing"]), len(r["extras"]), report])) + " |")
    for r in results:
        md += ["", "## " + r["source_name"] + " → " + r["web_name"], "",
               f"Nguồn: `{r['source_ref']}`. ID web: `{r['web_id']}`. Phạm vi: {r['scope']}.", "",
               "### Tên trong file chưa có tương ứng trong danh mục", ""]
        if r["missing"]:
            md += ["| Tên ngành trong file | Dòng Excel | Có trong báo cáo web |", "| --- | --- | --- |"]
            report_missing = set(r["report_missing"])
            for p in r["missing"]:
                md.append(f"| {cell(' / '.join(p['source_names']))} | {', '.join(map(str, p['source_rows']))} | {'Không' if p['name'] in report_missing else 'Có'} |")
        else:
            md += ["Không thiếu tên sau chuẩn hóa."]
        md += ["", "### Tên chỉ có trong danh mục web", "", "; ".join(r["extras"]) or "Không có.", "",
               "### Khác cách viết nhưng khớp sau chuẩn hóa", ""]
        if r["variants"]:
            md += ["| File | Web |", "| --- | --- |"]
            md += [f"| {cell(' / '.join(v['source']))} | {cell(v['web'])} |" for v in r["variants"]]
        else:
            md += ["Không có."]
        if r["has_training_report"]:
            md += ["", f"Báo cáo đào tạo trên web: {r['report_matched']}/{r['source_count']} tên nguồn khớp; chỉ có trong báo cáo: {'; '.join(r['report_extras']) or 'không có'}."]
        else:
            md += ["", "Bản ghi này chưa có báo cáo đào tạo từ nguồn."]
    md += ["", "## Kiểm tra thêm các bản ghi trong nhóm trường", "",
           "Các bản ghi dưới đây được kiểm tra riêng; không tự cộng danh mục của cơ sở con vào trường chính.", ""]
    for p in payload["additional_profiles"]:
        md += [f"- {p['name']} (`{p['id']}`), cha: `{p['parent_school_id'] or 'không có'}`. Danh mục {len(p['catalog'])} tên: {'; '.join(p['catalog']) or 'trống'}. Báo cáo: {len(p['report'])} tên."]
    out.with_suffix(".md").write_text("\n".join(md) + "\n", encoding="utf-8")
    print(json.dumps({k: v for k, v in payload.items() if k not in ["results", "additional_profiles"]}, ensure_ascii=False))
    for r in results:
        print(json.dumps({k: r[k] for k in ["source_row", "source_name", "web_name", "source_count", "catalog_count", "matched", "report_matched", "report_count"]} | {"missing": [p["name"] for p in r["missing"]], "extras": r["extras"]}, ensure_ascii=False))


if __name__ == "__main__":
    main()
