const envBase = (typeof import.meta !== 'undefined' && import.meta.env?.VITE_API_BASE_URL)
  ? import.meta.env.VITE_API_BASE_URL.replace(/\/+$/, '')
  : ''

async function get(path, params = {}) {
  const query = new URLSearchParams()
  Object.entries(params).forEach(([key, value]) => {
    if (value !== null && value !== undefined && value !== '') query.set(key, value)
  })

  const hosts = envBase ? [envBase, ''] : ['']
  let lastError = null

  for (const host of hosts) {
    try {
      const response = await fetch(`${host}${path}${query.size ? `?${query}` : ''}`, {
        headers: { Accept: 'application/json' }
      })
      if (response.ok) return await response.json()
      lastError = new Error(`Không thể tải dữ liệu (${response.status}).`)
    } catch (error) {
      lastError = error
    }
  }

  throw lastError || new Error('Không thể kết nối máy chủ.')
}

export const supplyDemandService = {
  getCatalogs() {
    return get('/api/supply-demand/catalogs')
  },

  getPortal(filters = {}) {
    return get('/api/supply-demand/portal', filters)
  }
}
