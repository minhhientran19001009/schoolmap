<template>
  <div class="absolute top-2.5 sm:top-3 right-2.5 sm:right-3 z-[1000] flex flex-col gap-1.5 sm:gap-2 select-none" ref="toolbarRef">
    
    <!-- Toggle Filter Panel Button -->
    <button 
      @click="$emit('toggle-filter-panel')"
      :class="isFilterPanelOpen ? 'bg-blue-800 text-white shadow-md ring-2 ring-blue-300' : 'bg-white text-slate-700 hover:bg-slate-50 shadow-sm'"
      class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl border border-slate-200/90 flex items-center justify-center transition-all cursor-pointer"
      title="Bật/Tắt Bộ lọc dữ liệu (Tìm kiếm, Cấp học, Địa bàn)">
      <i class="fa-solid fa-filter text-xs sm:text-sm"></i>
    </button>

    <div class="w-6 sm:w-8 mx-auto h-px bg-slate-200 my-0.5"></div>

    <!-- Base Layer Switcher Menu -->
    <div class="relative">
      <button 
        @click="toggleLayerMenu"
        :class="showLayerMenu ? 'bg-blue-800 text-white shadow-md' : 'bg-white text-slate-700 hover:bg-slate-50 shadow-sm'"
        class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl border border-slate-200/90 flex items-center justify-center transition-all cursor-pointer"
        title="Đổi nền bản đồ">
        <i class="fa-solid fa-map text-xs sm:text-sm"></i>
      </button>

      <div 
        v-if="showLayerMenu"
        class="absolute right-11 sm:right-12 top-0 bg-white border border-slate-200 rounded-xl shadow-xl p-2 w-48 sm:w-52 max-w-[calc(100vw-64px)] text-xs z-50 animate-in fade-in slide-in-from-right-1">
        <div class="font-bold text-slate-700 mb-2 px-1 text-[11px] uppercase tracking-wider">Lớp nền bản đồ</div>
        <div class="space-y-1">
          <button 
            @click="setBasemap('google_clean')"
            :class="activeBasemap === 'google_clean' ? 'bg-blue-50 text-blue-700 font-semibold border-blue-200' : 'text-slate-600 hover:bg-slate-50 border-transparent'"
            class="w-full text-left px-2.5 py-1.5 rounded-lg border flex items-center justify-between transition-colors cursor-pointer">
            <span>Google tối giản</span>
            <i v-if="activeBasemap === 'google_clean'" class="fa-solid fa-check text-xs"></i>
          </button>
          <button 
            @click="setBasemap('google_streets')"
            :class="activeBasemap === 'google_streets' ? 'bg-blue-50 text-blue-700 font-semibold border-blue-200' : 'text-slate-600 hover:bg-slate-50 border-transparent'"
            class="w-full text-left px-2.5 py-1.5 rounded-lg border flex items-center justify-between transition-colors cursor-pointer">
            <span>Đường phố Google</span>
            <i v-if="activeBasemap === 'google_streets'" class="fa-solid fa-check text-xs"></i>
          </button>
          <button 
            @click="setBasemap('satellite')"
            :class="activeBasemap === 'satellite' ? 'bg-blue-50 text-blue-700 font-semibold border-blue-200' : 'text-slate-600 hover:bg-slate-50 border-transparent'"
            class="w-full text-left px-2.5 py-1.5 rounded-lg border flex items-center justify-between transition-colors cursor-pointer">
            <span>Ảnh vệ tinh</span>
            <i v-if="activeBasemap === 'satellite'" class="fa-solid fa-check text-xs"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- Toggle Wards & Communes Boundaries -->
    <button 
      @click="$emit('toggle-wards-boundary')"
      :class="isWardsBoundaryActive ? 'bg-indigo-700 text-white shadow-md ring-2 ring-indigo-300' : 'bg-white text-slate-700 hover:bg-slate-50 shadow-sm'"
      class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl border border-slate-200/90 flex items-center justify-center transition-all cursor-pointer"
      title="Bật/Tắt Địa giới Xã/Phường Ninh Bình (129 xã/phường)">
      <i class="fa-solid fa-border-all text-xs sm:text-sm"></i>
    </button>

    <!-- Buffer Analysis Toggle -->
    <div class="relative">
      <button 
        @click="toggleBufferMenu"
        :class="isBufferActive ? 'bg-purple-600 text-white shadow-md ring-2 ring-purple-300' : 'bg-white text-slate-700 hover:bg-slate-50 shadow-sm'"
        class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl border border-slate-200/90 flex items-center justify-center transition-all cursor-pointer"
        title="Phân tích bán kính phục vụ (Buffer Analysis)">
        <i class="fa-solid fa-bullseye text-xs sm:text-sm"></i>
      </button>

      <div 
        v-if="showBufferMenu"
        class="absolute right-11 sm:right-12 top-0 bg-white border border-slate-200 rounded-xl shadow-xl p-2.5 w-48 sm:w-52 max-w-[calc(100vw-64px)] text-xs z-50 animate-in fade-in slide-in-from-right-1">
        <div class="font-bold text-slate-800 mb-1.5 text-[11px] uppercase tracking-wider flex items-center justify-between">
          <span>Bán kính đệm</span>
          <button 
            v-if="isBufferActive"
            @click="$emit('clear-buffer'); showBufferMenu = false"
            class="text-[10px] text-rose-600 hover:underline cursor-pointer">
            Tắt
          </button>
        </div>
        <p class="text-[11px] text-slate-500 mb-2">Chọn bán kính quét quanh trường:</p>
        <div class="grid grid-cols-3 gap-1">
          <button 
            v-for="r in [1, 3, 5]" 
            :key="r"
            @click="selectRadius(r)"
            :class="bufferRadius === r && isBufferActive ? 'bg-purple-700 text-white font-bold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
            class="py-1 text-center rounded-lg text-xs transition-colors cursor-pointer">
            {{ r }} km
          </button>
        </div>
      </div>
    </div>

    <div class="w-6 sm:w-8 mx-auto h-px bg-slate-200 my-0.5"></div>

    <!-- Locate User Position -->
    <button 
      @click="$emit('locate-user')"
      class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-white text-slate-700 hover:bg-slate-50 border border-slate-200/90 flex items-center justify-center shadow-sm transition-all cursor-pointer"
      title="Định vị vị trí hiện tại của tôi">
      <i class="fa-solid fa-crosshairs text-xs sm:text-sm text-blue-600"></i>
    </button>

    <!-- Reset to Ninh Binh Bounds -->
    <button 
      @click="$emit('reset-bounds')"
      class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-white text-slate-700 hover:bg-slate-50 border border-slate-200/90 flex items-center justify-center shadow-sm transition-all cursor-pointer"
      title="Xem toàn cảnh tỉnh Ninh Bình">
      <i class="fa-solid fa-arrows-to-eye text-xs sm:text-sm text-slate-600"></i>
    </button>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  isFilterPanelOpen: {
    type: Boolean,
    default: true
  },
  activeBasemap: {
    type: String,
    default: 'google_streets'
  },
  isWardsBoundaryActive: {
    type: Boolean,
    default: true
  },
  isHeatmapActive: {
    type: Boolean,
    default: false
  },
  isBufferActive: {
    type: Boolean,
    default: false
  },
  bufferRadius: {
    type: Number,
    default: 3
  }
})

const emit = defineEmits([
  'toggle-filter-panel',
  'change-basemap',
  'toggle-wards-boundary',
  'change-buffer-radius',
  'clear-buffer',
  'locate-user',
  'reset-bounds'
])

const toolbarRef = ref(null)
const showLayerMenu = ref(false)
const showBufferMenu = ref(false)

function toggleLayerMenu() {
  showLayerMenu.value = !showLayerMenu.value
  if (showLayerMenu.value) {
    showBufferMenu.value = false
  }
}

function toggleBufferMenu() {
  showBufferMenu.value = !showBufferMenu.value
  if (showBufferMenu.value) {
    showLayerMenu.value = false
  }
}

function setBasemap(type) {
  emit('change-basemap', type)
  showLayerMenu.value = false
}

function selectRadius(r) {
  emit('change-buffer-radius', r)
  showBufferMenu.value = false
}

function handleOutsideClick(e) {
  if (toolbarRef.value && !toolbarRef.value.contains(e.target)) {
    showLayerMenu.value = false
    showBufferMenu.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleOutsideClick)
})

onUnmounted(() => {
  document.removeEventListener('click', handleOutsideClick)
})
</script>
