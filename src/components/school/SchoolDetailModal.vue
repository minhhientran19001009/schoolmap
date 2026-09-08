<template>
  <div 
    v-if="school"
    class="fixed inset-0 z-[2000] flex items-center justify-center p-3 sm:p-4 bg-slate-900/60 backdrop-blur-xs animate-in fade-in select-none">
    
    <div 
      class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] flex flex-col shadow-2xl border border-slate-200 overflow-hidden animate-in zoom-in-95 duration-150">
      
      <!-- Header Banner -->
      <div class="relative bg-gradient-to-r from-blue-900 to-indigo-800 text-white p-4 sm:p-5 flex-shrink-0">
        <button 
          @click="$emit('close')"
          class="absolute top-4 right-4 text-white/70 hover:text-white bg-white/10 hover:bg-white/20 w-8 h-8 rounded-full flex items-center justify-center transition-colors">
          <i class="fa-solid fa-xmark text-sm"></i>
        </button>

        <div class="flex items-center gap-2 mb-2">
          <span 
            class="px-2.5 py-0.5 rounded-full text-[11px] font-bold tracking-wide uppercase bg-white/20 text-white backdrop-blur-xs border border-white/20">
            {{ levelLabel }}
          </span>
        </div>

        <h2 class="text-lg sm:text-xl font-bold leading-snug">{{ school.name }}</h2>
        <p class="text-xs text-blue-100 flex items-center gap-1.5 mt-1">
          <i class="fa-solid fa-location-dot text-blue-300"></i>
          <span>{{ school.address }}</span>
        </p>
      </div>

      <!-- Tab Navigation -->
      <div class="flex border-b border-slate-200 bg-slate-50 px-4 text-xs font-semibold text-slate-600 flex-shrink-0">
        <button 
          @click="activeTab = 'info'"
          :class="activeTab === 'info' ? 'text-blue-800 border-b-2 border-blue-800 bg-white' : 'hover:text-slate-900'"
          class="px-4 py-3 transition-colors flex items-center gap-1.5">
          <i class="fa-solid fa-circle-info"></i>
          <span>Thông tin chung</span>
        </button>
        <button 
          @click="activeTab = 'stats'"
          :class="activeTab === 'stats' ? 'text-blue-800 border-b-2 border-blue-800 bg-white' : 'hover:text-slate-900'"
          class="px-4 py-3 transition-colors flex items-center gap-1.5">
          <i class="fa-solid fa-chart-line"></i>
          <span>Quy mô & Lịch sử</span>
        </button>
        <button 
          @click="activeTab = 'facilities'"
          :class="activeTab === 'facilities' ? 'text-blue-800 border-b-2 border-blue-800 bg-white' : 'hover:text-slate-900'"
          class="px-4 py-3 transition-colors flex items-center gap-1.5">
          <i class="fa-solid fa-school-flag"></i>
          <span>Cơ sở vật chất</span>
        </button>
        <button 
          @click="activeTab = 'gis'"
          :class="activeTab === 'gis' ? 'text-blue-800 border-b-2 border-blue-800 bg-white' : 'hover:text-slate-900'"
          class="px-4 py-3 transition-colors flex items-center gap-1.5">
          <i class="fa-solid fa-map-pin"></i>
          <span>Tọa độ & GIS</span>
        </button>
      </div>

      <!-- Tab Content (Scrollable) -->
      <div class="flex-1 overflow-y-auto p-4 sm:p-5 text-xs text-slate-700 space-y-4">
        
        <!-- TAB 1: THÔNG TIN CHUNG -->
        <div v-if="activeTab === 'info'" class="space-y-4">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-slate-50 p-3.5 rounded-xl border border-slate-200">
            <div>
              <span class="text-slate-400 block text-[11px]">Mã định danh trường:</span>
              <span class="font-bold text-slate-800 text-sm font-mono">{{ school.code }}</span>
            </div>
            <div>
              <span class="text-slate-400 block text-[11px]">Năm thành lập:</span>
              <span class="font-semibold text-slate-800">{{ school.founded_year || 'Đang cập nhật' }}</span>
            </div>
            <div>
              <span class="text-slate-400 block text-[11px]">Hiệu trưởng:</span>
              <span class="font-semibold text-slate-800">{{ school.principal || 'Đang cập nhật' }}</span>
            </div>
            <div>
              <span class="text-slate-400 block text-[11px]">Cơ quan quản lý trực tiếp:</span>
              <span class="font-semibold text-slate-800">
                {{ school.education_level === 'thpt' ? 'Sở GD&ĐT Tỉnh Ninh Bình' : `Phòng GD&ĐT ${school.district_name}` }}
              </span>
            </div>
            <div>
              <span class="text-slate-400 block text-[11px]">Xã / Phường:</span>
              <span class="font-semibold text-slate-800">{{ school.ward }}</span>
            </div>
            <div>
              <span class="text-slate-400 block text-[11px]">Khu vực trước sáp nhập:</span>
              <span class="font-semibold text-slate-800">{{ school.legacy_province || 'Ninh Bình' }}</span>
            </div>
          </div>

          <!-- Contact Details -->
          <div>
            <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-1.5">
              <i class="fa-solid fa-address-book text-blue-600"></i>
              <span>Thông tin liên hệ</span>
            </h4>
            <div class="space-y-2">
              <div class="flex items-center gap-2 text-slate-600">
                <i class="fa-solid fa-phone w-4 text-center text-slate-400"></i>
                <span class="font-medium">{{ school.phone || 'Chưa cập nhật số điện thoại' }}</span>
              </div>
              <div class="flex items-center gap-2 text-slate-600">
                <i class="fa-solid fa-envelope w-4 text-center text-slate-400"></i>
                <span class="font-medium">{{ school.email || 'Chưa cập nhật email' }}</span>
              </div>
              <div class="flex items-center gap-2 text-slate-600">
                <i class="fa-solid fa-globe w-4 text-center text-slate-400"></i>
                <a v-if="school.website" :href="school.website" target="_blank" class="text-blue-600 hover:underline font-medium truncate">
                  {{ school.website }}
                </a>
                <span v-else class="text-slate-400 italic">Chưa có trang thông tin điện tử</span>
              </div>
            </div>
          </div>

          <!-- Description -->
          <div v-if="school.description" class="pt-2 border-t border-slate-100">
            <h4 class="font-bold text-slate-800 mb-1">Giới thiệu tổng quan</h4>
            <p class="text-slate-600 leading-relaxed">{{ school.description }}</p>
          </div>
        </div>

        <!-- TAB 2: QUY MÔ & LỊCH SỬ -->
        <div v-if="activeTab === 'stats'" class="space-y-4">
          <!-- Quick Stat Cards -->
          <div class="grid grid-cols-3 gap-3">
            <div class="bg-blue-50 p-3 rounded-xl text-center border border-blue-100">
              <div class="text-xl sm:text-2xl font-black text-blue-800">{{ school.student_count?.toLocaleString('vi-VN') }}</div>
              <div class="text-[11px] text-blue-600 font-semibold mt-0.5">Tổng số học sinh</div>
            </div>
            <div class="bg-emerald-50 p-3 rounded-xl text-center border border-emerald-100">
              <div class="text-xl sm:text-2xl font-black text-emerald-800">{{ school.teacher_count }}</div>
              <div class="text-[11px] text-emerald-600 font-semibold mt-0.5">Cán bộ & Giáo viên</div>
            </div>
            <div class="bg-purple-50 p-3 rounded-xl text-center border border-purple-100">
              <div class="text-xl sm:text-2xl font-black text-purple-800">{{ school.class_count }}</div>
              <div class="text-[11px] text-purple-600 font-semibold mt-0.5">Số lớp học</div>
            </div>
          </div>

          <!-- Ratios -->
          <div class="bg-slate-50 p-3 rounded-xl border border-slate-200 grid grid-cols-2 gap-3 text-center">
            <div>
              <span class="text-slate-500 text-[11px] block">Tỷ lệ Học sinh / Giáo viên:</span>
              <span class="font-bold text-slate-800 text-sm">
                {{ school.teacher_count ? (school.student_count / school.teacher_count).toFixed(1) : '—' }} hs/gv
              </span>
            </div>
            <div>
              <span class="text-slate-500 text-[11px] block">Quy mô bình quân Lớp:</span>
              <span class="font-bold text-slate-800 text-sm">
                {{ school.class_count ? Math.round(school.student_count / school.class_count) : '—' }} hs/lớp
              </span>
            </div>
          </div>

          <!-- Historical Table (Section 28 of Plan) -->
          <div>
            <h4 class="font-bold text-slate-800 mb-2 flex items-center justify-between">
              <span>Theo dõi quy mô qua các năm học</span>
              <span class="text-[10px] text-slate-400 font-normal">Nguồn dữ liệu Sở GD&ĐT</span>
            </h4>
            <div class="border border-slate-200 rounded-xl overflow-hidden">
              <table class="w-full text-left text-xs">
                <thead class="bg-slate-100 text-slate-600 font-bold">
                  <tr>
                    <th class="py-2 px-3">Năm học</th>
                    <th class="py-2 px-3">Học sinh</th>
                    <th class="py-2 px-3">Giáo viên</th>
                    <th class="py-2 px-3">Số lớp</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                  <tr v-for="h in school.history_stats" :key="h.year" class="hover:bg-slate-50">
                    <td class="py-2 px-3 font-semibold">{{ h.year }}</td>
                    <td class="py-2 px-3 font-bold text-blue-800">{{ h.students?.toLocaleString('vi-VN') }}</td>
                    <td class="py-2 px-3">{{ h.teachers }}</td>
                    <td class="py-2 px-3">{{ h.classes }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- TAB 3: CƠ SỞ VẬT CHẤT (Section 29 of Plan) -->
        <div v-if="activeTab === 'facilities'" class="space-y-4">
          <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
              <div class="flex items-center gap-2 text-slate-500 mb-1">
                <i class="fa-solid fa-door-open text-blue-600"></i>
                <span class="text-[11px] font-semibold">Phòng học</span>
              </div>
              <div class="text-base font-bold text-slate-800">{{ school.classroom_count || 0 }} phòng</div>
              <div class="text-[10px] text-slate-400 mt-0.5">Kiên cố 100%</div>
            </div>

            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
              <div class="flex items-center gap-2 text-slate-500 mb-1">
                <i class="fa-solid fa-computer text-emerald-600"></i>
                <span class="text-[11px] font-semibold">Phòng tin học</span>
              </div>
              <div class="text-base font-bold text-slate-800">{{ school.computer_room_count || 0 }} phòng</div>
              <div class="text-[10px] text-slate-400 mt-0.5">Kết nối Internet</div>
            </div>

            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
              <div class="flex items-center gap-2 text-slate-500 mb-1">
                <i class="fa-solid fa-flask-vial text-purple-600"></i>
                <span class="text-[11px] font-semibold">Phòng thí nghiệm</span>
              </div>
              <div class="text-base font-bold text-slate-800">{{ school.lab_count || 0 }} phòng</div>
              <div class="text-[10px] text-slate-400 mt-0.5">Lý, Hóa, Sinh</div>
            </div>

            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
              <div class="flex items-center gap-2 text-slate-500 mb-1">
                <i class="fa-solid fa-book text-amber-600"></i>
                <span class="text-[11px] font-semibold">Thư viện trường</span>
              </div>
              <div class="text-base font-bold text-slate-800">{{ school.library ? 'Đạt chuẩn' : 'Chưa có' }}</div>
              <div class="text-[10px] text-slate-400 mt-0.5">Sách giáo khoa, tham khảo</div>
            </div>

            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
              <div class="flex items-center gap-2 text-slate-500 mb-1">
                <i class="fa-solid fa-ruler-combined text-rose-600"></i>
                <span class="text-[11px] font-semibold">Diện tích khuôn viên</span>
              </div>
              <div class="text-base font-bold text-slate-800">{{ school.area_m2?.toLocaleString('vi-VN') || 0 }} m²</div>
              <div class="text-[10px] text-slate-400 mt-0.5">
                ~ {{ school.student_count ? (school.area_m2 / school.student_count).toFixed(1) : 0 }} m²/học sinh
              </div>
            </div>

            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
              <div class="flex items-center gap-2 text-slate-500 mb-1">
                <i class="fa-solid fa-shield-halved text-teal-600"></i>
                <span class="text-[11px] font-semibold">Xác minh dữ liệu</span>
              </div>
              <div class="text-xs font-bold text-emerald-700">Đã đối soát</div>
              <div class="text-[10px] text-slate-400 mt-0.5">{{ school.last_verified_at || 'Mới cập nhật' }}</div>
            </div>
          </div>
        </div>

        <!-- TAB 4: GIS & VỊ TRÍ -->
        <div v-if="activeTab === 'gis'" class="space-y-4">
          <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200 space-y-2">
            <div class="flex items-center justify-between">
              <span class="text-slate-500">Tọa độ GPS (WGS84):</span>
              <span class="font-mono font-bold text-blue-800 text-xs">{{ school.lat }}, {{ school.lng }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-slate-500">Đơn vị hành chính:</span>
              <span class="font-semibold text-slate-800">{{ school.ward }}, {{ school.district_name }}</span>
            </div>
          </div>

          <div class="flex gap-2">
            <a 
              :href="`https://www.google.com/maps/dir/?api=1&destination=${school.lat},${school.lng}`" 
              target="_blank"
              class="flex-1 py-2.5 px-3 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-xl font-semibold flex items-center justify-center gap-2 transition-colors border border-blue-200">
              <i class="fa-solid fa-diamond-turn-right text-blue-600"></i>
              <span>Chỉ đường trên Google Maps</span>
            </a>
            <button 
              @click="$emit('buffer-analyze', school); $emit('close')"
              class="flex-1 py-2.5 px-3 bg-purple-50 text-purple-700 hover:bg-purple-100 rounded-xl font-semibold flex items-center justify-center gap-2 transition-colors border border-purple-200">
              <i class="fa-solid fa-bullseye text-purple-600"></i>
              <span>Phân tích bán kính 3km</span>
            </button>
          </div>
        </div>

      </div>

      <!-- Modal Footer -->
      <div class="p-3 bg-slate-50 border-t border-slate-200 flex items-center justify-between flex-shrink-0">
        <div class="text-[11px] text-slate-400">
          Cập nhật: <span class="font-medium text-slate-600">{{ school.last_verified_at || '02/09/2026' }}</span>
        </div>
        <div class="flex gap-2">
          <button 
            v-if="!isReadOnly"
            @click="$emit('edit', school)"
            class="px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-white border border-slate-300 text-slate-700 hover:bg-slate-100 transition-all flex items-center gap-1.5">
            <i class="fa-solid fa-pen-to-square text-xs text-blue-600"></i>
            <span>Chỉnh sửa</span>
          </button>
          <button 
            @click="$emit('close')"
            class="px-4 py-1.5 text-xs font-semibold rounded-lg bg-blue-800 hover:bg-blue-900 text-white transition-all">
            Đóng
          </button>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { LEVEL_MAP } from '../../services/schoolService'

const props = defineProps({
  school: {
    type: Object,
    default: null
  },
  isReadOnly: {
    type: Boolean,
    default: false
  }
})

defineEmits(['close', 'edit', 'buffer-analyze'])

const activeTab = ref('info')

const levelLabel = computed(() => {
  if (!props.school) return ''
  return LEVEL_MAP[props.school.education_level]?.label || props.school.education_level
})
</script>
