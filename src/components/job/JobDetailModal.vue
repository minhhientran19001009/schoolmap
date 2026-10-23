<template>
  <div 
    v-if="job" 
    class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 md:p-6 select-text">
    
    <!-- Backdrop Overlay -->
    <div 
      @click="$emit('close')"
      class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity animate-fade-in"></div>

    <!-- Modal Content Box -->
    <div 
      class="relative w-full max-w-2xl max-h-[90vh] bg-white rounded-2xl shadow-2xl border border-slate-200 flex flex-col overflow-hidden z-10 animate-zoom-in">
      
      <!-- Modal Header (System Style) -->
      <div class="p-4 sm:p-6 border-b border-slate-100 bg-gradient-to-r from-blue-50/70 via-white to-indigo-50/40 flex items-start justify-between gap-3">
        <div class="flex items-start gap-3.5 min-w-0">
          <!-- Company Logo -->
          <div 
            :class="job.company_color || 'bg-blue-700'"
            class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl text-white font-black flex items-center justify-center flex-shrink-0 shadow-sm border border-slate-100 overflow-hidden p-1 text-center select-none">
            <span class="max-w-full truncate text-xs sm:text-sm font-black uppercase tracking-tight block">
              {{ job.company_logo || 'DN' }}
            </span>
          </div>

          <!-- Job & Company Info -->
          <div class="min-w-0">
            <div class="flex items-center gap-2 mb-1">
              <span 
                v-if="job.is_hot"
                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-orange-100 text-orange-700 text-[10px] font-bold">
                <i class="fa-solid fa-fire text-[9px]"></i>
                <span>Tuyển gấp</span>
              </span>
              <span class="text-xs text-slate-400 font-medium">Hạn nộp: {{ job.deadline }}</span>
            </div>

            <h2 class="text-base sm:text-lg font-bold text-slate-900 leading-snug">
              {{ job.title }}
            </h2>

            <p class="text-xs sm:text-sm font-semibold text-slate-600 mt-0.5">
              {{ job.company }}
            </p>

            <div class="flex flex-wrap items-center gap-2 mt-2.5">
              <!-- Salary -->
              <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 font-bold text-xs sm:text-sm">
                <i class="fa-solid fa-money-bill-wave text-emerald-600"></i>
                <span>{{ job.salary }}</span>
              </span>

              <!-- Location (Ward from CSDL) -->
              <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-medium text-xs">
                <i class="fa-solid fa-location-dot text-slate-400 text-[11px]"></i>
                <span>{{ job.ward_name }} • {{ job.address }}</span>
              </span>
            </div>
          </div>
        </div>

        <!-- Close Button -->
        <button 
          @click="$emit('close')"
          class="w-8 h-8 rounded-full bg-slate-200/70 hover:bg-slate-300 text-slate-600 flex items-center justify-center transition-colors flex-shrink-0 cursor-pointer"
          title="Đóng">
          <i class="fa-solid fa-xmark text-sm"></i>
        </button>
      </div>

      <!-- Scrollable Body (4 Core Sections) -->
      <div class="p-4 sm:p-6 overflow-y-auto space-y-6 flex-1 text-slate-700 text-xs sm:text-sm">
        
        <!-- Section 1: Mô tả công việc -->
        <div>
          <h4 class="text-sm font-bold text-slate-900 flex items-center gap-2 mb-2">
            <span class="w-1.5 h-4 bg-blue-600 rounded-full"></span>
            <span>Mô tả công việc</span>
          </h4>
          <ul class="space-y-1.5 leading-relaxed pl-4 list-disc marker:text-blue-600">
            <li v-for="(desc, idx) in job.description" :key="idx">
              {{ desc }}
            </li>
          </ul>
        </div>

        <!-- Section 2: Yêu cầu ứng viên -->
        <div>
          <h4 class="text-sm font-bold text-slate-900 flex items-center gap-2 mb-2">
            <span class="w-1.5 h-4 bg-orange-500 rounded-full"></span>
            <span>Yêu cầu ứng viên</span>
          </h4>
          <ul class="space-y-1.5 leading-relaxed pl-4 list-disc marker:text-orange-500">
            <li v-for="(req, idx) in job.requirements" :key="idx">
              {{ req }}
            </li>
          </ul>
        </div>

        <!-- Section 3: Quyền lợi & Đãi ngộ -->
        <div>
          <h4 class="text-sm font-bold text-slate-900 flex items-center gap-2 mb-2.5">
            <span class="w-1.5 h-4 bg-emerald-600 rounded-full"></span>
            <span>Quyền lợi & Đãi ngộ</span>
          </h4>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            <div 
              v-for="(b, idx) in job.benefits" 
              :key="idx"
              class="flex items-center gap-2 p-2.5 rounded-xl bg-emerald-50/70 border border-emerald-100 font-medium text-emerald-900 text-xs">
              <i class="fa-solid fa-circle-check text-emerald-600 text-sm flex-shrink-0"></i>
              <span>{{ b }}</span>
            </div>
          </div>
        </div>

        <!-- Section: Nơi có chương trình đào tạo (Mẫu Apps Script) -->
        <div v-if="job.training_schools && job.training_schools.length">
          <h4 class="text-sm font-bold text-slate-900 flex items-center justify-between mb-2.5">
            <span class="flex items-center gap-2">
              <span class="w-1.5 h-4 bg-indigo-600 rounded-full"></span>
              <span class="flex items-center gap-1.5">
                <i class="fa-solid fa-graduation-cap text-[#4f46e5]"></i>
                <span>Nơi có chương trình đào tạo kỹ năng</span>
              </span>
            </span>
            <span class="text-xs text-slate-400 font-normal">{{ job.training_schools.length }} cơ sở GDNN</span>
          </h4>
          <div class="space-y-2">
            <div 
              v-for="(school, sIdx) in job.training_schools" 
              :key="sIdx"
              class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/90 flex items-center justify-between gap-2 hover:bg-blue-50/50 transition-colors">
              <div class="flex items-start gap-2.5 min-w-0 pr-1">
                <i class="fa-solid fa-school text-indigo-600 text-xs mt-1 flex-shrink-0"></i>
                <div class="min-w-0">
                  <div class="font-bold text-xs text-slate-800 truncate" :title="school.name">{{ school.name }}</div>
                  <div class="text-[11px] text-slate-500 truncate">{{ school.level }} · {{ school.district }}</div>
                </div>
              </div>
              <router-link 
                :to="{ path: '/co-so-gdnn', query: { q: school.name } }"
                class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 font-bold text-xs hover:bg-blue-100 transition-colors flex-shrink-0">
                Xem
              </router-link>
            </div>
          </div>
        </div>

        <!-- Section 4: Thông tin liên hệ nhà tuyển dụng -->
        <div class="p-4 sm:p-5 rounded-xl bg-slate-50 border border-slate-200/90">
          <h4 class="text-sm font-bold text-slate-900 flex items-center gap-2 mb-3">
            <i class="fa-solid fa-id-card text-blue-600"></i>
            <span>Thông tin nhà tuyển dụng tại Ninh Bình</span>
          </h4>

          <div class="space-y-1.5 text-slate-700">
            <p><strong>Người liên hệ:</strong> {{ job.contact?.contact_person }}</p>
            <p><strong>Địa chỉ nộp hồ sơ:</strong> {{ job.contact?.address }}</p>
            <p v-if="job.contact?.email"><strong>Email:</strong> <a :href="`mailto:${job.contact?.email}`" class="text-blue-600 font-semibold underline">{{ job.contact?.email }}</a></p>
          </div>

          <!-- Direct Call & Zalo Buttons -->
          <div class="mt-4 flex flex-wrap gap-2.5">
            <a 
              :href="`tel:${job.contact?.phone}`"
              class="flex-1 min-w-[140px] py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm flex items-center justify-center gap-2 shadow-xs transition-colors">
              <i class="fa-solid fa-phone-volume"></i>
              <span>Gọi điện ngay</span>
            </a>

            <a 
              :href="`https://zalo.me/${job.contact?.zalo}`"
              target="_blank"
              rel="noopener noreferrer"
              class="flex-1 min-w-[140px] py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs sm:text-sm flex items-center justify-center gap-2 shadow-xs transition-colors">
              <i class="fa-solid fa-comments"></i>
              <span>Nhắn tin Zalo</span>
            </a>
          </div>

        </div>

      </div>

      <!-- Modal Footer -->
      <div class="p-3 sm:p-4 border-t border-slate-100 bg-slate-50 flex items-center justify-end">
        <button 
          @click="$emit('close')"
          class="px-5 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs transition-colors cursor-pointer">
          Đóng cửa sổ
        </button>
      </div>

    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  job: {
    type: Object,
    default: null
  }
})

defineEmits(['close'])
</script>
