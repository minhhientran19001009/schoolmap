from __future__ import annotations

import csv
import hashlib
import json
import re
import unicodedata
from collections import Counter, defaultdict
from copy import copy
from datetime import date
from pathlib import Path
from typing import Any
from xml.etree import ElementTree as ET
from zipfile import ZipFile

from openpyxl import Workbook, load_workbook
from openpyxl.styles import Alignment, Font, PatternFill
from openpyxl.utils import get_column_letter


ROOT = Path(__file__).resolve().parents[1]
SOURCE_DIR = ROOT / "tai_lieu"
OUTPUT_DIR = SOURCE_DIR / "du_lieu_sach"
DATA_OUTPUT = ROOT / "backend" / "database" / "data" / "gdnn_map_clean_2026_11_10.json"
REPORT_OUTPUT = ROOT / "docs" / "bao-cao-lam-sach-du-lieu-gdnn-2026-11-10.md"
GENERATED_DATE = date(2026, 11, 10).isoformat()


def compact(value: Any) -> str:
    if value is None:
        return ""
    text = unicodedata.normalize("NFC", str(value))
    text = text.replace("\u00a0", " ").replace("\u200b", "")
    return re.sub(r"\s+", " ", text).strip()


def ascii_key(value: Any) -> str:
    text = compact(value).lower().replace("đ", "d")
    text = unicodedata.normalize("NFD", text)
    text = "".join(char for char in text if unicodedata.category(char) != "Mn")
    return re.sub(r"[^a-z0-9]+", "-", text).strip("-")


def integer_or_none(value: Any) -> int | None:
    if value is None or compact(value) in {"", "-", "—"}:
        return None
    if isinstance(value, bool):
        return int(value)
    if isinstance(value, (int, float)):
        return int(round(value))
    cleaned = compact(value).replace(" ", "").replace(",", "")
    try:
        return int(round(float(cleaned)))
    except ValueError:
        return None


def normalized_school_code(value: Any) -> str | None:
    text = compact(value).upper().replace("Đ", "D")
    text = unicodedata.normalize("NFD", text)
    text = "".join(char for char in text if unicodedata.category(char) != "Mn")
    text = re.sub(r"[^A-Z0-9]+", "", text)
    return text or None


SCHOOL_NAME_CORRECTIONS = {
    ascii_key("Trường Cao đẳng công nghệ kinh tế và chế biến lâm sản"):
        "Trường Cao đẳng Công nghệ, Kinh tế và Chế biến lâm sản",
    ascii_key("Trường cao đẳng Phát thanh - Truyền hình I"):
        "Trường Cao đẳng Phát thanh - Truyền hình I",
    ascii_key("Trường Cao đẳng nghề số 20/BQP"):
        "Trường Cao đẳng nghề số 20/BQP",
    ascii_key("Trường trung cấp Đại Lâm"):
        "Trường Trung cấp Đại Lâm",
    ascii_key("Trường trung cấp nghề Quốc Tế"):
        "Trường Trung cấp nghề Quốc tế",
}


MAJOR_EXACT_CORRECTIONS = {
    ascii_key("Nguội lắp rắp cơ khí"): "Nguội lắp ráp cơ khí",
    ascii_key("Kỹ thuật sửa chữa lắp giáp máy tính"): "Kỹ thuật sửa chữa, lắp ráp máy tính",
    ascii_key("Ký thuật trông rau an toàn"): "Kỹ thuật trồng rau an toàn",
    ascii_key("K.thuật Sơn mài & khảm trai"): "Kỹ thuật sơn mài và khảm trai",
    ascii_key("Mộc XD và T.Trí nội thất"): "Mộc xây dựng và trang trí nội thất",
    ascii_key("Kỹ thuật máy lạnh & ĐHKK"): "Kỹ thuật máy lạnh và điều hòa không khí",
    ascii_key("Lái xe ôtô"): "Lái xe ô tô",
    ascii_key("Thú Y"): "Thú y",
}


def clean_school_name(value: Any) -> str:
    text = compact(value).strip(" ,;.-")
    return SCHOOL_NAME_CORRECTIONS.get(ascii_key(text), text)


def clean_major_name(value: Any) -> str:
    text = compact(value).strip(" ,;.-")
    if not text:
        return ""
    corrected = MAJOR_EXACT_CORRECTIONS.get(ascii_key(text))
    if corrected:
        return corrected
    text = re.sub(r"\bĐHKK\b", "điều hòa không khí", text, flags=re.IGNORECASE)
    text = re.sub(r"\bôt\s*ô\b", "ô tô", text, flags=re.IGNORECASE)
    text = re.sub(r"\s*&\s*", " và ", text)
    text = re.sub(r"\s+", " ", text).strip()
    return text[:1].upper() + text[1:] if text else text


def extract_codes(value: Any) -> list[str]:
    text = compact(value)
    if not text:
        return []
    return list(dict.fromkeys(re.findall(r"(?<!\d)\d{7,8}(?!\d)", text)))


def values_for_period(row_values: list[Any], offset: int) -> dict[str, Any]:
    reported = integer_or_none(row_values[offset])
    levels = {
        "cao_dang": integer_or_none(row_values[offset + 1]),
        "trung_cap": integer_or_none(row_values[offset + 2]),
        "so_cap_va_khac": integer_or_none(row_values[offset + 3]),
    }
    present = [value for value in levels.values() if value is not None]
    component_sum = sum(present) if present else None
    issues: list[str] = []
    unallocated = 0

    if reported is None and component_sum is None:
        clean_total = None
    elif component_sum is None or component_sum == 0:
        clean_total = reported if reported is not None else component_sum
        if reported and reported > 0:
            unallocated = reported
    elif reported is None:
        clean_total = component_sum
    elif reported == component_sum:
        clean_total = reported
    elif reported > component_sum:
        clean_total = reported
        unallocated = reported - component_sum
        issues.append("Tổng lớn hơn tổng các trình độ; phần chênh được giữ ở cột chưa phân bổ.")
    else:
        clean_total = component_sum
        issues.append("Tổng nhỏ hơn tổng các trình độ; dùng tổng các trình độ và yêu cầu xác minh.")

    return {
        "reported_total": reported,
        "clean_total": clean_total,
        **levels,
        "unallocated": unallocated,
        "issues": issues,
    }


def add_optional(left: int | None, right: int | None) -> int | None:
    if left is None and right is None:
        return None
    return (left or 0) + (right or 0)


def merge_period(target: dict[str, Any], source: dict[str, Any]) -> None:
    for key in ["reported_total", "clean_total", "cao_dang", "trung_cap", "so_cap_va_khac", "unallocated"]:
        target[key] = add_optional(target.get(key), source.get(key))
    target["issues"].extend(source.get("issues", []))
    target["issues"] = list(dict.fromkeys(target["issues"]))


def source_hash(path: Path) -> str:
    digest = hashlib.sha256()
    with path.open("rb") as handle:
        for chunk in iter(lambda: handle.read(1024 * 1024), b""):
            digest.update(chunk)
    return digest.hexdigest()


def load_existing_school_mappings() -> list[dict[str, Any]]:
    source = ROOT / "src" / "data" / "admissionResults.json"
    if not source.is_file():
        return []
    return json.loads(source.read_text(encoding="utf-8"))["institutions"]


CONFIRMED_PROJECT_IDS = {
    ascii_key("Trường Cao đẳng Văn hóa Nghệ thuật và Du lịch Nam Định"):
        "truong-cao-dang-van-hoa-nghe-thuat-va-du-519",
    ascii_key("Trường Cao đẳng Công nghệ, Kinh tế và Chế biến lâm sản"):
        "truong-cao-dang-cong-nghe-kinh-te-va-che-885",
    ascii_key("Trường Cao đẳng Công nghiệp Nam Định"):
        "truong-cao-dang-cong-nghiep-nam-dinh-351",
    ascii_key("Trường Cao đẳng Phát thanh - Truyền hình I"): "HN-CD-004",
    ascii_key("Trường Cao đẳng nghề số 20/BQP"):
        "truong-cao-dang-nghe-so-20-bqp-truong-th-148",
    ascii_key("Trường Cao đẳng Xây dựng Nam Định"):
        "truong-cao-dang-xay-dung-nam-dinh-174",
    ascii_key("Trường Trung cấp Đại Lâm"): "truong-trung-cap-dai-lam-111",
    ascii_key("Trường Trung cấp Kỹ thuật - Du lịch Công đoàn Ninh Bình"):
        "truong-trung-cap-ky-thuat-du-lich-cong-d-174",
    ascii_key("Trường Trung cấp Công nghệ số 8 Nam Định"):
        "truong-trung-cap-cong-nghe-so-8-nam-dinh-462",
    ascii_key("Trường Trung cấp GTVT Ninh Bình"):
        "truong-trung-cap-giao-thong-van-tai-tinh-836",
}

REVIEW_PROJECT_IDS = {
    ascii_key("Trường Cao đẳng Cơ điện Nông nghiệp và Môi trường"):
        "truong-cao-dang-co-gioi-ninh-binh",
    ascii_key("Trường Cao đẳng Kinh tế và Công nghệ tỉnh Ninh Bình"):
        "truong-cao-dang-kinh-te-va-cong-nghe-nam-928",
}


def project_match(name: str, existing_ids: set[str]) -> dict[str, Any]:
    key = ascii_key(name)
    confirmed = CONFIRMED_PROJECT_IDS.get(key)
    if confirmed and confirmed in existing_ids:
        return {"match_status": "matched", "project_school_id": confirmed, "candidate_project_school_id": None}
    candidate = REVIEW_PROJECT_IDS.get(key)
    if candidate and candidate in existing_ids:
        return {"match_status": "needs_review", "project_school_id": None, "candidate_project_school_id": candidate}
    return {"match_status": "unmatched", "project_school_id": None, "candidate_project_school_id": None}


def category_before(ws: Any, row: int) -> str:
    for probe in range(row - 1, 0, -1):
        value = compact(ws.cell(probe, 2).value).lower()
        if "trường cao đẳng" in value:
            return "cao_dang"
        if "trường trung cấp" in value:
            return "trung_cap"
        if "các trung tâm" in value:
            return "trung_tam"
    return "chua_xac_dinh"


def total_row_after(ws: Any, start_row: int) -> int:
    for row in range(start_row, ws.max_row + 1):
        label = compact(ws.cell(row, 5).value).lower()
        if label.startswith("tổng"):
            return row
    raise RuntimeError(f"Không tìm thấy dòng tổng sau dòng {start_row}")


def block_start_before(ws: Any, school_row: int) -> int:
    start = school_row
    probe = school_row - 1
    while probe > 4:
        label = compact(ws.cell(probe, 5).value).lower()
        section = compact(ws.cell(probe, 2).value).lower()
        if label.startswith("tổng") or section in {"trường cao đẳng", "trường trung cấp", "các trung tâm"}:
            break
        if compact(ws.cell(probe, 4).value) or compact(ws.cell(probe, 5).value):
            start = probe
            probe -= 1
            continue
        break
    return start


def parse_excel(path: Path) -> tuple[list[dict[str, Any]], list[dict[str, Any]]]:
    workbook_formula = load_workbook(path, data_only=False)
    workbook_values = load_workbook(path, data_only=True)
    ws_formula = workbook_formula["Mau 1 - KQTS"]
    ws_values = workbook_values["Mau 1 - KQTS"]
    existing = load_existing_school_mappings()
    existing_ids = {str(item["school_id"]) for item in existing}
    issues: list[dict[str, Any]] = []
    schools: list[dict[str, Any]] = []

    school_rows = []
    for row in range(1, ws_formula.max_row + 1):
        serial = ws_formula.cell(row, 1).value
        name = compact(ws_formula.cell(row, 2).value)
        if isinstance(serial, (int, float)) and name:
            school_rows.append(row)

    for school_row in school_rows:
        start_row = block_start_before(ws_formula, school_row)
        end_row = total_row_after(ws_formula, school_row)
        source_name = compact(ws_formula.cell(school_row, 2).value)
        clean_name = clean_school_name(source_name)
        source_code = compact(ws_formula.cell(school_row, 3).value) or None
        code = normalized_school_code(source_code)
        match = project_match(clean_name, existing_ids)
        school: dict[str, Any] = {
            "source_ref": f"Mau 1 - KQTS!{start_row}:{end_row}",
            "source_row": school_row,
            "source_name": source_name,
            "name": clean_name,
            "source_code": source_code,
            "code": code,
            "school_type": category_before(ws_formula, school_row),
            **match,
            "programs": [],
            "issues": [],
        }
        merged_programs: dict[str, dict[str, Any]] = {}

        for row in range(start_row, end_row):
            source_major = compact(ws_formula.cell(row, 5).value)
            if not source_major or source_major.lower().startswith("tổng"):
                continue
            clean_name_major = clean_major_name(source_major)
            codes = extract_codes(ws_formula.cell(row, 4).value)
            values = [ws_values.cell(row, column).value for column in range(1, 14)]
            current = values_for_period(values, 5)
            planned = values_for_period(values, 9)
            key = ascii_key(clean_name_major)
            record = {
                "source_rows": [row],
                "source_names": [source_major],
                "name": clean_name_major,
                "source_codes": codes,
                "codes_by_level": {
                    "cao_dang": [item for item in codes if item.startswith("6")],
                    "trung_cap": [item for item in codes if item.startswith("5")],
                    "other": [item for item in codes if not item.startswith(("5", "6"))],
                },
                "current_scale": current,
                "planned_2026": planned,
                "issues": [],
            }
            if source_major != clean_name_major:
                record["issues"].append("Tên ngành/nghề đã được chuẩn hóa chính tả hoặc cách viết.")
            if not codes:
                record["issues"].append("Nguồn không có mã ngành/nghề.")
            if current["issues"] or planned["issues"]:
                record["issues"].append("Số tổng và số theo trình độ chưa khớp hoàn toàn.")

            if key not in merged_programs:
                merged_programs[key] = record
            else:
                target = merged_programs[key]
                target["source_rows"].append(row)
                target["source_names"] = list(dict.fromkeys(target["source_names"] + [source_major]))
                target["source_codes"] = list(dict.fromkeys(target["source_codes"] + codes))
                for level, level_codes in record["codes_by_level"].items():
                    target["codes_by_level"][level] = list(dict.fromkeys(target["codes_by_level"][level] + level_codes))
                merge_period(target["current_scale"], current)
                merge_period(target["planned_2026"], planned)
                target["issues"] = list(dict.fromkeys(target["issues"] + record["issues"] + ["Đã gộp nhiều dòng cùng tên ngành/nghề của một cơ sở."]))

        school["programs"] = list(merged_programs.values())
        total_values = [ws_values.cell(end_row, column).value for column in range(1, 14)]
        source_current = values_for_period(total_values, 5)
        source_planned = values_for_period(total_values, 9)

        def aggregate(period_key: str) -> dict[str, int]:
            keys = ["clean_total", "cao_dang", "trung_cap", "so_cap_va_khac", "unallocated"]
            return {key: sum((program[period_key].get(key) or 0) for program in school["programs"]) for key in keys}

        computed_current = aggregate("current_scale")
        computed_planned = aggregate("planned_2026")
        school["summary"] = {
            "current_scale": {"source_total_row": source_current, "computed_from_clean_programs": computed_current},
            "planned_2026": {"source_total_row": source_planned, "computed_from_clean_programs": computed_planned},
        }
        if source_current["clean_total"] not in {None, computed_current["clean_total"]}:
            school["issues"].append("Tổng quy mô ở dòng tổng không khớp dữ liệu ngành/nghề đã làm sạch.")
        if source_planned["clean_total"] not in {None, computed_planned["clean_total"]}:
            school["issues"].append("Tổng dự kiến tuyển sinh ở dòng tổng không khớp dữ liệu ngành/nghề đã làm sạch.")
        if match["match_status"] != "matched":
            school["issues"].append("Chưa có ánh xạ chắc chắn tới một cơ sở trong dự án.")
        schools.append(school)

    duplicate_codes = {code for code, count in Counter(item["code"] for item in schools if item["code"]).items() if count > 1}
    for school in schools:
        if school["code"] in duplicate_codes:
            school["issues"].append("Mã cơ sở bị trùng với cơ sở khác trong file nguồn.")
        for issue in school["issues"]:
            issues.append({"scope": "school", "source_ref": school["source_ref"], "name": school["name"], "issue": issue})
        for program in school["programs"]:
            for issue in program["issues"]:
                issues.append({
                    "scope": "program",
                    "source_ref": f"Mau 1 - KQTS!{','.join(map(str, program['source_rows']))}",
                    "name": f"{school['name']} — {program['name']}",
                    "issue": issue,
                })
    return schools, issues


CENTER_NAMES = {
    ascii_key("TTGDNN-GDTX Nho Quan"): "Trung tâm GDNN-GDTX Nho Quan",
    ascii_key("TTGDNN-GDTX Gia Viễn"): "Trung tâm GDNN-GDTX Gia Viễn",
    ascii_key("TTGDNN-GDTX Tin học và Ngoại ngữ Ninh Bình"): "Trung tâm GDTX, Tin học và Ngoại ngữ Ninh Bình",
    ascii_key("TTGDNN-GDTX Tam Điệp"): "Trung tâm GDNN-GDTX Tam Điệp",
    ascii_key("TTGDNN-GDTX Yên Mô"): "Trung tâm GDNN-GDTX Yên Mô",
    ascii_key("TTGDNN-GDTX Yên Khánh"): "Trung tâm GDNN-GDTX Yên Khánh",
    ascii_key("TTGDNN-GDTX Kim Sơn"): "Trung tâm GDNN-GDTX Kim Sơn",
}

ADDRESS_OVERRIDES = {
    2: "Ngõ 569, đường Lê Duẩn, phường Phủ Lý, tỉnh Ninh Bình",
    3: "Tổ dân phố số 3, phường Duy Tiên, tỉnh Ninh Bình",
    4: "Thôn Nam Cao, xã Vĩnh Trụ, tỉnh Ninh Bình",
    5: "Tổ dân phố số 2, phường Kim Bảng, tỉnh Ninh Bình",
    6: "Thôn Non, xã Tân Thanh, tỉnh Ninh Bình",
    7: "Thôn Bình Nam, xã Bình Mỹ, tỉnh Ninh Bình",
    8: "Tổ dân phố số 1, khu dân cư Đệ Tứ, phường Thiên Trường, tỉnh Ninh Bình",
    9: "Tổ dân phố Trung Khuyên, phường Mỹ Lộc, tỉnh Ninh Bình",
    10: "Thôn Tân Lâm, xã Ý Yên, tỉnh Ninh Bình",
    11: "Xóm Tiền, xã Liên Minh, tỉnh Ninh Bình",
    12: "Đồng Côi, xã Nam Trực, tỉnh Ninh Bình",
    13: "Thôn Trường Giang, xã Cát Thành, tỉnh Ninh Bình",
    14: "Xóm 9, xã Hải Hưng, tỉnh Ninh Bình",
    15: "Xóm Hồng Hải, xã Giao Hưng, tỉnh Ninh Bình",
    16: "Xóm 3, Thọ Nghiệp, xã Xuân Hưng, tỉnh Ninh Bình",
    17: "Số 30 đường Trần Phú, Nghĩa Hưng, tỉnh Ninh Bình",
    18: "Thôn Tân Nhất, xã Nho Quan, tỉnh Ninh Bình",
    19: "Thôn Me, xã Gia Viễn, tỉnh Ninh Bình",
    20: "Ngõ 2, đường Tràng An, phường Hoa Lư, tỉnh Ninh Bình",
    21: "Tổ 3, phường Yên Sơn, tỉnh Ninh Bình",
    22: "Xóm Nam Thành, xã Yên Từ, tỉnh Ninh Bình",
    23: "Xóm 3, xã Khánh Nhạc, tỉnh Ninh Bình",
    24: "Xóm 4 Thượng Kiệm, xã Phát Diệm, tỉnh Ninh Bình",
}


def normalize_phone(value: Any) -> tuple[str | None, str | None]:
    digits = re.sub(r"\D", "", compact(value))
    if len(digits) == 9:
        digits = "0" + digits
    if len(digits) != 10 or not digits.startswith("0"):
        return (digits or None), "Số điện thoại không đủ 10 chữ số hoặc không bắt đầu bằng 0."
    return digits, None


def parse_docx(path: Path) -> tuple[list[dict[str, Any]], list[dict[str, Any]]]:
    with ZipFile(path) as archive:
        root = ET.fromstring(archive.read("word/document.xml"))
    namespace = {"w": "http://schemas.openxmlformats.org/wordprocessingml/2006/main"}
    table = root.find(".//w:tbl", namespace)
    if table is None:
        raise RuntimeError("Không tìm thấy bảng dữ liệu trong file Word.")
    centers: list[dict[str, Any]] = []
    issues: list[dict[str, Any]] = []
    rows = table.findall("./w:tr", namespace)
    for source_row, row in enumerate(rows[1:], start=2):
        cells = []
        for cell in row.findall("./w:tc", namespace):
            text = "".join(node.text or "" for node in cell.findall(".//w:t", namespace))
            cells.append(compact(text))
        if len(cells) < 3 or not cells[0]:
            continue
        source_name, source_address, source_phone = cells[:3]
        name = CENTER_NAMES.get(ascii_key(source_name), clean_school_name(source_name))
        phone, phone_issue = normalize_phone(source_phone)
        center = {
            "source_ref": f"Word table row {source_row}",
            "source_name": source_name,
            "name": name,
            "source_address": source_address,
            "address": ADDRESS_OVERRIDES.get(source_row, source_address),
            "source_director_phone": source_phone,
            "director_phone": phone,
            "phone_field_note": "Đây là số điện thoại giám đốc theo tiêu đề nguồn; không tự động ghi vào số điện thoại chung của cơ sở.",
            "project_school_id": None,
            "match_status": "needs_database_match",
            "issues": [],
        }
        if phone_issue:
            center["issues"].append(phone_issue)
        if source_address != center["address"]:
            center["issues"].append("Địa chỉ đã bỏ tên cán bộ và chuẩn hóa cách viết hành chính.")
        centers.append(center)
        for issue in center["issues"]:
            issues.append({"scope": "center", "source_ref": center["source_ref"], "name": name, "issue": issue})
    return centers, issues


def write_csv_files(schools: list[dict[str, Any]], centers: list[dict[str, Any]], issues: list[dict[str, Any]]) -> None:
    OUTPUT_DIR.mkdir(parents=True, exist_ok=True)
    with (OUTPUT_DIR / "co_so_gdnn_sach.csv").open("w", encoding="utf-8-sig", newline="") as handle:
        fields = ["source_ref", "source_name", "name", "source_code", "code", "school_type", "match_status", "project_school_id", "candidate_project_school_id", "program_count", "current_total", "planned_2026_total", "issues"]
        writer = csv.DictWriter(handle, fieldnames=fields)
        writer.writeheader()
        for school in schools:
            writer.writerow({
                **{key: school.get(key) for key in fields},
                "program_count": len(school["programs"]),
                "current_total": school["summary"]["current_scale"]["computed_from_clean_programs"]["clean_total"],
                "planned_2026_total": school["summary"]["planned_2026"]["computed_from_clean_programs"]["clean_total"],
                "issues": " | ".join(school["issues"]),
            })

    with (OUTPUT_DIR / "nganh_nghe_sach.csv").open("w", encoding="utf-8-sig", newline="") as handle:
        fields = ["school_name", "project_school_id", "match_status", "source_rows", "source_names", "name", "source_codes", "current_total", "current_cao_dang", "current_trung_cap", "current_so_cap_va_khac", "current_unallocated", "planned_2026_total", "planned_2026_cao_dang", "planned_2026_trung_cap", "planned_2026_so_cap_va_khac", "planned_2026_unallocated", "issues"]
        writer = csv.DictWriter(handle, fieldnames=fields)
        writer.writeheader()
        for school in schools:
            for program in school["programs"]:
                writer.writerow({
                    "school_name": school["name"],
                    "project_school_id": school["project_school_id"],
                    "match_status": school["match_status"],
                    "source_rows": ", ".join(map(str, program["source_rows"])),
                    "source_names": " | ".join(program["source_names"]),
                    "name": program["name"],
                    "source_codes": ", ".join(program["source_codes"]),
                    "current_total": program["current_scale"]["clean_total"],
                    "current_cao_dang": program["current_scale"]["cao_dang"],
                    "current_trung_cap": program["current_scale"]["trung_cap"],
                    "current_so_cap_va_khac": program["current_scale"]["so_cap_va_khac"],
                    "current_unallocated": program["current_scale"]["unallocated"],
                    "planned_2026_total": program["planned_2026"]["clean_total"],
                    "planned_2026_cao_dang": program["planned_2026"]["cao_dang"],
                    "planned_2026_trung_cap": program["planned_2026"]["trung_cap"],
                    "planned_2026_so_cap_va_khac": program["planned_2026"]["so_cap_va_khac"],
                    "planned_2026_unallocated": program["planned_2026"]["unallocated"],
                    "issues": " | ".join(program["issues"]),
                })

    with (OUTPUT_DIR / "dia_chi_trung_tam_sach.csv").open("w", encoding="utf-8-sig", newline="") as handle:
        fields = ["source_ref", "source_name", "name", "source_address", "address", "source_director_phone", "director_phone", "match_status", "project_school_id", "issues"]
        writer = csv.DictWriter(handle, fieldnames=fields)
        writer.writeheader()
        for center in centers:
            writer.writerow({**{key: center.get(key) for key in fields}, "issues": " | ".join(center["issues"])})

    with (OUTPUT_DIR / "du_lieu_can_xac_minh.csv").open("w", encoding="utf-8-sig", newline="") as handle:
        fields = ["scope", "source_ref", "name", "issue"]
        writer = csv.DictWriter(handle, fieldnames=fields)
        writer.writeheader()
        writer.writerows(issues)


def copy_sheet_rows(sheet: Any, rows: list[list[Any]], headers: list[str]) -> None:
    sheet.append(headers)
    for row in rows:
        sheet.append(row)
    header_fill = PatternFill("solid", fgColor="1D4ED8")
    for cell in sheet[1]:
        cell.font = Font(color="FFFFFF", bold=True)
        cell.fill = header_fill
        cell.alignment = Alignment(horizontal="center", vertical="center", wrap_text=True)
    sheet.freeze_panes = "A2"
    sheet.auto_filter.ref = sheet.dimensions
    for row in sheet.iter_rows(min_row=2):
        for cell in row:
            cell.alignment = Alignment(vertical="top", wrap_text=True)
    for column in range(1, sheet.max_column + 1):
        width = max(len(compact(sheet.cell(row, column).value)) for row in range(1, min(sheet.max_row, 300) + 1)) + 2
        sheet.column_dimensions[get_column_letter(column)].width = min(max(width, 12), 42)


def write_review_workbook(schools: list[dict[str, Any]], centers: list[dict[str, Any]], issues: list[dict[str, Any]]) -> Path:
    workbook = Workbook()
    instruction = workbook.active
    instruction.title = "Huong_dan"
    instruction_rows = [
        ["Bộ dữ liệu sạch GDNN/GDTX", f"Ngày tạo: {GENERATED_DATE}"],
        ["Nguyên tắc", "Không sửa file nguồn; giữ source_ref/source_rows; không tự ghép các trường có trạng thái needs_review hoặc unmatched."],
        ["Số điện thoại", "Cột trong Word là số điện thoại giám đốc, chỉ lưu ở director_phone; không tự ghi vào số điện thoại chung của cơ sở."],
        ["Số liệu", "clean_total được tính từ các cột trình độ; phần chênh dương so với tổng được giữ tại unallocated."],
        ["Nhập dự án", "Chỉ các cơ sở match_status=matched có thể dùng project_school_id. Các dòng còn lại phải xác nhận mã cơ sở."],
    ]
    for row in instruction_rows:
        instruction.append(row)
    instruction.column_dimensions["A"].width = 24
    instruction.column_dimensions["B"].width = 110
    for row in instruction.iter_rows():
        for cell in row:
            cell.alignment = Alignment(vertical="top", wrap_text=True)
    instruction["A1"].font = Font(bold=True, size=14)

    school_rows = []
    for item in schools:
        school_rows.append([
            item["source_ref"], item["source_name"], item["name"], item["source_code"], item["code"], item["school_type"],
            item["match_status"], item["project_school_id"], item["candidate_project_school_id"], len(item["programs"]),
            item["summary"]["current_scale"]["computed_from_clean_programs"]["clean_total"],
            item["summary"]["planned_2026"]["computed_from_clean_programs"]["clean_total"], " | ".join(item["issues"]),
        ])
    copy_sheet_rows(workbook.create_sheet("Co_so"), school_rows, ["Nguồn", "Tên nguồn", "Tên chuẩn hóa", "Mã nguồn", "Mã chuẩn hóa", "Loại cơ sở", "Trạng thái ghép", "ID dự án", "ID dự kiến cần duyệt", "Số ngành/nghề", "Quy mô sạch", "Dự kiến 2026 sạch", "Vấn đề"])

    program_rows = []
    for school in schools:
        for item in school["programs"]:
            program_rows.append([
                school["name"], school["project_school_id"], school["match_status"], ", ".join(map(str, item["source_rows"])),
                " | ".join(item["source_names"]), item["name"], ", ".join(item["source_codes"]),
                item["current_scale"]["clean_total"], item["current_scale"]["cao_dang"], item["current_scale"]["trung_cap"], item["current_scale"]["so_cap_va_khac"], item["current_scale"]["unallocated"],
                item["planned_2026"]["clean_total"], item["planned_2026"]["cao_dang"], item["planned_2026"]["trung_cap"], item["planned_2026"]["so_cap_va_khac"], item["planned_2026"]["unallocated"],
                " | ".join(item["issues"]),
            ])
    copy_sheet_rows(workbook.create_sheet("Nganh_nghe"), program_rows, ["Cơ sở", "ID dự án", "Trạng thái ghép", "Dòng nguồn", "Tên nguồn", "Tên chuẩn hóa", "Mã ngành/nghề", "Quy mô tổng", "Quy mô CĐ", "Quy mô TC", "Quy mô SC/khác", "Quy mô chưa phân bổ", "Dự kiến tổng", "Dự kiến CĐ", "Dự kiến TC", "Dự kiến SC/khác", "Dự kiến chưa phân bổ", "Vấn đề"])

    center_rows = [[item["source_ref"], item["source_name"], item["name"], item["source_address"], item["address"], item["source_director_phone"], item["director_phone"], item["match_status"], item["project_school_id"], " | ".join(item["issues"])] for item in centers]
    copy_sheet_rows(workbook.create_sheet("Trung_tam_dia_chi"), center_rows, ["Nguồn", "Tên nguồn", "Tên chuẩn hóa", "Địa chỉ nguồn", "Địa chỉ sạch", "SĐT nguồn", "SĐT giám đốc chuẩn hóa", "Trạng thái ghép", "ID dự án", "Vấn đề"])

    issue_rows = [[item["scope"], item["source_ref"], item["name"], item["issue"]] for item in issues]
    copy_sheet_rows(workbook.create_sheet("Can_xac_minh"), issue_rows, ["Phạm vi", "Nguồn", "Đối tượng", "Nội dung cần xác minh"])

    output = OUTPUT_DIR / "du_lieu_gdnn_sach_2026_11_10.xlsx"
    workbook.save(output)
    return output


def write_report(payload: dict[str, Any], issues: list[dict[str, Any]]) -> None:
    schools = payload["institutions"]
    centers = payload["centers"]
    programs = sum(len(item["programs"]) for item in schools)
    source_program_rows = sum(len(program["source_rows"]) for item in schools for program in item["programs"])
    statuses = Counter(item["match_status"] for item in schools)
    issue_counts = Counter(item["issue"] for item in issues)
    duplicate_codes = sorted(code for code, count in Counter(item["code"] for item in schools if item["code"]).items() if count > 1)
    report = f"""# Báo cáo làm sạch dữ liệu GDNN/GDTX ({GENERATED_DATE})

## Kết quả

- Excel nguồn: **{len(schools)} cơ sở**, **{source_program_rows} dòng ngành/nghề**, còn **{programs} bản ghi trường–ngành/nghề** sau khi gộp các dòng cùng tên trong từng cơ sở.
- Ghép với dữ liệu dự án hiện có: **{statuses.get('matched', 0)} khớp chắc chắn**, **{statuses.get('needs_review', 0)} cần duyệt**, **{statuses.get('unmatched', 0)} chưa ghép**.
- Word nguồn: **{len(centers)} trung tâm** có địa chỉ và số điện thoại giám đốc đã chuẩn hóa.
- Tổng số cảnh báo/truy vết: **{len(issues)}**. Cảnh báo không đồng nghĩa bản ghi sai; chúng chỉ ngăn việc nhập tự động thiếu kiểm soát.

Phân loại cảnh báo chính:

- **{issue_counts.get('Nguồn không có mã ngành/nghề.', 0)}** bản ghi ngành/nghề không có mã trong nguồn.
- **{issue_counts.get('Đã gộp nhiều dòng cùng tên ngành/nghề của một cơ sở.', 0)}** bản ghi sạch được gộp từ nhiều dòng cùng tên của một cơ sở.
- **{issue_counts.get('Số tổng và số theo trình độ chưa khớp hoàn toàn.', 0)}** bản ghi có số tổng và số theo trình độ chưa khớp.
- **{issue_counts.get('Tên ngành/nghề đã được chuẩn hóa chính tả hoặc cách viết.', 0)}** tên ngành/nghề được sửa lỗi chính tả/cách viết rõ ràng.
- **{issue_counts.get('Địa chỉ đã bỏ tên cán bộ và chuẩn hóa cách viết hành chính.', 0)}** địa chỉ được tách tên cán bộ và chuẩn hóa cách viết.

## Quy tắc làm sạch

1. Không sửa hai file nguồn và không xóa giá trị gốc; mọi bản ghi đều có `source_ref` hoặc `source_rows`.
2. Loại dòng tiêu đề/dòng tổng; gộp ngành trùng tên trong cùng cơ sở nhưng giữ toàn bộ mã và dòng nguồn.
3. Chuẩn hóa khoảng trắng, Unicode, cách viết “ô tô”, “điều hòa không khí”, ký hiệu `&`, cùng một số lỗi chính tả rõ ràng.
4. Tổng sạch ưu tiên tổng theo các trình độ. Chênh lệch còn lại được giữ ở `unallocated`, không tự phân bổ vào cao đẳng/trung cấp/sơ cấp.
5. Địa chỉ Word đã bỏ tên cán bộ khỏi cột địa chỉ và chuẩn hóa viết hoa/đơn vị hành chính.
6. Số điện thoại trong Word là **số của giám đốc**, không phải số tổng đài cơ sở; vì vậy không tự ghi vào `schools.phone`.

## Điểm chưa được nhập tự động

- Mã cơ sở trùng trong nguồn: **{', '.join(duplicate_codes) if duplicate_codes else 'Không có'}**.
- Hai tên có khả năng là cơ sở đổi tên/sáp nhập chỉ được đưa vào `candidate_project_school_id`, chưa coi là khớp.
- Cơ sở `unmatched` chưa có `project_school_id`; cần xác nhận bản ghi trường trong CSDL trước khi nhập quan hệ ngành/nghề.
- Các tổng không khớp vẫn được bảo toàn trong JSON và sheet `Can_xac_minh`; không làm tròn hoặc tự chia số.

## Tệp đầu ra

- `tai_lieu/du_lieu_sach/du_lieu_gdnn_sach_2026_11_10.xlsx`: bản Excel sạch để duyệt.
- `tai_lieu/du_lieu_sach/co_so_gdnn_sach.csv`: danh sách cơ sở.
- `tai_lieu/du_lieu_sach/nganh_nghe_sach.csv`: dữ liệu ngành/nghề dạng phẳng.
- `tai_lieu/du_lieu_sach/dia_chi_trung_tam_sach.csv`: địa chỉ và số giám đốc.
- `tai_lieu/du_lieu_sach/du_lieu_can_xac_minh.csv`: toàn bộ cảnh báo.
- `backend/database/data/gdnn_map_clean_2026_11_10.json`: dữ liệu có cấu trúc để viết lệnh import an toàn.

## Khuyến nghị nhập dự án

Chỉ cho phép lệnh import xử lý các cơ sở có `match_status=matched`, chạy **dry-run** trước và sao lưu các bảng `schools`, `training_majors`, `school_training_major`. Địa chỉ trung tâm phải ghép duy nhất theo tên/mã cơ sở; số điện thoại giám đốc nên lưu ở trường dữ liệu liên hệ lãnh đạo, không ghi đè số điện thoại chung.
"""
    REPORT_OUTPUT.write_text(report, encoding="utf-8")


def main() -> None:
    excel = next(SOURCE_DIR.glob("Copy of *.xlsx"))
    word = next(path for path in SOURCE_DIR.glob("*.docx") if not path.name.startswith("DANH"))
    schools, excel_issues = parse_excel(excel)
    centers, center_issues = parse_docx(word)
    issues = excel_issues + center_issues
    payload = {
        "schema_version": 1,
        "generated_date": GENERATED_DATE,
        "sources": [
            {"file": str(excel.relative_to(ROOT)), "sha256": source_hash(excel)},
            {"file": str(word.relative_to(ROOT)), "sha256": source_hash(word)},
        ],
        "import_policy": {
            "auto_import_school_statuses": ["matched"],
            "preserve_source_fields": True,
            "director_phone_is_school_phone": False,
            "requires_backup": True,
            "requires_dry_run": True,
        },
        "institutions": schools,
        "centers": centers,
        "issues": issues,
    }
    DATA_OUTPUT.parent.mkdir(parents=True, exist_ok=True)
    DATA_OUTPUT.write_text(json.dumps(payload, ensure_ascii=False, indent=2) + "\n", encoding="utf-8")
    write_csv_files(schools, centers, issues)
    workbook_output = write_review_workbook(schools, centers, issues)
    write_report(payload, issues)
    print(json.dumps({
        "institutions": len(schools),
        "source_program_rows": sum(len(program["source_rows"]) for item in schools for program in item["programs"]),
        "clean_programs": sum(len(item["programs"]) for item in schools),
        "centers": len(centers),
        "issues": len(issues),
        "json": str(DATA_OUTPUT.relative_to(ROOT)),
        "workbook": str(workbook_output.relative_to(ROOT)),
        "report": str(REPORT_OUTPUT.relative_to(ROOT)),
    }, ensure_ascii=False, indent=2))


if __name__ == "__main__":
    main()
