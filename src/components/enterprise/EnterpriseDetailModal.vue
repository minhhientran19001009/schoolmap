<template>
  <div
    v-if="isOpen && enterprise"
    class="fixed inset-0 z-[4000] flex items-center justify-center p-3 sm:p-6"
  >
    <button
      type="button"
      class="absolute inset-0 bg-slate-950/55 backdrop-blur-[2px]"
      aria-label="Đóng thông tin doanh nghiệp"
      @click="$emit('close')"
    ></button>

    <section
      class="relative flex max-h-[calc(100dvh-1.5rem)] w-full max-w-6xl flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl sm:max-h-[calc(100dvh-3rem)]"
      role="dialog"
      aria-modal="true"
      :aria-label="`Thông tin ${enterprise.name}`"
    >
      <header class="flex shrink-0 items-start justify-between gap-4 border-b border-slate-100 bg-white px-5 py-4 sm:px-7 sm:py-5">
        <div class="flex min-w-0 items-start gap-4">
          <div class="flex h-16 w-20 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-slate-100 bg-white p-1.5 shadow-2xs sm:h-20 sm:w-24">
            <img
              v-if="enterprise.logo_url || enterprise.logo_image"
              :src="enterprise.logo_url || enterprise.logo_image"
              :alt="`Logo ${enterprise.name}`"
              class="max-h-full max-w-full object-contain"
            />
            <span
              v-else
              :class="enterprise.color || 'bg-blue-700'"
              class="flex h-12 w-16 items-center justify-center rounded-xl px-1 text-center text-xs font-black uppercase tracking-tight text-white shadow-2xs sm:h-14 sm:w-20"
            >
              {{ enterprise.logo || 'DN' }}
            </span>
          </div>

          <div class="min-w-0 pt-0.5">
            <h2 class="text-lg font-black leading-snug text-slate-900 sm:text-2xl">
              {{ enterprise.name }}
            </h2>
            <p v-if="enterprise.short_name" class="mt-1 text-xs font-medium text-slate-500 sm:text-sm">
              Tên giao dịch: <strong class="text-slate-700">{{ enterprise.short_name }}</strong>
            </p>
            <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1.5 text-xs text-slate-500">
              <span class="inline-flex min-w-0 items-center gap-1.5">
                <i class="fa-solid fa-location-dot shrink-0 text-rose-500"></i>
                <span class="truncate">{{ enterprise.region_name || 'Tỉnh Ninh Bình' }}</span>
              </span>
              <a
                v-if="enterprise.website"
                :href="enterprise.website"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center gap-1.5 font-bold text-blue-600 hover:text-blue-700 hover:underline"
              >
                <i class="fa-solid fa-globe"></i>
                Trang web công ty
                <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>
              </a>
            </div>
          </div>
        </div>

        <button
          type="button"
          class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-100 text-slate-500 transition hover:bg-slate-200 hover:text-slate-800"
          title="Đóng"
          @click="$emit('close')"
        >
          <i class="fa-solid fa-xmark"></i>
        </button>
      </header>

      <div class="min-h-0 flex-1 overflow-y-auto bg-slate-50/80 px-4 py-5 sm:px-7 sm:py-6">
        <div class="grid grid-cols-1 items-start gap-5 lg:grid-cols-[minmax(0,1fr)_320px]">
          <div class="min-w-0 space-y-5">
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xs">
              <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-4 py-3.5 sm:px-5">
                <h3 class="flex items-center gap-2 text-sm font-extrabold text-slate-900 sm:text-base">
                  <i class="fa-solid fa-briefcase text-purple-600"></i>
                  Vị trí đang tuyển
                </h3>
                <span class="rounded-lg bg-purple-50 px-2.5 py-1 text-[11px] font-bold text-purple-600">
                  {{ enterprise.jobs?.length || 0 }} vị trí
                </span>
              </div>

              <div v-if="enterprise.jobs?.length" class="divide-y divide-slate-100">
                <router-link
                  v-for="job in enterprise.jobs"
                  :key="job.id"
                  :to="`/tuyen-dung/${job.id}`"
                  class="group flex items-start justify-between gap-3 px-4 py-4 transition hover:bg-blue-50/40 sm:px-5"
                >
                  <div class="min-w-0 flex-1">
                    <h4 class="line-clamp-2 text-sm font-bold leading-snug text-slate-900 transition group-hover:text-blue-700">
                      {{ job.title }}
                    </h4>
                    <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1.5 text-[11px] text-slate-500 sm:text-xs">
                      <span class="inline-flex items-center gap-1.5 font-bold text-emerald-600">
                        <i class="fa-solid fa-money-bill-wave"></i>
                        {{ job.salary || 'Thỏa thuận' }}
                      </span>
                      <span class="inline-flex items-center gap-1.5">
                        <i class="fa-solid fa-location-dot text-slate-400"></i>
                        {{ job.ward_name || job.address || 'Ninh Bình' }}
                      </span>
                      <span v-if="job.deadline" class="inline-flex items-center gap-1.5">
                        <i class="fa-regular fa-clock text-slate-400"></i>
                        Hạn {{ job.deadline }}
                      </span>
                    </div>
                  </div>
                  <span class="mt-0.5 inline-flex shrink-0 items-center gap-1 text-xs font-bold text-blue-600">
                    <span class="hidden sm:inline">Xem chi tiết</span>
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                  </span>
                </router-link>
              </div>

              <div v-else class="px-5 py-10 text-center">
                <span class="mx-auto flex h-11 w-11 items-center justify-center rounded-xl bg-slate-100 text-slate-400">
                  <i class="fa-solid fa-briefcase"></i>
                </span>
                <p class="mt-3 text-sm font-bold text-slate-700">Chưa có vị trí đang tuyển</p>
                <p class="mt-1 text-xs text-slate-500">Doanh nghiệp chưa đăng tin tuyển dụng trên hệ thống.</p>
              </div>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-2xs sm:p-5">
              <h3 class="text-sm font-extrabold text-slate-900 sm:text-base">Giới thiệu doanh nghiệp</h3>
              <p v-if="enterprise.description" class="mt-3 whitespace-pre-line text-xs leading-6 text-slate-600 sm:text-[13px]">
                {{ enterprise.description }}
              </p>
              <p v-else class="mt-3 text-xs text-slate-400">Thông tin giới thiệu đang được cập nhật.</p>
            </section>
          </div>

          <aside class="rounded-2xl border border-slate-200 bg-white p-4 shadow-2xs lg:sticky lg:top-0 sm:p-5">
            <h3 class="border-b border-slate-100 pb-3 text-sm font-extrabold text-slate-900 sm:text-base">
              Thông tin doanh nghiệp
            </h3>

            <dl class="divide-y divide-slate-100 text-xs">
              <div class="py-3.5">
                <dt class="flex items-center gap-2 font-semibold text-slate-400">
                  <i class="fa-solid fa-location-dot w-4 text-center text-rose-500"></i>
                  Địa chỉ
                </dt>
                <dd class="mt-1.5 pl-6 font-semibold leading-relaxed text-slate-700">{{ enterprise.address || 'Đang cập nhật' }}</dd>
              </div>

              <div class="py-3.5">
                <dt class="flex items-center gap-2 font-semibold text-slate-400">
                  <i class="fa-solid fa-users w-4 text-center text-emerald-600"></i>
                  Quy mô
                </dt>
                <dd class="mt-1.5 pl-6 font-bold text-slate-700">{{ workforceLabel(enterprise.employee_count) }}</dd>
              </div>

              <div class="py-3.5">
                <dt class="flex items-center gap-2 font-semibold text-slate-400">
                  <i class="fa-solid fa-layer-group w-4 text-center text-blue-600"></i>
                  Lĩnh vực
                </dt>
                <dd class="mt-1.5 pl-6 font-bold leading-relaxed text-slate-700">{{ enterprise.industry_name || 'Đang cập nhật' }}</dd>
              </div>

              <div class="py-3.5">
                <dt class="flex items-center gap-2 font-semibold text-slate-400">
                  <i class="fa-solid fa-receipt w-4 text-center text-slate-500"></i>
                  Mã số thuế
                </dt>
                <dd class="mt-1.5 pl-6 font-mono font-bold text-slate-700">{{ enterprise.tax_code || 'Đang cập nhật' }}</dd>
              </div>

              <div v-if="enterprise.phone" class="py-3.5">
                <dt class="flex items-center gap-2 font-semibold text-slate-400">
                  <i class="fa-solid fa-phone w-4 text-center text-blue-600"></i>
                  Điện thoại
                </dt>
                <dd class="mt-1.5 pl-6"><a :href="`tel:${enterprise.phone}`" class="font-bold text-blue-600 hover:underline">{{ enterprise.phone }}</a></dd>
              </div>

              <div v-if="enterprise.email" class="py-3.5">
                <dt class="flex items-center gap-2 font-semibold text-slate-400">
                  <i class="fa-solid fa-envelope w-4 text-center text-blue-600"></i>
                  Email
                </dt>
                <dd class="mt-1.5 min-w-0 pl-6"><a :href="`mailto:${enterprise.email}`" class="break-all font-bold text-blue-600 hover:underline">{{ enterprise.email }}</a></dd>
              </div>
            </dl>
          </aside>
        </div>
      </div>

      <footer class="flex shrink-0 items-center justify-between gap-3 border-t border-slate-100 bg-white px-5 py-3.5 sm:px-7">
        <button
          type="button"
          class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-700 transition hover:bg-slate-100"
          @click="$emit('close')"
        >
          Đóng
        </button>

        <router-link
          v-if="enterprise.jobs_count > 0"
          :to="{ path: '/tuyen-dung', query: { search: enterprise.short_name || enterprise.name } }"
          class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2 text-xs font-bold text-white shadow-xs transition hover:bg-blue-700"
        >
          Xem tất cả {{ enterprise.jobs_count }} vị trí
          <i class="fa-solid fa-arrow-right text-[10px]"></i>
        </router-link>
      </footer>
    </section>
  </div>
</template>

<script setup>
defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
  enterprise: {
    type: Object,
    default: null,
  },
})

defineEmits(['close'])

function workforceLabel(value) {
  const count = Number(value || 0)
  if (!count) return 'Đang cập nhật'
  return `${new Intl.NumberFormat('vi-VN').format(count)} nhân viên`
}
</script>
