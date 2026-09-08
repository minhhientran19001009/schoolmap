<template>
  <div class="h-full overflow-y-auto bg-slate-50 p-4 sm:p-6 select-none space-y-6">
    
    <!-- Dashboard Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
      <div>
        <h2 class="text-lg sm:text-xl font-bold text-slate-800 flex items-center gap-2">
          <i class="fa-solid fa-chart-pie text-blue-800"></i>
          <span>Dashboard Thống kê Mạng lưới Giáo dục Tỉnh Ninh Bình</span>
        </h2>
        <p class="text-xs text-slate-500 mt-0.5">Dữ liệu phân tích đa chiều phục vụ công tác theo dõi, quản lý và quy hoạch mạng lưới trường học</p>
      </div>
      <div class="flex items-center gap-2">
        <span class="text-xs text-slate-500 font-medium">Cập nhật: <strong class="text-slate-700">02/09/2026</strong></span>
      </div>
    </div>

    <!-- KPI Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-2.5 sm:gap-4">
      
      <!-- KPI 1: Tổng số trường -->
      <div class="bg-white p-3 sm:p-4 rounded-xl sm:rounded-2xl border border-slate-200 shadow-2xs">
        <div class="flex items-center justify-between text-slate-500 mb-1">
          <span class="text-[11px] sm:text-xs font-semibold">Tổng cơ sở giáo dục</span>
          <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center text-xs">
            <i class="fa-solid fa-school"></i>
          </div>
        </div>
        <div class="text-xl sm:text-2xl font-black text-slate-800">{{ totalSchools }}</div>
        <div class="text-[10px] sm:text-[11px] text-emerald-600 font-medium flex items-center gap-1 mt-1 truncate">
          <i class="fa-solid fa-arrow-trend-up"></i>
          <span>Phủ rộng 8 huyện/thành phố</span>
        </div>
      </div>

      <!-- KPI 2: Tổng học sinh -->
      <div class="bg-white p-3 sm:p-4 rounded-xl sm:rounded-2xl border border-slate-200 shadow-2xs">
        <div class="flex items-center justify-between text-slate-500 mb-1">
          <span class="text-[11px] sm:text-xs font-semibold">Tổng số học sinh</span>
          <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center text-xs">
            <i class="fa-solid fa-user-graduate"></i>
          </div>
        </div>
        <div class="text-xl sm:text-2xl font-black text-slate-800">{{ totalStudents.toLocaleString('vi-VN') }}</div>
        <div class="text-[10px] sm:text-[11px] text-blue-600 font-medium mt-1 truncate">
          ~ {{ (totalStudents / (totalTeachers || 1)).toFixed(1) }} HS / GV
        </div>
      </div>

      <!-- KPI 3: Tổng giáo viên -->
      <div class="bg-white p-3 sm:p-4 rounded-xl sm:rounded-2xl border border-slate-200 shadow-2xs">
        <div class="flex items-center justify-between text-slate-500 mb-1">
          <span class="text-[11px] sm:text-xs font-semibold">Tổng cán bộ & GV</span>
          <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-lg bg-purple-50 text-purple-700 flex items-center justify-center text-xs">
            <i class="fa-solid fa-chalkboard-user"></i>
          </div>
        </div>
        <div class="text-xl sm:text-2xl font-black text-slate-800">{{ totalTeachers.toLocaleString('vi-VN') }}</div>
        <div class="text-[10px] sm:text-[11px] text-slate-500 font-medium mt-1 truncate">
          100% đạt chuẩn trình độ
        </div>
      </div>

      <!-- KPI 4: Chuẩn quốc gia -->
      <div class="bg-white p-3 sm:p-4 rounded-xl sm:rounded-2xl border border-slate-200 shadow-2xs">
        <div class="flex items-center justify-between text-slate-500 mb-1">
          <span class="text-[11px] sm:text-xs font-semibold">Chuẩn Quốc gia</span>
          <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center text-xs">
            <i class="fa-solid fa-award"></i>
          </div>
        </div>
        <div class="text-xl sm:text-2xl font-black text-slate-800">{{ nationalStandardPercent }}%</div>
        <div class="text-[10px] sm:text-[11px] text-amber-600 font-medium mt-1 truncate">
          {{ nationalStandardCount }} trường đạt chuẩn
        </div>
      </div>

      <!-- KPI 5: Công lập / Ngoài công lập -->
      <div class="bg-white p-3 sm:p-4 rounded-xl sm:rounded-2xl border border-slate-200 shadow-2xs col-span-2 lg:col-span-1">
        <div class="flex items-center justify-between text-slate-500 mb-1">
          <span class="text-[11px] sm:text-xs font-semibold">Tỷ lệ Công lập</span>
          <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-lg bg-indigo-50 text-indigo-700 flex items-center justify-center text-xs">
            <i class="fa-solid fa-landmark"></i>
          </div>
        </div>
        <div class="text-xl sm:text-2xl font-black text-slate-800">{{ publicPercent }}%</div>
        <div class="text-[10px] sm:text-[11px] text-slate-500 font-medium mt-1 truncate">
          Tư thục: {{ 100 - publicPercent }}%
        </div>
      </div>

    </div>

    <!-- Charts Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
      
      <!-- Chart 1: Donut Chart - Schools by Level -->
      <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
        <div class="flex items-center justify-between mb-2">
          <h3 class="font-bold text-slate-800 text-sm flex items-center gap-1.5">
            <i class="fa-solid fa-chart-pie text-blue-600 text-xs"></i>
            <span>Cơ cấu cơ sở giáo dục theo Cấp học</span>
          </h3>
          <span class="text-[11px] text-slate-400">3 cấp học</span>
        </div>
        <div ref="levelChartRef" class="w-full h-72"></div>
      </div>

      <!-- Chart 2: Bar Chart - Schools by District -->
      <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
        <div class="flex items-center justify-between mb-2">
          <h3 class="font-bold text-slate-800 text-sm flex items-center gap-1.5">
            <i class="fa-solid fa-chart-column text-emerald-600 text-xs"></i>
            <span>Phân bổ trường theo 8 Huyện / Thành phố</span>
          </h3>
          <span class="text-[11px] text-slate-400">Đơn vị: Trường</span>
        </div>
        <div ref="districtChartRef" class="w-full h-72"></div>
      </div>

      <!-- Chart 3: Grouped Bar - Students & Teachers Comparison -->
      <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
        <div class="flex items-center justify-between mb-2">
          <h3 class="font-bold text-slate-800 text-sm flex items-center gap-1.5">
            <i class="fa-solid fa-users text-purple-600 text-xs"></i>
            <span>Quy mô Học sinh & Giáo viên theo Cấp học</span>
          </h3>
          <span class="text-[11px] text-slate-400">Quy mô nhân sự</span>
        </div>
        <div ref="scaleChartRef" class="w-full h-72"></div>
      </div>

      <!-- Chart 4: Pie Chart - Public vs Private -->
      <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
        <div class="flex items-center justify-between mb-2">
          <h3 class="font-bold text-slate-800 text-sm flex items-center gap-1.5">
            <i class="fa-solid fa-shapes text-amber-600 text-xs"></i>
            <span>Cơ cấu Loại hình Trường học</span>
          </h3>
          <span class="text-[11px] text-slate-400">Công lập / Ngoài CL</span>
        </div>
        <div ref="typeChartRef" class="w-full h-72"></div>
      </div>

    </div>

    <!-- District Breakdown Table -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
      <h3 class="font-bold text-slate-800 text-sm mb-3 flex items-center gap-1.5">
        <i class="fa-solid fa-table-cells text-blue-700 text-xs"></i>
        <span>Bảng tổng hợp số liệu theo Đơn vị hành chính cấp Huyện</span>
      </h3>
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
            <tr>
              <th class="py-2.5 px-3">Huyện / Thành phố</th>
              <th class="py-2.5 px-3 text-center">Tổng trường</th>
              <th class="py-2.5 px-3 text-center">Mầm non</th>
              <th class="py-2.5 px-3 text-center">Tiểu học</th>
              <th class="py-2.5 px-3 text-center">THCS</th>
              <th class="py-2.5 px-3 text-center">THPT</th>
              <th class="py-2.5 px-3 text-center">Khác</th>
              <th class="py-2.5 px-3 text-right">Tổng học sinh</th>
              <th class="py-2.5 px-3 text-right">Tổng giáo viên</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-slate-700">
            <tr v-for="d in districtSummary" :key="d.id" class="hover:bg-slate-50">
              <td class="py-2.5 px-3 font-semibold text-slate-800">{{ d.name }}</td>
              <td class="py-2.5 px-3 text-center font-bold text-blue-800">{{ d.total }}</td>
              <td class="py-2.5 px-3 text-center">{{ d.mam_non }}</td>
              <td class="py-2.5 px-3 text-center">{{ d.tieu_hoc }}</td>
              <td class="py-2.5 px-3 text-center">{{ d.thcs }}</td>
              <td class="py-2.5 px-3 text-center">{{ d.thpt }}</td>
              <td class="py-2.5 px-3 text-center">{{ d.other }}</td>
              <td class="py-2.5 px-3 text-right font-semibold text-slate-800">{{ d.students.toLocaleString('vi-VN') }}</td>
              <td class="py-2.5 px-3 text-right text-slate-600">{{ d.teachers }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick, watch } from 'vue'
import * as echarts from 'echarts'
import { LEVEL_MAP } from '../../services/schoolService'

const props = defineProps({
  schools: {
    type: Array,
    default: () => []
  },
  districts: {
    type: Array,
    default: () => []
  }
})

const levelChartRef = ref(null)
const districtChartRef = ref(null)
const scaleChartRef = ref(null)
const typeChartRef = ref(null)

let levelChartInstance = null
let districtChartInstance = null
let scaleChartInstance = null
let typeChartInstance = null

// KPI Computations
const totalSchools = computed(() => props.schools.length)
const totalStudents = computed(() => props.schools.reduce((acc, s) => acc + (s.student_count || 0), 0))
const totalTeachers = computed(() => props.schools.reduce((acc, s) => acc + (s.teacher_count || 0), 0))

const nationalStandardCount = computed(() => props.schools.filter(s => s.is_national_standard).length)
const nationalStandardPercent = computed(() => {
  if (!totalSchools.value) return 0
  return Math.round((nationalStandardCount.value / totalSchools.value) * 100)
})

const publicCount = computed(() => props.schools.filter(s => s.school_type === 'cong_lap').length)
const publicPercent = computed(() => {
  if (!totalSchools.value) return 0
  return Math.round((publicCount.value / totalSchools.value) * 100)
})

// District Breakdown Table
const districtSummary = computed(() => {
  return props.districts.map(d => {
    const dSchools = props.schools.filter(s => s.district_id === d.id)
    return {
      id: d.id,
      name: d.name,
      total: dSchools.length,
      mam_non: dSchools.filter(s => s.education_level === 'mam_non').length,
      tieu_hoc: dSchools.filter(s => s.education_level === 'tieu_hoc').length,
      thcs: dSchools.filter(s => s.education_level === 'thcs').length,
      thpt: dSchools.filter(s => s.education_level === 'thpt').length,
      other: dSchools.filter(s => !['mam_non', 'tieu_hoc', 'thcs', 'thpt'].includes(s.education_level)).length,
      students: dSchools.reduce((acc, s) => acc + (s.student_count || 0), 0),
      teachers: dSchools.reduce((acc, s) => acc + (s.teacher_count || 0), 0)
    }
  })
})

function initCharts() {
  if (!levelChartRef.value) return

  // Chart 1: Donut Level
  const levelCounts = {}
  props.schools.forEach(s => {
    const label = LEVEL_MAP[s.education_level]?.label || s.education_level
    levelCounts[label] = (levelCounts[label] || 0) + 1
  })

  levelChartInstance = echarts.init(levelChartRef.value)
  levelChartInstance.setOption({
    tooltip: { trigger: 'item', formatter: '{b}: {c} trường ({d}%)' },
    legend: { bottom: '0%', left: 'center', textStyle: { fontSize: 12.5, fontFamily: 'Roboto' } },
    color: ['#dc2626', '#16a34a', '#2563eb'],
    series: [
      {
        name: 'Cấp học',
        type: 'pie',
        radius: ['45%', '70%'],
        avoidLabelOverlap: false,
        itemStyle: { borderRadius: 8, borderColor: '#fff', borderWidth: 2 },
        label: { show: false },
        data: Object.entries(levelCounts).map(([name, value]) => ({ name, value }))
      }
    ]
  })

  // Chart 2: District Bar
  const districtData = districtSummary.value.map(d => ({ name: d.name.replace('Huyện ', 'H. ').replace('Thành phố ', 'TP. '), value: d.total }))
  districtChartInstance = echarts.init(districtChartRef.value)
  districtChartInstance.setOption({
    tooltip: { trigger: 'axis', axisPointer: { type: 'shadow' } },
    grid: { left: '3%', right: '4%', bottom: '15%', top: '5%', containLabel: true },
    xAxis: {
      type: 'category',
      data: districtData.map(d => d.name),
      axisLabel: { interval: 0, rotate: 25, fontSize: 11.5, fontFamily: 'Roboto' }
    },
    yAxis: { type: 'value', minInterval: 1 },
    series: [
      {
        name: 'Số trường',
        type: 'bar',
        data: districtData.map(d => d.value),
        itemStyle: { color: '#059669', borderRadius: [6, 6, 0, 0] }
      }
    ]
  })

  // Chart 3: Scale Comparison
  const scaleLevels = ['gdtx', 'cao_dang', 'dai_hoc']
  const scaleLabels = scaleLevels.map(l => LEVEL_MAP[l]?.label)
  const studentsByLevel = scaleLevels.map(l => props.schools.filter(s => s.education_level === l).reduce((acc, s) => acc + (s.student_count || 0), 0))
  const teachersByLevel = scaleLevels.map(l => props.schools.filter(s => s.education_level === l).reduce((acc, s) => acc + (s.teacher_count || 0), 0))

  scaleChartInstance = echarts.init(scaleChartRef.value)
  scaleChartInstance.setOption({
    tooltip: { trigger: 'axis' },
    legend: { top: '0%', textStyle: { fontSize: 12.5, fontFamily: 'Roboto' } },
    grid: { left: '3%', right: '4%', bottom: '5%', top: '15%', containLabel: true },
    xAxis: { type: 'category', data: scaleLabels, axisLabel: { fontSize: 12 } },
    yAxis: { type: 'value' },
    series: [
      {
        name: 'Học sinh',
        type: 'bar',
        data: studentsByLevel,
        itemStyle: { color: '#1e40af', borderRadius: [4, 4, 0, 0] }
      },
      {
        name: 'Giáo viên',
        type: 'bar',
        data: teachersByLevel,
        itemStyle: { color: '#7c3aed', borderRadius: [4, 4, 0, 0] }
      }
    ]
  })

  // Chart 4: Type Donut
  typeChartInstance = echarts.init(typeChartRef.value)
  typeChartInstance.setOption({
    tooltip: { trigger: 'item', formatter: '{b}: {c} ({d}%)' },
    legend: { bottom: '0%', textStyle: { fontSize: 12.5, fontFamily: 'Roboto' } },
    color: ['#2563eb', '#f97316'],
    series: [
      {
        name: 'Loại hình',
        type: 'pie',
        radius: '65%',
        itemStyle: { borderRadius: 8, borderColor: '#fff', borderWidth: 2 },
        data: [
          { name: 'Công lập', value: publicCount.value },
          { name: 'Ngoài công lập / Tư thục', value: totalSchools.value - publicCount.value }
        ]
      }
    ]
  })
}

function handleResize() {
  levelChartInstance?.resize()
  districtChartInstance?.resize()
  scaleChartInstance?.resize()
  typeChartInstance?.resize()
}

onMounted(() => {
  nextTick(() => {
    initCharts()
    window.addEventListener('resize', handleResize)
  })
})

onUnmounted(() => {
  window.removeEventListener('resize', handleResize)
  levelChartInstance?.dispose()
  districtChartInstance?.dispose()
  scaleChartInstance?.dispose()
  typeChartInstance?.dispose()
})

watch(() => props.schools, () => {
  nextTick(() => {
    initCharts()
  })
}, { deep: true })
</script>
