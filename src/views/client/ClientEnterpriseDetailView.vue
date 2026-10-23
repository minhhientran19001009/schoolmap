<template>
  <div class="h-full overflow-y-auto bg-slate-50 selection:bg-blue-100 selection:text-blue-900">
    <div class="border-b border-slate-200 bg-white px-4 py-3 sm:px-6">
      <div class="mx-auto flex max-w-6xl items-center justify-between gap-3 text-xs">
        <nav class="flex min-w-0 items-center gap-1.5 text-slate-500">
          <router-link to="/danh-sach-doanh-nghiep" class="shrink-0 transition hover:text-blue-600">
            Doanh nghiệp
          </router-link>
          <i class="fa-solid fa-chevron-right text-[8px] text-slate-300"></i>
          <span class="truncate font-semibold text-slate-700">{{ enterprise?.name || 'Chi tiết doanh nghiệp' }}</span>
        </nav>

        <router-link
          to="/danh-sach-doanh-nghiep"
          class="inline-flex shrink-0 items-center gap-1.5 rounded-lg bg-slate-100 px-3 py-1.5 font-bold text-slate-700 transition hover:bg-slate-200"
        >
          <i class="fa-solid fa-arrow-left text-[10px]"></i>
          <span class="hidden sm:inline">Quay lại danh sách</span>
          <span class="sm:hidden">Quay lại</span>
        </router-link>
      </div>
    </div>

    <main class="mx-auto w-full max-w-6xl px-4 py-5 sm:px-6 sm:py-7">
      <div v-if="!enterprise" class="rounded-2xl border border-slate-200 bg-white px-5 py-12 text-center shadow-2xs">
        <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-rose-50 text-xl text-rose-500">
          <i class="fa-solid fa-building-circle-xmark"></i>
        </span>
        <h1 class="mt-4 text-lg font-black text-slate-900">Không tìm thấy doanh nghiệp</h1>
        <p class="mt-1 text-xs text-slate-500">Doanh nghiệp này có thể không còn tồn tại trong danh mục hiện tại.</p>
        <router-link
          to="/danh-sach-doanh-nghiep"
          class="mt-5 inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-blue-700"
        >
          Xem danh sách doanh nghiệp
        </router-link>
      </div>

      <template v-else>
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-2xs sm:p-6">
          <div class="flex flex-col gap-5 sm:flex-row sm:items-center">
            <div class="flex h-20 w-24 shrink-0 items-center justify-center overflow-hidden rounded-2xl border border-slate-100 bg-white p-2 shadow-2xs sm:h-24 sm:w-28">
              <img
                v-if="enterprise.logo_url || enterprise.logo_image"
                :src="enterprise.logo_url || enterprise.logo_image"
                :alt="`Logo ${enterprise.name}`"
                class="max-h-full max-w-full object-contain"
              />
              <span
                v-else
                :class="enterprise.color || 'bg-blue-700'"
                class="flex h-14 w-20 items-center justify-center rounded-xl px-1 text-center text-xs font-black uppercase tracking-tight text-white shadow-2xs"
              >
                {{ enterprise.logo || 'DN' }}
              </span>
            </div>

            <div class="min-w-0 flex-1">
              <h1 class="text-xl font-black leading-snug text-slate-900 sm:text-2xl lg:text-3xl">
                {{ enterprise.name }}
              </h1>
              <p v-if="enterprise.short_name" class="mt-1 text-xs font-medium text-slate-500 sm:text-sm">
                Tên giao dịch: <strong class="text-slate-700">{{ enterprise.short_name }}</strong>
              </p>

              <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-2 text-xs text-slate-500">
                <span class="inline-flex items-center gap-1.5">
                  <i class="fa-solid fa-location-dot text-rose-500"></i>
                  {{ enterprise.region_name || 'Tỉnh Ninh Bình' }}
                </span>
                <span class="inline-flex items-center gap-1.5 font-bold text-purple-600">
                  <i class="fa-solid fa-briefcase"></i>
                  {{ enterprise.jobs_count || 0 }} vị trí đang tuyển
                </span>
                <a
                  v-if="enterprise.website"
                  :href="enterprise.website"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="inline-flex items-center gap-1.5 font-bold text-blue-600 hover:underline"
                >
                  <i class="fa-solid fa-globe"></i>
                  Trang web công ty
                  <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>
                </a>
              </div>
            </div>

            <router-link
              v-if="enterprise.jobs_count > 0"
              :to="{ path: '/tuyen-dung', query: { search: enterprise.short_name || enterprise.name } }"
              class="inline-flex h-11 shrink-0 items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 text-xs font-bold text-white shadow-xs transition hover:bg-blue-700"
            >
              Xem việc làm
              <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </router-link>
          </div>
        </section>

        <div class="mt-5 grid grid-cols-1 items-start gap-5 lg:grid-cols-[minmax(0,1fr)_320px]">
          <div class="min-w-0 space-y-5">
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xs">
              <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-4 py-3.5 sm:px-5">
                <h2 class="flex items-center gap-2 text-sm font-extrabold text-slate-900 sm:text-base">
                  <i class="fa-solid fa-briefcase text-purple-600"></i>
                  Vị trí đang tuyển
                </h2>
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
                    <h3 class="line-clamp-2 text-sm font-bold leading-snug text-slate-900 transition group-hover:text-blue-700">
                      {{ job.title }}
                    </h3>
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
                      <span v-if="job.recruitment_count" class="inline-flex items-center gap-1.5">
                        <i class="fa-solid fa-users text-slate-400"></i>
                        {{ formatNumber(job.recruitment_count) }} người
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
              <h2 class="text-sm font-extrabold text-slate-900 sm:text-base">Giới thiệu doanh nghiệp</h2>
              <p v-if="enterprise.description" class="mt-3 whitespace-pre-line text-xs leading-6 text-slate-600 sm:text-[13px]">
                {{ enterprise.description }}
              </p>
              <p v-else class="mt-3 text-xs text-slate-400">Thông tin giới thiệu đang được cập nhật.</p>
            </section>
          </div>

          <aside class="rounded-2xl border border-slate-200 bg-white p-4 shadow-2xs lg:sticky lg:top-5 sm:p-5">
            <h2 class="border-b border-slate-100 pb-3 text-sm font-extrabold text-slate-900 sm:text-base">
              Thông tin doanh nghiệp
            </h2>

            <dl class="divide-y divide-slate-100 text-xs">
              <div class="py-3.5">
                <dt class="flex items-center gap-2 font-semibold text-slate-400"><i class="fa-solid fa-location-dot w-4 text-center text-rose-500"></i>Địa chỉ</dt>
                <dd class="mt-1.5 pl-6 font-semibold leading-relaxed text-slate-700">{{ enterprise.address || 'Đang cập nhật' }}</dd>
              </div>
              <div class="py-3.5">
                <dt class="flex items-center gap-2 font-semibold text-slate-400"><i class="fa-solid fa-users w-4 text-center text-emerald-600"></i>Quy mô</dt>
                <dd class="mt-1.5 pl-6 font-bold text-slate-700">{{ workforceLabel(enterprise.employee_count) }}</dd>
              </div>
              <div class="py-3.5">
                <dt class="flex items-center gap-2 font-semibold text-slate-400"><i class="fa-solid fa-layer-group w-4 text-center text-blue-600"></i>Lĩnh vực</dt>
                <dd class="mt-1.5 pl-6 font-bold leading-relaxed text-slate-700">{{ enterprise.industry_name || 'Đang cập nhật' }}</dd>
              </div>
              <div class="py-3.5">
                <dt class="flex items-center gap-2 font-semibold text-slate-400"><i class="fa-solid fa-receipt w-4 text-center text-slate-500"></i>Mã số thuế</dt>
                <dd class="mt-1.5 pl-6 font-mono font-bold text-slate-700">{{ enterprise.tax_code || 'Đang cập nhật' }}</dd>
              </div>
              <div v-if="enterprise.phone" class="py-3.5">
                <dt class="flex items-center gap-2 font-semibold text-slate-400"><i class="fa-solid fa-phone w-4 text-center text-blue-600"></i>Điện thoại</dt>
                <dd class="mt-1.5 pl-6"><a :href="`tel:${enterprise.phone}`" class="font-bold text-blue-600 hover:underline">{{ enterprise.phone }}</a></dd>
              </div>
              <div v-if="enterprise.email" class="py-3.5">
                <dt class="flex items-center gap-2 font-semibold text-slate-400"><i class="fa-solid fa-envelope w-4 text-center text-blue-600"></i>Email</dt>
                <dd class="mt-1.5 min-w-0 pl-6"><a :href="`mailto:${enterprise.email}`" class="break-all font-bold text-blue-600 hover:underline">{{ enterprise.email }}</a></dd>
              </div>
            </dl>
          </aside>
        </div>
      </template>
    </main>

    <ClientFooter class="mt-auto" />
  </div>
</template>

<script setup>
import { computed, watch } from 'vue'
import { useRoute } from 'vue-router'
import { enterpriseService } from '../../services/enterpriseService'

const route = useRoute()

const enterprise = computed(() => enterpriseService.getById(route.params.id))

watch(enterprise, (value) => {
  document.title = value
    ? `${value.name} — GDNN Ninh Bình`
    : 'Không tìm thấy doanh nghiệp — GDNN Ninh Bình'
}, { immediate: true })

function formatNumber(value) {
  return new Intl.NumberFormat('vi-VN').format(Number(value || 0))
}

function workforceLabel(value) {
  const count = Number(value || 0)
  if (!count) return 'Đang cập nhật'
  return `${formatNumber(count)} nhân viên`
}
</script>
