const API_BASE = '/api/demo/school-portal'

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
    throw new Error(validationMessage || payload?.message || 'Không thể kết nối cổng Cơ sở GDNN.')
  }

  return payload
}

export const schoolPortalService = {
  selectSchool(schoolId) {
    return request('/select-school', {
      method: 'POST',
      body: JSON.stringify({ school_id: schoolId }),
    })
  },

  getCapacities() {
    return request('/capacities')
  },

  saveCapacity(data) {
    return request('/capacities', {
      method: 'POST',
      body: JSON.stringify(data),
    })
  },

  getOpportunities() {
    return request('/opportunities')
  },

  saveProposal(data) {
    return request('/proposals', {
      method: 'POST',
      body: JSON.stringify(data),
    })
  },

  getFieldProposals() {
    return request('/field-proposals')
  },

  saveFieldProposal(data) {
    return request('/field-proposals', {
      method: 'POST',
      body: JSON.stringify(data),
    })
  },
}
