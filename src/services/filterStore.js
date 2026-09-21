import { reactive, ref } from 'vue'

export const filterStore = reactive({
  search: '',
  level: 'all',
  ward: 'all',
  majorId: 'all',

  reset() {
    this.search = ''
    this.level = 'all'
    this.ward = 'all'
    this.majorId = 'all'
  },

  hasActiveFilter() {
    return this.search !== '' || this.level !== 'all' || this.ward !== 'all' || this.majorId !== 'all'
  }
})

// Trigger flying to a school on the map when chosen from autocomplete search
export const targetSchoolToFly = ref(null)
