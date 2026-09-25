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
      
      <!-- Modal Header (Vieclam24h Style) -->
      <div class="p-4 sm:p-6 border-b border-slate-100 bg-[#faf8ff] flex items-start justify-between gap-3">
        <div class="flex items-start gap-3.5 min-w-0">
          <!-- Company Logo -->
          <div 
            :class="job.company_color || 'bg-[#3b1d74]'"
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

            <p class="text-xs sm:text-sm font-semibold text-[#6c3fb8] mt-0.5">
              {{ job.company }}
            </p>

            <div class="flex flex-wrap items-center gap-2 mt-2.5">
              <!-- Salary -->
              <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-blue-50 text-[#1d4ed8] font-bold text-xs sm:text-sm">
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
            <span class="w-1.5 h-4 bg-[#6c3fb8] rounded-full"></span>
            <span>Mô tả công việc</span>
          </h4>
          <ul class="space-y-1.5 leading-relaxed pl-4 list-disc marker:text-[#6c3fb8]">
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

        <!-- Section 4: Thông tin liên hệ & Ứng tuyển nhanh -->
        <div class="p-4 sm:p-5 rounded-xl bg-[#faf8ff] border border-[#e9e3ff]">
          <h4 class="text-sm font-bold text-[#3b1d74] flex items-center gap-2 mb-3">
            <i class="fa-solid fa-id-card text-[#6c3fb8]"></i>
            <span>Thông tin nhà tuyển dụng tại Ninh Bình</span>
          </h4>

          <div class="space-y-1.5 text-slate-700">
            <p><strong>Người liên hệ:</strong> {{ job.contact?.contact_person }}</p>
            <p><strong>Địa chỉ nộp hồ sơ:</strong> {{ job.contact?.address }}</p>
            <p v-if="job.contact?.email"><strong>Email:</strong> <a :href="`mailto:${job.contact?.email}`" class="text-[#6c3fb8] font-semibold underline">{{ job.contact?.email }}</a></p>
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
              class="flex-1 min-w-[140px] py-2.5 px-4 rounded-xl bg-[#0068ff] hover:bg-blue-700 text-white font-bold text-xs sm:text-sm flex items-center justify-center gap-2 shadow-xs transition-colors">
              <i class="fa-solid fa-comments"></i>
              <span>Nhắn tin Zalo</span>
            </a>
          </div>

          <!-- Fast Contact Form -->
          <div class="mt-5 pt-4 border-t border-[#e9e3ff]">
            <p class="text-xs font-bold text-slate-800 mb-2">Để lại thông tin để công ty liên hệ phỏng vấn:</p>
            <form @submit.prevent="submitApplication" class="space-y-2">
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                <input 
                  v-model="applicantName"
                  type="text" 
                  placeholder="Họ và tên của bạn *"
                  required
                  class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white focus:border-[#6c3fb8] focus:ring-1 focus:ring-purple-200 outline-none"
                />
                <input 
                  v-model="applicantPhone"
                  type="tel" 
                  placeholder="Số điện thoại liên hệ *"
                  required
                  class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white focus:border-[#6c3fb8] focus:ring-1 focus:ring-purple-200 outline-none"
                />
              </div>
              <input 
                v-model="applicantSchool"
                type="text" 
                placeholder="Trường hoặc nghề đào tạo (ví dụ: Cao đẳng Cơ điện, ngành Hàn...)"
                class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white focus:border-[#6c3fb8] focus:ring-1 focus:ring-purple-200 outline-none"
              />
              <button 
                type="submit"
                :disabled="isSubmitted"
                class="w-full py-2.5 rounded-xl bg-[#6c3fb8] hover:bg-[#5b32a0] text-white font-bold text-xs transition-colors shadow-xs flex items-center justify-center gap-2 cursor-pointer disabled:bg-slate-400">
                <i v-if="isSubmitted" class="fa-solid fa-check"></i>
                <i v-else class="fa-solid fa-paper-plane"></i>
                <span>{{ isSubmitted ? 'Đã gửi hồ sơ thành công!' : 'Ứng tuyển ngay' }}</span>
              </button>
            </form>
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
import { ref } from 'vue'

const props = defineProps({
  job: {
    type: Object,
    default: null
  }
})

defineEmits(['close'])

const applicantName = ref('')
const applicantPhone = ref('')
const applicantSchool = ref('')
const isSubmitted = ref(false)

function submitApplication() {
  if (!applicantName.value || !applicantPhone.value) return
  isSubmitted.value = true
  setTimeout(() => {
    applicantName.value = ''
    applicantPhone.value = ''
    applicantSchool.value = ''
    setTimeout(() => {
      isSubmitted.value = false
    }, 4000)
  }, 500)
}
</script>
