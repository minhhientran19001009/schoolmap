const fs = require('node:fs')
const path = require('node:path')
const XLSX = require('xlsx')

const root = path.resolve(__dirname, '..')
const sourceAdmissions = path.join(root, '2026 Bang tong hop quy mô tuyen sinh.xlsx')
const sourceJobs = path.join(root, 'TinTuyenDung_tu_10_nguoi_tang_dan thoi diem 9-2026.xlsx')
const outputDirectory = path.join(root, 'src', 'data')

function readRows(file, sheetName) {
  const workbook = XLSX.readFile(file)
  const sheet = workbook.Sheets[sheetName || workbook.SheetNames[0]]
  return XLSX.utils.sheet_to_json(sheet, { header: 1, defval: null, raw: false })
}

function clean(value) {
  return String(value ?? '').replace(/\s+/g, ' ').trim()
}

function numberOrNull(value) {
  const text = clean(value)
  if (!text || text === '-' || text === '—') return null
  const parsed = Number(text.replace(/[,.\s]/g, ''))
  return Number.isFinite(parsed) ? parsed : null
}

const schoolMappings = [
  [/nghề số 20\/bqp/i, 'truong-cao-dang-nghe-so-20-bqp-truong-th-148', 'NB-77525'],
  [/cao đẳng xây dựng nđ/i, 'truong-cao-dang-xay-dung-nam-dinh-174', 'NB-71714'],
  [/cđ công nghiệp nam định/i, 'truong-cao-dang-cong-nghiep-nam-dinh-351', 'NB-88976'],
  [/kinh tế và công nghệ nđ/i, 'truong-cao-dang-kinh-te-va-cong-nghe-nam-928', 'NB-80777'],
  [/vhnt.*du lịch nđ/i, 'truong-cao-dang-van-hoa-nghe-thuat-va-du-519', 'NB-76252'],
  [/cao đẳng cơ giới ninh bình/i, 'truong-cao-dang-co-gioi-ninh-binh', 'NB-CD-003'],
  [/cao đẳng nghề lilama 1/i, 'truong-cao-dang-nghe-lilama-1-382', 'NB-13143'],
  [/phát thanh truyền hình i/i, 'HN-CD-004', '37108'],
  [/cao đẳng y tế ninh bình/i, 'NB-CD-002', '37022'],
  [/công nghệ, kinh tế và chế biến lâm sản/i, 'truong-cao-dang-cong-nghe-kinh-te-va-che-885', 'NB-15187'],
  [/trung cấp công nghệ số 8/i, 'truong-trung-cap-cong-nghe-so-8-nam-dinh-462', 'NB-88920'],
  [/trung cấp gtvt nđ/i, 'truong-trung-cap-giao-thong-van-tai-tinh-836', 'NB-39032'],
  [/trung cấp đại lâm/i, 'truong-trung-cap-dai-lam-111', 'NB-59920'],
  [/trung cấp kỹ thuật.*du lịch công đoàn/i, 'truong-trung-cap-ky-thuat-du-lich-cong-d-174', 'NB-14262'],
]

function mapSchool(name) {
  return schoolMappings.find(([pattern]) => pattern.test(name)) || []
}

function importAdmissions() {
  const rows = readRows(sourceAdmissions, 'Mau 1 - KQTS')
  const institutions = []
  let current = null

  rows.forEach((row, sheetIndex) => {
    const first = clean(row[0])
    const institutionName = clean(row[1]) || clean(row[2])
    const isInstitutionHeader = /^[IVX]+$/i.test(first) && institutionName

    if (isInstitutionHeader) {
      const [, schoolId, schoolCode] = mapSchool(institutionName)
      current = {
        school_id: schoolId || null,
        school_code: schoolCode || null,
        source_name: institutionName,
        source_code: clean(row[3]) || null,
        summary: {
          total: numberOrNull(row[4]),
          cao_dang: numberOrNull(row[5]),
          trung_cap: numberOrNull(row[6]),
          so_cap: numberOrNull(row[7]),
        },
        programs: [],
      }
      institutions.push(current)
      return
    }

    if (!current || (first && !/^\d+$/.test(first))) return
    const name = clean(row[2])
    if (!name || /^(?:hệ\s+(?:cao đẳng|trung cấp|sơ cấp)|sơ cấp)$/i.test(name)) return

    current.programs.push({
      source_row: sheetIndex + 1,
      code: clean(row[1]) || null,
      name,
      total: numberOrNull(row[4]),
      cao_dang: numberOrNull(row[5]),
      trung_cap: numberOrNull(row[6]),
      so_cap: numberOrNull(row[7]),
    })
  })

  const unmatched = institutions.filter((school) => !school.school_id)
  if (unmatched.length) {
    throw new Error(`Unmatched source schools: ${unmatched.map((s) => s.source_name).join('; ')}`)
  }

  return {
    source_file: path.basename(sourceAdmissions),
    display_period: 'Kỳ báo cáo',
    note: 'Số liệu được giữ nguyên theo bảng nguồn; dấu gạch ngang hoặc ô trống là chưa có số liệu.',
    institutions,
  }
}

const logoColors = ['bg-blue-700', 'bg-emerald-700', 'bg-violet-700', 'bg-amber-600', 'bg-indigo-700', 'bg-teal-700']

function logoFor(company) {
  const words = company.replace(/^(công ty|cty|chi nhánh)\s*/i, '').split(/\s+/).filter(Boolean)
  return words.slice(0, 2).map((word) => word[0]).join('').toUpperCase().slice(0, 5) || 'DN'
}

function fakeDeadline(index) {
  const date = new Date(Date.UTC(2026, 10, 3 + 15 + ((index * 17) % 75)))
  return `${String(date.getUTCDate()).padStart(2, '0')}/${String(date.getUTCMonth() + 1).padStart(2, '0')}/${date.getUTCFullYear()}`
}

function fakePhone(index) {
  const prefixes = ['032', '052', '072', '082', '092']
  const suffix = (index * 32452843 + 49979687) % 10000000
  return `${prefixes[index % prefixes.length]}${String(suffix).padStart(7, '0')}`
}

function scrubDates(text) {
  return clean(text)
    .replace(/(?:từ\s*)?\d{1,2}[/-]\d{1,2}[/-]\d{2,4}\s*(?:đến|[-–])\s*\d{1,2}[/-]\d{1,2}[/-]\d{2,4}/gi, 'theo thời hạn hợp đồng')
    .replace(/\b\d{1,2}[/-]\d{1,2}[/-]\d{2,4}\b/g, 'theo kế hoạch tuyển dụng')
}

function safeAddress(value) {
  const address = clean(value)
  if (/\b\d{8,}\b/.test(address)) return 'Ninh Bình'
  return address || 'Ninh Bình'
}

function salaryLevel(text) {
  const numbers = [...String(text).matchAll(/\d+(?:[.,]\d+)?/g)].map((m) => Number(m[0].replace(',', '.')))
  const average = numbers.length ? numbers.reduce((sum, n) => sum + n, 0) / numbers.length : 0
  if (!average || /thỏa thuận|không yêu cầu/i.test(text)) return 'negotiable'
  if (average < 7) return 'under_7'
  if (average < 10) return '7_10'
  if (average < 15) return '10_15'
  return 'above_15'
}

function importJobs() {
  const rows = readRows(sourceJobs, 'Tuyen_tu_10')
  const postings = rows.slice(1).filter((row) => clean(row[1]) && clean(row[2])).map((row, index) => {
    const title = clean(row[1])
    const company = clean(row[2])
    const sourceDescription = scrubDates(row[12])
    const description = sourceDescription
      .split(/\s+-\s+/)
      .map(clean)
      .filter(Boolean)
    const requirements = [
      clean(row[8]) && `Trình độ chuyên môn: ${clean(row[8])}`,
      clean(row[5]) && `Vị trí: ${clean(row[5])}`,
      clean(row[14]) && `Độ tuổi theo tin nguồn: ${clean(row[14])}`,
      clean(row[13]) && `Giới tính theo tin nguồn: ${clean(row[13])}`,
    ].filter(Boolean)
    const count = numberOrNull(row[4]) || 0
    const sourceOccupation = clean(row[7])
    const rawIndustry = clean(row[3]).replace(/[,;]+/g, '').trim()
    const colorIndex = [...company].reduce((sum, char) => sum + char.charCodeAt(0), 0) % logoColors.length

    return {
      id: `source-job-${String(index + 1).padStart(3, '0')}`,
      source_row: Number(clean(row[0])) || index + 1,
      title,
      company,
      company_logo: logoFor(company),
      company_color: logoColors[colorIndex],
      ward_code: null,
      ward_name: 'Ninh Bình',
      address: safeAddress(row[11]),
      salary: clean(row[6]) || 'Thỏa thuận',
      salary_level: salaryLevel(row[6]),
      industry: null,
      industry_name: sourceOccupation || rawIndustry || title,
      source_business_sector: rawIndustry || null,
      source_occupation: sourceOccupation || null,
      experience: 'Theo yêu cầu trong tin tuyển dụng',
      education: clean(row[8]) || 'Theo yêu cầu trong tin tuyển dụng',
      deadline: fakeDeadline(index),
      is_hot: false,
      benefits: [],
      description: description.length ? description : [title],
      requirements,
      contact: {
        contact_person: 'Phòng Nhân sự',
        phone: fakePhone(index),
        zalo: null,
        email: null,
        can_contact: false,
        address: safeAddress(row[11]),
      },
      profession: `${title} ${sourceOccupation} ${rawIndustry} ${sourceDescription}`.trim(),
      recruitment_count: count,
      skills: [],
      training_schools: [],
      demands: [{ company, quantity: count }],
      source_work_schedule: clean(row[15]) || null,
      source_file: path.basename(sourceJobs),
    }
  })

  return postings
}

fs.mkdirSync(outputDirectory, { recursive: true })
const admissions = importAdmissions()
fs.writeFileSync(path.join(outputDirectory, 'admissionResults.json'), `${JSON.stringify(admissions, null, 2)}\n`, 'utf8')
const result = {
  admissionInstitutions: admissions.institutions.length,
  admissionPrograms: admissions.institutions.reduce((sum, school) => sum + school.programs.length, 0),
  unmatchedSchools: admissions.institutions.filter((school) => !school.school_id).map((school) => school.source_name),
}
if (!process.argv.includes('--admissions-only')) {
  const jobs = importJobs()
  fs.writeFileSync(path.join(outputDirectory, 'jobPostings.json'), `${JSON.stringify(jobs, null, 2)}\n`, 'utf8')
  result.jobPostings = jobs.length
  result.recruitmentTotal = jobs.reduce((sum, job) => sum + job.recruitment_count, 0)
  result.fakePhoneCount = jobs.filter((job) => Boolean(job.contact.phone) && !job.contact.can_contact).length
}
console.log(JSON.stringify(result, null, 2))
