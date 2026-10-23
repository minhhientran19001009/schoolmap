<template>
  <div class="h-full overflow-y-auto bg-slate-50 selection:bg-blue-100 selection:text-blue-900" ref="pageContainer">
    
    <!-- 1. Top Hero Section (System Light Theme) -->
    <section class="relative overflow-hidden border-b border-slate-200/90 bg-gradient-to-b from-blue-50/70 via-indigo-50/30 to-slate-50">
      <div class="absolute inset-0 opacity-40 bg-[radial-gradient(#93c5fd_1px,transparent_1px)] [background-size:20px_20px] pointer-events-none"></div>
      
      <div class="relative mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
          <div class="max-w-2xl">
            <div class="mb-2.5 inline-flex items-center gap-2 rounded-full border border-blue-200/80 bg-white/90 px-3.5 py-1 text-xs font-bold text-blue-700 shadow-2xs backdrop-blur-xs">
              <i class="fa-solid fa-city text-[11px] text-blue-600"></i>
              <span>Mạng lưới doanh nghiệp liên kết GDNN Ninh Bình</span>
            </div>
            
            <h1 class="text-2xl font-extrabold tracking-tight sm:text-3xl lg:text-4xl leading-tight text-slate-900">
              Danh sách Doanh nghiệp & Nhà tuyển dụng
            </h1>
            
            <p class="mt-2 text-xs sm:text-sm md:text-base text-slate-600 max-w-2xl leading-relaxed">
              Cổng tra cứu các tập đoàn, doanh nghiệp FDI, doanh nghiệp đối tác đào tạo nghề và tiếp nhận học viên tốt nghiệp GDNN trên địa bàn tỉnh Ninh Bình.
            </p>
          </div>

          <!-- Quick Navigation Actions -->
          <div class="flex flex-wrap items-center gap-2.5 flex-shrink-0">
            <router-link
              to="/tuyen-dung"
              class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 px-4 py-2.5 text-xs sm:text-sm font-bold text-white shadow-xs hover:shadow-soft active:scale-95 transition-all">
              <i class="fa-solid fa-briefcase"></i>
              <span>Xem tin tuyển dụng</span>
            </router-link>

            <router-link
              to="/map"
              class="inline-flex items-center gap-2 rounded-xl bg-white hover:bg-slate-50 border border-slate-200/90 px-4 py-2.5 text-xs sm:text-sm font-bold text-slate-700 shadow-2xs hover:shadow-soft active:scale-95 transition-all">
              <i class="fa-solid fa-map-location-dot text-indigo-600"></i>
              <span>Bản đồ GDNN</span>
            </router-link>
          </div>
        </div>

      </div>
    </section>

    <!-- 2. Main Directory & Filter Section -->
    <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8 space-y-6">
      
      <!-- Unified Search & Filter Card -->
      <div class="rounded-2xl border border-slate-200/90 bg-white p-3 shadow-xs sm:p-3.5">
        <div class="grid grid-cols-1 gap-2.5 md:grid-cols-[minmax(0,1fr)_minmax(280px,380px)]">
          <div class="relative flex items-center">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-sm text-slate-400">
              <i class="fa-solid fa-magnifying-glass"></i>
            </div>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Tìm doanh nghiệp, mã số thuế, địa chỉ..."
              class="h-11 w-full rounded-xl border border-slate-200/90 bg-slate-50/70 pl-10 pr-9 text-xs font-medium text-slate-800 shadow-2xs outline-none transition-all placeholder:text-slate-400 hover:bg-slate-100/70 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100 sm:text-sm"
            />
            <button
              v-if="searchQuery"
              type="button"
              class="absolute right-3 flex h-5 w-5 items-center justify-center rounded-full text-slate-400 transition hover:bg-slate-200 hover:text-slate-700"
              title="Xóa tìm kiếm"
              @click="searchQuery = ''">
              <i class="fa-solid fa-xmark text-xs"></i>
            </button>
          </div>

          <div ref="industryDropdownRef" class="relative">
            <button
              type="button"
              :class="industryDropdownOpen ? 'border-blue-400 bg-white text-blue-700 ring-2 ring-blue-100' : (selectedIndustry !== 'all' ? 'border-blue-200 bg-blue-50/40 text-blue-700' : 'border-slate-200/90 bg-slate-50/70 text-slate-700 hover:bg-slate-100/70')"
              class="relative flex h-11 w-full items-center rounded-xl border pl-10 text-left text-xs font-bold shadow-2xs outline-none transition-all sm:text-sm"
              :aria-expanded="industryDropdownOpen"
              @click="industryDropdownOpen = !industryDropdownOpen">
              <i :class="selectedIndustryOption.icon" class="absolute left-3.5 text-xs text-blue-600"></i>
              <span class="block truncate" :class="selectedIndustry !== 'all' ? 'pr-14' : 'pr-9'">{{ selectedIndustryOption.name }}</span>
            </button>

            <button
              v-if="selectedIndustry !== 'all'"
              type="button"
              class="absolute right-8 top-1/2 flex h-5 w-5 -translate-y-1/2 items-center justify-center rounded-full text-slate-400 transition hover:bg-white hover:text-rose-600"
              title="Xóa lọc lĩnh vực"
              @click.stop="clearIndustry">
              <i class="fa-solid fa-xmark text-[10px]"></i>
            </button>
            <i class="fa-solid fa-chevron-down pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 text-[10px] text-slate-400 transition-transform duration-200" :class="industryDropdownOpen ? 'rotate-180 text-blue-600' : ''"></i>

            <Transition name="filter-popover">
              <div v-if="industryDropdownOpen" class="absolute right-0 top-full z-[1000] mt-1.5 w-full overflow-hidden rounded-2xl border border-slate-200 bg-white p-1.5 shadow-xl">
                <div class="max-h-72 space-y-0.5 overflow-y-auto pr-0.5">
                  <button
                    v-for="industry in industries"
                    :key="industry.id"
                    type="button"
                    :class="selectedIndustry === industry.id ? 'bg-blue-50 text-blue-700' : 'text-slate-700 hover:bg-slate-50'"
                    class="flex w-full items-center gap-2.5 rounded-xl px-3 py-2.5 text-left text-xs font-semibold transition"
                    @click="selectIndustry(industry.id)">
                    <span :class="selectedIndustry === industry.id ? 'bg-white text-blue-600' : 'bg-slate-100 text-slate-500'" class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg">
                      <i :class="industry.icon" class="text-[11px]"></i>
                    </span>
                    <span class="min-w-0 flex-1 truncate">{{ industry.name }}</span>
                    <i v-if="selectedIndustry === industry.id" class="fa-solid fa-check text-[11px] text-blue-600"></i>
                  </button>
                </div>
              </div>
            </Transition>
          </div>
        </div>
      </div>

      <!-- Results Count Header -->
      <div class="flex items-center justify-between px-1">
        <h2 class="text-sm font-bold text-slate-800">
          Tìm thấy <strong class="text-blue-600">{{ filteredEnterprises.length }}</strong> doanh nghiệp phù hợp
        </h2>
        <span class="text-xs text-slate-500">
          Hiển thị toàn bộ danh bạ doanh nghiệp đối tác
        </span>
      </div>

      <!-- Enterprise Cards Grid & Pagination -->
      <div v-if="filteredEnterprises.length > 0" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
          <EnterpriseCard 
            v-for="ent in paginatedEnterprises" 
            :key="ent.id"
            :enterprise="ent"
            @select="goToEnterpriseDetail"
          />
        </div>

        <!-- Premium Modern Pagination Bar -->
        <div 
          v-if="totalPages > 1 || filteredEnterprises.length > pageSize"
          class="bg-white rounded-2xl border border-slate-200/90 p-3 sm:p-4 shadow-2xs flex flex-col sm:flex-row items-center justify-between gap-3 sm:gap-4 select-none">
          
          <!-- Left info: items counter & Page size selector -->
          <div class="text-xs text-slate-500 order-2 sm:order-1 flex items-center justify-center sm:justify-start gap-2 flex-wrap">
            <span>
              Hiển thị <strong class="text-slate-800">{{ (currentPage - 1) * pageSize + 1 }} - {{ Math.min(currentPage * pageSize, filteredEnterprises.length) }}</strong>
              trên <strong class="text-blue-600">{{ filteredEnterprises.length }}</strong> doanh nghiệp
            </span>

            <div class="hidden sm:flex items-center gap-1.5 ml-2 border-l border-slate-200 pl-3">
              <span class="text-slate-400">Hiển thị:</span>
              <select 
                v-model="pageSize" 
                class="h-7 px-2 text-xs font-semibold text-slate-700 bg-slate-50 border border-slate-200 rounded-lg outline-none cursor-pointer hover:bg-slate-100">
                <option :value="6">6 / trang</option>
                <option :value="9">9 / trang</option>
                <option :value="12">12 / trang</option>
                <option :value="18">18 / trang</option>
              </select>
            </div>
          </div>

          <!-- Right: Page Navigation Buttons -->
          <nav class="flex items-center gap-1 sm:gap-1.5 order-1 sm:order-2" aria-label="Phân trang doanh nghiệp">
            <!-- Nút Trước -->
            <button 
              @click="prevPage"
              :disabled="currentPage === 1"
              :class="currentPage === 1 
                ? 'opacity-40 cursor-not-allowed text-slate-300 border-slate-100 bg-slate-50' 
                : 'hover:bg-blue-50 text-slate-700 hover:text-blue-700 border-slate-200 hover:border-blue-200 bg-white cursor-pointer active:scale-95 shadow-2xs'"
              class="h-8 sm:h-9 px-2.5 sm:px-3 rounded-xl border text-xs font-semibold flex items-center gap-1 transition-all"
              title="Trang trước">
              <i class="fa-solid fa-chevron-left text-[10px]"></i>
              <span class="hidden sm:inline">Trước</span>
            </button>

            <!-- Danh sách số trang -->
            <template v-for="(p, idx) in displayedPages" :key="idx">
              <span 
                v-if="p === '...'"
                class="h-8 sm:h-9 w-6 sm:w-8 flex items-center justify-center text-slate-400 text-xs font-bold">
                ...
              </span>
              <button 
                v-else
                @click="goToPage(p)"
                :class="currentPage === p 
                  ? 'bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 text-white font-bold shadow-xs border-transparent' 
                  : 'bg-white hover:bg-blue-50 text-slate-700 hover:text-blue-700 border-slate-200 hover:border-blue-200 font-semibold shadow-2xs'"
                class="h-8 sm:h-9 w-8 sm:w-9 rounded-xl border text-xs flex items-center justify-center transition-all cursor-pointer active:scale-95">
                {{ p }}
              </button>
            </template>

            <!-- Nút Sau -->
            <button 
              @click="nextPage"
              :disabled="currentPage === totalPages"
              :class="currentPage === totalPages 
                ? 'opacity-40 cursor-not-allowed text-slate-300 border-slate-100 bg-slate-50' 
                : 'hover:bg-blue-50 text-slate-700 hover:text-blue-700 border-slate-200 hover:border-blue-200 bg-white cursor-pointer active:scale-95 shadow-2xs'"
              class="h-8 sm:h-9 px-2.5 sm:px-3 rounded-xl border text-xs font-semibold flex items-center gap-1 transition-all"
              title="Trang sau">
              <span class="hidden sm:inline">Sau</span>
              <i class="fa-solid fa-chevron-right text-[10px]"></i>
            </button>
          </nav>
        </div>
      </div>

      <!-- Empty State -->
      <div 
        v-else 
        class="bg-white rounded-3xl border border-slate-200/90 p-8 sm:p-12 text-center space-y-4 max-w-xl mx-auto shadow-xs">
        <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-2xl mx-auto">
          <i class="fa-solid fa-building-circle-xmark"></i>
        </div>
        <div>
          <h3 class="font-bold text-base text-slate-900">Không tìm thấy doanh nghiệp phù hợp</h3>
          <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-sm mx-auto">
            Không có doanh nghiệp nào phù hợp với các tiêu chí tìm kiếm hiện tại. Vui lòng thử lại với từ khóa hoặc bộ lọc khác.
          </p>
        </div>
        <button 
          @click="resetFilters"
          class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition-colors shadow-xs cursor-pointer">
          <i class="fa-solid fa-rotate-left"></i>
          <span>Xóa bộ lọc & Xem tất cả</span>
        </button>
      </div>

    </main>

    <ClientFooter class="mt-auto" />

  </div>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import { enterpriseService, ENTERPRISE_INDUSTRIES } from '../../services/enterpriseService'
import EnterpriseCard from '../../components/enterprise/EnterpriseCard.vue'

const router = useRouter()

const pageContainer = ref(null)
const industryDropdownRef = ref(null)
const searchQuery = ref('')
const selectedIndustry = ref('all')
const industryDropdownOpen = ref(false)

const currentPage = ref(1)
const pageSize = ref(9)

const industries = ENTERPRISE_INDUSTRIES
const selectedIndustryOption = computed(() => industries.find((item) => item.id === selectedIndustry.value) || industries[0])

const filteredEnterprises = computed(() => {
  return enterpriseService.filterEnterprises({
    search: searchQuery.value,
    industry: selectedIndustry.value
  })
})

const totalPages = computed(() => {
  return Math.ceil(filteredEnterprises.value.length / pageSize.value) || 1
})

const paginatedEnterprises = computed(() => {
  const start = (currentPage.value - 1) * pageSize.value
  return filteredEnterprises.value.slice(start, start + pageSize.value)
})

const displayedPages = computed(() => {
  const total = totalPages.value
  const current = currentPage.value
  if (total <= 7) {
    return Array.from({ length: total }, (_, i) => i + 1)
  }
  if (current <= 3) {
    return [1, 2, 3, 4, '...', total]
  } else if (current >= total - 2) {
    return [1, '...', total - 3, total - 2, total - 1, total]
  } else {
    return [1, '...', current - 1, current, current + 1, '...', total]
  }
})

function goToPage(page) {
  if (page < 1 || page > totalPages.value || page === currentPage.value) return
  currentPage.value = page
  scrollToTop()
}

function prevPage() {
  if (currentPage.value > 1) {
    goToPage(currentPage.value - 1)
  }
}

function nextPage() {
  if (currentPage.value < totalPages.value) {
    goToPage(currentPage.value + 1)
  }
}

function scrollToTop() {
  if (pageContainer.value) {
    pageContainer.value.scrollTo({ top: 380, behavior: 'smooth' })
  }
}

// Reset page to 1 whenever any filter criteria changes
watch([searchQuery, selectedIndustry, pageSize], () => {
  currentPage.value = 1
})

function resetFilters() {
  searchQuery.value = ''
  selectedIndustry.value = 'all'
  industryDropdownOpen.value = false
  currentPage.value = 1
}

function selectIndustry(id) {
  selectedIndustry.value = id
  industryDropdownOpen.value = false
}

function clearIndustry() {
  selectedIndustry.value = 'all'
  industryDropdownOpen.value = false
}

function handleClickOutside(event) {
  if (industryDropdownRef.value && !industryDropdownRef.value.contains(event.target)) {
    industryDropdownOpen.value = false
  }
}

function goToEnterpriseDetail(enterprise) {
  if (!enterprise?.id) return
  router.push({ name: 'ClientEnterpriseDetail', params: { id: enterprise.id } })
}

onMounted(() => document.addEventListener('click', handleClickOutside))
onBeforeUnmount(() => document.removeEventListener('click', handleClickOutside))
</script>

<style scoped>
.filter-popover-enter-active,
.filter-popover-leave-active {
  transition: opacity 150ms ease, transform 150ms ease;
  transform-origin: top right;
}

.filter-popover-enter-from,
.filter-popover-leave-to {
  opacity: 0;
  transform: translateY(-4px) scale(0.98);
}
</style>
