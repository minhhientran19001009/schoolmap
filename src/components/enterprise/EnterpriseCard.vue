<template>
  <article
    role="button"
    tabindex="0"
    class="group flex min-h-[142px] cursor-pointer items-center gap-4 rounded-2xl border border-slate-200/80 bg-white px-5 py-5 shadow-2xs transition-all duration-200 hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-blue-100 sm:px-6"
    @click="$emit('select', enterprise)"
    @keydown.enter="$emit('select', enterprise)"
    @keydown.space.prevent="$emit('select', enterprise)"
  >
    <div class="flex h-16 w-20 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-white p-1.5">
      <img
        v-if="enterprise.logo_url || enterprise.logo_image"
        :src="enterprise.logo_url || enterprise.logo_image"
        :alt="`Logo ${enterprise.name}`"
        class="max-h-full max-w-full object-contain"
      />
      <span
        v-else
        :class="enterprise.color || 'bg-blue-700'"
        class="flex h-12 w-16 items-center justify-center rounded-xl px-1 text-center text-[11px] font-black uppercase tracking-tight text-white shadow-2xs"
      >
        {{ enterprise.logo || 'DN' }}
      </span>
    </div>

    <div class="min-w-0 flex-1">
      <h3
        class="line-clamp-2 text-base font-extrabold leading-snug text-slate-900 transition-colors group-hover:text-blue-700 sm:text-[17px]"
        :title="enterprise.name"
      >
        {{ enterprise.name }}
      </h3>

      <div class="mt-2.5 space-y-2 text-xs">
        <div
          v-if="enterprise.jobs_count > 0"
          class="flex w-fit items-center gap-1.5 rounded-lg bg-purple-50 px-2.5 py-1 font-bold text-purple-600"
        >
          <i class="fa-solid fa-briefcase text-[11px]"></i>
          <span>{{ enterprise.jobs_count }} vị trí đang tuyển</span>
        </div>
        <div v-else class="flex items-center gap-1.5 font-medium text-slate-400">
          <i class="fa-solid fa-briefcase text-[11px]"></i>
          <span>Chưa có vị trí đang tuyển</span>
        </div>

        <div class="flex items-center gap-1.5 font-semibold text-slate-700">
          <i class="fa-solid fa-medal text-[12px] text-orange-500"></i>
          <span>{{ workforceLabel(enterprise.employee_count) }}</span>
        </div>
      </div>
    </div>
  </article>
</template>

<script setup>
defineProps({
  enterprise: {
    type: Object,
    required: true,
  },
})

defineEmits(['select'])

function workforceLabel(value) {
  const count = Number(value || 0)
  if (!count) return 'Chưa cập nhật quy mô'

  const thresholds = [10000, 5000, 1000, 500, 300]
  const threshold = thresholds.find((item) => count >= item)
  if (threshold) return `Trên ${new Intl.NumberFormat('vi-VN').format(threshold)} nhân viên`
  return `${new Intl.NumberFormat('vi-VN').format(count)} nhân viên`
}
</script>
