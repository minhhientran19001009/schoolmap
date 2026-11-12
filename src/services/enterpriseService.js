import { computed } from 'vue'
import { publicDirectory } from './publicDirectoryService'
import { jobService } from './jobService'

// Comprehensive catalog of enterprises across Ninh Bình & vicinity
export const ENTERPRISE_INDUSTRIES = [
  { id: 'all', name: 'Tất cả lĩnh vực', icon: 'fa-solid fa-layer-group' },
  { id: 'dien_tu', name: 'Điện tử, viễn thông, máy tính', icon: 'fa-solid fa-microchip' },
  { id: 'o_to_co_khi', name: 'Ô tô, xe máy & Cơ khí', icon: 'fa-solid fa-wrench' },
  { id: 'da_giay', name: 'Da giày', icon: 'fa-solid fa-shoe-prints' },
  { id: 'det_may', name: 'Dệt may, thời trang', icon: 'fa-solid fa-shirt' },
  { id: 'linh_kien', name: 'Linh kiện & Công nghiệp hỗ trợ', icon: 'fa-solid fa-gears' },
  { id: 'du_lich_dich_vu', name: 'Du lịch, khách sạn & Dịch vụ', icon: 'fa-solid fa-hotel' },
  { id: 'nong_nghiep_tp', name: 'Nông nghiệp & Chế biến thực phẩm', icon: 'fa-solid fa-seedling' },
  { id: 'cntt_phan_mem', name: 'Công nghệ thông tin & Phần mềm', icon: 'fa-solid fa-laptop-code' },
  { id: 'xay_dung', name: 'Xây dựng & Kiến trúc', icon: 'fa-solid fa-trowel-bricks' },
  { id: 'duoc_hoa_chat', name: 'Dược phẩm, hóa chất & Năng lượng', icon: 'fa-solid fa-flask' },
  { id: 'khac', name: 'Lĩnh vực khác', icon: 'fa-solid fa-building' }
]

export const ENTERPRISE_REGIONS = [
  { id: 'all', name: 'Tất cả khu vực / KCN' },
  { id: 'kcn_gian_khau', name: 'KCN Gián Khẩu (Gia Viễn)' },
  { id: 'kcn_phuc_son', name: 'KCN Phúc Sơn (TP. Ninh Bình)' },
  { id: 'kcn_tam_diep', name: 'KCN Tam Điệp (TP. Tam Điệp)' },
  { id: 'kcn_dong_van', name: 'KCN Đồng Văn / Duy Hà' },
  { id: 'kcn_bao_minh', name: 'KCN Bảo Minh' },
  { id: 'kcn_my_thuan', name: 'KCN Mỹ Thuận' },
  { id: 'tp_ninh_binh', name: 'TP. Ninh Bình' },
  { id: 'tp_tam_diep', name: 'TP. Tam Điệp' }
]

export const ENTERPRISE_SCALES = [
  { id: 'all', label: 'Tất cả quy mô lao động' },
  { id: 'above_5000', label: 'Trên 5.000 lao động' },
  { id: '1000_5000', label: '1.000 - 5.000 lao động' },
  { id: '500_1000', label: '500 - 1.000 lao động' },
  { id: 'under_500', label: 'Dưới 500 lao động' }
]

function normalizeEnterpriseName(value) {
  return String(value || '')
    .toLocaleLowerCase('vi')
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .replace(/đ/g, 'd')
    .replace(/\bcong ty trach nhiem huu han\b/g, 'cong ty tnhh')
    .replace(/\bcong ty co phan\b/g, 'cong ty cp')
    .replace(/\bcty\b/g, 'cong ty')
    .replace(/[^a-z0-9]+/g, ' ')
    .replace(/\s+/g, ' ')
    .trim()
}

const DIRECTORY_ENTERPRISES = computed(() => publicDirectory.value.employers.map(enterprise => {
  const jobs = jobService.getAll().filter(job => Number(job.employer_id) === Number(enterprise.employer_id))
  return { partner_schools: [], logo: 'DN', color: 'bg-blue-700', industry: 'khac', ...enterprise, jobs_count: jobs.length, jobs }
}).sort((a, b) => Number(b.jobs_count > 0) - Number(a.jobs_count > 0) || a.name.localeCompare(b.name, 'vi')))

export const enterpriseService = {
  getAll() {
    return DIRECTORY_ENTERPRISES.value
  },

  getById(id) {
    const list = this.getAll()
    return list.find(e => String(e.id) === String(id)) || null
  },

  getByCompanyName(name) {
    const normalizedName = normalizeEnterpriseName(name)
    if (!normalizedName) return null
    return DIRECTORY_ENTERPRISES.value.find((enterprise) => normalizeEnterpriseName(enterprise.name) === normalizedName) || null
  },

  getIndustries() {
    return ENTERPRISE_INDUSTRIES
  },

  getRegions() {
    return ENTERPRISE_REGIONS
  },

  getScales() {
    return ENTERPRISE_SCALES
  },

  // Filter enterprises by multi-criteria
  filterEnterprises({ search = '', industry = 'all', region = 'all', scale = 'all', hasJobsOnly = false }) {
    let result = this.getAll()

    // 1. Search keyword
    if (search && search.trim()) {
      const q = normalizeEnterpriseName(search)
      result = result.filter(e => 
        normalizeEnterpriseName(e.name).includes(q) ||
        normalizeEnterpriseName(e.short_name).includes(q) ||
        normalizeEnterpriseName(e.address).includes(q) ||
        normalizeEnterpriseName(e.tax_code).includes(q) ||
        normalizeEnterpriseName(e.industry_name).includes(q) ||
        normalizeEnterpriseName(e.region_name).includes(q)
      )
    }

    // 2. Filter by industry
    if (industry && industry !== 'all') {
      result = result.filter(e => e.industry === industry)
    }

    // 3. Filter by region
    if (region && region !== 'all') {
      result = result.filter(e => e.region === region)
    }

    // 4. Filter by workforce scale
    if (scale && scale !== 'all') {
      if (scale === 'above_5000') {
        result = result.filter(e => e.employee_count >= 5000)
      } else if (scale === '1000_5000') {
        result = result.filter(e => e.employee_count >= 1000 && e.employee_count < 5000)
      } else if (scale === '500_1000') {
        result = result.filter(e => e.employee_count >= 500 && e.employee_count < 1000)
      } else if (scale === 'under_500') {
        result = result.filter(e => e.employee_count < 500)
      }
    }

    // 5. Filter only hiring enterprises
    if (hasJobsOnly) {
      result = result.filter(e => e.jobs_count > 0)
    }

    return result
  }
}
