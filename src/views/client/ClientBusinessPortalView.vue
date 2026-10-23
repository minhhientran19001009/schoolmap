<template>
  <div class="h-full overflow-y-auto bg-slate-50 selection:bg-purple-100 selection:text-purple-900">
    <section class="relative overflow-hidden border-b border-slate-200/90 bg-gradient-to-br from-purple-50 via-violet-50/60 to-slate-50">
      <div class="absolute inset-0 opacity-35 bg-[radial-gradient(#c4b5fd_1px,transparent_1px)] [background-size:22px_22px]"></div>
      <div class="relative mx-auto max-w-7xl px-4 py-7 sm:px-6 sm:py-9 lg:px-8">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
          <div class="max-w-3xl">
            <span class="inline-flex items-center gap-2 rounded-full border border-purple-200 bg-white/90 px-3 py-1 text-xs font-bold text-purple-700 shadow-2xs backdrop-blur-sm">
              <i class="fa-solid fa-building-circle-check"></i>
              Đang sử dụng vai trò Doanh nghiệp
            </span>
            <h1 class="mt-4 text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl lg:text-4xl">Không gian Doanh nghiệp</h1>
            <p class="mt-2 max-w-2xl text-sm leading-relaxed text-slate-600 sm:text-base">
              Khai báo nhu cầu nhân lực theo ngành/nghề, trình độ và kỳ báo cáo để kết nối với các cơ sở GDNN phù hợp.
            </p>
            <p v-if="activeEmployer" class="mt-3 inline-flex max-w-full items-center gap-2 rounded-xl border border-purple-200 bg-white px-3 py-2 text-sm font-bold text-slate-700 shadow-2xs">
              <i class="fa-solid fa-building shrink-0 text-purple-600"></i>
              <span class="truncate">{{ activeEmployer.name }}</span>
              <span v-if="activeEmployer.tax_code" class="shrink-0 text-xs font-semibold text-purple-700">MST: {{ activeEmployer.tax_code }}</span>
            </p>
          </div>

          <div class="flex flex-wrap gap-2.5">
            <button type="button" @click="openDemandModal" class="inline-flex items-center gap-2 rounded-xl bg-purple-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-purple-700 active:scale-[0.98]">
              <i class="fa-solid fa-plus"></i>
              Khai báo nhu cầu
            </button>
            <router-link to="/tuyen-dung" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 shadow-2xs transition hover:border-purple-200 hover:bg-purple-50 hover:text-purple-700">
              <i class="fa-solid fa-briefcase"></i>
              Xem việc làm GDNN
            </router-link>
          </div>
        </div>
      </div>
    </section>

    <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
      <section class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        <article v-for="stat in stats" :key="stat.label" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-2xs">
          <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
              <p class="text-xs font-semibold text-slate-500">{{ stat.label }}</p>
              <p class="mt-2 truncate text-xl font-black text-slate-900 sm:text-2xl">{{ stat.value }}</p>
              <p class="mt-0.5 truncate text-[11px] text-slate-400">{{ stat.unit }}</p>
            </div>
            <span :class="stat.iconClass" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl">
              <i :class="stat.icon" class="text-sm"></i>
            </span>
          </div>
        </article>
      </section>

      <div class="mt-5 grid grid-cols-1 gap-5 lg:grid-cols-[280px_minmax(0,1fr)]">
        <aside class="space-y-4">
          <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-2xs">
            <h2 class="text-sm font-bold text-slate-900">Chức năng Doanh nghiệp</h2>
            <div class="mt-3 space-y-1.5">
              <div v-for="item in businessFunctions" :key="item.label" :class="item.active ? 'border-purple-200 bg-purple-50 text-purple-800' : 'border-transparent text-slate-600'" class="flex items-center gap-2.5 rounded-xl border px-3 py-2.5 text-xs font-semibold">
                <i :class="item.icon" class="w-4 text-center"></i>
                <span>{{ item.label }}</span>
              </div>
            </div>
          </section>

          <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-2xs">
            <h2 class="text-sm font-bold text-slate-900">Nguyên tắc khai báo</h2>
            <ul class="mt-3 space-y-2.5 text-xs leading-relaxed text-slate-600">
              <li class="flex gap-2"><i class="fa-solid fa-check mt-0.5 text-purple-600"></i><span>Mỗi nhu cầu được xác định theo ngành, trình độ và kỳ.</span></li>
              <li class="flex gap-2"><i class="fa-solid fa-check mt-0.5 text-purple-600"></i><span>Số lượng là nhân lực doanh nghiệp thực tế cần.</span></li>
              <li class="flex gap-2"><i class="fa-solid fa-check mt-0.5 text-purple-600"></i><span>Kỹ năng được dùng để đối chiếu với năng lực đào tạo.</span></li>
            </ul>
          </section>
        </aside>

        <section class="rounded-2xl border border-slate-200 bg-white shadow-2xs">
          <div class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
              <h2 class="flex items-center gap-2 text-base font-bold text-slate-900">
                <i class="fa-solid fa-users text-purple-600"></i>
                Nhu cầu nhân lực
              </h2>
              <p class="mt-1 text-xs text-slate-500">Dữ liệu đã lưu của doanh nghiệp đang được chọn.</p>
            </div>
            <button type="button" @click="openDemandModal" class="inline-flex items-center justify-center gap-2 rounded-xl bg-purple-600 px-4 py-2.5 text-xs font-bold text-white shadow-xs transition hover:bg-purple-700">
              <i class="fa-solid fa-plus"></i>
              Thêm nhu cầu
            </button>
          </div>

          <p v-if="catalogError" class="m-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-xs font-medium text-amber-800">{{ catalogError }}</p>

          <div v-if="demandRows.length" class="divide-y divide-slate-100">
            <article v-for="row in demandRows" :key="row.id" class="p-4 sm:p-5">
              <div class="flex flex-col gap-3 xl:flex-row xl:items-start xl:justify-between">
                <div class="min-w-0">
                  <div class="flex flex-wrap items-center gap-2">
                    <h3 class="font-bold text-slate-900">{{ row.major }}</h3>
                    <span class="rounded-md bg-blue-50 px-2 py-0.5 text-[11px] font-bold text-blue-700">{{ row.level }}</span>
                    <span class="rounded-md bg-purple-50 px-2 py-0.5 text-[11px] font-bold text-purple-700">{{ purposeLabel(row.purpose) }}</span>
                  </div>
                  <p class="mt-1 text-xs font-medium text-slate-600">{{ row.employer }}</p>
                  <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-500">
                    <span><i class="fa-regular fa-calendar mr-1 text-slate-400"></i>{{ row.period }}</span>
                    <span><i class="fa-solid fa-location-dot mr-1 text-rose-500"></i>{{ row.location }}</span>
                    <span v-if="row.salary"><i class="fa-solid fa-money-bill-wave mr-1 text-emerald-500"></i>{{ row.salary }}</span>
                    <span v-if="row.age_range"><i class="fa-solid fa-user-clock mr-1 text-slate-400"></i>{{ row.age_range }}</span>
                  </div>
                  <div class="mt-2 flex flex-wrap gap-1.5">
                    <span v-for="skill in row.skills" :key="skill" class="rounded-lg border border-slate-200 bg-slate-50 px-2 py-1 text-[11px] font-medium text-slate-600">{{ skill }}</span>
                  </div>
                </div>
                <div class="flex shrink-0 items-baseline gap-1 rounded-xl border border-purple-100 bg-purple-50 px-4 py-3 text-purple-800">
                  <strong class="text-2xl font-black">{{ formatNumber(row.quantity) }}</strong>
                  <span class="text-xs font-semibold">người / kỳ</span>
                </div>
              </div>
            </article>
          </div>

          <div v-else class="flex flex-col items-center px-5 py-12 text-center">
            <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-purple-50 text-purple-600"><i class="fa-solid fa-users text-xl"></i></span>
            <h3 class="mt-4 text-sm font-bold text-slate-800">Chưa có nhu cầu nhân lực nào được khai báo</h3>
            <p class="mt-1 max-w-md text-xs leading-relaxed text-slate-500">Chọn “Khai báo nhu cầu” để bổ sung dữ liệu cho doanh nghiệp này.</p>
          </div>
        </section>
      </div>
    </main>

    <div v-if="isDemandModalOpen" class="fixed inset-0 z-[4000] flex items-center justify-center p-3 sm:p-6">
      <button type="button" class="absolute inset-0 bg-slate-950/55 backdrop-blur-[2px]" aria-label="Đóng biểu mẫu" @click="closeDemandModal"></button>
      <section class="relative flex max-h-[calc(100dvh-1.5rem)] w-full max-w-4xl flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl sm:max-h-[calc(100dvh-3rem)]">
        <header class="flex shrink-0 items-start justify-between gap-4 border-b border-slate-100 px-5 py-4 sm:px-7 sm:py-5">
          <div>
            <h2 class="text-xl font-black text-slate-900 sm:text-2xl">Khai báo nhu cầu nhân lực</h2>
            <p class="mt-1 text-xs text-slate-500 sm:text-sm">Các trường có dấu * là bắt buộc.</p>
          </div>
          <button type="button" @click="closeDemandModal" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-100 text-slate-500 transition hover:bg-slate-200" title="Đóng"><i class="fa-solid fa-xmark"></i></button>
        </header>

        <form class="flex min-h-0 flex-1 flex-col" @submit.prevent="submitDemand">
          <div class="min-h-0 flex-1 overflow-y-auto px-5 py-5 sm:px-7">
          <p v-if="formError" class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-xs font-medium text-rose-700">{{ formError }}</p>

          <fieldset :disabled="isCatalogLoading || isSaving" class="grid grid-cols-1 gap-x-5 gap-y-4 sm:grid-cols-2 disabled:opacity-60">
            <div class="sm:col-span-2">
              <span class="mb-1.5 block text-sm font-bold text-slate-800">Doanh nghiệp *</span>
              <div class="flex min-h-12 w-full items-center gap-3 rounded-xl border border-purple-200 bg-purple-50 px-3.5 py-2.5 text-sm">
                <i class="fa-solid fa-building text-purple-600"></i>
                <div class="min-w-0 flex-1">
                  <p class="truncate font-bold text-slate-800">{{ activeEmployer?.name }}</p>
                  <p v-if="activeEmployer?.tax_code" class="text-xs text-slate-500">Mã số thuế: {{ activeEmployer.tax_code }}</p>
                </div>
                <span class="shrink-0 rounded-lg bg-white px-2 py-1 text-[11px] font-bold text-purple-700">Đã chọn</span>
              </div>
            </div>

            <div class="min-w-0">
              <span class="mb-1.5 block text-sm font-bold text-slate-800">Ngành, nghề *</span>
              <FormSelectPopover
                v-model="demandForm.training_major_id"
                :options="majorOptions"
                :disabled="isCatalogLoading || isSaving"
                placeholder="Chọn ngành/nghề"
                search-placeholder="Tìm tên ngành/nghề..."
                icon="fa-solid fa-graduation-cap"
                tone="purple"
                searchable
                required
              />
            </div>

            <div class="min-w-0">
              <span class="mb-1.5 block text-sm font-bold text-slate-800">Trình độ *</span>
              <FormSelectPopover
                v-model="demandForm.training_level_id"
                :options="levelOptions"
                :disabled="isCatalogLoading || isSaving"
                placeholder="Chọn trình độ"
                icon="fa-solid fa-layer-group"
                tone="purple"
                required
              />
            </div>

            <div class="min-w-0">
              <span class="mb-1.5 block text-sm font-bold text-slate-800">Kỳ báo cáo *</span>
              <FormSelectPopover
                v-model="demandForm.reporting_period_id"
                :options="periodOptions"
                :disabled="isCatalogLoading || isSaving"
                placeholder="Chọn kỳ báo cáo"
                icon="fa-solid fa-calendar-days"
                tone="purple"
                required
              />
            </div>

            <div class="min-w-0">
              <span class="mb-1.5 block text-sm font-bold text-slate-800">Mục đích *</span>
              <FormSelectPopover
                v-model="demandForm.purpose"
                :options="purposeOptions"
                :disabled="isCatalogLoading || isSaving"
                placeholder="Chọn mục đích"
                icon="fa-solid fa-bullseye"
                tone="purple"
                required
              />
            </div>

            <label>
              <span class="mb-1.5 block text-sm font-bold text-slate-800">Số người cần *</span>
              <input v-model.number="demandForm.quantity" required min="1" max="100000" type="number" placeholder="Ví dụ: 120" class="h-11 w-full rounded-xl border border-slate-200/90 bg-slate-50/70 px-3.5 text-sm text-slate-700 shadow-2xs outline-none transition-all hover:bg-slate-100/70 focus:border-purple-500 focus:bg-white focus:ring-2 focus:ring-purple-100" />
            </label>

            <label>
              <span class="mb-1.5 block text-sm font-bold text-slate-800">Khu vực / địa điểm *</span>
              <div class="relative">
                <i class="fa-solid fa-lock pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                <input v-model.trim="demandForm.location" disabled aria-disabled="true" maxlength="200" type="text" placeholder="Chưa có địa điểm trong hồ sơ doanh nghiệp" class="h-11 w-full cursor-not-allowed rounded-xl border border-slate-200/90 bg-slate-100 pl-10 pr-3.5 text-sm font-medium text-slate-500 shadow-2xs outline-none disabled:opacity-100" />
              </div>
              <span class="mt-1.5 block text-[11px] leading-snug text-slate-500">Tự động lấy từ hồ sơ doanh nghiệp đang đăng nhập.</span>
            </label>

            <label>
              <span class="mb-1.5 block text-sm font-bold text-slate-800">Độ tuổi</span>
              <input v-model.trim="demandForm.age_range" maxlength="80" type="text" placeholder="Ví dụ: 18 - 35 tuổi" class="h-11 w-full rounded-xl border border-slate-200/90 bg-slate-50/70 px-3.5 text-sm text-slate-700 shadow-2xs outline-none transition-all hover:bg-slate-100/70 focus:border-purple-500 focus:bg-white focus:ring-2 focus:ring-purple-100" />
            </label>

            <label>
              <span class="mb-1.5 block text-sm font-bold text-slate-800">Thu nhập dự kiến</span>
              <input v-model.trim="demandForm.salary" maxlength="100" type="text" placeholder="Ví dụ: 9 - 15 triệu/tháng" class="h-11 w-full rounded-xl border border-slate-200/90 bg-slate-50/70 px-3.5 text-sm text-slate-700 shadow-2xs outline-none transition-all hover:bg-slate-100/70 focus:border-purple-500 focus:bg-white focus:ring-2 focus:ring-purple-100" />
            </label>

            <label class="sm:col-span-2">
              <span class="mb-1.5 block text-sm font-bold text-slate-800">Kỹ năng yêu cầu, cách nhau bằng dấu phẩy *</span>
              <input v-model.trim="demandForm.skills" required maxlength="500" type="text" placeholder="Ví dụ: PLC, Đọc bản vẽ, An toàn điện" class="h-11 w-full rounded-xl border border-slate-200/90 bg-slate-50/70 px-3.5 text-sm text-slate-700 shadow-2xs outline-none transition-all hover:bg-slate-100/70 focus:border-purple-500 focus:bg-white focus:ring-2 focus:ring-purple-100" />
            </label>

            <label class="sm:col-span-2">
              <span class="mb-1.5 block text-sm font-bold text-slate-800">Ghi chú / điều kiện phối hợp</span>
              <textarea v-model.trim="demandForm.note" maxlength="2000" rows="3" placeholder="Thông tin bổ sung dành cho cơ sở đào tạo hoặc cơ quan quản lý" class="w-full rounded-xl border border-slate-200/90 bg-slate-50/70 px-3.5 py-3 text-sm text-slate-700 shadow-2xs outline-none transition-all hover:bg-slate-100/70 focus:border-purple-500 focus:bg-white focus:ring-2 focus:ring-purple-100"></textarea>
            </label>
          </fieldset>
          </div>

          <div class="flex shrink-0 flex-col-reverse gap-2 border-t border-slate-100 bg-slate-50 px-5 py-4 sm:flex-row sm:justify-end sm:px-7">
            <button type="button" @click="closeDemandModal" class="rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-50">Hủy</button>
            <button type="submit" :disabled="isCatalogLoading || isSaving" class="rounded-xl bg-purple-600 px-6 py-2.5 text-sm font-bold text-white shadow-xs transition hover:bg-purple-700 disabled:cursor-not-allowed disabled:bg-slate-400">
              <i :class="isSaving ? 'fa-solid fa-spinner fa-spin' : 'fa-solid fa-check'" class="mr-1.5"></i>
              {{ isSaving ? 'Đang lưu...' : 'Lưu nhu cầu' }}
            </button>
          </div>
        </form>
      </section>
    </div>

    <div v-if="toastMessage" class="fixed bottom-5 left-1/2 z-[90] -translate-x-1/2 rounded-xl bg-slate-900 px-4 py-3 text-xs font-bold text-white shadow-xl">
      <i class="fa-solid fa-circle-check mr-1.5 text-purple-400"></i>{{ toastMessage }}
    </div>

    <ClientFooter class="mt-auto" />
  </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import FormSelectPopover from '../../components/form/FormSelectPopover.vue'
import { businessPortalService } from '../../services/businessPortalService'
import { portalRoleService } from '../../services/portalRoleService'
import { schoolService } from '../../services/schoolService'
import { supplyDemandService } from '../../services/supplyDemandService'

const router = useRouter()
const activeEmployer = ref(portalRoleService.getEmployer())
const majorOptions = ref([])
const levelOptions = ref([])
const periodOptions = ref([])
const demandRows = ref([])
const isCatalogLoading = ref(false)
const isSaving = ref(false)
const isDemandModalOpen = ref(false)
const catalogError = ref('')
const formError = ref('')
const toastMessage = ref('')

const fallbackLevels = [
  { id: 'duoi_3_thang', name: 'Dưới 3 tháng' },
  { id: 'so_cap', name: 'Sơ cấp' },
  { id: 'trung_cap', name: 'Trung cấp' },
  { id: 'cao_dang', name: 'Cao đẳng' },
]

const fallbackPeriods = [
  { id: '2026-Q4', name: 'Quý IV/2026' },
  { id: '2027-Q1', name: 'Quý I/2027' },
]

const purposeOptions = [
  { id: 'RECRUITMENT', name: 'Tuyển dụng' },
  { id: 'NEW_TRAINING', name: 'Đào tạo mới' },
  { id: 'RETRAINING', name: 'Đào tạo lại' },
]

function emptyDemandForm() {
  return {
    training_major_id: '',
    training_level_id: '',
    reporting_period_id: '',
    purpose: 'RECRUITMENT',
    quantity: null,
    location: '',
    age_range: '',
    salary: '',
    skills: '',
    note: '',
  }
}

const demandForm = reactive(emptyDemandForm())

const stats = computed(() => [
  { label: 'Doanh nghiệp đang đăng nhập', value: activeEmployer.value ? '01' : '0', unit: activeEmployer.value?.tax_code ? `MST: ${activeEmployer.value.tax_code}` : 'doanh nghiệp', icon: 'fa-solid fa-building', iconClass: 'bg-purple-50 text-purple-600' },
  { label: 'Ngành/nghề', value: formatNumber(majorOptions.value.length), unit: 'ngành đang sử dụng', icon: 'fa-solid fa-book-open', iconClass: 'bg-blue-50 text-blue-600' },
  { label: 'Nhu cầu đã lưu', value: formatNumber(demandRows.value.length), unit: 'bản ghi của doanh nghiệp', icon: 'fa-solid fa-list-check', iconClass: 'bg-amber-50 text-amber-600' },
  { label: 'Tổng nhân lực cần', value: formatNumber(demandRows.value.reduce((sum, row) => sum + Number(row.quantity || 0), 0)), unit: 'người / kỳ', icon: 'fa-solid fa-users', iconClass: 'bg-emerald-50 text-emerald-600' },
])

const businessFunctions = [
  { label: 'Tổng quan', icon: 'fa-solid fa-chart-pie' },
  { label: 'Hồ sơ doanh nghiệp', icon: 'fa-solid fa-building' },
  { label: 'Nhu cầu nhân lực', icon: 'fa-solid fa-users', active: true },
  { label: 'Tin tuyển dụng', icon: 'fa-solid fa-briefcase' },
  { label: 'Kết nối cơ sở GDNN', icon: 'fa-solid fa-handshake' },
]

function formatNumber(value) {
  return new Intl.NumberFormat('vi-VN').format(Number(value || 0))
}

function purposeLabel(value) {
  return purposeOptions.find((item) => item.id === value)?.name || value
}

function resetForm() {
  Object.assign(demandForm, emptyDemandForm())
  demandForm.training_major_id = String(majorOptions.value[0]?.id || '')
  demandForm.training_level_id = String(levelOptions.value[0]?.id || '')
  demandForm.reporting_period_id = String(periodOptions.value[0]?.id || '')
  demandForm.location = activeEmployer.value?.address || activeEmployer.value?.region || ''
  formError.value = ''
}

function openDemandModal() {
  resetForm()
  isDemandModalOpen.value = true
}

function closeDemandModal() {
  if (isSaving.value) return
  isDemandModalOpen.value = false
  formError.value = ''
}

function withEmployer(row) {
  return {
    ...row,
    employer: activeEmployer.value?.name || '',
    skills: Array.isArray(row?.skills) ? row.skills : [],
  }
}

async function submitDemand() {
  const skills = demandForm.skills.split(',').map((item) => item.trim()).filter(Boolean)
  if (!demandForm.training_major_id || !demandForm.training_level_id || !demandForm.reporting_period_id || !demandForm.purpose || !demandForm.location || Number(demandForm.quantity) < 1 || skills.length === 0) {
    formError.value = 'Vui lòng điền đầy đủ các trường bắt buộc và nhập ít nhất một kỹ năng.'
    return
  }

  isSaving.value = true
  formError.value = ''
  try {
    const response = await businessPortalService.saveDemand({
      training_major_id: Number(demandForm.training_major_id),
      training_level_id: demandForm.training_level_id,
      reporting_period_id: Number(demandForm.reporting_period_id),
      purpose: demandForm.purpose,
      quantity: Number(demandForm.quantity),
      location: demandForm.location,
      age_range: demandForm.age_range || null,
      salary: demandForm.salary || null,
      skills,
      note: demandForm.note || null,
    })

    const savedRow = withEmployer(response.demand || {})
    const existingIndex = demandRows.value.findIndex((row) => String(row.id) === String(savedRow.id))
    if (existingIndex >= 0) demandRows.value.splice(existingIndex, 1, savedRow)
    else demandRows.value.unshift(savedRow)

    isDemandModalOpen.value = false
    toastMessage.value = response.message || 'Đã lưu nhu cầu nhân lực.'
    window.setTimeout(() => { toastMessage.value = '' }, 3000)
  } catch (error) {
    formError.value = error?.message || 'Không thể lưu nhu cầu nhân lực.'
  } finally {
    isSaving.value = false
  }
}

async function loadPortalData() {
  if (!activeEmployer.value) {
    await router.replace('/map')
    return
  }

  isCatalogLoading.value = true
  catalogError.value = ''
  const [catalogsResult, demandsResult] = await Promise.allSettled([
    supplyDemandService.getCatalogs(),
    businessPortalService.getDemands(),
  ])

  if (catalogsResult.status === 'fulfilled') {
    const catalogs = catalogsResult.value || {}
    majorOptions.value = Array.isArray(catalogs.training_majors) ? catalogs.training_majors : []
    levelOptions.value = Array.isArray(catalogs.training_levels) && catalogs.training_levels.length ? catalogs.training_levels : fallbackLevels
    periodOptions.value = Array.isArray(catalogs.reporting_periods) && catalogs.reporting_periods.length ? catalogs.reporting_periods : fallbackPeriods
  } else {
    levelOptions.value = fallbackLevels
    periodOptions.value = fallbackPeriods
  }

  if (!majorOptions.value.length) {
    try {
      majorOptions.value = await schoolService.getTrainingMajors()
    } catch (error) {
      majorOptions.value = []
    }
  }

  if (demandsResult.status === 'fulfilled') {
    const payload = demandsResult.value || {}
    if (payload.employer) {
      activeEmployer.value = payload.employer
      portalRoleService.setEmployer(payload.employer)
    }
    demandRows.value = Array.isArray(payload.demands) ? payload.demands.map(withEmployer) : []
  } else {
    const message = demandsResult.reason?.message || 'Không tải được dữ liệu nhu cầu của doanh nghiệp.'
    if (message.includes('Chưa chọn doanh nghiệp')) {
      portalRoleService.clearEmployer()
      await router.replace('/map')
      return
    }
    catalogError.value = message
  }

  if (!majorOptions.value.length && !catalogError.value) {
    catalogError.value = 'Danh mục ngành/nghề chưa tải được. Hãy bảo đảm máy chủ Laravel đang chạy.'
  }
  isCatalogLoading.value = false
}

onMounted(loadPortalData)
</script>
