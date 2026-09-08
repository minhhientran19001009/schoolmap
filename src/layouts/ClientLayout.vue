<template>
  <div class="w-screen h-screen overflow-hidden flex flex-col bg-slate-50 font-sans select-none">
    <!-- Public Header (56px) -->
    <header class="h-14 bg-white border-b border-slate-200 z-30 flex items-center px-2.5 sm:px-4 md:px-6 gap-2 sm:gap-3 flex-shrink-0 shadow-2xs">
      <!-- Logo and Brand -->
      <router-link to="/map" class="flex items-center gap-2 flex-shrink-0 group">
        <div class="w-8 h-8 rounded-xl bg-blue-800 text-white flex items-center justify-center font-bold text-sm shadow-sm group-hover:scale-105 transition-transform flex-shrink-0">
          <i class="fa-solid fa-graduation-cap"></i>
        </div>
        <div class="flex flex-col">
          <span class="font-bold text-slate-800 text-xs sm:text-base leading-tight group-hover:text-blue-800 transition-colors">
            <span class="sm:hidden">Bản đồ Ninh Bình</span>
            <span class="hidden sm:inline">Bản đồ số Giáo dục Ninh Bình</span>
          </span>
          <span class="text-[10px] text-slate-500 font-medium hidden md:block">
            Cổng Thông tin Tra cứu Mạng lưới Cơ sở Giáo dục
          </span>
        </div>
      </router-link>

      <!-- Top Filter Bar (Search, District, School Type) -->
      <TopFilterBar />
    </header>

    <!-- Main Public Content View -->
    <main class="flex-1 w-full overflow-hidden relative">
      <router-view />
    </main>
  </div>
</template>

<script setup>
import TopFilterBar from '../components/layout/TopFilterBar.vue'
</script>
