import { reactive, ref } from 'vue'

export const filterStore = reactive({
  search: '',
  level: 'all',
  ward: 'all',

  reset() {
    this.search = ''
    this.level = 'all'
    this.ward = 'all'
  },

  hasActiveFilter() {
    return this.search !== '' || this.level !== 'all' || this.ward !== 'all'
  }
})

// Trigger flying to a school on the map when chosen from autocomplete search
export const targetSchoolToFly = ref(null)
