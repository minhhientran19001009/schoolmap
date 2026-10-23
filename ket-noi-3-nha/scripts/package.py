"""Create the readable guide and an installation archive; no network operations."""
import html
import json
import re
import shutil
import zipfile
from pathlib import Path

root = Path(__file__).resolve().parent.parent
output = root.parent / "outputs"
output.mkdir(exist_ok=True)

def inline(text):
    value = html.escape(text)
    value = re.sub(r"`([^`]+)`", r"<code>\1</code>", value)
    value = re.sub(r"\*\*([^*]+)\*\*", r"<strong>\1</strong>", value)
    value = re.sub(r"\[([^]]+)\]\((https://[^ )]+)\)", r'<a href="\2" target="_blank" rel="noopener noreferrer">\1</a>', value)
    return value

lines = (root / "README.md").read_text().splitlines()
parts = []
index = 0
while index < len(lines):
    line = lines[index]
    if not line.strip():
        index += 1
        continue
    if line.startswith("#"):
        level = len(line) - len(line.lstrip("#"))
        parts.append(f"<h{level}>{inline(line[level:].strip())}</h{level}>")
        index += 1
    elif line.startswith("|"):
        rows = []
        while index < len(lines) and lines[index].startswith("|"):
            cells = [cell.strip() for cell in lines[index].strip("|").split("|")]
            if not all(re.fullmatch(r":?-+:?", cell) for cell in cells):
                rows.append(cells)
            index += 1
        parts.append('<div class="table-wrap"><table><thead><tr>' + "".join("<th>" + inline(c) + "</th>" for c in rows[0]) + "</tr></thead><tbody>" + "".join("<tr>" + "".join("<td>" + inline(c) + "</td>" for c in row) + "</tr>" for row in rows[1:]) + "</tbody></table></div>")
    elif line.startswith("- ") or re.match(r"\d+\. ", line):
        ordered = bool(re.match(r"\d+\. ", line))
        tag = "ol" if ordered else "ul"
        items = []
        pattern = r"\d+\. " if ordered else r"- "
        while index < len(lines) and re.match(pattern, lines[index]):
            items.append("<li>" + inline(re.sub("^" + pattern, "", lines[index])) + "</li>")
            index += 1
        parts.append("<" + tag + ">" + "".join(items) + "</" + tag + ">")
    else:
        paragraph = [line]
        index += 1
        while index < len(lines) and lines[index].strip() and not re.match(r"^(#|\||- |\d+\. )", lines[index]):
            paragraph.append(lines[index])
            index += 1
        parts.append("<p>" + inline(" ".join(paragraph)) + "</p>")

style = """
:root{color-scheme:light}*{box-sizing:border-box}body{margin:0;background:#edf2f8;color:#20354d;font:16px/1.7 Arial,Helvetica,sans-serif}.bar{background:#103e7b;color:white;padding:18px 24px;font-size:14px;letter-spacing:.03em}.bar strong{display:block;font-size:20px;letter-spacing:.05em}main{max-width:940px;margin:30px auto;padding:38px 46px;background:white;border:1px solid #dce4ef;border-radius:12px}h1{font-size:29px;line-height:1.3;color:#0b3e82;margin:0 0 18px}h2{font-size:22px;line-height:1.4;margin:35px 0 12px;border-top:1px solid #e0e7f1;padding-top:25px;color:#0b3e82}p{margin:12px 0}li{margin:9px 0}ul,ol{padding-left:25px}strong{font-weight:700}code{font-size:.93em;background:#eef3fa;color:#123f78;padding:2px 5px;border-radius:4px;overflow-wrap:anywhere}a{color:#0b579e;text-underline-offset:3px;overflow-wrap:anywhere}.table-wrap{overflow:auto;margin:18px 0}table{border-collapse:collapse;width:100%;font-size:14px}th,td{padding:12px 14px;text-align:left;vertical-align:top;border:1px solid #dce4ef}th{background:#edf3fa;color:#103e7b}footer{max-width:940px;margin:20px auto 40px;padding:0 24px;color:#586c84;font-size:13px}@media(max-width:680px){main{padding:24px 20px;margin:15px 10px}h1{font-size:25px}h2{font-size:20px}td,th{padding:9px}}@media print{body{background:white;font-size:11pt}.bar{background:white;color:#103e7b;border-bottom:2px solid #103e7b;padding:0 0 12px}main{border:0;margin:0;padding:16px 0}h2{break-after:avoid}tr{break-inside:avoid}a{color:#20354d}footer{margin:18px 0;padding:0}}
"""
guide = '<!doctype html><html lang="vi"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Hướng dẫn cài Cổng kết nối 3 nhà trên Google Sites</title><style>' + style + '</style></head><body><div class="bar"><strong>KẾT NỐI CUNG CẦU</strong>Hướng dẫn cài đặt · Google Sites / Apps Script / Sheets</div><main>' + "\n".join(parts) + '</main><footer>Bộ mã riêng tư · Không yêu cầu máy chủ riêng · 01/10/2026</footer></body></html>\n'
(root / "HUONG_DAN.html").write_text(guide)
shutil.copy2(root / "HUONG_DAN.html", output / "huong-dan-google-sites.html")
shutil.copy2(root / "XEM_THU.html", output / "xem-thu-ket-noi-3-nha.html")
archive = output / "ket-noi-3-nha-google-sites.zip"
with zipfile.ZipFile(archive, "w", zipfile.ZIP_DEFLATED, compresslevel=9) as z:
    for file in sorted(root.rglob("*")):
        if file.is_file() and "node_modules" not in file.parts and file.name != "metafile.json":
            z.write(file, Path("ket-noi-3-nha-google-sites") / file.relative_to(root))
with zipfile.ZipFile(archive) as z:
    assert z.testzip() is None
    assert all("install/" + file in "\n".join(z.namelist()) for file in ["Code.gs", "Rules.gs", "Data.gs", "Index.html", "appsscript.json"])
print(json.dumps({"archive": str(archive), "bytes": archive.stat().st_size, "guide": str(output / "huong-dan-google-sites.html"), "preview": str(output / "xem-thu-ket-noi-3-nha.html")}, ensure_ascii=False))
