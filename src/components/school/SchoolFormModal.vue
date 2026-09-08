<template>
  <div class="fixed inset-0 z-[2000] flex items-center justify-center p-3 sm:p-4 bg-slate-900/60 backdrop-blur-xs select-none">
    <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[92vh] flex flex-col shadow-2xl border border-slate-200 overflow-hidden animate-in zoom-in-95 duration-150">
      
      <!-- Modal Header -->
      <div class="p-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between flex-shrink-0">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-800 flex items-center justify-center text-sm font-bold">
            <i :class="isEdit ? 'fa-solid fa-pen' : 'fa-solid fa-plus'"></i>
          </div>
          <div>
            <h3 class="font-bold text-slate-800 text-sm sm:text-base">
              {{ isEdit ? 'Cập nhật cơ sở giáo dục' : 'Thêm mới cơ sở giáo dục' }}
            </h3>
            <p class="text-[11px] text-slate-500">Nhập đầy đủ thông tin chuẩn hóa theo quy định của Sở GD&ĐT</p>
          </div>
        </div>
        <button 
          @click="$emit('close')"
          class="text-slate-400 hover:text-slate-600 w-8 h-8 rounded-full flex items-center justify-center">
          <i class="fa-solid fa-xmark text-sm"></i>
        </button>
      </div>

      <!-- Form Fields (Scrollable) -->
      <form @submit.prevent="handleSubmit" class="flex-1 overflow-y-auto p-4 sm:p-5 text-xs space-y-3.5">
        
        <!-- Row 1: Code & Name -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
          <div>
            <label class="block font-semibold text-slate-700 mb-1">Mã trường <span class="text-rose-500">*</span></label>
            <input 
              v-model="formData.code"
              type="text" 
              required
              placeholder="VD: 37050"
              class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
            />
          </div>
          <div class="sm:col-span-2">
            <label class="block font-semibold text-slate-700 mb-1">Tên cơ sở giáo dục <span class="text-rose-500">*</span></label>
            <input 
              v-model="formData.name"
              type="text" 
              required
              placeholder="VD: Trường THPT Ninh Bình"
              class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
            />
          </div>
        </div>

        <!-- Row 2: Level & Type -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div>
            <label class="block font-semibold text-slate-700 mb-1">Cấp học <span class="text-rose-500">*</span></label>
            <select 
              v-model="formData.education_level"
              class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:border-blue-500 outline-none">
              <option value="mam_non">Mầm non</option>
              <option value="tieu_hoc">Tiểu học</option>
              <option value="thcs">THCS</option>
              <option value="thpt">THPT</option>
              <option value="gdtx">GDTX - GDNN</option>
              <option value="cao_dang">Cao đẳng</option>
              <option value="dai_hoc">Đại học</option>
            </select>
          </div>
          <div>
            <label class="block font-semibold text-slate-700 mb-1">Loại hình <span class="text-rose-500">*</span></label>
            <select 
              v-model="formData.school_type"
              class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:border-blue-500 outline-none">
              <option value="cong_lap">Công lập</option>
              <option value="tu_thuc">Tư thục / Ngoài công lập</option>
            </select>
          </div>
        </div>

        <!-- Row 3: District & Ward -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div>
            <label class="block font-semibold text-slate-700 mb-1">Huyện / Thành phố <span class="text-rose-500">*</span></label>
            <select 
              v-model="formData.district_id"
              @change="onDistrictChange"
              class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:border-blue-500 outline-none">
              <option v-for="d in districts" :key="d.id" :value="d.id">
                {{ d.name }}
              </option>
            </select>
          </div>
          <div>
            <label class="block font-semibold text-slate-700 mb-1">Xã / Phường / Thị trấn <span class="text-rose-500">*</span></label>
            <input 
              v-model="formData.ward"
              type="text"
              required
              placeholder="VD: Phường Đông Thành"
              class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:border-blue-500 outline-none"
            />
          </div>
        </div>

        <!-- Row 4: Address -->
        <div>
          <label class="block font-semibold text-slate-700 mb-1">Địa chỉ chi tiết <span class="text-rose-500">*</span></label>
          <input 
            v-model="formData.address"
            type="text"
            required
            placeholder="Số nhà, tên đường, phố..."
            class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:border-blue-500 outline-none"
          />
        </div>

        <!-- Row 5: Coordinates (Lat / Lng) with Fast Picker -->
        <div class="bg-blue-50/70 p-3 rounded-xl border border-blue-100 space-y-2">
          <div class="flex items-center justify-between">
            <span class="font-bold text-blue-900 flex items-center gap-1.5">
              <i class="fa-solid fa-map-pin text-blue-600"></i>
              <span>Tọa độ địa lý (WGS84)</span>
            </span>
            <span class="text-[10px] text-blue-700">Tỉnh Ninh Bình: Lat ~20.0-20.5, Lng ~105.7-106.2</span>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-slate-600 mb-1">Vĩ độ (Latitude) <span class="text-rose-500">*</span></label>
              <input 
                v-model.number="formData.lat"
                type="number"
                step="0.0001"
                required
                placeholder="VD: 20.2530"
                class="w-full px-3 py-1.5 bg-white border border-slate-300 rounded-lg focus:border-blue-500 outline-none font-mono"
              />
            </div>
            <div>
              <label class="block text-slate-600 mb-1">Kinh độ (Longitude) <span class="text-rose-500">*</span></label>
              <input 
                v-model.number="formData.lng"
                type="number"
                step="0.0001"
                required
                placeholder="VD: 105.9750"
                class="w-full px-3 py-1.5 bg-white border border-slate-300 rounded-lg focus:border-blue-500 outline-none font-mono"
              />
            </div>
          </div>
        </div>

        <!-- Row 6: Scale (Students, Teachers, Classes) -->
        <div class="grid grid-cols-3 gap-3">
          <div>
            <label class="block font-semibold text-slate-700 mb-1">Số học sinh</label>
            <input 
              v-model.number="formData.student_count"
              type="number"
              min="0"
              class="w-full px-3 py-1.5 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white outline-none"
            />
          </div>
          <div>
            <label class="block font-semibold text-slate-700 mb-1">Số giáo viên</label>
            <input 
              v-model.number="formData.teacher_count"
              type="number"
              min="0"
              class="w-full px-3 py-1.5 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white outline-none"
            />
          </div>
          <div>
            <label class="block font-semibold text-slate-700 mb-1">Số lớp học</label>
            <input 
              v-model.number="formData.class_count"
              type="number"
              min="0"
              class="w-full px-3 py-1.5 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white outline-none"
            />
          </div>
        </div>

        <!-- Row 7: Contact Info -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
          <div>
            <label class="block font-semibold text-slate-700 mb-1">Số điện thoại</label>
            <input 
              v-model="formData.phone"
              type="text"
              placeholder="0229..."
              class="w-full px-3 py-1.5 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white outline-none"
            />
          </div>
          <div>
            <label class="block font-semibold text-slate-700 mb-1">Email</label>
            <input 
              v-model="formData.email"
              type="email"
              placeholder="truong@ninhbinh.edu.vn"
              class="w-full px-3 py-1.5 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white outline-none"
            />
          </div>
          <div>
            <label class="block font-semibold text-slate-700 mb-1">Hiệu trưởng</label>
            <input 
              v-model="formData.principal"
              type="text"
              placeholder="Họ và tên..."
              class="w-full px-3 py-1.5 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white outline-none"
            />
          </div>
        </div>

        <!-- Row 8: Facilities & Standard -->
        <div class="flex items-center gap-4 pt-1">
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" v-model="formData.is_national_standard" class="rounded text-blue-600">
            <span class="font-semibold text-slate-700">Đạt chuẩn quốc gia</span>
          </label>
          <div v-if="formData.is_national_standard" class="flex items-center gap-1.5">
            <span class="text-slate-500">Mức độ:</span>
            <select v-model.number="formData.national_standard_level" class="px-2 py-0.5 border rounded">
              <option :value="1">Mức độ 1</option>
              <option :value="2">Mức độ 2</option>
            </select>
          </div>
        </div>
      </form>

      <!-- Modal Actions -->
      <div class="p-3 bg-slate-50 border-t border-slate-200 flex items-center justify-end gap-2 flex-shrink-0">
        <button 
          @click="$emit('close')"
          type="button"
          class="px-4 py-2 text-xs font-semibold rounded-xl bg-white border border-slate-300 text-slate-700 hover:bg-slate-100 transition-all">
          Hủy bỏ
        </button>
        <button 
          @click="handleSubmit"
          type="button"
          class="px-5 py-2 text-xs font-semibold rounded-xl bg-blue-800 hover:bg-blue-900 text-white shadow-sm transition-all flex items-center gap-1.5">
          <i class="fa-solid fa-floppy-disk text-xs"></i>
          <span>Lưu thông tin</span>
        </button>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
  schoolToEdit: {
    type: Object,
    default: null
  },
  districts: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['close', 'save'])

const isEdit = computed(() => !!props.schoolToEdit)

const formData = ref({
  code: '',
  name: '',
  education_level: 'thpt',
  school_type: 'cong_lap',
  district_id: 'tp-ninh-binh',
  district_name: 'Thành phố Ninh Bình',
  ward: 'Phường Đông Thành',
  address: '',
  lat: 20.2530,
  lng: 105.9750,
  phone: '',
  email: '',
  website: '',
  principal: '',
  student_count: 500,
  teacher_count: 40,
  class_count: 15,
  classroom_count: 18,
  area_m2: 10000,
  is_national_standard: true,
  national_standard_level: 1,
  status: 'VERIFIED'
})

watch(() => props.schoolToEdit, (val) => {
  if (val) {
    formData.value = { ...val }
  }
}, { immediate: true })

function onDistrictChange() {
  const d = props.districts.find(item => item.id === formData.value.district_id)
  if (d) {
    formData.value.district_name = d.name
    if (d.center && !isEdit.value) {
      formData.value.lat = d.center[0]
      formData.value.lng = d.center[1]
    }
  }
}

function handleSubmit() {
  if (!formData.value.name || !formData.value.code) {
    alert('Vui lòng nhập đầy đủ Mã trường và Tên trường!')
    return
  }
  emit('save', { ...formData.value })
}
</script>
