import * as XLSX from 'xlsx'

export const excelService = {
  /**
   * Export given school list to an Excel workbook (.xlsx)
   */
  exportSchoolsToExcel(schools, filename = 'Danh_sach_co_so_giao_duc_Ninh_Binh.xlsx') {
    const formattedData = schools.map((s, idx) => ({
      'STT': idx + 1,
      'Mã trường': s.code,
      'Tên trường': s.name,
      'Cấp học': s.education_level,
      'Loại hình': s.school_type === 'cong_lap' ? 'Công lập' : 'Tư thục',
      'Huyện/Thành phố': s.district_name,
      'Xã/Phường': s.ward,
      'Địa chỉ': s.address,
      'Vĩ độ (Lat)': s.lat,
      'Kinh độ (Lng)': s.lng,
      'Số điện thoại': s.phone || '',
      'Email': s.email || '',
      'Website': s.website || '',
      'Hiệu trưởng': s.principal || '',
      'Chuẩn QG': s.is_national_standard ? `Mức ${s.national_standard_level}` : 'Chưa đạt',
      'Số học sinh': s.student_count || 0,
      'Số giáo viên': s.teacher_count || 0,
      'Số lớp': s.class_count || 0,
      'Số phòng học': s.classroom_count || 0,
      'Diện tích (m2)': s.area_m2 || 0,
      'Trạng thái': s.status || 'VERIFIED'
    }))

    const worksheet = XLSX.utils.json_to_sheet(formattedData)
    const workbook = XLSX.utils.book_new()
    XLSX.utils.book_append_sheet(workbook, worksheet, 'DS_Truong_Hoc')
    XLSX.writeFile(workbook, filename)
  },

  /**
   * Generate and download sample template for data import
   */
  downloadSampleTemplate() {
    const sampleRows = [
      {
        'Mã trường': '37099',
        'Tên trường': 'Trường Cao đẳng Công nghệ Mẫu',
        'Cấp học (gdtx/cao_dang/dai_hoc)': 'cao_dang',
        'Loại hình (cong_lap/tu_thuc)': 'cong_lap',
        'Huyện/Thành phố': 'Thành phố Ninh Bình',
        'Xã/Phường': 'Phường Đông Thành',
        'Địa chỉ': 'Số 10 Lê Hồng Phong, TP. Ninh Bình',
        'Vĩ độ (Lat)': 20.2605,
        'Kinh độ (Lng)': 105.9750,
        'Số điện thoại': '0229 387 0000',
        'Email': 'thpt.demo@ninhbinh.edu.vn',
        'Website': 'https://demo.ninhbinh.edu.vn',
        'Hiệu trưởng': 'Nguyễn Văn A',
        'Số học sinh': 1200,
        'Số giáo viên': 75,
        'Số lớp': 28,
        'Số phòng học': 30,
        'Diện tích (m2)': 15000
      },
      {
        'Mã trường': '37100',
        'Tên trường': 'Trung tâm GDNN - GDTX Mẫu',
        'Cấp học (gdtx/cao_dang/dai_hoc)': 'gdtx',
        'Loại hình (cong_lap/tu_thuc)': 'tu_thuc',
        'Huyện/Thành phố': 'Thành phố Tam Điệp',
        'Xã/Phường': 'Phường Bắc Sơn',
        'Địa chỉ': 'Đường Quang Trung, TP. Tam Điệp',
        'Vĩ độ (Lat)': 20.1560,
        'Kinh độ (Lng)': 105.9020,
        'Số điện thoại': '0229 386 1111',
        'Email': 'mn.anhduong@gmail.com',
        'Website': '',
        'Hiệu trưởng': 'Trần Thị B',
        'Số học sinh': 350,
        'Số giáo viên': 26,
        'Số lớp': 12,
        'Số phòng học': 14,
        'Diện tích (m2)': 3500
      }
    ]

    const worksheet = XLSX.utils.json_to_sheet(sampleRows)
    const workbook = XLSX.utils.book_new()
    XLSX.utils.book_append_sheet(workbook, worksheet, 'Mau_Import_Truong_Hoc')
    XLSX.writeFile(workbook, 'Mau_Import_Co_So_Giao_Duc_Ninh_Binh.xlsx')
  },

  /**
   * Parse uploaded Excel file and validate data against rules in Plan section 24-25
   * @param {File} file 
   * @param {Array} existingSchools 
   * @returns {Promise<Object>}
   */
  parseAndValidateExcel(file, existingSchools = []) {
    return new Promise((resolve, reject) => {
      const reader = new FileReader()

      reader.onload = (e) => {
        try {
          const data = new Uint8Array(e.target.result)
          const workbook = XLSX.read(data, { type: 'array' })
          const firstSheetName = workbook.SheetNames[0]
          const worksheet = workbook.Sheets[firstSheetName]
          const rawRows = XLSX.utils.sheet_to_json(worksheet, { defval: '' })

          if (rawRows.length === 0) {
            resolve({
              success: false,
              message: 'Tập tin Excel không có dữ liệu!',
              rows: [],
              summary: { total: 0, valid: 0, error: 0, warning: 0 }
            })
            return
          }

          const existingCodes = new Set(existingSchools.map(s => String(s.code).trim()))
          const seenCodesInFile = new Set()

          let validCount = 0
          let errorCount = 0
          let warningCount = 0

          const parsedRows = rawRows.map((row, index) => {
            const rowNumber = index + 2 // header is row 1
            const errors = []
            const warnings = []

            // Extract fields with multiple key variations
            const code = String(row['Mã trường'] || row['code'] || row['Ma truong'] || '').trim()
            const name = String(row['Tên trường'] || row['name'] || row['Ten truong'] || '').trim()
            let level = String(row['Cấp học (gdtx/cao_dang/dai_hoc)'] || row['Cấp học (mam_non/tieu_hoc/thcs/thpt/gdtx/cao_dang/dai_hoc)'] || row['Cấp học'] || row['level'] || '').trim().toLowerCase()
            let type = String(row['Loại hình (cong_lap/tu_thuc)'] || row['Loại hình'] || row['type'] || '').trim().toLowerCase()
            const district = String(row['Huyện/Thành phố'] || row['district'] || '').trim()
            const ward = String(row['Xã/Phường'] || row['ward'] || '').trim()
            const address = String(row['Địa chỉ'] || row['address'] || '').trim()
            const latRaw = row['Vĩ độ (Lat)'] || row['lat'] || row['latitude']
            const lngRaw = row['Kinh độ (Lng)'] || row['lng'] || row['longitude']
            const phone = String(row['Số điện thoại'] || row['phone'] || '').trim()
            const email = String(row['Email'] || row['email'] || '').trim()
            const website = String(row['Website'] || row['website'] || '').trim()
            const principal = String(row['Hiệu trưởng'] || row['principal'] || '').trim()
            const students = parseInt(row['Số học sinh'] || row['students'] || 0) || 0
            const teachers = parseInt(row['Số giáo viên'] || row['teachers'] || 0) || 0
            const classes = parseInt(row['Số lớp'] || row['classes'] || 0) || 0
            const classrooms = parseInt(row['Số phòng học'] || row['classrooms'] || 0) || 0
            const area = parseInt(row['Diện tích (m2)'] || row['area'] || 0) || 0

            // Normalization
            if (type.includes('công lập') || type === 'cl') type = 'cong_lap'
            if (type.includes('tư thục') || type.includes('ngoài công lập') || type === 'tt') type = 'tu_thuc'
            if (!type) type = 'cong_lap'

            if (level.includes('mầm non')) level = 'mam_non'
            else if (level.includes('tiểu học')) level = 'tieu_hoc'
            else if (level.includes('thcs')) level = 'thcs'
            else if (level.includes('thpt')) level = 'thpt'
            else if (level.includes('gdtx') || level.includes('nghề')) level = 'gdtx'
            else if (level.includes('cao đẳng')) level = 'cao_dang'
            else if (level.includes('đại học')) level = 'dai_hoc'

            // RULE 1: Code validation
            if (!code) {
              errors.push('Thiếu mã trường (bắt buộc)')
            } else if (seenCodesInFile.has(code)) {
              errors.push(`Trùng lặp mã trường "${code}" trong cùng file Excel`)
            } else if (existingCodes.has(code)) {
              warnings.push(`Mã trường "${code}" đã tồn tại trong hệ thống (sẽ ghi đè/cập nhật)`)
            }
            if (code) seenCodesInFile.add(code)

            // RULE 2: Name validation
            if (!name) {
              errors.push('Thiếu tên cơ sở giáo dục')
            }

            // RULE 3: Level validation
            const validLevels = ['gdtx', 'gdnn', 'cao_dang', 'dai_hoc']
            if (!level || !validLevels.includes(level)) {
              errors.push(`Cấp học "${level}" không hợp lệ (hệ thống chỉ hỗ trợ 3 cấp học: gdtx, cao_dang, dai_hoc)`)
            }

            // RULE 4: Lat/Lng validation
            const lat = parseFloat(latRaw)
            const lng = parseFloat(lngRaw)

            if (isNaN(lat) || isNaN(lng) || !latRaw || !lngRaw) {
              errors.push('Tọa độ (Vĩ độ / Kinh độ) không hợp lệ hoặc để trống')
            } else {
              // Check bounds: Ninh Binh is roughly Lat 19.95 - 20.50, Lng 105.60 - 106.30
              if (lat < 19.95 || lat > 20.50 || lng < 105.60 || lng > 106.30) {
                errors.push(`Tọa độ [${lat}, ${lng}] nằm ngoài phạm vi địa giới tỉnh Ninh Bình (Lat: 20.0-20.5, Lng: 105.6-106.2)`)
              }
            }

            // RULE 5: Warnings for missing contact info
            if (!phone) {
              warnings.push('Chưa có số điện thoại liên hệ')
            }
            if (!email) {
              warnings.push('Chưa có email đơn vị')
            }

            const isValid = errors.length === 0

            if (isValid) {
              validCount++
            } else {
              errorCount++
            }
            if (warnings.length > 0) {
              warningCount++
            }

            return {
              rowNumber,
              code,
              name,
              education_level: level,
              school_type: type,
              district_name: district || 'Thành phố Ninh Bình',
              district_id: 'tp-ninh-binh',
              ward: ward || 'Đang cập nhật',
              address: address || 'Đang cập nhật',
              lat: isNaN(lat) ? 20.25 : lat,
              lng: isNaN(lng) ? 105.97 : lng,
              phone,
              email,
              website,
              principal,
              student_count: students,
              teacher_count: teachers,
              class_count: classes,
              classroom_count: classrooms,
              area_m2: area,
              is_national_standard: false,
              national_standard_level: 0,
              status: isValid ? 'VERIFIED' : 'NEED_REVIEW',
              isValid,
              errors,
              warnings
            }
          })

          resolve({
            success: true,
            rows: parsedRows,
            summary: {
              total: parsedRows.length,
              valid: validCount,
              error: errorCount,
              warning: warningCount
            }
          })
        } catch (err) {
          reject(new Error('Lỗi khi đọc file Excel: ' + err.message))
        }
      }

      reader.onerror = () => reject(new Error('Không thể đọc tệp tin đã chọn.'))
      reader.readAsArrayBuffer(file)
    })
  }
}
