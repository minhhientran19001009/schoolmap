import initialJobs from '../data/jobPostings.json'

export const INDUSTRIES = [
  { id: 'all', name: 'Tất cả ngành nghề', icon: 'fa-solid fa-layer-group' },
  { id: 'co_khi', name: 'Kỹ thuật cơ khí', icon: 'fa-solid fa-wrench' },
  { id: 'dien_dien_tu', name: 'Điện - Điện tử - Viễn thông', icon: 'fa-solid fa-microchip' },
  { id: 'det_may', name: 'Dệt may - Giày da', icon: 'fa-solid fa-shirt' },
  { id: 'du_lich', name: 'Du lịch - Khách sạn - Dịch vụ', icon: 'fa-solid fa-hotel' },
  { id: 'nong_nghiep', name: 'Nông - Lâm - Thủy sản', icon: 'fa-solid fa-seedling' },
  { id: 'xay_dung', name: 'Xây dựng - Kiến trúc', icon: 'fa-solid fa-trowel-bricks' },
  { id: 'cntt', name: 'Công nghệ thông tin', icon: 'fa-solid fa-laptop-code' },
  { id: 'kinh_doanh', name: 'Kinh doanh - Quản lý', icon: 'fa-solid fa-chart-line' }
]

export const SALARY_RANGES = [
  { id: 'all', label: 'Tất cả mức lương' },
  { id: 'under_7', label: 'Dưới 7 triệu' },
  { id: '7_10', label: '7 - 10 triệu' },
  { id: '10_15', label: '10 - 15 triệu' },
  { id: 'above_15', label: 'Trên 15 triệu' }
]

export const jobService = {
  getAll() {
    return initialJobs
  },

  getById(id) {
    return initialJobs.find(j => j.id === id) || null
  },

  getIndustries() {
    return INDUSTRIES
  },

  getSalaryRanges() {
    return SALARY_RANGES
  },

  filterJobs({ search = '', wardCode = 'all', industry = 'all', salaryLevel = 'all' }) {
    let result = [...initialJobs]

    // 1. Search keyword (tiêu đề, tên công ty, địa chỉ, ngành)
    if (search && search.trim()) {
      const q = search.trim().toLowerCase()
      result = result.filter(j => 
        j.title.toLowerCase().includes(q) ||
        j.company.toLowerCase().includes(q) ||
        j.address.toLowerCase().includes(q) ||
        j.ward_name.toLowerCase().includes(q) ||
        j.industry_name.toLowerCase().includes(q)
      )
    }

    // 2. Filter by Ward from CSDL
    if (wardCode && wardCode !== 'all') {
      result = result.filter(j => 
        j.ward_code === wardCode || 
        j.ward_name.toLowerCase() === wardCode.toLowerCase()
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

    return result
  }
}
