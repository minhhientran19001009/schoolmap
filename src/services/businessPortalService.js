const API_BASE = '/api/demo/business-portal'

async function request(path, options = {}) {
  const response = await fetch(`${API_BASE}${path}`, {
    credentials: 'same-origin',
    ...options,
    headers: {
      Accept: 'application/json',
      ...(options.body ? { 'Content-Type': 'application/json' } : {}),
      ...(options.headers || {}),
    },
  })

  const payload = await response.json().catch(() => ({}))
  if (!response.ok) {
    const validationMessage = payload?.errors
      ? Object.values(payload.errors).flat()[0]
      : null
    throw new Error(validationMessage || payload?.message || 'Không thể kết nối cổng Doanh nghiệp.')
  }

  return payload
}

export const businessPortalService = {
  getEmployers() {
    return request('/employers')
  },

  selectEmployer(employerId) {
    return request('/select-employer', {
      method: 'POST',
      body: JSON.stringify({ employer_id: employerId }),
    })
  },

  getDemands() {
    return request('/demands')
  },

  saveDemand(data) {
    return request('/demands', {
      method: 'POST',
      body: JSON.stringify(data),
    })
  },
}
