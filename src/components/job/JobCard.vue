<template>
  <div 
    ref="cardRef"
    role="link"
    tabindex="0"
    :aria-label="`Xem chi tiết việc làm ${job.title}`"
    @click="handleSelect"
    @keydown.enter.prevent="handleSelect"
    @mouseenter="scheduleQuickView"
    @mouseleave="cancelQuickView"
    @pointerdown="cancelQuickView"
    :class="previewActive ? 'border-blue-400 ring-2 ring-blue-100 shadow-md' : ''"
    class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs hover:-translate-y-0.5 hover:border-blue-300 hover:shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-200 transition-all duration-200 cursor-pointer flex flex-col justify-between group relative overflow-hidden h-full">
    
    <!-- Top Content Area -->
    <div>
      <!-- 1. Header: Company Logo & Job Title (Balanced 2-line title height) -->
      <div class="flex items-start gap-2.5 sm:gap-3">
        <!-- Company Logo Box -->
        <div
          :class="job.company_color || 'bg-blue-700'"
          class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl text-white font-black flex items-center justify-center flex-shrink-0 shadow-2xs border border-white/20 overflow-hidden text-center select-none p-1">
          <span 
            :class="getLogoTextClass(job.company_logo)"
            class="max-w-full truncate text-center font-black uppercase leading-tight block">
            {{ job.company_logo || 'DN' }}
          </span>
        </div>

        <!-- Title & Company (Fixed height slots to prevent uneven vertical rhythm) -->
        <div class="flex-1 min-w-0 pr-1">
          <h3 
            ref="titleRef"
            class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-blue-700 transition-colors line-clamp-2 leading-snug h-9 sm:h-10 flex items-start"
            :title="job.title">
            {{ job.title }}
          </h3>

          <p 
            class="text-[11px] sm:text-xs text-slate-500 font-medium line-clamp-1 mt-0.5 h-4 sm:h-4.5 flex items-center"
            :title="job.company">
            <span class="truncate">{{ job.company }}</span>
          </p>
        </div>
      </div>

      <!-- 2. Metadata Bar: Salary & Location (Fixed height row for horizontal alignment) -->
      <div class="mt-3 flex items-center justify-between gap-2 text-xs h-5">
        <!-- Salary -->
        <span class="font-bold text-emerald-600 flex items-center gap-1.5 text-xs sm:text-[13px] truncate">
          <i class="fa-solid fa-money-bill-wave text-emerald-500 text-[11px] flex-shrink-0"></i>
          <span class="truncate">{{ formatSalary(job.salary) }}</span>
        </span>

        <!-- Location (Ward) -->
        <div class="text-slate-500 font-medium flex items-center gap-1 text-[11px] sm:text-xs flex-shrink-0 max-w-[50%] truncate" :title="job.address">
          <i class="fa-solid fa-location-dot text-slate-400 text-[10px] sm:text-[11px] flex-shrink-0"></i>
          <span class="truncate">{{ job.ward_name }}</span>
        </div>
      </div>

      <!-- 3. Training Institutions Count Badge (Quick view opens after a delayed card hover) -->
      <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center h-9">
        <div 
          v-if="job.training_schools && job.training_schools.length"
          class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg bg-indigo-50/80 border border-indigo-100/80 text-indigo-700 text-[11px] font-medium h-6">
          <i class="fa-solid fa-graduation-cap text-xs text-indigo-600"></i>
          <span><strong>{{ job.training_schools.length }}</strong> cơ sở GDNN đào tạo</span>
        </div>

        <div v-else class="text-slate-400 text-[10.5px] italic h-6 flex items-center">
          Doanh nghiệp tự đào tạo
        </div>

      </div>
    </div>

    <!-- 4. Bottom Row: Remaining time / Apply CTA -->
    <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400 h-7">
      <span class="flex items-center gap-1.5 text-slate-500">
        <i class="fa-regular fa-clock text-[10px] text-slate-400"></i>
        <span>Còn {{ getDaysLeft(job.deadline) }} ngày</span>
      </span>

      <span class="text-blue-600 hover:text-blue-800 font-bold group-hover:translate-x-0.5 transition-all flex items-center gap-1 flex-shrink-0 text-xs">
        <span>Ứng tuyển</span>
        <i class="fa-solid fa-angle-right text-[10px]"></i>
      </span>
    </div>

  </div>
</template>

<script setup>
import { onBeforeUnmount, ref } from 'vue'

const props = defineProps({
  job: {
    type: Object,
    required: true
  },
  previewActive: {
    type: Boolean,
    default: false,
  }
})

const emit = defineEmits(['select', 'quick-view', 'view-school'])
const cardRef = ref(null)
const titleRef = ref(null)

const QUICK_VIEW_DELAY_MS = 1200
let quickViewTimer = null

function canUseHoverPreview() {
  return typeof window !== 'undefined'
    && window.matchMedia('(hover: hover) and (pointer: fine)').matches
}

function scheduleQuickView() {
  cancelQuickView()
  if (!canUseHoverPreview()) return

  quickViewTimer = window.setTimeout(() => {
    const cardRect = cardRef.value?.getBoundingClientRect()
    const titleRect = titleRef.value?.getBoundingClientRect()

    emit('quick-view', {
      job: props.job,
      anchorRect: serializeRect(cardRect),
      titleRect: serializeRect(titleRect),
    })
    quickViewTimer = null
  }, QUICK_VIEW_DELAY_MS)
}

function serializeRect(rect) {
  if (!rect) return null

  return {
    top: rect.top,
    right: rect.right,
    bottom: rect.bottom,
    left: rect.left,
    width: rect.width,
    height: rect.height,
  }
}

function cancelQuickView() {
  if (quickViewTimer === null) return
  window.clearTimeout(quickViewTimer)
  quickViewTimer = null
}

function handleSelect() {
  cancelQuickView()
  emit('select', props.job)
}

onBeforeUnmount(cancelQuickView)




function getLogoTextClass(logo) {
  const str = String(logo || '').trim()
  if (str.length <= 3) return 'text-xs tracking-normal'
  if (str.length <= 5) return 'text-[10px] tracking-tight'
  if (str.length <= 7) return 'text-[8px] tracking-tighter'
  return 'text-[7px] tracking-tighter'
}

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
