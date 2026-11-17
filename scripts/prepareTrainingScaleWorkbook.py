"""Prepare training scale figures without changing quotas or enrolled counts."""
from __future__ import annotations

import argparse
from copy import copy
import json
from pathlib import Path

import openpyxl
from openpyxl.styles import Alignment, Font, PatternFill

import cleanGdnnMapSources as source

ROOT = Path(__file__).resolve().parents[1]
FIELDS = ["reported_total", "cao_dang", "trung_cap", "so_cap_va_khac"]


def nullable_sum(values):
    present = [v for v in values if v is not None]
    return sum(present) if present else None


def assess(values):
    total, *levels = values
    component_sum = nullable_sum(levels)
    if total is None and component_sum is None:
        return None, None, "Thiếu số liệu"
    if total is None:
        return component_sum, None, "Tổng trống; chỉ có số theo trình độ"
    if component_sum is None:
        return None, total if total else 0, "Chỉ có tổng; chưa có phân bổ trình độ"
    if total < component_sum:
        return component_sum, None, "Cần xác minh: tổng nhỏ hơn tổng trình độ"
    if total > component_sum:
        return component_sum, total - component_sum, "Có số chưa phân bổ trình độ"
    return component_sum, 0, "Khớp"


def style(sheet, widths):
    sheet.freeze_panes = "A2"
    sheet.auto_filter.ref = sheet.dimensions
    sheet.row_dimensions[1].height = 42
    for cell in sheet[1]:
        cell.fill = PatternFill("solid", fgColor="1E40AF")
        cell.font = Font(bold=True, color="FFFFFF")
    for row in sheet:
        for cell in row:
            cell.alignment = Alignment(vertical="top", wrap_text=True)
            if isinstance(cell.value, (int, float)):
                cell.number_format = "#,##0"
    for col, width in widths.items():
        sheet.column_dimensions[col].width = width


def main():
    parser = argparse.ArgumentParser()
    parser.add_argument("export", type=Path)
    parser.add_argument("source", type=Path)
    parser.add_argument("output", type=Path)
    args = parser.parse_args()
    if args.export.resolve() == args.output.resolve():
        raise RuntimeError("Output must differ from the previous workbook.")
    hashes = {str(p): source.source_hash(p) for p in [args.export, args.source]}
    schools, _ = source.parse_excel(args.source)
    original = openpyxl.load_workbook(args.source, data_only=True)["Mau 1 - KQTS"]
    audited = json.loads((ROOT / "docs/doi-chieu-ten-nganh-vps-2026-11-17.json").read_text(encoding="utf-8"))
    if hashes[str(args.source)] != audited["source_sha256"]:
        raise RuntimeError("Source differs from the reviewed workbook.")
    mapping = {}
    for item in audited["results"]:
        if item["source_row"] == 151 and "truong-th" not in item["web_id"]:
            continue
        mapping[item["source_row"]] = item
    book = openpyxl.load_workbook(args.export)
    original_values = {(s.title, c.coordinate): c.value for s in book for row in s for c in row}
    main_sheet = book["Mau_Nhap_Lieu"]
    school_rows = {main_sheet.cell(r, 1).value: r for r in range(4, main_sheet.max_row + 1)}
    guide = book.create_sheet("HD_Quy_mo")
    guide.append(["Nội dung", "Quy ước"])
    guide.append(["Nguồn", args.source.name + "; sheet Mau 1 - KQTS; cột F–I: Quy mô đào tạo."])
    guide.append(["Phạm vi", "Chỉ quy mô đào tạo hiện tại. Không lấy cột J–M dự kiến tuyển sinh 2026."])
    guide.append(["Mau_Nhap_Lieu", "Giữ nguyên mọi ô trong tab nhập liệu, bao gồm 102 tên ngành đã bổ sung. Quy mô đào tạo không tự gán sang tổng sinh viên đang học, tuyển sinh hàng năm hay chỉ tiêu ngành."])
    guide.append(["QM_Tong_hop", "15 cơ sở; số tổng và CĐ/TC/SC và khác giữ nguyên ô nguồn. Các cột cộng đối chiếu được tách riêng."])
    guide.append(["QM_Theo_nganh", "256 dòng trường–tên ngành đã gộp tên trùng qua các trình độ. Ô gộp số không có dữ liệu nào vẫn để trống; 0 là số 0 nguồn."])
    guide.append(["QM_Dong_goc", "304 dòng ngành từ file nguồn, nguyên số liệu F–I và tên ngành gốc; dùng truy vết, không cộng cùng với sheet đã gộp."])
    guide.append(["QM_Can_xac_minh", "Những tổng khác tổng trình độ và tổng trống; không tự sửa tổng nguồn hoặc tự phân bổ chênh lệch."])
    guide.append(["Ghép cơ sở", "Dùng các dòng đã đối chiếu và hai ánh xạ người dùng đã đồng ý. Báo cáo số 20/BQP giữ tại Trường Thi theo lần đối chiếu trước, không nhân đôi sang Vị Khê."])
    guide.append(["Cách tải lên web", "Sau khi cập nhật code mới: Admin → Danh sách Trường học → Nhập quy mô đào tạo. Chọn file này và giữ Chỉ kiểm tra để đối chiếu; bỏ chọn để ghi. Chức năng này đọc QM_Tong_hop và QM_Theo_nganh, không nhập lại thông tin trường/chỉ tiêu từ tab đầu."])
    guide.append(["Mã ngành", "Không sửa mã ngành và không tạo cấu trúc mã mới."])
    guide.append(["Tệp nguồn SHA256", hashes[str(args.source)]])
    summary = book.create_sheet("QM_Tong_hop")
    summary.append(["Tên cơ sở trong nguồn", "Tên cơ sở trong mẫu nhập", "Dòng mẫu nhập", "Mã nhóm trong mẫu nhập", "Dòng tổng nguồn", "Tổng quy mô nguồn", "Cao đẳng nguồn", "Trung cấp nguồn", "Sơ cấp và khác nguồn", "Cộng các trình độ ở dòng tổng", "Chưa phân bổ tại dòng tổng", "Tổng cộng từ các dòng ngành (đối chiếu)", "Trạng thái dòng tổng", "Ghi nhận"])
    detail = book.create_sheet("QM_Theo_nganh")
    detail.append(["Tên cơ sở trong nguồn", "Tên cơ sở trong mẫu nhập", "Dòng mẫu nhập", "Tên ngành chuẩn hóa", "Tên ngành nguyên văn", "Dòng nguồn", "Tổng quy mô nguồn (gộp)", "Cao đẳng nguồn (gộp)", "Trung cấp nguồn (gộp)", "Sơ cấp và khác nguồn (gộp)", "Cộng các trình độ", "Chưa phân bổ", "Trạng thái"])
    raw = book.create_sheet("QM_Dong_goc")
    raw.append(["Tên cơ sở trong nguồn", "Tên cơ sở trong mẫu nhập", "Dòng mẫu nhập", "Tên ngành gốc", "Dòng nguồn", "Tổng quy mô nguồn", "Cao đẳng nguồn", "Trung cấp nguồn", "Sơ cấp và khác nguồn", "Cộng các trình độ", "Chưa phân bổ", "Trạng thái"])
    issues = book.create_sheet("QM_Can_xac_minh")
    issues.append(["Cấp dữ liệu", "Tên cơ sở", "Tên ngành", "Dòng nguồn", "Tổng nguồn", "Cộng trình độ", "Chưa phân bổ", "Trạng thái", "Cách xử lý trong file"])
    result = []
    program_count = raw_count = 0
    for school in schools:
        target = mapping[school["source_row"]]
        row = school_rows[target["web_name"]]
        end = int(school["source_ref"].split(":")[-1])
        total_values = [source.integer_or_none(original.cell(end, c).value) for c in range(6, 10)]
        comp, unallocated, status = assess(total_values)
        computed = school["summary"]["current_scale"]["computed_from_clean_programs"]["clean_total"]
        note = ""
        if total_values[0] is not None and total_values[0] != computed:
            note = f"Tổng nguồn {total_values[0]} khác tổng đối chiếu từ ngành {computed}; chưa sửa số gốc."
        if school["source_row"] == 240:
            note += " Ghép vào Khu A theo xác nhận người dùng ở bước bổ sung ngành."
        if school["source_row"] == 214:
            note += " Ghép vào Phân hiệu Ninh Bình theo xác nhận người dùng ở bước bổ sung ngành."
        summary.append([school["source_name"], target["web_name"], row, main_sheet.cell(row, 2).value, end, *total_values, comp, unallocated, computed, status, note])
        if status != "Khớp":
            issues.append(["Tổng cơ sở", school["source_name"], "", end, total_values[0], comp, unallocated, status,
                           "Giữ nguyên số nguồn; chỉ cộng đối chiếu riêng. Chưa coi tổng tính lại là số được xác nhận."])
        for program in school["programs"]:
            raw_values = []
            for source_row in program["source_rows"]:
                values = [source.integer_or_none(original.cell(source_row, c).value) for c in range(6, 10)]
                raw_values.append(values)
                pcomp, punalloc, pstatus = assess(values)
                raw.append([school["source_name"], target["web_name"], row, source.compact(original.cell(source_row, 5).value), source_row, *values, pcomp, punalloc, pstatus])
                raw_count += 1
                if pstatus in {"Cần xác minh: tổng nhỏ hơn tổng trình độ", "Có số chưa phân bổ trình độ", "Chỉ có tổng; chưa có phân bổ trình độ", "Thiếu số liệu"}:
                    issues.append(["Dòng ngành", school["source_name"], source.compact(original.cell(source_row, 5).value), source_row,
                                   values[0], pcomp, punalloc, pstatus, "Giữ nguyên ô trống/0 và số nguồn. Không tự phân bổ sang trình độ khác."])
            merged = [nullable_sum(values[c] for values in raw_values) for c in range(4)]
            pcomp, punalloc, pstatus = assess(merged)
            detail.append([school["source_name"], target["web_name"], row, program["name"], " / ".join(program["source_names"]), ", ".join(map(str, program["source_rows"])), *merged, pcomp, punalloc, pstatus])
            program_count += 1
        result.append({"source_name": school["source_name"], "target_name": target["web_name"], "export_row": row,
                       "total_source": total_values[0], "college": total_values[1], "intermediate": total_values[2],
                       "elementary_other": total_values[3], "unallocated": unallocated, "computed_total": computed, "status": status})
    for sheet in [summary, detail, raw, issues]:
        style(sheet, {"A": 58, "B": 58, "C": 22, "D": 48, "E": 35, "F": 24, "G": 24, "H": 24, "I": 24, "J": 25, "K": 25, "L": 30, "M": 48, "N": 75})
    style(guide, {"A": 30, "B": 130})
    guide.row_dimensions[4].height = 65
    for sheet in [summary, detail, raw]:
        for cells in sheet.iter_rows(min_row=2):
            for c in cells:
                if isinstance(c.value, str) and ("Cần xác minh" in c.value or "chưa phân bổ" in c.value):
                    c.fill = PatternFill("solid", fgColor="FEF3C7")
    args.output.parent.mkdir(parents=True, exist_ok=True)
    book.save(args.output)
    reread = openpyxl.load_workbook(args.output, data_only=False)
    assert program_count == 256 and raw_count == 304 and len(result) == 15
    for (title, coord), value in original_values.items():
        assert reread[title][coord].value == value, (title, coord)
    for s in [summary, detail, raw, issues]:
        for cells in s:
            for c in cells:
                assert reread[s.title][c.coordinate].value == (None if c.value == "" else c.value), (s.title, c.coordinate)
    for path, digest in hashes.items():
        assert source.source_hash(Path(path)) == digest
    report_path = args.output.with_suffix(".md")
    md = ["# Phân tích quy mô đào tạo từ tài liệu ngày 17/11/2026", "",
          f"Nguồn: `{args.source}`. Workbook đầu vào: `{args.export.name}`. Workbook kết quả: `{args.output.name}`.", "",
          "Phạm vi: cột F–I của sheet Mau 1 - KQTS. Không dùng cột J–M dự kiến tuyển sinh. Giữ nguyên toàn bộ các sheet cũ, gồm 102 tên ngành đã bổ sung. Không đổi tổng sinh viên đang học, chỉ tiêu, tuyển sinh năm hoặc mã ngành.", "",
          "Tệp mới có 15 dòng tổng cơ sở, 256 dòng ngành đã gộp và 304 dòng ngành gốc. Số 0 được giữ là 0, ô trống được giữ trống. Không cộng trùng các dòng tổng với các dòng ngành hoặc sheet gộp với sheet gốc.", "",
          "| Cơ sở | Tổng nguồn | CĐ | TC | SC và khác | Chưa phân bổ | Tổng từ dòng ngành để đối chiếu | Trạng thái |",
          "| --- | ---: | ---: | ---: | ---: | ---: | ---: | --- |"]
    fmt = lambda v: "Trống" if v is None else f"{v:,}".replace(",", ".")
    for r in result:
        md.append("| " + " | ".join([r["source_name"], *[fmt(r[k]) for k in ["total_source", "college", "intermediate", "elementary_other", "unallocated", "computed_total"]], r["status"]]) + " |")
    md += ["", "Các điểm cần xác minh:", "",
           "- Cơ điện Nông nghiệp và Môi trường: tổng 9.650, các trình độ cộng 9.550. Phần 100 nằm ở Sư phạm GDNN (dòng 135); không tự phân bổ.",
           "- Kinh tế và Công nghệ tỉnh Ninh Bình: tổng 6.005, CĐ + TC là 5.540. Ngành Đào tạo lái xe (dòng 282) ghi tổng 500 nhưng TC 35, phần chênh 465 chưa phân bổ.",
           "- Đại Lâm: tổng nguồn ghi 0, TC 450 và SC/khác 4.700. Tổng 5.150 chỉ là số cộng đối chiếu, chưa thay tổng gốc thành số này.",
           "- Nam Tam Điệp: tổng nguồn trống, SC và khác 8.500; chỉ tính 8.500 ở cột đối chiếu, giữ ô tổng nguồn trống.", "",
           "Cộng các dòng ngành phục vụ đối chiếu là 46.146, gồm CĐ 5.985, TC 19.416, SC/khác 20.180 và chưa phân bổ 565. Đây không phải tổng đã xác nhận để tự ghi vào chỉ tiêu hoặc số sinh viên.", "",
           "Sau khi cập nhật code mới, vào Admin → Danh sách Trường học → Nhập quy mô đào tạo. Chọn workbook này và chạy chế độ Chỉ kiểm tra trước; bỏ chọn để ghi. Bộ nhập riêng chỉ xử lý các sheet QM_Tong_hop và QM_Theo_nganh; giữ nguyên mã ngành, chỉ tiêu, số sinh viên và thông tin trường. Bảng chính ở Tổng quan hiển thị quy mô, đơn vị người học; nguồn trống hiển thị —, nguồn 0 hiển thị 0; tổng mâu thuẫn không tự chốt. Có sao lưu báo cáo bị tác động và transaction trước khi ghi."]
    report_path.write_text("\n".join(md) + "\n", encoding="utf-8")
    print(json.dumps({"output": str(args.output), "report": str(report_path), "school_count": len(result),
                      "grouped_programs": program_count, "source_rows": raw_count, "original_sheets_preserved": True,
                      "summary": result}, ensure_ascii=False, indent=2))


if __name__ == "__main__":
    main()
