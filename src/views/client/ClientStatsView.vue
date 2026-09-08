<template>
  <div class="w-full h-full overflow-y-auto bg-slate-50">
    <StatisticsDashboard 
      :schools="schools"
      :districts="districts"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import StatisticsDashboard from '../../components/dashboard/StatisticsDashboard.vue'
import { schoolService, liveSchools } from '../../services/schoolService'

const schools = liveSchools
const districts = ref(schoolService.getDistricts())

onMounted(async () => {
  await schoolService.syncFromApi()
})
</script>
