import { shallowRef } from 'vue'

export const publicDirectory = shallowRef({ jobs: [], employers: [], career_fields: [] })
export const publicDirectoryError = shallowRef('')
let loadedAt = 0
let pending = null

export function loadPublicDirectory(force = false) {
  if (!force && loadedAt && Date.now() - loadedAt < 30000) return Promise.resolve()
  if (pending) return pending
  pending = fetch('/api/public/directory', { headers: { Accept: 'application/json' } })
    .then(async response => {
      if (!response.ok) throw new Error('Không tải được danh sách doanh nghiệp và việc làm.')
      const data = await response.json()
      if (!Array.isArray(data.jobs) || !Array.isArray(data.employers)) throw new Error('Dữ liệu danh mục không hợp lệ.')
      publicDirectory.value = data
      publicDirectoryError.value = ''
      loadedAt = Date.now()
    })
    .catch(error => { publicDirectoryError.value = error.message })
    .finally(() => { pending = null })
  return pending
}
