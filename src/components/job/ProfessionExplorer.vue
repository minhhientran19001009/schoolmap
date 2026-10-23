<template>
  <section class="space-y-4 sm:space-y-5">
    <div v-if="error" class="flex flex-col gap-3 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-xs text-amber-900 sm:flex-row sm:items-center sm:justify-between">
      <p><i class="fa-solid fa-triangle-exclamation mr-1.5 text-amber-600"></i>Chưa tải được số liệu cung – cầu; danh mục lĩnh vực và việc làm vẫn được hiển thị.</p>
      <button type="button" class="shrink-0 rounded-lg bg-amber-600 px-3 py-1.5 font-bold text-white transition hover:bg-amber-700" @click="$emit('reload')">Tải lại số liệu</button>
    </div>

    <div v-if="loading" class="flex min-h-72 flex-col items-center justify-center rounded-2xl border border-slate-200 bg-white text-center shadow-2xs">
      <i class="fa-solid fa-spinner fa-spin text-2xl text-blue-600"></i>
      <p class="mt-3 text-sm font-semibold text-slate-600">Đang tổng hợp các lĩnh vực nghề nghiệp...</p>
    </div>

    <div v-else-if="professions.length" class="grid grid-cols-1 items-stretch gap-4 lg:grid-cols-2 xl:grid-cols-3">
      <article v-for="profession in professions" :key="profession.id" class="flex h-full flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white p-4 shadow-2xs transition hover:border-blue-200 hover:shadow-sm sm:p-5">
        <div class="flex min-h-[64px] items-start gap-3">
          <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-700">
            <i :class="profession.icon || 'fa-solid fa-briefcase'"></i>
          </span>
          <div class="min-w-0 flex-1">
            <h3 class="text-base font-black leading-snug text-slate-900 sm:text-lg">{{ profession.name }}</h3>
            <p class="mt-1 line-clamp-2 text-xs leading-relaxed text-slate-500">{{ profession.description }}</p>
          </div>
        </div>

        <div class="mt-4 grid grid-cols-2 gap-2.5">
          <div class="rounded-xl border border-blue-100 bg-blue-50/70 px-3 py-2.5">
            <p class="text-xl font-black leading-none text-blue-700">{{ formatNumber(profession.demand) }}</p>
            <p class="mt-1.5 text-[10px] font-bold text-slate-500">Người cần tuyển</p>
          </div>
          <div class="rounded-xl border border-emerald-100 bg-emerald-50/70 px-3 py-2.5">
            <p class="text-xl font-black leading-none text-emerald-700">{{ formatNumber(profession.job_count) }}</p>
            <p class="mt-1.5 text-[10px] font-bold text-slate-500">Việc làm đang tuyển</p>
          </div>
        </div>

        <div class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-2">
          <div class="min-w-0 rounded-xl border border-slate-200 bg-slate-50/70 px-3 py-2.5">
            <div class="min-w-0">
              <p class="text-xl font-black leading-none text-emerald-700">{{ formatNumber(profession.school_count) }}</p>
              <p class="mt-1.5 truncate text-[10px] font-bold text-slate-500">Cơ sở đào tạo</p>
            </div>
          </div>

          <div class="min-w-0 rounded-xl border border-slate-200 bg-slate-50/70 px-3 py-2.5">
            <div class="min-w-0">
              <p class="text-xl font-black leading-none text-blue-700">{{ formatNumber(profession.recruiters?.length || 0) }}</p>
              <p class="mt-1.5 truncate text-[10px] font-bold text-slate-500">Doanh nghiệp tuyển dụng</p>
            </div>
          </div>
        </div>

        <div class="mt-auto flex justify-end pt-4">
          <button type="button" class="flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-xs font-bold text-white shadow-2xs transition hover:bg-blue-700 active:scale-[0.98] sm:w-auto" @click="$emit('view-jobs', profession)">
            Xem việc làm
            <i class="fa-solid fa-arrow-right text-[10px]"></i>
          </button>
        </div>
      </article>
    </div>

    <div v-else class="flex min-h-72 flex-col items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 text-center shadow-2xs">
      <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-blue-600"><i class="fa-solid fa-briefcase text-xl"></i></span>
      <h3 class="mt-4 text-sm font-bold text-slate-800">Không tìm thấy lĩnh vực phù hợp</h3>
      <p class="mt-1 max-w-md text-xs leading-relaxed text-slate-500">Hãy thay đổi từ khóa hoặc chọn lại lĩnh vực nghề nghiệp.</p>
    </div>
  </section>
</template>

<script setup>
defineProps({
  professions: { type: Array, default: () => [] },
  period: { type: Object, default: null },
  loading: { type: Boolean, default: false },
  error: { type: String, default: '' },
})

defineEmits(['view-school', 'view-jobs', 'reload'])

function formatNumber(value) {
  return new Intl.NumberFormat('vi-VN').format(Number(value || 0))
}
</script>
