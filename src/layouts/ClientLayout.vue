<template>
  <div class="w-screen h-screen overflow-hidden flex flex-col bg-slate-50 font-sans select-none">
    <!-- Public Header (56px - 60px) - Modern Portal & Apple Segmented Control Aesthetic -->
    <header class="h-14 sm:h-[58px] bg-white/95 backdrop-blur-md border-b border-slate-200/90 z-40 flex items-center justify-between px-2 sm:px-4 md:px-6 gap-1.5 sm:gap-3 flex-shrink-0 shadow-xs sticky top-0 transition-all">
      
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

      <!-- Center / Primary Nav: Switch Tabs (Bản đồ vs Việc làm) -->
      <nav class="segmented-control-track inline-flex items-center p-0.5 sm:p-1 bg-slate-100/95 rounded-2xl flex-shrink-0 border border-slate-200/80 shadow-inner">
        <!-- Tab 1: Bản đồ GDNN -->
        <router-link 
          to="/map"
          :class="isMapRoute ? 'nav-tab-active-map' : 'nav-tab-inactive'"
          class="nav-tab-item px-2 sm:px-3 py-1 sm:py-1.5 text-xs rounded-xl transition-all flex items-center gap-1 sm:gap-1.5 cursor-pointer whitespace-nowrap">
          <i class="fa-solid fa-map-location-dot text-[11px] sm:text-xs" :class="isMapRoute ? 'text-blue-600' : 'text-slate-400'"></i>
          <span class="hidden md:inline">Bản đồ GDNN</span>
          <span class="md:hidden">Bản đồ</span>
        </router-link>

        <!-- Tab 2: Việc làm GDNN -->
        <router-link 
          to="/tuyen-dung"
          :class="isJobsRoute ? 'nav-tab-active-jobs' : 'nav-tab-inactive'"
          class="nav-tab-item px-2 sm:px-3 py-1 sm:py-1.5 text-xs rounded-xl transition-all flex items-center gap-1 sm:gap-1.5 cursor-pointer relative whitespace-nowrap">
          <i class="fa-solid fa-briefcase text-[11px] sm:text-xs" :class="isJobsRoute ? 'text-purple-600' : 'text-slate-400'"></i>
          <span class="hidden md:inline">Việc làm GDNN</span>
          <span class="md:hidden">Việc làm</span>
          <!-- Live Ping Notification Badge -->
          <span class="relative flex h-2 w-2 ml-0.5" title="Đang có tuyển dụng mới">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
          </span>
        </router-link>
      </nav>

      <!-- Center-Right Filter Area (Visible on Map view) -->
      <TopFilterBar v-if="isMapRoute" class="flex-1 md:flex-initial min-w-0" />
      <div v-else class="flex-1"></div>

      <!-- Right Action: Dành cho Nhà tuyển dụng (Responsive: Icon-only on mobile & small tablet, full on desktop) -->
      <div class="flex items-center gap-1.5 sm:gap-2.5 flex-shrink-0">
        <div class="h-5 w-px bg-slate-200 hidden sm:block"></div>
        
        <router-link 
          to="/admin" 
          class="employer-btn inline-flex items-center justify-center gap-1.5 sm:gap-2 text-xs font-bold text-purple-700 bg-purple-50 hover:bg-purple-100 active:scale-95 transition-all py-1.5 px-2.5 sm:px-3.5 rounded-xl border border-purple-200/80 shadow-xs hover:shadow-soft whitespace-nowrap flex-shrink-0"
          title="Kênh quản lý bài đăng dành cho Nhà tuyển dụng">
          <i class="fa-solid fa-user-tie text-xs text-purple-600 flex-shrink-0"></i>
          <span class="hidden xl:inline">Dành cho Nhà tuyển dụng</span>
          <span class="hidden md:inline xl:hidden">Nhà tuyển dụng</span>
        </router-link>
      </div>
    </header>

    <!-- Main Public Content View -->
    <main class="flex-1 w-full overflow-hidden relative">
      <router-view />
    </main>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import TopFilterBar from '../components/layout/TopFilterBar.vue'

const route = useRoute()
const isMapRoute = computed(() => !route.path || route.path === '/' || route.path.startsWith('/map'))
const isJobsRoute = computed(() => route.path.startsWith('/tuyen-dung') || route.path.startsWith('/viec-lam'))
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

.nav-tab-active-jobs {
  background-color: #ffffff;
  color: #6c3fb8 !important;
  font-weight: 700 !important;
  box-shadow: 0 1px 3px rgba(108, 63, 184, 0.12), 0 1px 2px rgba(0, 0, 0, 0.04);
  border: 1px solid rgba(243, 232, 255, 0.9);
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
</style>
