import sourceJobs from '../data/jobPostings.json'

export const CAREER_FIELDS = [
  { id: 'co_khi', name: 'Cơ khí - Chế tạo - Ô tô', icon: 'fa-solid fa-gears', description: 'Gia công, lắp ráp, bảo trì máy móc và công nghệ ô tô.' },
  { id: 'dien_dien_tu', name: 'Điện - Điện tử - Tự động hóa', icon: 'fa-solid fa-bolt', description: 'Điện công nghiệp, điện tử, điều khiển và dây chuyền tự động.' },
  { id: 'cntt', name: 'Công nghệ thông tin - Truyền thông', icon: 'fa-solid fa-laptop-code', description: 'Phần mềm, mạng máy tính, dữ liệu và nội dung số.' },
  { id: 'xay_dung', name: 'Xây dựng - Kiến trúc - Vật liệu', icon: 'fa-solid fa-trowel-bricks', description: 'Thi công, vận hành thiết bị và sản xuất vật liệu xây dựng.' },
  { id: 'giao_thong_logistics', name: 'Giao thông - Vận tải - Logistics', icon: 'fa-solid fa-truck-fast', description: 'Vận tải, kho vận, chuỗi cung ứng và dịch vụ logistics.' },
  { id: 'det_may', name: 'Dệt may - Da giày - Thời trang', icon: 'fa-solid fa-shirt', description: 'May công nghiệp, thiết kế, kiểm soát chất lượng và da giày.' },
  { id: 'du_lich', name: 'Du lịch - Nhà hàng - Khách sạn', icon: 'fa-solid fa-hotel', description: 'Lưu trú, ẩm thực, lữ hành và dịch vụ khách hàng.' },
  { id: 'nong_nghiep', name: 'Nông nghiệp - Thủy sản - Thực phẩm', icon: 'fa-solid fa-seedling', description: 'Nông nghiệp công nghệ cao, thủy sản và chế biến thực phẩm.' },
  { id: 'kinh_doanh', name: 'Kinh doanh - Tài chính - Hành chính', icon: 'fa-solid fa-chart-line', description: 'Kinh doanh, bán hàng, kế toán, quản trị và hành chính.' },
  { id: 'y_te', name: 'Y tế - Dược - Chăm sóc sức khỏe', icon: 'fa-solid fa-heart-pulse', description: 'Điều dưỡng, dược, chăm sóc và hỗ trợ sức khỏe.' },
  { id: 'hoa_chat_nang_luong', name: 'Hóa chất - Năng lượng - Môi trường', icon: 'fa-solid fa-flask', description: 'Hóa chất, năng lượng, xử lý môi trường và công nghệ xanh.' },
  { id: 'van_hoa_nghe_thuat', name: 'Văn hóa - Nghệ thuật - Thiết kế', icon: 'fa-solid fa-palette', description: 'Thiết kế, mỹ thuật, biểu diễn, báo chí và nghề thủ công.' },
  { id: 'giao_duc_dich_vu', name: 'Giáo dục - Xã hội - Dịch vụ cộng đồng', icon: 'fa-solid fa-people-group', description: 'Giáo dục, công tác xã hội và các dịch vụ phục vụ cộng đồng.' },
]

export const INDUSTRIES = [
  { id: 'all', name: 'Tất cả lĩnh vực nghề nghiệp', icon: 'fa-solid fa-layer-group' },
  ...CAREER_FIELDS,
]

function normalizeText(value) {
  return String(value || '')
    .toLocaleLowerCase('vi')
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .replace(/đ/g, 'd')
    .replace(/\s+/g, ' ')
    .trim()
}

const CAREER_FIELD_RULES = [
  ['y_te', ['y te', 'duoc', 'dieu duong', 'cham soc suc khoe', 'ky thuat xet nghiem', 'ho sinh']],
  ['hoa_chat_nang_luong', ['hoa chat', 'nang luong', 'moi truong', 'xu ly nuoc', 'cong nghe xanh']],
  ['van_hoa_nghe_thuat', ['van hoa', 'nghe thuat', 'my thuat', 'thiet ke do hoa', 'thu cong', 'bieu dien', 'bao chi', 'nhac cu']],
  ['giao_thong_logistics', ['logistics', 'kho van', 'van tai', 'lai xe', 'giao thong', 'chuoi cung ung']],
  ['du_lich', ['du lich', 'khach san', 'nha hang', 'le tan', 'lu hanh', 'che bien mon an', 'am thuc']],
  ['det_may', ['det', 'det may', 'may cong nghiep', 'thoi trang', 'da giay', 'giay da']],
  ['cntt', ['cong nghe thong tin', 'tin hoc', 'lap trinh', 'phan mem', 'mang may tinh', 'truyen thong so']],
  ['xay_dung', ['xay dung', 'kien truc', 'cong trinh', 'silicat', 'vat lieu', 'cap thoat nuoc', 'may thi cong']],
  ['nong_nghiep', ['nong nghiep', 'thuy san', 'chan nuoi', 'trong trot', 'lam nghiep', 'bao ve thuc vat', 'cong nghe thuc pham', 'thu y']],
  ['dien_dien_tu', ['co dien tu', 'dien tu', 'dien cong nghiep', 'dien dan dung', 'tu dong hoa', 'dien lanh', 'plc']],
  ['co_khi', ['co khi', 'o to', 'han', 'cat got', 'cnc', 'che tao', 'sua chua xe']],
  ['kinh_doanh', ['kinh doanh', 'ke toan', 'tai chinh', 'ban hang', 'marketing', 'quan tri', 'hanh chinh']],
  ['giao_duc_dich_vu', ['giao duc', 'su pham', 'cong tac xa hoi', 'bao chi', 'phap luat', 'gdtx', 'huong nghiep']],
]

function inferCareerFieldId(value) {
  const normalized = normalizeText(value)
  const matchesKeyword = (keyword) => keyword.includes(' ')
    ? normalized.includes(keyword)
    : (` ${normalized} `).includes(` ${keyword} `)

  return CAREER_FIELD_RULES.find(([, keywords]) => keywords.some(matchesKeyword))?.[0]
    || 'giao_duc_dich_vu'
}

const classifiedJobs = sourceJobs.map((job) => {
  const inferredFieldId = inferCareerFieldId(`${job.profession} ${job.title} ${job.industry_name}`)
  const existingFieldId = CAREER_FIELDS.some((item) => item.id === job.industry) ? job.industry : null
  // Logistics was previously grouped under general business. Other existing
  // classifications remain authoritative to avoid ambiguous keywords such as
  // "ô tô" in a sales position or "lái xe" in construction machinery.
  const fieldId = inferredFieldId === 'giao_thong_logistics' && job.industry === 'kinh_doanh'
    ? inferredFieldId
    : (existingFieldId || inferredFieldId)
  const field = CAREER_FIELDS.find((item) => item.id === fieldId)

  return {
    ...job,
    industry: fieldId,
    industry_name: field?.name || job.industry_name,
  }
})

export const SALARY_RANGES = [
  { id: 'all', label: 'Tất cả mức lương' },
  { id: 'under_7', label: 'Dưới 7 triệu' },
  { id: '7_10', label: '7 - 10 triệu' },
  { id: '10_15', label: '10 - 15 triệu' },
  { id: 'above_15', label: 'Trên 15 triệu' }
]

export const jobService = {
  getCareerFieldId(value) {
    return inferCareerFieldId(value)
  },

  getAll() {
    return classifiedJobs
  },

  getById(id) {
    return classifiedJobs.find(j => j.id === id) || null
  },

  getIndustries() {
    return INDUSTRIES
  },

  getCareerFields(professions = [], schools = [], trainingMajors = []) {
    const fields = new Map(CAREER_FIELDS.map((field) => [field.id, {
      ...field,
      job_count: 0,
      job_openings: 0,
      demand: 0,
      capacity: 0,
      proposed: 0,
      gap: 0,
      companies: 0,
      school_count: 0,
      majors: [],
      levels: [],
      skills: [],
      capacity_skills: [],
      schools: [],
      demands: [],
      recruiters: [],
      _company_names: new Set(),
      _recruiters: new Map(),
    }]))

    const addUnique = (items, value) => {
      if (value && !items.some((item) => normalizeText(item) === normalizeText(value))) items.push(value)
    }

    const mergeLabels = (...values) => {
      const labels = []
      values
        .flatMap((value) => String(value || '').split(' · '))
        .map((value) => value.trim())
        .filter(Boolean)
        .forEach((value) => addUnique(labels, value))
      return labels.join(' · ')
    }

    const addSchool = (field, school) => {
      if (!field || !school?.name) return

      const schoolId = school.id || school.school_id || null
      const existing = field.schools.find((item) => (
        (schoolId && String(item.id || '') === String(schoolId))
        || normalizeText(item.name) === normalizeText(school.name)
      ))

      if (existing) {
        existing.level = mergeLabels(existing.level, school.level)
        existing.code ||= school.code || null
        existing.location ||= school.location || null
        existing.address ||= school.address || null
        existing.ward ||= school.ward || null
        existing.district_name ||= school.district_name || null
        existing.province_name ||= school.province_name || null
        existing.capacity = Number(existing.capacity || 0) + Number(school.capacity || 0)
        existing.skills = Array.from(new Set([...(existing.skills || []), ...(school.skills || [])]))
        return
      }

      field.schools.push({
        id: schoolId,
        code: school.code || null,
        name: school.name,
        level: school.level || null,
        location: school.location || null,
        address: school.address || null,
        ward: school.ward || null,
        district_name: school.district_name || null,
        province_name: school.province_name || null,
        capacity: Number(school.capacity || 0),
        skills: school.skills || [],
      })
    }

    classifiedJobs.forEach((job) => {
      const field = fields.get(job.industry) || fields.get(inferCareerFieldId(`${job.profession} ${job.title} ${job.industry_name}`))
      if (!field) return

      field.job_count += 1
      field.job_openings += Number(job.recruitment_count || 0)
      field._company_names.add(job.company)
      addUnique(field.majors, job.profession)
      ;(job.skills || []).forEach((skill) => addUnique(field.skills, skill))
      const recruiterKey = normalizeText(job.company)
      if (!field._recruiters.has(recruiterKey)) {
        field._recruiters.set(recruiterKey, {
          employer: job.company,
          company_logo: job.company_logo || null,
          company_color: job.company_color || null,
          quantity: 0,
          positions: [],
        })
      }
      const recruiter = field._recruiters.get(recruiterKey)
      recruiter.quantity += Number(job.recruitment_count || 0)
      recruiter.positions.push({
        id: job.id,
        title: job.title,
        quantity: Number(job.recruitment_count || 0),
        location: job.ward_name || job.address || null,
      })
    })

    professions.forEach((profession) => {
      const field = fields.get(inferCareerFieldId(profession.major))
      if (!field) return

      field.demand += Number(profession.demand || 0)
      field.capacity += Number(profession.capacity || 0)
      field.proposed += Number(profession.proposed || 0)
      addUnique(field.majors, profession.major)
      ;(profession.skills || []).forEach((skill) => addUnique(field.skills, skill))
      ;(profession.capacity_skills || []).forEach((skill) => addUnique(field.capacity_skills, skill))
      ;(profession.levels || []).forEach((level) => {
        if (!field.levels.some((item) => String(item.id) === String(level.id))) field.levels.push(level)
      })
      ;(profession.schools || []).forEach((school) => {
        addSchool(field, school)
      })
      ;(profession.demands || []).forEach((demand) => {
        field.demands.push({ ...demand, major: profession.major })
        if (demand.employer) field._company_names.add(demand.employer)
      })
    })

    const majorNamesById = new Map(
      trainingMajors
        .filter((major) => major?.id && major?.name)
        .map((major) => [String(major.id), major.name]),
    )

    schools
      .filter((school) => school?.status !== 'INACTIVE')
      .forEach((school) => {
        const majorNames = []
        ;(school.training_major_ids || []).forEach((majorId) => {
          addUnique(majorNames, majorNamesById.get(String(majorId)))
        })
        ;(school.training_major_summary || []).forEach((major) => addUnique(majorNames, major?.name))

        majorNames.forEach((majorName) => {
          const field = fields.get(inferCareerFieldId(majorName))
          addSchool(field, {
            id: school.id,
            code: school.code,
            name: school.name,
            level: (school.education_levels_list || []).join(' · ') || school.education_level_name || null,
            location: school.ward || school.district_name || null,
            address: school.address || null,
            ward: school.ward || null,
            district_name: school.district_name || null,
            province_name: school.province_name || null,
          })
        })
      })

    return Array.from(fields.values())
      .map((field) => ({
        ...field,
        demand: field.demand || field.job_openings,
        gap: Math.max(0, (field.demand || field.job_openings) - field.capacity),
        companies: field._company_names.size,
        school_count: new Set(field.schools.map((school) => school.id || normalizeText(school.name))).size,
        majors: field.majors.sort((a, b) => a.localeCompare(b, 'vi')),
        schools: field.schools.sort((a, b) => String(a.name || '').localeCompare(String(b.name || ''), 'vi')),
        demands: field.demands.sort((a, b) => Number(b.quantity || 0) - Number(a.quantity || 0)),
        recruiters: Array.from(field._recruiters.values())
          .map((recruiter) => ({
            ...recruiter,
            positions: recruiter.positions.sort((a, b) => Number(b.quantity || 0) - Number(a.quantity || 0)),
          }))
          .sort((a, b) => Number(b.quantity || 0) - Number(a.quantity || 0)),
        _company_names: undefined,
        _recruiters: undefined,
      }))
      .sort((a, b) => (b.job_count + b.demand) - (a.job_count + a.demand))
  },

  getSalaryRanges() {
    return SALARY_RANGES
  },

  getAllSkills() {
    const skills = new Set()
    classifiedJobs.forEach(j => {
      ;(j.skills || []).forEach(s => skills.add(s))
    })
    return Array.from(skills)
  },

  filterJobs({ search = '', wardCode = 'all', industry = 'all', salaryLevel = 'all', skill = 'all' }) {
    let result = [...classifiedJobs]

    // 1. Search keyword (tiêu đề, tên công ty, địa chỉ, ngành)
    if (search && search.trim()) {
      const q = search.trim().toLowerCase()
      result = result.filter(j => 
        j.title.toLowerCase().includes(q) ||
        j.company.toLowerCase().includes(q) ||
        j.address.toLowerCase().includes(q) ||
        j.ward_name.toLowerCase().includes(q) ||
        j.industry_name.toLowerCase().includes(q) ||
        (j.skills && j.skills.some(s => s.toLowerCase().includes(q)))
      )
    }

    // 2. Filter by Ward from CSDL
    if (wardCode && wardCode !== 'all') {
      const wRaw = wardCode.toLowerCase().trim()
      const wClean = wRaw.replace(/^(xã|phường|thị trấn)\s+/i, '').trim()
      result = result.filter(j => 
        j.ward_code === wardCode || 
        (j.ward_name && (j.ward_name.toLowerCase() === wRaw || j.ward_name.toLowerCase() === wClean)) ||
        (j.address && (j.address.toLowerCase().includes(wRaw) || j.address.toLowerCase().includes(wClean)))
      )
    }

    // 3. Filter by Industry
    if (industry && industry !== 'all') {
      result = result.filter(j => j.industry === industry)
    }

    // 4. Filter by Salary Level
    if (salaryLevel && salaryLevel !== 'all') {
      result = result.filter(j => j.salary_level === salaryLevel)
    }

    // 5. Filter by Skill
    if (skill && skill !== 'all') {
      const sk = skill.toLowerCase()
      result = result.filter(j => (j.skills || []).some(s => s.toLowerCase() === sk))
    }

    return result
  },

  getProfessionsSummary({ search = '', industry = 'all' } = {}) {
    const map = new Map()
    classifiedJobs.forEach(job => {
      const p = job.profession || job.title
      if (!map.has(p)) {
        map.set(p, {
          profession: p,
          industry: job.industry,
          industry_name: job.industry_name,
          recruitment_count: 0,
          skills: new Set(),
          training_schools: [],
          demands: []
        })
      }
      const entry = map.get(p)
      entry.recruitment_count += (job.recruitment_count || 100)
      ;(job.skills || []).forEach(s => entry.skills.add(s))
      ;(job.training_schools || []).forEach(ts => {
        if (!entry.training_schools.some(x => x.name === ts.name)) {
          entry.training_schools.push(ts)
        }
      })
      ;(job.demands || [{ company: job.company, quantity: job.recruitment_count || 50 }]).forEach(d => {
        if (!entry.demands.some(x => x.company === d.company)) {
          entry.demands.push(d)
        }
      })
    })

    let list = Array.from(map.values()).map(item => ({
      ...item,
      skills: Array.from(item.skills)
    }))

    if (search && search.trim()) {
      const q = search.trim().toLowerCase()
      list = list.filter(p => 
        p.profession.toLowerCase().includes(q) ||
        p.skills.some(s => s.toLowerCase().includes(q)) ||
        p.training_schools.some(ts => ts.name.toLowerCase().includes(q) || ts.district.toLowerCase().includes(q)) ||
        p.demands.some(d => d.company.toLowerCase().includes(q))
      )
    }

    if (industry && industry !== 'all') {
      list = list.filter(p => p.industry === industry)
    }

    return list
  }
}
