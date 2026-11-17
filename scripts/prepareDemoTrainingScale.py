"""Fill missing local demo scale figures; retain source sheets and per-cell provenance."""
from __future__ import annotations

from concurrent.futures import ThreadPoolExecutor
import hashlib
import json
from pathlib import Path
import random
import re
import unicodedata
from urllib.request import urlopen

import openpyxl
from openpyxl.styles import Alignment

from prepareTrainingScaleWorkbook import style

ROOT = Path(__file__).resolve().parents[1]
INPUT = ROOT / "docs/exports/Danh_Sach_Truong_Hoc_Ninh_Binh_2026_11_17_Nganh_Va_Quy_Mo.xlsx"
OUTPUT = ROOT / "docs/exports/Danh_Sach_Truong_Hoc_Ninh_Binh_2026_11_17_Quy_Mo_Demo.xlsx"
FIELDS = ["reported_total", "cao_dang", "trung_cap", "so_cap_va_khac", "dai_hoc", "gdtx"]
PRIMARY = {"cao_dang": 1, "trung_cap": 2, "so_cap": 3, "so_cap_va_khac": 3, "dai_hoc": 4, "gdtx": 5}


def key(text):
    return re.sub(r"[^\w]+", " ", unicodedata.normalize("NFC", text).casefold()).strip()


def get(path):
    with urlopen("http://127.0.0.1:8000" + path, timeout=30) as response:
        return json.load(response)


def main():
    listing = get("/api/schools")
    with ThreadPoolExecutor(max_workers=5) as pool:
        profiles = list(pool.map(lambda s: get("/api/schools/" + s["id"]), listing))
    by_name = {key(s["name"]): s for s in profiles}
    assert len(by_name) == len(profiles), "Duplicate school names need review."
    book = openpyxl.load_workbook(INPUT)
    summary = book["QM_Tong_hop"]
    details = book["QM_Theo_nganh"]
    main_sheet = book["Mau_Nhap_Lieu"]
    book.copy_worksheet(summary).title = "QM_Tong_hop_goc"
    book.copy_worksheet(details).title = "QM_Theo_nganh_goc"
    summary["O1"] = "Trường số giả lập"
    summary["P1"] = "Đại học"
    summary["Q1"] = "GDTX - GDNN"
    details["N1"] = "Trường số giả lập"
    details["O1"] = "Đại học"
    details["P1"] = "GDTX - GDNN"
    tracking = book.create_sheet("Theo_doi_so_demo")
    tracking.append(["Sheet", "Ô", "Trường", "Ngành", "Giá trị trước", "Giá trị sau", "Nguồn số liệu"])
    entries = {}
    fake_count = derived_count = 0

    def change(sheet, row, column, value, school, major, generated=True):
        nonlocal fake_count, derived_count
        old = sheet.cell(row, column).value
        assert old is None, f"Never overwrite an existing source value: {sheet.title}!{row}:{column}"
        sheet.cell(row, column, value)
        tracking.append([sheet.title, sheet.cell(row, column).coordinate, school, major, None, value,
                         "Giả lập phục vụ demo" if generated else "Cộng từ số liệu đã có"])
        if generated:
            fake_count += 1
        else:
            derived_count += 1

    for row in range(2, summary.max_row + 1):
        target_name = summary.cell(row, 2).value
        assert key(target_name) in by_name, f"No unique local school for {target_name}"
        profile = by_name[key(target_name)]
        entries[profile["id"]] = {"summary_row": row, "main_row": int(summary.cell(row, 3).value), "profile": profile}

    for profile in profiles:
        if profile["id"] in entries:
            continue
        matching = [r for r in range(4, main_sheet.max_row + 1) if key(str(main_sheet.cell(r, 1).value or "")) == key(profile["name"])]
        assert len(matching) <= 1
        if matching:
            main_row = matching[0]
        else:
            main_row = main_sheet.max_row + 1
            main_sheet.cell(main_row, 1, profile["name"])
            main_sheet.cell(main_row, 2, profile.get("code"))
            main_sheet.cell(main_row, 4, profile.get("education_level_name"))
        name = main_sheet.cell(main_row, 1).value
        summary.append([name, name, main_row, main_sheet.cell(main_row, 2).value])
        summary_row = summary.max_row
        entries[profile["id"]] = {"summary_row": summary_row, "main_row": main_row, "profile": profile}
        source_programs = (profile.get("training_source_report") or {}).get("programs", [])
        if source_programs:
            old_summary = (profile["training_source_report"].get("summary", {}).get("current_scale") or {})
            old_total = old_summary.get("source_total_row") or {}
            for column, field in zip([6, 7, 8, 9, 16, 17], FIELDS):
                summary.cell(summary_row, column, old_total.get(field))
            summary.cell(summary_row, 15, ", ".join(old_summary.get("demo_fields", [])) or None)
            for program in source_programs:
                data = program.get("current_scale", {})
                details.append([name, name, main_row, program["name"], program["name"], None,
                                data.get("reported_total"), data.get("cao_dang"), data.get("trung_cap"), data.get("so_cap_va_khac")])
                details.cell(details.max_row, 15, data.get("dai_hoc"))
                details.cell(details.max_row, 16, data.get("gdtx"))
                details.cell(details.max_row, 14, ", ".join(program.get("demo_fields", [])) or None)
        else:
            majors = {}
            for major in profile.get("training_majors", []):
                if not major.get("name"):
                    continue
                majors.setdefault(key(major["name"]), major["name"])
            if not majors:
                raise RuntimeError("No named major for " + name + "; do not invent a training programme.")
            for major in majors.values():
                details.append([name, name, main_row, major, major])

    by_main = {e["main_row"]: e for e in entries.values()}
    detail_by_main = {}
    columns = [7, 8, 9, 10, 15, 16]
    for row in range(2, details.max_row + 1):
        main_row = int(details.cell(row, 3).value)
        entry = by_main[main_row]
        school = details.cell(row, 2).value
        major = details.cell(row, 4).value
        profile = entry["profile"]
        rng = random.Random(hashlib.sha256((profile["id"] + "|" + major).encode()).hexdigest())
        values = [details.cell(row, col).value for col in columns]
        old = values[:]
        preferred = PRIMARY.get(str(profile.get("education_level") or "cao_dang").replace("-", "_"), 1)
        if any(token in major.casefold() for token in ["lái xe", "sơ cấp", "ngắn hạn"]):
            preferred = 3
        known_sum = sum(v for v in values[1:] if v is not None)
        entirely_missing = all(v is None for v in values)
        if entirely_missing:
            amount = rng.randrange(30, 141) * 5 if preferred == 4 else rng.randrange(8, 61) * 5
            values = [amount, 0, 0, 0, 0, 0]
            values[preferred] = amount
        else:
            missing_levels = [i for i in range(1, 6) if values[i] is None]
            residual = max(0, values[0] - known_sum) if values[0] is not None else 0
            allocate_to = preferred if preferred in missing_levels else (missing_levels[0] if missing_levels else None)
            for i in missing_levels:
                values[i] = residual if i == allocate_to else 0
            if values[0] is None:
                values[0] = sum(values[1:])
        fake_fields = [f.strip() for f in str(details.cell(row, 14).value or "").split(",") if f.strip()]
        for i, value in enumerate(values):
            if old[i] is None:
                generated = entirely_missing or i != 0
                change(details, row, columns[i], value, school, major, generated)
                if generated:
                    fake_fields.append(FIELDS[i])
        details.cell(row, 14, ", ".join(dict.fromkeys(fake_fields)) if fake_fields else None)
        details.cell(row, 11, sum(values[1:]))
        details.cell(row, 12, max(0, values[0] - sum(values[1:])) if values[0] >= sum(values[1:]) else None)
        details.cell(row, 13, "Cần đối chiếu số tổng nguồn" if values[0] < sum(values[1:]) else ("Có số giả lập phục vụ demo" if fake_fields else "Khớp"))
        detail_by_main.setdefault(main_row, []).append(values)

    for entry in entries.values():
        row = entry["summary_row"]
        main_row = entry["main_row"]
        school = summary.cell(row, 2).value
        data = detail_by_main[main_row]
        totals = [sum(v[i] for v in data) for i in range(6)]
        fake_fields = [f.strip() for f in str(summary.cell(row, 15).value or "").split(",") if f.strip()]
        is_new_report = row > book["QM_Tong_hop_goc"].max_row
        summary_columns = [6, 7, 8, 9, 16, 17]
        for i, column in enumerate(summary_columns):
            if summary.cell(row, column).value is None:
                generated = is_new_report or i != 0
                change(summary, row, column, totals[i], school, "Tổng cơ sở", generated)
                if generated:
                    fake_fields.append(FIELDS[i])
        summary.cell(row, 15, ", ".join(dict.fromkeys(fake_fields)) if fake_fields else None)
        summary.cell(row, 10, sum(summary.cell(row, c).value for c in [7, 8, 9, 16, 17]))
        summary.cell(row, 12, sum(t[0] for t in data))
        summary.cell(row, 13, "Có số giả lập phục vụ demo" if fake_fields else "Giữ số nguồn")
    guide = book["HD_Quy_mo"]
    guide.append(["Phiên bản demo", "File này có số giả lập ở những ô trống/báo cáo chưa có; các số nguồn đã có, kể cả 0 và tổng mâu thuẫn, giữ nguyên. Sheet Theo_doi_so_demo ghi từng ô. Không coi đây là báo cáo chính thức."])
    guide.append(["Phạm vi tải", "Nhập bằng chức năng Nhập quy mô đào tạo, không dùng nhập thông tin trường. Các dòng cơ sở thêm để tham chiếu chỉ chứa tên/mã/cấp học."])
    for sheet in [summary, details, tracking]:
        style(sheet, {"A": 55, "B": 55, "C": 22, "D": 50, "E": 40, "F": 25, "G": 24, "H": 24, "I": 24, "J": 24, "K": 24, "L": 24, "M": 42, "N": 65, "O": 65, "P": 25, "Q": 25})
    book.save(OUTPUT)
    verify = openpyxl.load_workbook(OUTPUT, data_only=True)
    for title in ["QM_Tong_hop", "QM_Theo_nganh"]:
        original = verify[title + "_goc"]
        changed = verify[title]
        number_columns = range(6, 10) if title == "QM_Tong_hop" else range(7, 11)
        for row in range(2, original.max_row + 1):
            for column in number_columns:
                value = original.cell(row, column).value
                assert value is None or changed.cell(row, column).value == value
    result = {"output": str(OUTPUT), "schools": len(entries), "programs": details.max_row - 1,
              "generated_cells": fake_count, "derived_totals": derived_count, "source_numbers_preserved": True}
    print(json.dumps(result, ensure_ascii=False))
    OUTPUT.with_suffix(".json").write_text(json.dumps(result, ensure_ascii=False, indent=2), encoding="utf-8")


if __name__ == "__main__":
    main()
