const SESSION_KEY = 'schoolmap_demo_portal_role'
const SCHOOL_SESSION_KEY = 'schoolmap_demo_school'
const EMPLOYER_SESSION_KEY = 'schoolmap_demo_employer'
const VALID_ROLES = new Set(['school', 'business'])

export const portalRoleService = {
  get() {
    try {
      const role = window.sessionStorage.getItem(SESSION_KEY)
      return VALID_ROLES.has(role) ? role : null
    } catch (error) {
      return null
    }
  },

  set(role) {
    if (!VALID_ROLES.has(role)) return false
    try {
      window.sessionStorage.setItem(SESSION_KEY, role)
      return true
    } catch (error) {
      return false
    }
  },

  getSchool() {
    try {
      const school = JSON.parse(window.sessionStorage.getItem(SCHOOL_SESSION_KEY) || 'null')
      return school?.id && school?.name ? school : null
    } catch (error) {
      return null
    }
  },

  setSchool(school) {
    if (!school?.id || !school?.name) return false
    try {
      window.sessionStorage.setItem(SCHOOL_SESSION_KEY, JSON.stringify({
        id: String(school.id),
        code: school.code || '',
        name: school.name,
        address: school.address || '',
        ward: school.ward || '',
      }))
      return true
    } catch (error) {
      return false
    }
  },

  clearSchool() {
    try {
      window.sessionStorage.removeItem(SCHOOL_SESSION_KEY)
    } catch (error) {
      // Session storage can be unavailable in privacy-restricted contexts.
    }
  },

  getEmployer() {
    try {
      const employer = JSON.parse(window.sessionStorage.getItem(EMPLOYER_SESSION_KEY) || 'null')
      return employer?.id && employer?.name ? employer : null
    } catch (error) {
      return null
    }
  },

  setEmployer(employer) {
    if (!employer?.id || !employer?.name) return false
    try {
      window.sessionStorage.setItem(EMPLOYER_SESSION_KEY, JSON.stringify({
        id: Number(employer.id),
        name: employer.name,
        tax_code: employer.tax_code || '',
        industry: employer.industry || '',
        region: employer.region || '',
        employee_count: Number(employer.employee_count || 0),
        address: employer.address || '',
        is_verified: Boolean(employer.is_verified),
      }))
      return true
    } catch (error) {
      return false
    }
  },

  clearEmployer() {
    try {
      window.sessionStorage.removeItem(EMPLOYER_SESSION_KEY)
    } catch (error) {
      // Session storage can be unavailable in privacy-restricted contexts.
    }
  },

  canAccess(role) {
    if (!VALID_ROLES.has(role) || this.get() !== role) return false
    if (role === 'school') return Boolean(this.getSchool())
    if (role === 'business') return Boolean(this.getEmployer())
    return false
  },
}
