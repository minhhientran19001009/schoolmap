<template>
  <div class="w-full h-full relative overflow-hidden">
    <!-- Map Component -->
    <SchoolMap 
      ref="mapRef"
      :schools="schools"
      :districts="districts"
      @view-detail="openDetailModal"
    />

    <!-- School Detail Drawer (Right Sidebar View) -->
    <SchoolDetailDrawer 
      v-if="selectedSchool"
      :school="selectedSchool"
      @close="selectedSchool = null"
      @buffer-analyze="handleBufferAnalyze"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue'
import SchoolMap from '../../components/map/SchoolMap.vue'
import SchoolDetailDrawer from '../../components/school/SchoolDetailDrawer.vue'
import { schoolService, liveSchools } from '../../services/schoolService'

const schools = liveSchools
const districts = ref(schoolService.getDistricts())
const selectedSchool = ref(null)
const mapRef = ref(null)

async function loadData() {
  await schoolService.syncFromApi()
}

function openDetailModal(school) {
  selectedSchool.value = school
}

function handleBufferAnalyze(school) {
  nextTick(() => {
    mapRef.value?.triggerBufferForSchool(school, 3)
  })
}

onMounted(() => {
  loadData()

  // Handle URL query parameters e.g. ?school=37001 or ?buffer=37001
  const params = new URLSearchParams(window.location.search)
  const schoolParam = params.get('school')
  if (schoolParam) {
    const s = schools.value.find(item => item.id === schoolParam || item.code === schoolParam)
    if (s) {
      selectedSchool.value = s
      nextTick(() => {
        mapRef.value?.flyToSchool(s)
      })
    }
  }

  const bufferParam = params.get('buffer')
  if (bufferParam) {
    const s = schools.value.find(item => item.id === bufferParam || item.code === bufferParam)
    if (s) {
      nextTick(() => {
        mapRef.value?.triggerBufferForSchool(s, 3)
      })
    }
  }
})
</script>
