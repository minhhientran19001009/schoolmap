<template>
  <div 
    @click="$emit('select', job)"
    class="bg-white rounded-xl border border-slate-200 p-3.5 sm:p-4 hover:border-[#6c3fb8] hover:shadow-md transition-all duration-200 cursor-pointer flex flex-col justify-between group relative overflow-hidden">
    
    <!-- Top Row: Logo & Title -->
    <div>
      <div class="flex items-start gap-3">
        <!-- Company Logo Box (Vieclam24h style) -->
        <div 
          :class="job.company_color || 'bg-[#3b1d74]'"
          class="w-12 h-12 rounded-xl text-white font-black text-xs flex items-center justify-center flex-shrink-0 shadow-2xs border border-slate-100 tracking-wider">
          {{ job.company_logo || 'DN' }}
        </div>

        <!-- Title & Company -->
        <div class="flex-1 min-w-0 pr-1">
          <h3 
            class="text-sm font-bold text-slate-900 group-hover:text-[#6c3fb8] transition-colors line-clamp-1 leading-snug"
            :title="job.title">
            {{ job.title }}
          </h3>

          <p 
            class="text-xs text-slate-500 font-medium line-clamp-1 mt-0.5"
            :title="job.company">
            {{ job.company }}
          </p>
        </div>
      </div>

      <!-- Middle: Salary & Location (Vieclam24h format - responsive safe) -->
      <div class="mt-3.5 flex items-center justify-between gap-2 text-xs">
        <!-- Salary (Prominent Blue / Green) -->
        <div class="font-bold text-[#1d4ed8] flex items-center gap-1.5 text-xs sm:text-[13px] min-w-0 flex-1">
          <i class="fa-solid fa-money-bill-wave text-emerald-600 flex-shrink-0"></i>
          <span class="truncate" :title="job.salary">{{ formatSalary(job.salary) }}</span>
        </div>

        <!-- Location (Ward from CSDL) -->
        <div class="text-slate-500 font-medium flex items-center gap-1 truncate text-xs flex-shrink-0 max-w-[48%]" :title="job.address">
          <i class="fa-solid fa-location-dot text-slate-400 text-[11px] flex-shrink-0"></i>
          <span class="truncate">{{ job.ward_name }}</span>
        </div>
      </div>
    </div>

    <!-- Bottom Row: Remaining time / Quick Apply CTA -->
    <div class="mt-3.5 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
      <span class="flex items-center gap-1">
        <i class="fa-regular fa-clock text-[10px]"></i>
        <span>Còn {{ getDaysLeft(job.deadline) }} ngày</span>
      </span>

      <span class="text-[#6c3fb8] font-semibold group-hover:underline flex items-center gap-1 flex-shrink-0">
        <span>Ứng tuyển</span>
        <i class="fa-solid fa-angle-right text-[10px]"></i>
      </span>
    </div>

  </div>
</template>

<script setup>
const props = defineProps({
  job: {
    type: Object,
    required: true
  }
})

defineEmits(['select'])

function formatSalary(sal) {
  if (!sal) return 'Thỏa thuận'
  const match = sal.match(/^([^(]+)/)
  return match ? match[1].trim() : sal
}

function getDaysLeft(deadlineStr) {
  if (!deadlineStr) return 15
  // Calculate or mock 10-30 days
  const hash = deadlineStr.split('').reduce((acc, char) => acc + char.charCodeAt(0), 0)
  return 5 + (hash % 25)
}
</script>
