import { ref } from 'vue'
import districts from '../data/ninhbinh_districts.json'

export const ALLOWED_LEVELS = ['gdtx', 'trung_cap', 'cao_dang', 'dai_hoc']

export const EDUCATION_LEVELS = [
  { id: 'all', label: 'Tất cả cấp học', color: '#1e40af', icon: 'fa-solid fa-graduation-cap' },
  { id: 'gdtx', label: 'GDTX - GDNN', color: '#dc2626', icon: 'fa-solid fa-graduation-cap' },
  { id: 'trung_cap', label: 'Trung cấp', color: '#f59e0b', icon: 'fa-solid fa-graduation-cap' },
  { id: 'cao_dang', label: 'Cao đẳng', color: '#16a34a', icon: 'fa-solid fa-graduation-cap' },
  { id: 'dai_hoc', label: 'Đại học', color: '#2563eb', icon: 'fa-solid fa-graduation-cap' },
]

export const LEVEL_MAP = EDUCATION_LEVELS.reduce((acc, item) => {
  acc[item.id] = item
  return acc
}, {})

export const SCHOOL_TYPES = [
  { id: 'all', label: 'Tất cả loại hình' },
  { id: 'cong_lap', label: 'Công lập' },
  { id: 'tu_thuc', label: 'Ngoài công lập / Tư thục' },
]

export const STATUS_MAP = {
  VERIFIED: { label: 'Đã xác minh', color: 'bg-emerald-100 text-emerald-700 border-emerald-300' },
  NEED_REVIEW: { label: 'Cần kiểm tra', color: 'bg-amber-100 text-amber-700 border-amber-300' },
  UNVERIFIED: { label: 'Chưa xác minh', color: 'bg-rose-100 text-rose-700 border-rose-300' },
}

// Live reactive state: populated exclusively from the MySQL database
export const liveSchools = ref([])
export const isSchoolsLoading = ref(false)

const envBase = (typeof import.meta !== 'undefined' && import.meta.env?.VITE_API_BASE_URL) ? import.meta.env.VITE_API_BASE_URL.replace(/\/+$/, '') : ''

const API_ENDPOINTS = [
  ...(envBase ? [`${envBase}/api/schools`] : []),
  '/api/schools',
  'http://localhost:8000/api/schools',
  'http://127.0.0.1:8000/api/schools'
]

async function requestApi(path, options = {}) {
  const isLocal = typeof window !== 'undefined' && (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1')
  const hosts = envBase 
    ? [envBase, '', 'http://localhost:8000', 'http://127.0.0.1:8000']
    : (isLocal ? ['', 'http://localhost:8000', 'http://127.0.0.1:8000'] : [''])
  for (const host of hosts) {
    try {
      const url = host ? `${host}${path}` : path
      const res = await fetch(url, {
        ...options,
        headers: {
          'Accept': 'application/json',
          ...(options.headers || {})
        }
      })
      if (res.ok) {
        return await res.json()
      }
    } catch (e) {
      // try next host
    }
  }
  return null
}

export const schoolService = {
  liveSchools,

  // Initialize service: clear old local storage seed caches and pull fresh data from MySQL
  async init() {
    try {
      // Purge all legacy client-side mock/seed localStorage keys
      for (let i = 0; i < localStorage.length; i++) {
        const key = localStorage.key(i)
        if (key && key.startsWith('nb_education_schools')) {
          localStorage.removeItem(key)
        }
      }
    } catch (e) {}

    return await this.syncFromApi()
  },

  // Fetch directly from DB via backend API
  async syncFromApi() {
    isSchoolsLoading.value = true
    try {
      for (const endpoint of API_ENDPOINTS) {
        try {
          const res = await fetch(endpoint, {
            headers: { 'Accept': 'application/json' }
          })
          if (res.ok) {
            const data = await res.json()
            if (Array.isArray(data)) {
              const filtered = data.filter(s => ALLOWED_LEVELS.includes(s.education_level))
              liveSchools.value = filtered
              isSchoolsLoading.value = false
              return filtered
            }
          }
        } catch (err) {
          // try next
        }
      }
    } finally {
      isSchoolsLoading.value = false
    }
    return liveSchools.value
  },

  getAll() {
    return liveSchools.value
  },

  async loadSchools() {
    if (liveSchools.value.length === 0) {
      return await this.syncFromApi()
    }
    // Background refresh
    this.syncFromApi()
    return liveSchools.value
  },

  getById(id) {
    return liveSchools.value.find(s => s.id === id || s.code === id) || null
  },

  async add(school) {
    const res = await requestApi('/api/schools', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(school)
    })
    await this.syncFromApi()
    return res || school
  },

  async create(school) {
    return await this.add(school)
  },

  async update(idOrSchool, data) {
    const targetId = typeof idOrSchool === 'object' ? idOrSchool.id : idOrSchool
    const updateData = typeof idOrSchool === 'object' ? idOrSchool : data

    const res = await requestApi(`/api/schools/${targetId}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(updateData)
    })
    await this.syncFromApi()
    return res || updateData
  },

  async delete(id) {
    await requestApi(`/api/schools/${id}`, {
      method: 'DELETE'
    })
    await this.syncFromApi()
    return true
  },

  getDistricts() {
    return districts
  },

  async getWards() {
    // Load wards directly from MySQL DB
    const data = await requestApi('/api/wards')
    if (Array.isArray(data) && data.length > 0) {
      return data
    }
    // Fallback to GIS boundary features
    try {
      const gisData = await import('../data/gis/ninh_binh_wards.json')
      const wardsGeoJSON = gisData.default || gisData
      return (wardsGeoJSON.features || []).map(f => ({
        id: f.properties.code,
        code: f.properties.code,
        name: f.properties.name,
        full_name: f.properties.fullName || f.properties.name,
        unit_type: f.properties.fullName?.startsWith('Phường') ? 'Phường' : (f.properties.fullName?.startsWith('Thị trấn') ? 'Thị trấn' : 'Xã')
      })).sort((a, b) => a.name.localeCompare(b.name, 'vi'))
    } catch (e) {
      return []
    }
  }
}
