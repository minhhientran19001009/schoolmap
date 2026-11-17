"""Supplement confirmed school-major names in an exported workbook copy."""
from __future__ import annotations

import argparse
from copy import copy
from pathlib import Path
import json
import re

import openpyxl
from openpyxl.styles import Alignment, Font, PatternFill

import auditSchoolMajorNames as audit
import cleanGdnnMapSources as source

ROOT = Path(__file__).resolve().parents[1]


def existing_names(value):
    return [part.split(":", 1)[0].strip() for part in str(value or "").split(";") if part.strip()]


def import_safe_name(value):
    # Semicolons delimit entries in SchoolExcelService, even inside a name.
    return re.sub(r"\s*;\s*", ", ", value).strip()


def format_sheet(sheet, widths):
    sheet.freeze_panes = "A2"
    sheet.auto_filter.ref = sheet.dimensions
    for cell in sheet[1]:
        cell.fill = PatternFill("solid", fgColor="1E40AF")
        cell.font = Font(color="FFFFFF", bold=True)
    for row in sheet.iter_rows():
        for cell in row:
            cell.alignment = Alignment(vertical="top", wrap_text=True)
    for column, width in widths.items():
        sheet.column_dimensions[column].width = width


def main():
    parser = argparse.ArgumentParser()
    parser.add_argument("export", type=Path)
    parser.add_argument("source", type=Path)
    parser.add_argument("output", type=Path)
    parser.add_argument("--include-reviewed-mappings", action="store_true",
                        help="Include the two school mappings explicitly accepted by the user.")
    args = parser.parse_args()
    if args.export.resolve() == args.output.resolve():
        raise RuntimeError("Use a separate output file to retain the original export.")
    original_hash = source.source_hash(args.export)
    schools, _ = source.parse_excel(args.source)
    source_by_row = {s["source_row"]: s for s in schools}
    audited = json.loads((ROOT / "docs/doi-chieu-ten-nganh-vps-2026-11-17.json").read_text(encoding="utf-8"))
    if source.source_hash(args.source) != audited["source_sha256"]:
        raise RuntimeError("Source workbook differs from the audited document.")
    book = openpyxl.load_workbook(args.export)
    main_sheet = book["Mau_Nhap_Lieu"]
    if main_sheet.cell(3, 19).value != "Ngành đào tạo & Chỉ tiêu":
        raise RuntimeError("Unexpected school export format.")
    original_values = {(sheet.title, c.coordinate): c.value for sheet in book for row in sheet for c in row}
    names_to_rows = {}
    for row in range(4, main_sheet.max_row + 1):
        name = main_sheet.cell(row, 1).value
        if name:
            names_to_rows.setdefault(name, []).append(row)
    log = book.create_sheet("Bo_sung_Nganh")
    log.append(["Trường trong file xuất", "Dòng file xuất", "Tên ngành nguồn", "Tên bổ sung", "Dòng nguồn Excel", "Chỉ tiêu nhập", "Giải thích"])
    pending = book.create_sheet("Can_doi_chieu")
    pending.append(["Trường trong nguồn", "Ứng viên trong file xuất", "Dòng file xuất", "Tên ngành nguồn chưa khớp", "Dòng nguồn", "Lý do chưa bổ sung"])
    changed = {}
    covered = []
    seen_sources = set()
    for result in audited["results"]:
        if result["source_row"] in seen_sources:
            continue
        # The whole-school report was previously assigned to Trường Thi.
        # It already contains all 23 names; never copy them into Vị Khê.
        if result["source_row"] == 151 and "truong-th" not in result["web_id"]:
            continue
        seen_sources.add(result["source_row"])
        rows = names_to_rows.get(result["web_name"], [])
        if len(rows) != 1:
            raise RuntimeError(f"Export row is not unique: {result['web_name']}")
        row = rows[0]
        cell = main_sheet.cell(row, 19)
        current_keys = {audit.key(n) for n in existing_names(cell.value)}
        programs = source_by_row[result["source_row"]]["programs"]
        missing = [p for p in programs if audit.key(p["name"]) not in current_keys]
        if result["source_row"] in {214, 240} and not args.include_reviewed_mappings:
            for program in missing:
                pending.append([result["source_name"], result["web_name"], row,
                                " / ".join(program["source_names"]), ", ".join(map(str, program["source_rows"])), result["scope"]])
            continue
        additions = []
        for program in missing:
            name = import_safe_name(program["name"])
            if audit.key(name) in current_keys:
                continue
            additions.append(f"{name}:0")
            current_keys.add(audit.key(name))
            note = "Chỉ bổ sung tên. Chỉ tiêu 0 là giá trị chưa khai báo; không dùng quy mô/kế hoạch nguồn làm chỉ tiêu năm."
            if result["source_row"] in {214, 240}:
                note += " Đã bổ sung vào cơ sở tương ứng theo xác nhận của người dùng."
            if ";" in program["name"]:
                note += " Đổi dấu ; bên trong tên thành dấu phẩy để không bị tách thành nhiều ngành khi nhập."
            log.append([result["web_name"], row, " / ".join(program["source_names"]), name,
                        ", ".join(map(str, program["source_rows"])), 0, note])
        if additions:
            old = str(cell.value or "")
            cell.value = old.rstrip() + ("; " if old.strip() else "") + "; ".join(additions)
            alignment = copy(cell.alignment)
            alignment.wrap_text = True
            cell.alignment = alignment
            main_sheet.row_dimensions[row].height = max(main_sheet.row_dimensions[row].height or 15, 180)
            changed[row] = additions
        covered.append({"name": result["web_name"], "row": row, "added": len(additions), "source_names": len(programs)})
    # Verify every pre-existing value except specifically edited major cells.
    for (sheet, coordinate), value in original_values.items():
        if sheet == main_sheet.title and coordinate in {f"S{row}" for row in changed}:
            continue
        if book[sheet][coordinate].value != value:
            raise AssertionError(f"Unexpected edit: {sheet}!{coordinate}")
    for entry in covered:
        got = {audit.key(n) for n in existing_names(main_sheet.cell(entry["row"], 19).value)}
        source_row = next(r["source_row"] for r in audited["results"] if r["web_name"] == entry["name"])
        expected = {audit.key(import_safe_name(p["name"])) for p in source_by_row[source_row]["programs"]}
        if not expected <= got:
            raise AssertionError(f"Source majors still missing: {entry['name']}")
    format_sheet(log, {"A": 55, "B": 16, "C": 55, "D": 55, "E": 22, "F": 18, "G": 85})
    format_sheet(pending, {"A": 60, "B": 65, "C": 18, "D": 55, "E": 20, "F": 85})
    args.output.parent.mkdir(parents=True, exist_ok=True)
    book.save(args.output)
    check = openpyxl.load_workbook(args.output, data_only=False)
    for (sheet, coordinate), value in original_values.items():
        if sheet == main_sheet.title and coordinate in {f"S{row}" for row in changed}:
            continue
        if check[sheet][coordinate].value != value:
            raise AssertionError(f"Value changed during save: {sheet}!{coordinate}")
    for row in changed:
        assert check[main_sheet.title].cell(row, 19).value == main_sheet.cell(row, 19).value
    check.close()
    assert source.source_hash(args.export) == original_hash
    print(json.dumps({"output": str(args.output), "added_names": sum(map(len, changed.values())),
                      "changed_rows": changed, "pending_names": pending.max_row - 1,
                      "covered_schools": covered, "original_preserved": True}, ensure_ascii=False, indent=2))


if __name__ == "__main__":
    main()
