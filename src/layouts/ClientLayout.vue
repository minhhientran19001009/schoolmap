<template>
  <div class="w-screen h-screen overflow-hidden flex flex-col bg-slate-50 font-sans select-none" @click="closePortalMenus">
    <!-- Public Header (56px - 60px) - Modern Portal & Apple Segmented Control Aesthetic -->
    <header class="h-14 sm:h-[58px] bg-slate-50/95 backdrop-blur-md border-b border-slate-200/90 z-[1200] flex items-center px-2 sm:px-4 md:px-6 gap-1.5 sm:gap-3 flex-shrink-0 shadow-xs sticky top-0 transition-all">
      
      <!-- Left: Logo and Brand -->
      <router-link to="/map" class="flex items-center gap-1.5 sm:gap-2.5 flex-shrink-0 group">
        <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-gradient-to-tr from-blue-700 via-indigo-600 to-purple-600 text-white flex items-center justify-center font-bold text-xs sm:text-sm shadow-xs group-hover:scale-105 group-hover:shadow-soft transition-all duration-200 flex-shrink-0">
          <i class="fa-solid fa-graduation-cap"></i>
        </div>
        <div class="flex flex-col min-w-0">
          <span class="font-extrabold text-slate-800 text-xs sm:text-sm md:text-base leading-tight group-hover:text-blue-700 transition-colors whitespace-nowrap">
            <span class="hidden md:inline">Bản đồ số GDNN Ninh Bình</span>
            <span class="hidden sm:inline md:hidden">GDNN Ninh Bình</span>
            <span class="hidden min-[480px]:inline sm:hidden">GDNN</span>
          </span>
          <span class="text-[11px] text-slate-500 font-medium hidden lg:block leading-tight mt-0.5">
            Cổng tra cứu cơ sở GDNN & Việc làm
          </span>
        </div>
      </router-link>

      <!-- Center / Primary Nav -->
      <nav class="segmented-control-track inline-flex items-center p-0.5 sm:p-1 bg-slate-100/95 rounded-2xl flex-shrink-0 lg:w-[540px] xl:w-[580px] border border-slate-200/80 shadow-inner">
        <!-- Tab 1: Bản đồ GDNN -->
        <router-link
          to="/map"
          :class="isMapRoute ? 'nav-tab-active-map' : 'nav-tab-inactive'"
          class="nav-tab-item px-2 sm:px-2.5 py-1 sm:py-1.5 text-xs rounded-xl transition-all flex items-center gap-1 sm:gap-1.5 lg:flex-1 lg:justify-center cursor-pointer whitespace-nowrap">
          <i class="fa-solid fa-map-location-dot text-[11px] sm:text-xs" :class="isMapRoute ? 'text-blue-600' : 'text-slate-400'"></i>
          <span class="hidden md:inline">Bản đồ GDNN</span>
          <span class="md:hidden">Bản đồ</span>
        </router-link>

        <!-- Tab 2: Danh sách cơ sở -->
        <router-link
          to="/co-so-gdnn"
          :class="isSchoolListRoute ? 'nav-tab-active-schools' : 'nav-tab-inactive'"
          class="nav-tab-item px-2 sm:px-2.5 py-1 sm:py-1.5 text-xs rounded-xl transition-all flex items-center gap-1 sm:gap-1.5 lg:flex-1 lg:justify-center cursor-pointer whitespace-nowrap">
          <i class="fa-solid fa-building-columns text-[11px] sm:text-xs" :class="isSchoolListRoute ? 'text-blue-600' : 'text-slate-400'"></i>
          <span class="hidden md:inline">Cơ sở GDNN</span>
          <span class="md:hidden">Cơ sở</span>
        </router-link>

        <!-- Tab 3: Doanh nghiệp -->
        <router-link
          to="/danh-sach-doanh-nghiep"
          :class="isEnterpriseRoute ? 'nav-tab-active-enterprises' : 'nav-tab-inactive'"
          class="nav-tab-item px-2 sm:px-2.5 py-1 sm:py-1.5 text-xs rounded-xl transition-all flex items-center gap-1 sm:gap-1.5 lg:flex-1 lg:justify-center cursor-pointer whitespace-nowrap">
          <i class="fa-solid fa-city text-[11px] sm:text-xs" :class="isEnterpriseRoute ? 'text-blue-600' : 'text-slate-400'"></i>
          <span class="hidden md:inline">Doanh nghiệp</span>
          <span class="md:hidden">Doanh nghiệp</span>
        </router-link>

        <!-- Tab 4: Việc làm GDNN -->
        <router-link 
          to="/tuyen-dung"
          :class="isJobsRoute ? 'nav-tab-active-jobs' : 'nav-tab-inactive'"
          class="nav-tab-item px-2 sm:px-2.5 py-1 sm:py-1.5 text-xs rounded-xl transition-all flex items-center gap-1 sm:gap-1.5 lg:flex-1 lg:justify-center cursor-pointer relative whitespace-nowrap">
          <i class="fa-solid fa-briefcase text-[11px] sm:text-xs" :class="isJobsRoute ? 'text-blue-600' : 'text-slate-400'"></i>
          <span class="hidden md:inline">Việc làm</span>
          <span class="md:hidden">Việc làm</span>
        </router-link>
      </nav>

      <!-- Center-Right Filter Area (Visible on Map view) -->
      <TopFilterBar v-if="isMapRoute" class="flex-1 min-w-0" />
      <div v-else class="flex-1"></div>

      <!-- Public demo portals for schools and employers -->
      <div class="flex items-center gap-1.5 sm:gap-2.5 flex-shrink-0">
        <div class="h-5 w-px bg-slate-200 hidden sm:block"></div>

        <div class="relative">
          <button
            type="button"
            :disabled="Boolean(switchingRole)"
            :class="selectedPortalRole === 'school' ? 'ring-2 ring-emerald-200 border-emerald-300' : ''"
            @click.stop="toggleSchoolMenu"
            class="portal-btn inline-flex items-center justify-center gap-1.5 text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 active:scale-95 transition-all py-1.5 px-2.5 sm:px-3 rounded-xl border border-emerald-200/80 shadow-xs whitespace-nowrap flex-shrink-0"
            title="Chọn Cơ sở GDNN để đăng nhập"
            aria-label="Chọn Cơ sở GDNN để đăng nhập"
            :aria-expanded="schoolMenuOpen">
            <i class="fa-solid fa-school text-xs text-emerald-600 flex-shrink-0"></i>
            <span class="hidden 2xl:inline">Vai trò Cơ sở GDNN</span>
          </button>

          <Transition name="portal-menu">
            <div
              v-if="schoolMenuOpen"
              class="portal-menu-panel absolute right-0 top-full z-[70] mt-2 flex max-h-[calc(100dvh-4.5rem)] w-[min(380px,calc(100vw-1rem))] flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white text-left shadow-2xl"
              @click.stop
            >
            <div class="border-b border-slate-100 p-3.5">
              <p class="text-sm font-bold text-slate-900">Chọn cơ sở GDNN</p>
              <p class="mt-0.5 text-[11px] text-slate-500">Đăng nhập theo cơ sở để khai báo đúng phạm vi dữ liệu.</p>
              <div class="relative mt-3">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                <input
                  v-model="schoolSearch"
                  type="search"
                  placeholder="Tìm tên hoặc mã cơ sở..."
                  class="h-10 w-full rounded-xl border border-slate-200 bg-slate-50 pl-9 pr-3 text-xs text-slate-700 outline-none focus:border-emerald-400 focus:bg-white focus:ring-2 focus:ring-emerald-100"
                  @click.stop
                />
              </div>
            </div>

            <div class="min-h-0 flex-1 overflow-y-auto p-2">
              <div v-if="schoolMenuLoading" class="flex items-center justify-center gap-2 py-8 text-xs text-slate-500">
                <i class="fa-solid fa-spinner animate-spin text-emerald-600"></i>
                Đang tải danh sách cơ sở...
              </div>
              <div v-else-if="schoolMenuError" class="rounded-xl bg-rose-50 px-3 py-3 text-xs leading-relaxed text-rose-700">{{ schoolMenuError }}</div>
              <template v-else>
                <button
                  v-for="school in filteredPortalSchools"
                  :key="school.id"
                  type="button"
                  @click="selectPortalSchool(school)"
                  class="flex w-full items-start gap-3 rounded-xl px-3 py-2.5 text-left transition duration-150 ease-out hover:translate-x-0.5 hover:bg-emerald-50"
                >
                  <span class="mt-0.5 flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                    <i class="fa-solid fa-school text-xs"></i>
                  </span>
                  <span class="min-w-0 flex-1">
                    <span class="block text-xs font-bold leading-snug text-slate-800">{{ school.name }}</span>
                    <span class="mt-0.5 block truncate text-[11px] text-slate-500">{{ school.code || 'Chưa có mã' }}<template v-if="school.ward"> · {{ school.ward }}</template></span>
                  </span>
                  <i v-if="selectedPortalSchool?.id === String(school.id)" class="fa-solid fa-circle-check mt-2 text-sm text-emerald-600"></i>
                </button>

                <p v-if="filteredPortalSchools.length === 0" class="py-8 text-center text-xs text-slate-500">Không tìm thấy cơ sở phù hợp.</p>
              </template>
            </div>
            </div>
          </Transition>
        </div>

        <div class="relative">
          <button
            type="button"
            :disabled="Boolean(switchingRole)"
            :class="selectedPortalRole === 'business' ? 'ring-2 ring-purple-200 border-purple-300' : ''"
            @click.stop="toggleEmployerMenu"
            class="employer-btn inline-flex items-center justify-center gap-1.5 sm:gap-2 text-xs font-bold text-purple-700 bg-purple-50 hover:bg-purple-100 active:scale-95 transition-all py-1.5 px-2.5 sm:px-3.5 rounded-xl border border-purple-200/80 shadow-xs hover:shadow-soft whitespace-nowrap flex-shrink-0"
            title="Chọn Doanh nghiệp để đăng nhập"
            aria-label="Chọn Doanh nghiệp để đăng nhập"
            :aria-expanded="employerMenuOpen">
            <i class="fa-solid fa-user-tie text-xs text-purple-600 flex-shrink-0"></i>
            <span class="hidden 2xl:inline">Vai trò Doanh nghiệp</span>
          </button>

          <Transition name="portal-menu">
            <div
              v-if="employerMenuOpen"
              class="portal-menu-panel absolute right-0 top-full z-[70] mt-2 flex max-h-[calc(100dvh-4.5rem)] w-[min(400px,calc(100vw-1rem))] flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white text-left shadow-2xl"
              @click.stop
            >
            <div class="border-b border-slate-100 p-3.5">
              <p class="text-sm font-bold text-slate-900">Chọn doanh nghiệp</p>
              <p class="mt-0.5 text-[11px] text-slate-500">Đăng nhập theo doanh nghiệp để khai báo đúng nhu cầu nhân lực.</p>
              <div class="relative mt-3">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                <input
                  v-model="employerSearch"
                  type="search"
                  placeholder="Tìm tên, mã số thuế hoặc lĩnh vực..."
                  class="h-10 w-full rounded-xl border border-slate-200 bg-slate-50 pl-9 pr-3 text-xs text-slate-700 outline-none focus:border-purple-400 focus:bg-white focus:ring-2 focus:ring-purple-100"
                  @click.stop
                />
              </div>
            </div>

            <div class="min-h-0 flex-1 overflow-y-auto p-2">
              <div v-if="employerMenuLoading" class="flex items-center justify-center gap-2 py-8 text-xs text-slate-500">
                <i class="fa-solid fa-spinner animate-spin text-purple-600"></i>
                Đang tải danh sách doanh nghiệp...
              </div>
              <div v-else-if="employerMenuError" class="rounded-xl bg-rose-50 px-3 py-3 text-xs leading-relaxed text-rose-700">{{ employerMenuError }}</div>
              <template v-else>
                <button
                  v-for="employer in filteredPortalEmployers"
                  :key="employer.id"
                  type="button"
                  @click="selectPortalEmployer(employer)"
                  class="flex w-full items-start gap-3 rounded-xl px-3 py-2.5 text-left transition duration-150 ease-out hover:translate-x-0.5 hover:bg-purple-50"
                >
                  <span class="mt-0.5 flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-purple-50 text-purple-600">
                    <i class="fa-solid fa-building text-xs"></i>
                  </span>
                  <span class="min-w-0 flex-1">
                    <span class="block text-xs font-bold leading-snug text-slate-800">{{ employer.name }}</span>
                    <span class="mt-0.5 block truncate text-[11px] text-slate-500">{{ employer.tax_code || employer.industry || 'Chưa có thông tin phân loại' }}</span>
                  </span>
                  <i v-if="Number(selectedPortalEmployer?.id) === Number(employer.id)" class="fa-solid fa-circle-check mt-2 text-sm text-purple-600"></i>
                </button>

                <p v-if="filteredPortalEmployers.length === 0" class="py-8 text-center text-xs text-slate-500">Không tìm thấy doanh nghiệp phù hợp.</p>
              </template>
            </div>
            </div>
          </Transition>
        </div>
      </div>
    </header>

    <!-- Main Public Content View -->
    <main class="flex-1 w-full overflow-hidden relative">
      <router-view :key="route.fullPath" />
    </main>


    <Transition name="role-switch">
      <div v-if="switchingRole" class="fixed inset-0 z-[4000] flex items-center justify-center bg-slate-950/55 p-4 backdrop-blur-sm" role="status" aria-live="polite">
        <div class="w-full max-w-sm rounded-3xl border border-white/70 bg-white px-7 py-8 text-center shadow-2xl">
          <div class="relative mx-auto h-16 w-16">
            <span :class="switchingRole === 'school' ? 'border-emerald-500' : 'border-purple-500'" class="absolute inset-0 animate-spin rounded-full border-[3px] border-t-transparent"></span>
            <span :class="switchingRole === 'school' ? 'bg-emerald-50 text-emerald-600' : 'bg-purple-50 text-purple-600'" class="absolute inset-2 flex items-center justify-center rounded-full text-xl">
              <i :class="switchingRole === 'school' ? 'fa-solid fa-school' : 'fa-solid fa-user-tie'"></i>
            </span>
          </div>
          <h2 class="mt-5 text-lg font-black text-slate-900">Đang chuyển vai trò</h2>
          <p class="mt-1.5 text-sm text-slate-500">{{ switchingRoleLabel }}</p>
          <p v-if="switchingEntityName" class="mt-1 line-clamp-2 text-xs font-semibold text-slate-700">{{ switchingEntityName }}</p>
          <div class="mt-5 h-1.5 overflow-hidden rounded-full bg-slate-100">
            <span :class="switchingRole === 'school' ? 'bg-emerald-500' : 'bg-purple-500'" class="role-loading-bar block h-full rounded-full"></span>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import TopFilterBar from '../components/layout/TopFilterBar.vue'
import { businessPortalService } from '../services/businessPortalService'
import { portalRoleService } from '../services/portalRoleService'
import { schoolPortalService } from '../services/schoolPortalService'
import { schoolService } from '../services/schoolService'

const route = useRoute()
const router = useRouter()
const switchingRole = ref(null)
const selectedPortalRole = ref(portalRoleService.get())
const selectedPortalSchool = ref(portalRoleService.getSchool())
const selectedPortalEmployer = ref(portalRoleService.getEmployer())
const switchingEntityName = ref('')
const schoolMenuOpen = ref(false)
const schoolMenuLoading = ref(false)
const schoolMenuError = ref('')
const schoolSearch = ref('')
const portalSchools = ref([])
const employerMenuOpen = ref(false)
const employerMenuLoading = ref(false)
const employerMenuError = ref('')
const employerSearch = ref('')
const portalEmployers = ref([])
const isMapRoute = computed(() => !route.path || route.path === '/' || route.path.startsWith('/map'))
const isSchoolListRoute = computed(() => route.path.startsWith('/co-so-gdnn') || route.path.startsWith('/danh-sach-co-so'))
const isEnterpriseRoute = computed(() => route.path.startsWith('/danh-sach-doanh-nghiep') || route.path.startsWith('/doanh-nghiep-gdnn') || route.path.startsWith('/doanh-nghiep-doi-tac'))
const isJobsRoute = computed(() => route.path.startsWith('/tuyen-dung') || route.path.startsWith('/viec-lam'))
const switchingRoleLabel = computed(() => switchingRole.value === 'school'
  ? 'Đang mở không gian dành cho Cơ sở GDNN...'
  : 'Đang mở không gian dành cho Doanh nghiệp...')
const filteredPortalSchools = computed(() => {
  const search = normalizeSearch(schoolSearch.value)
  if (!search) return portalSchools.value
  return portalSchools.value.filter((school) => normalizeSearch(`${school.name || ''} ${school.code || ''} ${school.ward || ''}`).includes(search))
})
const filteredPortalEmployers = computed(() => {
  const search = normalizeSearch(employerSearch.value)
  if (!search) return portalEmployers.value
  return portalEmployers.value.filter((employer) => normalizeSearch(`${employer.name || ''} ${employer.tax_code || ''} ${employer.industry || ''} ${employer.region || ''} ${employer.address || ''}`).includes(search))
})

function delay(milliseconds) {
  return new Promise((resolve) => window.setTimeout(resolve, milliseconds))
}

function normalizeSearch(value) {
  return String(value || '')
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .replace(/đ/g, 'd')
    .toLocaleLowerCase('vi')
    .trim()
}

async function loadPortalSchools() {
  if (portalSchools.value.length || schoolMenuLoading.value) return
  schoolMenuLoading.value = true
  schoolMenuError.value = ''
  try {
    const schools = await schoolService.syncFromApi()
    portalSchools.value = [...(schools || [])]
      .filter((school) => school.status !== 'INACTIVE')
      .sort((a, b) => String(a.name || '').localeCompare(String(b.name || ''), 'vi'))
    if (!portalSchools.value.length) schoolMenuError.value = 'Chưa tải được danh sách cơ sở từ hệ thống.'
  } catch (error) {
    schoolMenuError.value = 'Không thể tải danh sách cơ sở. Hãy kiểm tra máy chủ Laravel.'
  } finally {
    schoolMenuLoading.value = false
  }
}

function toggleSchoolMenu() {
  employerMenuOpen.value = false
  schoolMenuOpen.value = !schoolMenuOpen.value
  if (schoolMenuOpen.value) loadPortalSchools()
}

async function selectPortalSchool(school) {
  if (switchingRole.value) return
  schoolMenuOpen.value = false
  switchingRole.value = 'school'
  switchingEntityName.value = school.name
  try {
    const [payload] = await Promise.all([
      schoolPortalService.selectSchool(String(school.id)),
      delay(700),
    ])
    const selectedSchool = payload.school || school
    portalRoleService.clearEmployer()
    portalRoleService.setSchool(selectedSchool)
    portalRoleService.set('school')
    selectedPortalSchool.value = portalRoleService.getSchool()
    selectedPortalEmployer.value = null
    selectedPortalRole.value = 'school'
    await router.push({ path: '/vai-tro/nha-truong', query: { school: selectedSchool.id } })
    await delay(250)
  } catch (error) {
    schoolMenuError.value = error?.message || 'Không thể đăng nhập theo cơ sở đã chọn.'
    schoolMenuOpen.value = true
  } finally {
    switchingRole.value = null
    switchingEntityName.value = ''
  }
}

async function loadPortalEmployers() {
  if (portalEmployers.value.length || employerMenuLoading.value) return
  employerMenuLoading.value = true
  employerMenuError.value = ''
  try {
    const payload = await businessPortalService.getEmployers()
    portalEmployers.value = Array.isArray(payload.employers) ? payload.employers : []
    if (!portalEmployers.value.length) employerMenuError.value = 'Chưa có doanh nghiệp đang hoạt động trong hệ thống.'
  } catch (error) {
    employerMenuError.value = error?.message || 'Không thể tải danh sách doanh nghiệp.'
  } finally {
    employerMenuLoading.value = false
  }
}

function toggleEmployerMenu() {
  schoolMenuOpen.value = false
  employerMenuOpen.value = !employerMenuOpen.value
  if (employerMenuOpen.value) loadPortalEmployers()
}

function closePortalMenus() {
  schoolMenuOpen.value = false
  employerMenuOpen.value = false
}

async function selectPortalEmployer(employer) {
  if (switchingRole.value) return
  employerMenuOpen.value = false
  switchingRole.value = 'business'
  switchingEntityName.value = employer.name
  try {
    const [payload] = await Promise.all([
      businessPortalService.selectEmployer(Number(employer.id)),
      delay(700),
    ])
    const selectedEmployer = payload.employer || employer
    portalRoleService.clearSchool()
    portalRoleService.setEmployer(selectedEmployer)
    portalRoleService.set('business')
    selectedPortalSchool.value = null
    selectedPortalEmployer.value = portalRoleService.getEmployer()
    selectedPortalRole.value = 'business'
    await router.push({ path: '/vai-tro/doanh-nghiep', query: { employer: selectedEmployer.id } })
    await delay(250)
  } catch (error) {
    employerMenuError.value = error?.message || 'Không thể đăng nhập theo doanh nghiệp đã chọn.'
    employerMenuOpen.value = true
  } finally {
    switchingRole.value = null
    switchingEntityName.value = ''
  }
}
</script>

<style scoped>
/* Segmented Control Styling */
.segmented-control-track {
  background-color: #f1f5f9;
  box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.06);
}

.nav-tab-item {
  user-select: none;
  font-weight: 500;
  color: #64748b;
  border: 1px solid transparent;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.nav-tab-inactive:hover {
  color: #1e293b;
  background-color: rgba(226, 232, 240, 0.6);
}

.nav-tab-active-map {
  background-color: #ffffff;
  color: #1d4ed8 !important;
  font-weight: 700 !important;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08), 0 1px 2px rgba(0, 0, 0, 0.04);
  border: 1px solid rgba(219, 234, 254, 0.9);
}

.nav-tab-active-schools {
  background-color: #ffffff;
  color: #1d4ed8 !important;
  font-weight: 700 !important;
  box-shadow: 0 1px 3px rgba(29, 78, 216, 0.12), 0 1px 2px rgba(0, 0, 0, 0.04);
  border: 1px solid rgba(219, 234, 254, 0.9);
}

.nav-tab-active-enterprises {
  background-color: #ffffff;
  color: #1d4ed8 !important;
  font-weight: 700 !important;
  box-shadow: 0 1px 3px rgba(29, 78, 216, 0.12), 0 1px 2px rgba(0, 0, 0, 0.04);
  border: 1px solid rgba(219, 234, 254, 0.9);
}

.nav-tab-active-jobs {
  background-color: #ffffff;
  color: #1d4ed8 !important;
  font-weight: 700 !important;
  box-shadow: 0 1px 3px rgba(29, 78, 216, 0.12), 0 1px 2px rgba(0, 0, 0, 0.04);
  border: 1px solid rgba(219, 234, 254, 0.9);
}

/* Employer Action Button */
.employer-btn {
  user-select: none;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.employer-btn:hover {
  background-color: #f3e8ff;
  border-color: #d8b4fe;
}

.portal-btn {
  user-select: none;
}

.portal-btn:disabled,
.employer-btn:disabled {
  cursor: wait;
  opacity: 0.7;
}

.portal-menu-panel {
  transform-origin: top right;
  will-change: transform, opacity;
}

.portal-menu-enter-active {
  transition:
    opacity 180ms ease-out,
    transform 220ms cubic-bezier(0.16, 1, 0.3, 1);
}

.portal-menu-leave-active {
  pointer-events: none;
  transition:
    opacity 130ms ease-in,
    transform 150ms cubic-bezier(0.4, 0, 1, 1);
}

.portal-menu-enter-from,
.portal-menu-leave-to {
  opacity: 0;
  transform: translateY(-6px) scale(0.975);
}

.portal-menu-enter-to,
.portal-menu-leave-from {
  opacity: 1;
  transform: translateY(0) scale(1);
}

@media (prefers-reduced-motion: reduce) {
  .portal-menu-enter-active,
  .portal-menu-leave-active {
    transition-duration: 1ms;
  }
}

.role-loading-bar {
  animation: role-loading 0.95s ease-in-out forwards;
  transform-origin: left;
}

.role-switch-enter-active,
.role-switch-leave-active {
  transition: opacity 0.18s ease;
}

.role-switch-enter-from,
.role-switch-leave-to {
  opacity: 0;
}

@keyframes role-loading {
  from { transform: scaleX(0.08); }
  to { transform: scaleX(1); }
}
</style>
