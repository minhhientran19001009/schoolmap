<template>
  <div class="fixed inset-0 z-[2000] flex items-center justify-center p-3 sm:p-4 bg-slate-900/60 backdrop-blur-xs select-none">
    <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[92vh] flex flex-col shadow-2xl border border-slate-200 overflow-hidden animate-in zoom-in-95 duration-150">
      
      <!-- Header -->
      <div class="p-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between flex-shrink-0">
        <div class="flex items-center gap-2.5">
          <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center text-sm font-bold">
            <i class="fa-solid fa-file-excel"></i>
          </div>
          <div>
            <h3 class="font-bold text-slate-800 text-sm sm:text-base">
              Nhập dữ liệu cơ sở giáo dục từ file Excel
            </h3>
            <p class="text-[11px] text-slate-500">
              Hệ thống tự động kiểm tra trùng mã, thẩm tra tọa độ và địa giới hành chính tỉnh Ninh Bình
            </p>
          </div>
        </div>

        <button 
          @click="$emit('close')"
          class="text-slate-400 hover:text-slate-600 w-8 h-8 rounded-full flex items-center justify-center">
          <i class="fa-solid fa-xmark text-sm"></i>
        </button>
      </div>

      <!-- Content Area -->
      <div class="flex-1 overflow-y-auto p-4 sm:p-5 text-xs space-y-4">
        
        <!-- Step 1: Upload Box & Template Download -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
          
          <!-- Upload Area -->
          <div 
            @dragover.prevent="isDragging = true"
            @dragleave.prevent="isDragging = false"
            @drop.prevent="handleDrop"
            :class="isDragging ? 'border-blue-500 bg-blue-50/50' : 'border-slate-300 bg-slate-50 hover:bg-slate-100/50'"
            class="md:col-span-2 border-2 border-dashed rounded-2xl p-4 text-center transition-all flex flex-col items-center justify-center cursor-pointer min-h-[130px]"
            @click="triggerFileInput">
            
            <input 
              ref="fileInputRef" 
              type="file" 
              accept=".xlsx, .xls" 
              class="hidden" 
              @change="handleFileChange" 
            />

            <i class="fa-solid fa-cloud-arrow-up text-2xl text-emerald-600 mb-2"></i>
            <p class="font-bold text-slate-800">
              {{ currentFile ? currentFile.name : 'Kéo thả file Excel vào đây hoặc Nhấn để chọn' }}
            </p>
            <p class="text-[11px] text-slate-500 mt-0.5">
              Hỗ trợ định dạng .xlsx, .xls (Tối đa 5.000 dòng)
            </p>
          </div>

          <!-- Template Download & Rules Box -->
          <div class="bg-blue-50/70 p-3.5 rounded-2xl border border-blue-100 flex flex-col justify-between">
            <div>
              <div class="font-bold text-blue-900 mb-1 flex items-center gap-1.5">
                <i class="fa-solid fa-circle-question text-blue-600"></i>
                <span>Mẫu tệp chuẩn</span>
              </div>
              <p class="text-[11px] text-blue-800/80 leading-relaxed mb-3">
                Sử dụng đúng cấu trúc cột để đảm bảo việc thẩm tra dữ liệu và tọa độ GIS chính xác.
              </p>
            </div>

            <button 
              @click="downloadTemplate"
              class="w-full py-2 px-3 bg-white hover:bg-blue-100/80 text-blue-800 border border-blue-200 rounded-xl font-bold text-xs flex items-center justify-center gap-1.5 shadow-2xs transition-all">
              <i class="fa-solid fa-download text-blue-600"></i>
              <span>Tải file Excel mẫu</span>
            </button>
          </div>

        </div>

        <!-- Step 2: Validation Summary Dashboard (If parsed) -->
        <div v-if="summary" class="space-y-3">
          
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
            
            <div class="bg-slate-100 p-3 rounded-xl border border-slate-200 text-center">
              <span class="text-slate-500 text-[11px] block">Tổng số dòng đọc được</span>
              <span class="text-lg font-black text-slate-800">{{ summary.total }}</span>
            </div>

            <div class="bg-emerald-50 p-3 rounded-xl border border-emerald-200 text-center">
              <span class="text-emerald-700 text-[11px] block">Hợp lệ (Sẵn sàng)</span>
              <span class="text-lg font-black text-emerald-700">{{ summary.valid }}</span>
            </div>

            <div class="bg-rose-50 p-3 rounded-xl border border-rose-200 text-center">
              <span class="text-rose-700 text-[11px] block">Phát hiện lỗi</span>
              <span class="text-lg font-black text-rose-700">{{ summary.error }}</span>
            </div>

            <div class="bg-amber-50 p-3 rounded-xl border border-amber-200 text-center">
              <span class="text-amber-700 text-[11px] block">Cảnh báo thiếu tin</span>
              <span class="text-lg font-black text-amber-700">{{ summary.warning }}</span>
            </div>

          </div>

          <!-- Preview Data Table -->
          <div>
            <div class="flex items-center justify-between mb-1.5">
              <h4 class="font-bold text-slate-800 flex items-center gap-1.5">
                <i class="fa-solid fa-list-check text-blue-700"></i>
                <span>Xem trước kết quả thẩm tra dữ liệu</span>
              </h4>
              <div class="flex gap-2">
                <span class="text-[11px] text-slate-500">
                  <span class="inline-block w-2.5 h-2.5 rounded-full bg-rose-500 mr-1"></span>Dòng lỗi
                </span>
                <span class="text-[11px] text-slate-500">
                  <span class="inline-block w-2.5 h-2.5 rounded-full bg-amber-500 mr-1"></span>Cảnh báo
                </span>
                <span class="text-[11px] text-slate-500">
                  <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500 mr-1"></span>Hợp lệ
                </span>
              </div>
            </div>

            <div class="max-h-64 overflow-auto border border-slate-200 rounded-xl">
              <table class="w-full text-left text-xs">
                <thead class="bg-slate-100 text-slate-600 font-bold sticky top-0 z-10 border-b border-slate-200">
                  <tr>
                    <th class="py-2 px-2.5 text-center w-10">Dòng</th>
                    <th class="py-2 px-2.5">Mã trường</th>
                    <th class="py-2 px-2.5">Tên cơ sở giáo dục</th>
                    <th class="py-2 px-2.5">Cấp học</th>
                    <th class="py-2 px-2.5">Địa bàn</th>
                    <th class="py-2 px-2.5 text-center">Tọa độ Lat, Lng</th>
                    <th class="py-2 px-2.5">Tình trạng kiểm tra</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                  <tr 
                    v-for="row in parsedRows" 
                    :key="row.rowNumber"
                    :class="!row.isValid ? 'bg-rose-50/70' : (row.warnings.length > 0 ? 'bg-amber-50/50' : 'hover:bg-slate-50')">
                    
                    <td class="py-2 px-2.5 text-center font-mono text-slate-400 font-bold">
                      {{ row.rowNumber }}
                    </td>
                    <td class="py-2 px-2.5 font-mono font-semibold text-slate-800">
                      {{ row.code || '—' }}
                    </td>
                    <td class="py-2 px-2.5 font-bold text-slate-800">
                      {{ row.name }}
                    </td>
                    <td class="py-2 px-2.5 uppercase text-[10px] font-semibold text-slate-600">
                      {{ row.education_level }}
                    </td>
                    <td class="py-2 px-2.5 text-slate-600">
                      {{ row.district_name }}
                    </td>
                    <td class="py-2 px-2.5 text-center font-mono text-[11px]">
                      {{ row.lat }}, {{ row.lng }}
                    </td>
                    <td class="py-2 px-2.5">
                      <div v-if="!row.isValid" class="text-rose-700 text-[11px] font-medium space-y-0.5">
                        <div v-for="(err, eIdx) in row.errors" :key="eIdx" class="flex items-center gap-1">
                          <i class="fa-solid fa-triangle-exclamation text-rose-500"></i>
                          <span>{{ err }}</span>
                        </div>
                      </div>
                      <div v-else-if="row.warnings.length > 0" class="text-amber-700 text-[11px] font-medium space-y-0.5">
                        <div v-for="(w, wIdx) in row.warnings" :key="wIdx" class="flex items-center gap-1">
                          <i class="fa-solid fa-circle-exclamation text-amber-500"></i>
                          <span>{{ w }}</span>
                        </div>
                      </div>
                      <span v-else class="text-emerald-600 font-bold text-[11px] flex items-center gap-1">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Đạt chuẩn</span>
                      </span>
                    </td>

                  </tr>
                </tbody>
              </table>
            </div>
          </div>

        </div>

      </div>

      <!-- Footer -->
      <div class="p-3 bg-slate-50 border-t border-slate-200 flex items-center justify-between flex-shrink-0">
        <div class="text-xs text-slate-500">
          <span v-if="summary">
            Sẵn sàng nhập <strong class="text-emerald-700">{{ summary.valid }}</strong> dòng hợp lệ vào cơ sở dữ liệu
          </span>
          <span v-else>Vui lòng chọn file Excel để bắt đầu</span>
        </div>

        <div class="flex gap-2">
          <button 
            @click="$emit('close')"
            type="button"
            class="px-4 py-2 text-xs font-semibold rounded-xl bg-white border border-slate-300 text-slate-700 hover:bg-slate-100 transition-all">
            Đóng
          </button>

          <button 
            v-if="summary && summary.valid > 0"
            @click="confirmImport"
            type="button"
            class="px-5 py-2 text-xs font-bold rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white shadow-sm flex items-center gap-1.5 transition-all">
            <i class="fa-solid fa-check"></i>
            <span>Xác nhận nhập {{ summary.valid }} trường</span>
          </button>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { excelService } from '../../services/excelService'

const props = defineProps({
  existingSchools: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['close', 'import-complete'])

const fileInputRef = ref(null)
const currentFile = ref(null)
const isDragging = ref(false)
const parsedRows = ref([])
const summary = ref(null)

function triggerFileInput() {
  fileInputRef.value?.click()
}

function downloadTemplate() {
  excelService.downloadSampleTemplate()
}

function handleDrop(e) {
  isDragging.value = false
  const files = e.dataTransfer.files
  if (files.length > 0) {
    processFile(files[0])
  }
}

function handleFileChange(e) {
  const files = e.target.files
  if (files && files.length > 0) {
    processFile(files[0])
  }
}

async function processFile(file) {
  currentFile.value = file
  try {
    const result = await excelService.parseAndValidateExcel(file, props.existingSchools)
    parsedRows.value = result.rows
    summary.value = result.summary
  } catch (err) {
    alert(err.message)
  }
}

function confirmImport() {
  if (!parsedRows.value || parsedRows.value.length === 0) return

  const validSchoolsToImport = parsedRows.value
    .filter(r => r.isValid)
    .map(r => ({
      id: 'NB-' + (r.education_level || 'SCH').toUpperCase() + '-' + r.code,
      code: r.code,
      name: r.name,
      education_level: r.education_level,
      school_type: r.school_type,
      district_id: r.district_id,
      district_name: r.district_name,
      ward: r.ward,
      address: r.address,
      lat: r.lat,
      lng: r.lng,
      phone: r.phone,
      email: r.email,
      website: r.website,
      principal: r.principal,
      student_count: r.student_count,
      teacher_count: r.teacher_count,
      class_count: r.class_count,
      classroom_count: r.classroom_count,
      area_m2: r.area_m2,
      is_national_standard: false,
      national_standard_level: 0,
      status: 'VERIFIED',
      last_verified_at: new Date().toISOString().split('T')[0]
    }))

  emit('import-complete', validSchoolsToImport)
  emit('close')
}
</script>
