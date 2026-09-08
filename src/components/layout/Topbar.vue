<template>
  <header class="fixed top-0 left-0 right-0 h-14 bg-white border-b border-slate-200 z-30 flex items-center px-3 sm:px-4 gap-2 sm:gap-3 select-none">
    <!-- Sidebar Toggle Button -->
    <button 
      @click="$emit('toggle-sidebar')"
      class="w-9 h-9 flex items-center justify-center rounded-lg hover:bg-slate-100 text-blue-800 transition-colors flex-shrink-0"
      title="Bật/Tắt Menu">
      <i class="fa-solid fa-bars text-base"></i>
    </button>

    <!-- Logo and Brand Title -->
    <div class="flex items-center gap-2.5 flex-shrink-0 cursor-pointer" @click="$emit('change-view', 'map')">
      <div class="w-8 h-8 rounded-full bg-blue-800 text-white flex items-center justify-center font-bold text-sm shadow-sm flex-shrink-0">
        <i class="fa-solid fa-graduation-cap"></i>
      </div>
      <div class="flex flex-col">
        <span class="font-bold text-slate-800 text-sm sm:text-base leading-tight">Bản đồ số Giáo dục Ninh Bình</span>
        <span class="text-[10px] text-slate-500 font-medium hidden sm:block">Sở Giáo dục và Đào tạo Tỉnh Ninh Bình</span>
      </div>
    </div>

    <div class="w-px h-5 bg-slate-200 hidden md:block mx-1"></div>

    <!-- Dynamic Breadcrumb / Page Title -->
    <div class="items-center gap-1.5 text-xs text-slate-500 hidden md:flex truncate">
      <span class="font-medium text-slate-700">{{ currentViewTitle }}</span>
    </div>

    <div class="flex-1"></div>

    <!-- Quick Navigation Pills -->
    <div class="hidden lg:flex items-center gap-1 bg-slate-100 p-1 rounded-xl">
      <button 
        @click="$emit('change-view', 'map')" 
        :class="currentView === 'map' ? 'bg-white text-blue-700 shadow-sm font-semibold' : 'text-slate-600 hover:text-slate-900'"
        class="px-3 py-1 text-xs rounded-lg transition-all flex items-center gap-1.5">
        <i class="fa-solid fa-map-location-dot"></i>
        <span>Bản đồ</span>
      </button>
      <button 
        @click="$emit('change-view', 'dashboard')" 
        :class="currentView === 'dashboard' ? 'bg-white text-blue-700 shadow-sm font-semibold' : 'text-slate-600 hover:text-slate-900'"
        class="px-3 py-1 text-xs rounded-lg transition-all flex items-center gap-1.5">
        <i class="fa-solid fa-chart-pie"></i>
        <span>Thống kê</span>
      </button>
      <button 
        @click="$emit('change-view', 'table')" 
        :class="currentView === 'table' ? 'bg-white text-blue-700 shadow-sm font-semibold' : 'text-slate-600 hover:text-slate-900'"
        class="px-3 py-1 text-xs rounded-lg transition-all flex items-center gap-1.5">
        <i class="fa-solid fa-table-list"></i>
        <span>Danh sách</span>
      </button>
    </div>

    <!-- Action: Import Excel -->
    <button
      @click="$emit('open-excel-import')"
      class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 text-xs font-semibold transition-all">
      <i class="fa-solid fa-file-excel text-emerald-600"></i>
      <span>Nhập Excel</span>
    </button>

    <!-- User Profile Dropdown -->
    <div class="relative" ref="dropdownRef">
      <button 
        @click="isDropdownOpen = !isDropdownOpen"
        class="flex items-center gap-2 pl-1.5 pr-2.5 py-1 rounded-lg hover:bg-slate-100 transition-colors border border-transparent hover:border-slate-200">
        <div class="w-7 h-7 rounded-full bg-blue-700 text-white flex items-center justify-center text-xs font-bold flex-shrink-0 shadow-sm">
          {{ currentUser.avatar }}
        </div>
        <div class="text-left hidden sm:block">
          <div class="text-xs font-semibold text-slate-800 leading-none max-w-28 truncate">{{ currentUser.name }}</div>
          <div class="text-[10px] text-blue-600 font-medium leading-tight mt-0.5">{{ currentUser.roleLabel }}</div>
        </div>
        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 ml-0.5"></i>
      </button>

      <!-- Dropdown Menu -->
      <div 
        v-if="isDropdownOpen" 
        class="absolute right-0 top-11 w-56 bg-white border border-slate-200 rounded-xl shadow-xl py-1.5 z-50 animate-in fade-in zoom-in-95 duration-100">
        <div class="px-3 py-2 border-b border-slate-100">
          <p class="text-xs font-bold text-slate-800">{{ currentUser.name }}</p>
          <p class="text-[11px] text-slate-500">{{ currentUser.email }}</p>
          <span class="inline-block mt-1 px-2 py-0.5 bg-blue-50 text-blue-700 rounded text-[10px] font-semibold border border-blue-200">
            {{ currentUser.roleLabel }}
          </span>
        </div>

        <div class="px-2 py-1 text-[10px] uppercase font-semibold tracking-wider text-slate-400">Chuyển đổi vai trò demo</div>
        
        <button 
          @click="setRole('admin')"
          :class="currentUser.role === 'admin' ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-600 hover:bg-slate-50'"
          class="w-full text-left px-3 py-1.5 text-xs flex items-center justify-between rounded-md transition-colors">
          <span>Quản trị viên (Super Admin)</span>
          <i v-if="currentUser.role === 'admin'" class="fa-solid fa-check text-blue-600 text-xs"></i>
        </button>

        <button 
          @click="setRole('manager')"
          :class="currentUser.role === 'manager' ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-600 hover:bg-slate-50'"
          class="w-full text-left px-3 py-1.5 text-xs flex items-center justify-between rounded-md transition-colors">
          <span>Cán bộ QLGD (Editor)</span>
          <i v-if="currentUser.role === 'manager'" class="fa-solid fa-check text-blue-600 text-xs"></i>
        </button>

        <button 
          @click="setRole('viewer')"
          :class="currentUser.role === 'viewer' ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-600 hover:bg-slate-50'"
          class="w-full text-left px-3 py-1.5 text-xs flex items-center justify-between rounded-md transition-colors">
          <span>Khách tra cứu (Viewer)</span>
          <i v-if="currentUser.role === 'viewer'" class="fa-solid fa-check text-blue-600 text-xs"></i>
        </button>

        <div class="border-t border-slate-100 my-1 pt-1"></div>

        <button 
          @click="$emit('reset-data'); isDropdownOpen = false"
          class="w-full flex items-center gap-2.5 px-3 py-2 text-xs text-amber-700 hover:bg-amber-50 transition-colors">
          <i class="fa-solid fa-rotate-left w-4 text-center text-amber-500"></i>
          <span>Khôi phục dữ liệu gốc</span>
        </button>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  currentView: {
    type: String,
    default: 'map'
  }
})

const emit = defineEmits(['toggle-sidebar', 'change-view', 'open-excel-import', 'reset-data'])

const isDropdownOpen = ref(false)
const dropdownRef = ref(null)

const currentUser = ref({
  name: 'Nguyễn Văn Hòa',
  email: 'qly.csgd@ninhbinh.edu.vn',
  role: 'admin',
  roleLabel: 'Super Admin',
  avatar: 'H'
})

const currentViewTitle = computed(() => {
  switch (props.currentView) {
    case 'map': return 'Bản đồ tương tác mạng lưới cơ sở giáo dục'
    case 'dashboard': return 'Báo cáo & Thống kê tổng hợp số liệu giáo dục'
    case 'table': return 'Quản trị danh mục cơ sở giáo dục'
    default: return 'Bản đồ số Giáo dục Ninh Bình'
  }
})

function setRole(role) {
  if (role === 'admin') {
    currentUser.value = {
      name: 'Nguyễn Văn Hòa',
      email: 'admin.gis@ninhbinh.edu.vn',
      role: 'admin',
      roleLabel: 'Super Admin',
      avatar: 'H'
    }
  } else if (role === 'manager') {
    currentUser.value = {
      name: 'Trần Thị Mai',
      email: 'canbo.qldl@ninhbinh.edu.vn',
      role: 'manager',
      roleLabel: 'Cán bộ QLGD',
      avatar: 'M'
    }
  } else {
    currentUser.value = {
      name: 'Người xem công khai',
      email: 'khach.tracuu@ninhbinh.edu.vn',
      role: 'viewer',
      roleLabel: 'Khách tra cứu',
      avatar: 'K'
    }
  }
  isDropdownOpen.value = false
}

function handleClickOutside(event) {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
    isDropdownOpen.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>
