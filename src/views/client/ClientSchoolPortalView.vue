<template>
  <div class="h-full overflow-y-auto bg-slate-50 selection:bg-emerald-100 selection:text-emerald-900">
    <section class="relative overflow-hidden border-b border-slate-200/90 bg-gradient-to-br from-emerald-50 via-teal-50/60 to-slate-50">
      <div class="absolute inset-0 opacity-35 bg-[radial-gradient(#86efac_1px,transparent_1px)] [background-size:22px_22px]"></div>
      <div class="relative mx-auto max-w-7xl px-4 py-7 sm:px-6 sm:py-9 lg:px-8">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
          <div class="max-w-3xl">
            <span class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-white/90 px-3 py-1 text-xs font-bold text-emerald-700 shadow-2xs backdrop-blur-sm">
              <i class="fa-solid fa-school-circle-check"></i>
              Đang sử dụng vai trò Cơ sở GDNN
            </span>
            <h1 class="mt-4 text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl lg:text-4xl">Không gian Cơ sở GDNN</h1>
            <p class="mt-2 max-w-2xl text-sm leading-relaxed text-slate-600 sm:text-base">
              Quản lý thông tin cơ sở, ngành/nghề đào tạo và khai báo năng lực có thể cung cấp cho thị trường lao động theo từng kỳ.
            </p>
            <p v-if="activeSchool" class="mt-3 inline-flex max-w-full items-center gap-2 rounded-xl border border-emerald-200 bg-white px-3 py-2 text-sm font-bold text-slate-700 shadow-2xs">
              <i class="fa-solid fa-building-columns shrink-0 text-emerald-600"></i>
              <span class="truncate">{{ activeSchool.name }}</span>
              <span v-if="activeSchool.code" class="shrink-0 text-xs font-semibold text-emerald-700">({{ activeSchool.code }})</span>
            </p>
          </div>

          <div class="flex flex-wrap gap-2.5">
            <button
              type="button"
              @click="openCapacityModal"
              class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-700 active:scale-[0.98]"
            >
              <i class="fa-solid fa-plus"></i>
              Khai báo năng lực
            </button>
            <router-link
              to="/co-so-gdnn"
              class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 shadow-2xs transition hover:border-emerald-200 hover:bg-emerald-50 hover:text-emerald-700"
            >
              <i class="fa-solid fa-building-columns"></i>
              Xem danh mục cơ sở
            </router-link>
          </div>
        </div>
      </div>
    </section>

    <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
      <section class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        <article v-for="stat in stats" :key="stat.label" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-2xs">
          <div class="flex items-start justify-between gap-3">
            <div>
              <p class="text-xs font-semibold text-slate-500">{{ stat.label }}</p>
              <p class="mt-2 text-xl font-black text-slate-900 sm:text-2xl">{{ stat.value }}</p>
              <p class="mt-0.5 text-[11px] text-slate-400">{{ stat.unit }}</p>
            </div>
            <span :class="stat.iconClass" class="flex h-9 w-9 items-center justify-center rounded-xl">
              <i :class="stat.icon" class="text-sm"></i>
            </span>
          </div>
        </article>
      </section>

      <div class="mt-5 grid grid-cols-1 gap-5 lg:grid-cols-[230px_minmax(0,1fr)]">
        <aside class="lg:sticky lg:top-4 lg:self-start">
          <section class="rounded-2xl border border-slate-200 bg-white p-3.5 shadow-2xs">
            <h2 class="text-sm font-bold text-slate-900">Chức năng Nhà trường</h2>
            <div class="mt-3 grid grid-cols-2 gap-2 lg:grid-cols-1">
              <button
                v-for="item in schoolFunctions"
                :key="item.id"
                type="button"
                :class="activeSchoolSection === item.id ? item.activeClass : 'border-slate-200 bg-white text-slate-700 hover:border-slate-300 hover:bg-slate-50'"
                class="group flex min-w-0 items-center gap-2.5 rounded-xl border px-3 py-2.5 text-left text-xs font-semibold transition"
                @click="activeSchoolSection = item.id"
              >
                <span :class="activeSchoolSection === item.id ? item.iconActiveClass : 'bg-slate-100 text-slate-500'" class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg transition">
                  <i :class="item.icon" class="text-[11px]"></i>
                </span>
                <span class="min-w-0 flex-1 truncate">{{ item.label }}</span>
                <i :class="activeSchoolSection === item.id ? item.chevronClass : 'text-slate-300'" class="fa-solid fa-chevron-right text-[9px] transition group-hover:translate-x-0.5"></i>
              </button>
            </div>
          </section>
        </aside>

        <div class="space-y-5">
          <section v-if="activeSchoolSection === 'capacities'" id="school-capacities" class="rounded-2xl border border-slate-200 bg-white shadow-2xs">
            <div class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
              <div>
                <h2 class="flex items-center gap-2 text-base font-bold text-slate-900">
                  <i class="fa-solid fa-graduation-cap text-emerald-600"></i>
                  Năng lực có thể cung cấp
                </h2>
                <p class="mt-1 text-xs text-slate-500">Dữ liệu năng lực đã lưu của cơ sở đang được chọn.</p>
              </div>
              <button type="button" @click="openCapacityModal" class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white shadow-xs transition hover:bg-emerald-700">
                <i class="fa-solid fa-plus"></i>
                Thêm khai báo
              </button>
            </div>

            <div v-if="capacityRows.length" class="divide-y divide-slate-100">
              <article v-for="row in capacityRows" :key="row.id" class="p-4 sm:p-5">
                <div class="flex flex-col gap-3 xl:flex-row xl:items-start xl:justify-between">
                  <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                      <h3 class="font-bold text-slate-900">{{ row.major }}</h3>
                      <span class="rounded-md bg-blue-50 px-2 py-0.5 text-[11px] font-bold text-blue-700">{{ row.level }}</span>
                      <span class="rounded-md bg-emerald-50 px-2 py-0.5 text-[11px] font-bold text-emerald-700">Đang hoạt động</span>
                    </div>
                    <p class="mt-1 text-xs font-medium text-slate-600">{{ row.school }}</p>
                    <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-500">
                      <span><i class="fa-regular fa-calendar mr-1 text-slate-400"></i>{{ row.period }}</span>
                      <span><i class="fa-solid fa-location-dot mr-1 text-rose-500"></i>{{ row.location }}</span>
                      <span v-if="row.duration"><i class="fa-regular fa-clock mr-1 text-slate-400"></i>{{ row.duration }}</span>
                    </div>
                    <div class="mt-2 flex flex-wrap gap-1.5">
                      <span v-for="skill in row.skills" :key="skill" class="rounded-lg border border-slate-200 bg-slate-50 px-2 py-1 text-[11px] font-medium text-slate-600">{{ skill }}</span>
                    </div>
                  </div>
                  <div class="flex flex-shrink-0 items-baseline gap-1 rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-emerald-800">
                    <strong class="text-2xl font-black">{{ formatNumber(row.quantity) }}</strong>
                    <span class="text-xs font-semibold">người / kỳ</span>
                  </div>
                </div>
              </article>
            </div>

            <div v-else class="flex flex-col items-center px-5 py-12 text-center">
              <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
                <i class="fa-solid fa-chart-column text-xl"></i>
              </span>
              <h3 class="mt-4 text-sm font-bold text-slate-800">Chưa có năng lực nào được khai báo</h3>
              <p class="mt-1 max-w-md text-xs leading-relaxed text-slate-500">Chọn “Khai báo năng lực” để bổ sung thông tin cung ứng cho cơ sở này.</p>
            </div>
          </section>

          <section v-else id="school-proposals" class="rounded-2xl border border-slate-200 bg-white shadow-2xs">
            <div class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
              <div>
                <h2 class="flex items-center gap-2 text-base font-bold text-slate-900">
                  <i class="fa-solid fa-handshake-angle text-blue-600"></i>
                  Đề xuất đáp ứng theo lĩnh vực
                </h2>
                <p class="mt-1 text-xs text-slate-500">Nhu cầu được tổng hợp từ các việc làm doanh nghiệp đang tuyển trong từng lĩnh vực nghề nghiệp.</p>
              </div>
              <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex w-fit items-center rounded-xl bg-blue-50 px-3 py-2 text-xs font-bold text-blue-700">
                  {{ formatNumber(careerFieldRows.length) }} lĩnh vực tuyển dụng
                </span>
                <button type="button" :disabled="!careerFieldRows.length" @click="openProposalModal()" class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-xs font-bold text-white shadow-xs transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:bg-slate-300">
                  <i class="fa-solid fa-plus"></i>
                  Thêm đề xuất
                </button>
              </div>
            </div>

            <p v-if="fieldProposalError" class="m-5 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-xs font-medium text-amber-800">{{ fieldProposalError }}</p>

            <div v-if="isFieldProposalLoading" class="flex items-center justify-center gap-2 px-5 py-12 text-sm font-semibold text-slate-500">
              <i class="fa-solid fa-spinner fa-spin text-blue-600"></i>
              Đang tổng hợp nhu cầu theo lĩnh vực...
            </div>

            <div v-else-if="careerFieldRows.length" class="grid grid-cols-1 gap-4 p-4 xl:grid-cols-2 xl:p-5">
              <article v-for="field in careerFieldRows" :key="field.id" class="flex flex-col rounded-2xl border border-slate-200 bg-slate-50/50 p-4">
                <div class="flex items-start gap-3">
                  <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                    <i :class="field.icon" class="text-sm"></i>
                  </span>
                  <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                      <h3 class="text-base font-black text-slate-900">{{ field.name }}</h3>
                      <span v-if="field.proposal" class="rounded-lg bg-emerald-50 px-2 py-1 text-[11px] font-bold text-emerald-700">
                        Đã đề xuất {{ formatNumber(field.proposal.quantity) }} người
                      </span>
                    </div>
                    <p class="mt-1 text-xs leading-relaxed text-slate-500">{{ field.description }}</p>
                  </div>
                </div>

                <div class="mt-4 grid grid-cols-3 divide-x divide-slate-200 rounded-xl border border-slate-200 bg-white py-3 text-center">
                  <div class="px-2">
                    <strong class="block text-lg font-black text-blue-700">{{ formatNumber(field.job_openings) }}</strong>
                    <span class="text-[10px] font-semibold text-slate-500">Người cần tuyển</span>
                  </div>
                  <div class="px-2">
                    <strong class="block text-lg font-black text-slate-800">{{ formatNumber(field.job_count) }}</strong>
                    <span class="text-[10px] font-semibold text-slate-500">Việc làm</span>
                  </div>
                  <div class="px-2">
                    <strong class="block text-lg font-black text-slate-800">{{ formatNumber(field.companies) }}</strong>
                    <span class="text-[10px] font-semibold text-slate-500">Doanh nghiệp</span>
                  </div>
                </div>

                <div class="mt-4 flex flex-col gap-3 border-t border-slate-200 pt-4 sm:flex-row sm:items-center sm:justify-between">
                  <p class="text-xs font-bold text-emerald-700">
                    {{ field.proposal ? '1 đề xuất' : '0 đề xuất' }}
                  </p>
                  <button type="button" @click="openProposalModal(field)" class="shrink-0 rounded-xl border border-blue-200 bg-white px-3.5 py-2 text-xs font-bold text-blue-700 shadow-xs transition hover:border-blue-300 hover:bg-blue-50">
                    <i class="fa-solid fa-handshake-angle mr-1"></i>
                    {{ field.proposal ? 'Cập nhật đề xuất' : 'Đề xuất đáp ứng' }}
                  </button>
                </div>
              </article>
            </div>

            <div v-else class="flex flex-col items-center px-5 py-12 text-center">
              <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-blue-600">
                <i class="fa-solid fa-handshake-angle text-xl"></i>
              </span>
              <h3 class="mt-4 text-sm font-bold text-slate-800">Chưa có việc làm đang tuyển dụng</h3>
              <p class="mt-1 max-w-md text-xs leading-relaxed text-slate-500">Khi doanh nghiệp đăng việc làm, nhu cầu sẽ được tự động tổng hợp theo lĩnh vực tại đây.</p>
            </div>
          </section>

        </div>
      </div>
    </main>

    <div v-if="isCapacityModalOpen" class="fixed inset-0 z-[4000] flex items-center justify-center p-3 sm:p-6">
      <button type="button" class="absolute inset-0 bg-slate-950/55 backdrop-blur-[2px]" aria-label="Đóng biểu mẫu" @click="closeCapacityModal"></button>
      <section class="relative flex max-h-[calc(100dvh-1.5rem)] w-full max-w-4xl flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl sm:max-h-[calc(100dvh-3rem)]">
        <header class="flex shrink-0 items-start justify-between gap-4 border-b border-slate-100 px-5 py-4 sm:px-7 sm:py-5">
          <div>
            <h2 class="text-xl font-black text-slate-900 sm:text-2xl">Khai báo năng lực đào tạo</h2>
            <p class="mt-1 text-xs text-slate-500 sm:text-sm">Các trường có dấu * là bắt buộc.</p>
          </div>
          <button type="button" @click="closeCapacityModal" class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-slate-100 text-slate-500 transition hover:bg-slate-200" title="Đóng">
            <i class="fa-solid fa-xmark"></i>
          </button>
        </header>

        <form class="flex min-h-0 flex-1 flex-col" @submit.prevent="submitCapacity">
          <div class="min-h-0 flex-1 overflow-y-auto px-5 py-5 sm:px-7">
          <p v-if="catalogError" class="mb-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-xs font-medium text-amber-800">{{ catalogError }}</p>
          <p v-if="formError" class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-xs font-medium text-rose-700">{{ formError }}</p>

          <fieldset :disabled="isCatalogLoading || isSaving" class="grid grid-cols-1 gap-x-5 gap-y-4 sm:grid-cols-2 disabled:opacity-60">
            <div class="sm:col-span-2">
              <span class="mb-1.5 block text-sm font-bold text-slate-800">Tên cơ sở GDNN *</span>
              <div class="flex min-h-12 w-full items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-3.5 py-2.5 text-sm">
                <i class="fa-solid fa-building-columns text-emerald-600"></i>
                <div class="min-w-0 flex-1">
                  <p class="truncate font-bold text-slate-800">{{ activeSchool?.name }}</p>
                  <p v-if="activeSchool?.code" class="text-xs text-slate-500">Mã: {{ activeSchool.code }}</p>
                </div>
                <span class="shrink-0 rounded-lg bg-white px-2 py-1 text-[11px] font-bold text-emerald-700">Đã chọn</span>
              </div>
            </div>

            <div class="min-w-0">
              <span class="mb-1.5 block text-sm font-bold text-slate-800">Ngành, nghề *</span>
              <FormSelectPopover
                v-model="capacityForm.training_major_id"
                :options="majorOptions"
                :disabled="isCatalogLoading || isSaving"
                placeholder="Chọn ngành/nghề"
                search-placeholder="Tìm tên ngành/nghề..."
                icon="fa-solid fa-graduation-cap"
                searchable
                required
              />
            </div>

            <div class="min-w-0">
              <span class="mb-1.5 block text-sm font-bold text-slate-800">Trình độ *</span>
              <FormSelectPopover
                v-model="capacityForm.training_level_id"
                :options="levelOptions"
                :disabled="isCatalogLoading || isSaving"
                placeholder="Chọn trình độ"
                icon="fa-solid fa-layer-group"
                required
              />
            </div>

            <div class="min-w-0">
              <span class="mb-1.5 block text-sm font-bold text-slate-800">Kỳ *</span>
              <FormSelectPopover
                v-model="capacityForm.reporting_period_id"
                :options="periodOptions"
                :disabled="isCatalogLoading || isSaving"
                placeholder="Chọn kỳ báo cáo"
                icon="fa-solid fa-calendar-days"
                required
              />
            </div>

            <label>
              <span class="mb-1.5 block text-sm font-bold text-slate-800">Năng lực dự kiến (người / kỳ) *</span>
              <input v-model.number="capacityForm.quantity" required min="1" max="100000" type="number" placeholder="Ví dụ: 120" class="h-11 w-full rounded-xl border border-slate-200/90 bg-slate-50/70 px-3.5 text-sm text-slate-700 shadow-2xs outline-none transition-all hover:bg-slate-100/70 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-100" />
            </label>

            <label>
              <span class="mb-1.5 block text-sm font-bold text-slate-800">Khu vực / địa điểm *</span>
              <div class="relative">
                <i class="fa-solid fa-lock pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                <input v-model.trim="capacityForm.location" disabled aria-disabled="true" maxlength="255" type="text" placeholder="Chưa có địa điểm trong hồ sơ cơ sở" class="h-11 w-full cursor-not-allowed rounded-xl border border-slate-200/90 bg-slate-100 pl-10 pr-3.5 text-sm font-medium text-slate-500 shadow-2xs outline-none disabled:opacity-100" />
              </div>
              <span class="mt-1.5 block text-[11px] leading-snug text-slate-500">Tự động lấy từ hồ sơ cơ sở đang đăng nhập.</span>
            </label>

            <label>
              <span class="mb-1.5 block text-sm font-bold text-slate-800">Thời lượng / hình thức đào tạo</span>
              <input v-model.trim="capacityForm.duration" maxlength="120" type="text" placeholder="Ví dụ: 3 tháng, đào tạo tập trung" class="h-11 w-full rounded-xl border border-slate-200/90 bg-slate-50/70 px-3.5 text-sm text-slate-700 shadow-2xs outline-none transition-all hover:bg-slate-100/70 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-100" />
            </label>

            <label class="sm:col-span-2">
              <span class="mb-1.5 block text-sm font-bold text-slate-800">Kỹ năng có thể đào tạo, cách nhau bằng dấu phẩy *</span>
              <input v-model.trim="capacityForm.skills" required maxlength="500" type="text" placeholder="Ví dụ: PLC, Đọc bản vẽ, An toàn điện" class="h-11 w-full rounded-xl border border-slate-200/90 bg-slate-50/70 px-3.5 text-sm text-slate-700 shadow-2xs outline-none transition-all hover:bg-slate-100/70 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-100" />
            </label>

            <label class="sm:col-span-2">
              <span class="mb-1.5 block text-sm font-bold text-slate-800">Đội ngũ, thiết bị và điều kiện đào tạo</span>
              <textarea v-model.trim="capacityForm.equipment" maxlength="2000" rows="3" placeholder="Mô tả giáo viên, xưởng thực hành, thiết bị..." class="w-full rounded-xl border border-slate-200/90 bg-slate-50/70 px-3.5 py-3 text-sm text-slate-700 shadow-2xs outline-none transition-all hover:bg-slate-100/70 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-100"></textarea>
            </label>

            <label class="sm:col-span-2">
              <span class="mb-1.5 block text-sm font-bold text-slate-800">Ghi chú / điều kiện phối hợp</span>
              <textarea v-model.trim="capacityForm.note" maxlength="2000" rows="2" placeholder="Thông tin bổ sung dành cho doanh nghiệp hoặc cơ quan quản lý" class="w-full rounded-xl border border-slate-200/90 bg-slate-50/70 px-3.5 py-3 text-sm text-slate-700 shadow-2xs outline-none transition-all hover:bg-slate-100/70 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-100"></textarea>
            </label>
          </fieldset>
          </div>

          <div class="flex shrink-0 flex-col-reverse gap-2 border-t border-slate-100 bg-slate-50 px-5 py-4 sm:flex-row sm:justify-end sm:px-7">
            <button type="button" @click="closeCapacityModal" class="rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-50">Hủy</button>
            <button type="submit" :disabled="isCatalogLoading || isSaving" class="rounded-xl bg-emerald-600 px-6 py-2.5 text-sm font-bold text-white shadow-xs transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:bg-slate-400">
              <i :class="isSaving ? 'fa-solid fa-spinner fa-spin' : 'fa-solid fa-check'" class="mr-1.5"></i>
              {{ isSaving ? 'Đang lưu...' : 'Lưu khai báo' }}
            </button>
          </div>
        </form>
      </section>
    </div>

    <div v-if="isProposalModalOpen && selectedCareerField" class="fixed inset-0 z-[4000] flex items-center justify-center p-3 sm:p-6">
      <button type="button" class="absolute inset-0 bg-slate-950/55 backdrop-blur-[2px]" aria-label="Đóng đề xuất" @click="closeProposalModal"></button>
      <section class="relative flex max-h-[calc(100dvh-1.5rem)] w-full max-w-3xl flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl sm:max-h-[calc(100dvh-3rem)]">
        <header class="flex shrink-0 items-start justify-between gap-4 border-b border-slate-100 px-5 py-4 sm:px-7 sm:py-5">
          <div>
            <h2 class="text-xl font-black text-slate-900 sm:text-2xl">Đề xuất đáp ứng theo lĩnh vực</h2>
            <p class="mt-1 text-xs text-slate-500 sm:text-sm">Một đề xuất đại diện cho khả năng đáp ứng toàn bộ nhóm việc làm cùng lĩnh vực.</p>
          </div>
          <button type="button" @click="closeProposalModal" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-100 text-slate-500 transition hover:bg-slate-200" title="Đóng">
            <i class="fa-solid fa-xmark"></i>
          </button>
        </header>

        <form class="flex min-h-0 flex-1 flex-col" @submit.prevent="submitProposal">
          <div class="min-h-0 flex-1 overflow-y-auto px-5 py-5 sm:px-7">
          <div class="block">
            <span class="mb-1.5 block text-sm font-bold text-slate-800">Lĩnh vực việc làm *</span>
            <FormSelectPopover
              :model-value="proposalForm.career_field_id"
              :options="careerFieldRows"
              :disabled="isSavingProposal"
              placeholder="Chọn lĩnh vực việc làm"
              search-placeholder="Tìm lĩnh vực việc làm..."
              icon="fa-solid fa-briefcase"
              tone="blue"
              searchable
              required
              @update:model-value="selectProposalField"
            />
          </div>

          <p v-if="proposalError" class="mt-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-xs font-medium text-rose-700">{{ proposalError }}</p>

          <fieldset :disabled="isSavingProposal" class="mt-5 space-y-4 disabled:opacity-60">
            <label class="block">
              <span class="mb-1.5 block text-sm font-bold text-slate-800">Số người đề xuất *</span>
              <input v-model.number="proposalForm.quantity" required min="1" :max="selectedCareerField.job_openings || 100000" type="number" placeholder="Nhập số người có thể đáp ứng" class="h-11 w-full rounded-xl border border-slate-200/90 bg-slate-50/70 px-3.5 text-sm text-slate-700 shadow-2xs outline-none transition-all hover:bg-slate-100/70 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100" />
            </label>

            <label class="block">
              <span class="mb-1.5 block text-sm font-bold text-slate-800">Phương án đáp ứng *</span>
              <textarea v-model.trim="proposalForm.plan" required minlength="10" maxlength="3000" rows="4" placeholder="Thời gian, nội dung đào tạo, kỹ năng cần bổ sung và điều kiện phối hợp..." class="w-full rounded-xl border border-slate-200/90 bg-slate-50/70 px-3.5 py-3 text-sm text-slate-700 shadow-2xs outline-none transition-all hover:bg-slate-100/70 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"></textarea>
            </label>
          </fieldset>
          </div>

          <div class="flex shrink-0 flex-col-reverse gap-2 border-t border-slate-100 bg-slate-50 px-5 py-4 sm:flex-row sm:justify-end sm:px-7">
            <button type="button" @click="closeProposalModal" class="rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-50">Hủy</button>
            <button type="submit" :disabled="isSavingProposal || !selectedCareerField" class="rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-bold text-white shadow-xs transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:bg-slate-400">
              <i :class="isSavingProposal ? 'fa-solid fa-spinner fa-spin' : 'fa-solid fa-paper-plane'" class="mr-1.5"></i>
              {{ isSavingProposal ? 'Đang lưu...' : (selectedCareerField.proposal ? 'Cập nhật đề xuất' : 'Gửi đề xuất') }}
            </button>
          </div>
        </form>
      </section>
    </div>

    <div v-if="toastMessage" class="fixed bottom-5 left-1/2 z-[90] -translate-x-1/2 rounded-xl bg-slate-900 px-4 py-3 text-xs font-bold text-white shadow-xl">
      <i class="fa-solid fa-circle-check mr-1.5 text-emerald-400"></i>{{ toastMessage }}
    </div>

    <ClientFooter class="mt-auto" />
  </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import FormSelectPopover from '../../components/form/FormSelectPopover.vue'
import { jobService } from '../../services/jobService'
import { portalRoleService } from '../../services/portalRoleService'
import { schoolPortalService } from '../../services/schoolPortalService'
import { schoolService } from '../../services/schoolService'
import { supplyDemandService } from '../../services/supplyDemandService'

const router = useRouter()
const activeSchool = ref(portalRoleService.getSchool())
const majorOptions = ref([])
const levelOptions = ref([])
const periodOptions = ref([])
const capacityRows = ref([])
const fieldProposals = ref([])
const isCatalogLoading = ref(false)
const isFieldProposalLoading = ref(false)
const isSaving = ref(false)
const isSavingProposal = ref(false)
const isCapacityModalOpen = ref(false)
const isProposalModalOpen = ref(false)
const catalogError = ref('')
const fieldProposalError = ref('')
const formError = ref('')
const proposalError = ref('')
const toastMessage = ref('')

const fallbackLevels = [
  { id: 'duoi_3_thang', name: 'Dưới 3 tháng' },
  { id: 'so_cap', name: 'Sơ cấp' },
  { id: 'trung_cap', name: 'Trung cấp' },
  { id: 'cao_dang', name: 'Cao đẳng' },
]

const fallbackPeriods = [
  { id: '2026-Q4', name: 'Quý IV/2026' },
  { id: '2027-Q1', name: 'Quý I/2027' },
]

function emptyCapacityForm() {
  return {
    training_major_id: '',
    training_level_id: '',
    reporting_period_id: '',
    quantity: null,
    location: '',
    duration: '',
    skills: '',
    equipment: '',
    note: '',
  }
}

const capacityForm = reactive(emptyCapacityForm())

function emptyProposalForm() {
  return {
    career_field_id: '',
    quantity: null,
    plan: '',
  }
}

const proposalForm = reactive(emptyProposalForm())
const activeSchoolSection = ref('capacities')

const proposalByField = computed(() => new Map(
  fieldProposals.value.map((proposal) => [String(proposal.career_field_id), proposal]),
))
const careerFieldRows = computed(() => jobService.getCareerFields([])
  .filter((field) => Number(field.job_count) > 0)
  .map((field) => {
    const capacities = capacityRows.value.filter((capacity) => jobService.getCareerFieldId(capacity.major) === field.id)
    return {
      ...field,
      proposal: proposalByField.value.get(String(field.id)) || null,
      capacity_count: capacities.length,
      capacity_quantity: capacities.reduce((sum, capacity) => sum + Number(capacity.quantity || 0), 0),
    }
  }))
const selectedCareerField = computed(() => careerFieldRows.value.find(
  (field) => String(field.id) === String(proposalForm.career_field_id),
) || null)
const proposalCount = computed(() => fieldProposals.value.length)

const stats = computed(() => [
  { label: 'Cơ sở đang đăng nhập', value: activeSchool.value ? '01' : '0', unit: activeSchool.value?.code || 'cơ sở', icon: 'fa-solid fa-school', iconClass: 'bg-blue-50 text-blue-600' },
  { label: 'Năng lực đã khai báo', value: formatNumber(capacityRows.value.length), unit: 'bản ghi của cơ sở', icon: 'fa-solid fa-list-check', iconClass: 'bg-emerald-50 text-emerald-600' },
  { label: 'Lĩnh vực tuyển dụng', value: formatNumber(careerFieldRows.value.length), unit: 'nhóm có việc làm', icon: 'fa-solid fa-bullseye', iconClass: 'bg-amber-50 text-amber-600' },
  { label: 'Đề xuất đã gửi', value: formatNumber(proposalCount.value), unit: 'đề xuất của cơ sở', icon: 'fa-solid fa-handshake-angle', iconClass: 'bg-violet-50 text-violet-600' },
])

const schoolFunctions = [
  {
    id: 'capacities',
    label: 'Năng lực cung ứng',
    icon: 'fa-solid fa-graduation-cap',
    activeClass: 'border-emerald-200 bg-emerald-50 text-emerald-800',
    iconActiveClass: 'bg-white text-emerald-600',
    chevronClass: 'text-emerald-500',
  },
  {
    id: 'proposals',
    label: 'Đề xuất đáp ứng',
    icon: 'fa-solid fa-handshake-angle',
    activeClass: 'border-blue-200 bg-blue-50 text-blue-800',
    iconActiveClass: 'bg-white text-blue-600',
    chevronClass: 'text-blue-500',
  },
]

function formatNumber(value) {
  return new Intl.NumberFormat('vi-VN').format(Number(value || 0))
}

function resetForm() {
  Object.assign(capacityForm, emptyCapacityForm())
  capacityForm.training_major_id = String(majorOptions.value[0]?.id || '')
  capacityForm.training_level_id = String(levelOptions.value[0]?.id || '')
  capacityForm.reporting_period_id = String(periodOptions.value[0]?.id || '')
  capacityForm.location = activeSchool.value?.address || activeSchool.value?.ward || ''
  formError.value = ''
}

function openCapacityModal() {
  resetForm()
  isCapacityModalOpen.value = true
}

function closeCapacityModal() {
  if (isSaving.value) return
  isCapacityModalOpen.value = false
  formError.value = ''
}

function hydrateProposalForm() {
  proposalForm.quantity = selectedCareerField.value?.proposal?.quantity || null
  proposalForm.plan = selectedCareerField.value?.proposal?.plan || ''
  proposalError.value = ''
}

function selectProposalField(fieldId) {
  proposalForm.career_field_id = String(fieldId)
  hydrateProposalForm()
}

function openProposalModal(field = null) {
  const selected = field || careerFieldRows.value.find((item) => !item.proposal) || careerFieldRows.value[0]
  if (!selected) return

  proposalForm.career_field_id = String(selected.id)
  hydrateProposalForm()
  isProposalModalOpen.value = true
}

function closeProposalModal() {
  if (isSavingProposal.value) return
  isProposalModalOpen.value = false
  Object.assign(proposalForm, emptyProposalForm())
  proposalError.value = ''
}

function withSchool(row) {
  return {
    ...row,
    school: activeSchool.value?.name || '',
    skills: Array.isArray(row?.skills) ? row.skills : [],
  }
}

async function submitCapacity() {
  const skills = capacityForm.skills.split(',').map((item) => item.trim()).filter(Boolean)
  if (!capacityForm.training_major_id || !capacityForm.training_level_id || !capacityForm.reporting_period_id || !capacityForm.location || Number(capacityForm.quantity) < 1 || skills.length === 0) {
    formError.value = 'Vui lòng điền đầy đủ các trường bắt buộc và nhập ít nhất một kỹ năng.'
    return
  }

  isSaving.value = true
  formError.value = ''

  try {
    const response = await schoolPortalService.saveCapacity({
      training_major_id: Number(capacityForm.training_major_id),
      training_level_id: capacityForm.training_level_id,
      reporting_period_id: Number(capacityForm.reporting_period_id),
      quantity: Number(capacityForm.quantity),
      location: capacityForm.location,
      duration: capacityForm.duration || null,
      skills,
      equipment: capacityForm.equipment || null,
      note: capacityForm.note || null,
    })

    const savedRow = withSchool(response.capacity || {})
    const existingIndex = capacityRows.value.findIndex((row) => String(row.id) === String(savedRow.id))
    if (existingIndex >= 0) capacityRows.value.splice(existingIndex, 1, savedRow)
    else capacityRows.value.unshift(savedRow)

    isCapacityModalOpen.value = false
    toastMessage.value = response.message || 'Đã lưu khai báo năng lực.'
    window.setTimeout(() => { toastMessage.value = '' }, 3000)
  } catch (error) {
    formError.value = error?.message || 'Không thể lưu khai báo năng lực.'
  } finally {
    isSaving.value = false
  }
}

async function submitProposal() {
  const field = selectedCareerField.value
  const quantity = Number(proposalForm.quantity)
  if (!field || quantity < 1 || quantity > Number(field.job_openings || 100000) || proposalForm.plan.trim().length < 10) {
    proposalError.value = 'Vui lòng chọn lĩnh vực, nhập số người không vượt quá tổng nhu cầu và phương án tối thiểu 10 ký tự.'
    return
  }

  isSavingProposal.value = true
  proposalError.value = ''

  try {
    const response = await schoolPortalService.saveFieldProposal({
      career_field_id: field.id,
      quantity,
      plan: proposalForm.plan.trim(),
    })
    isProposalModalOpen.value = false
    Object.assign(proposalForm, emptyProposalForm())
    await loadFieldProposals()
    toastMessage.value = response.message || 'Đã lưu đề xuất đáp ứng.'
    window.setTimeout(() => { toastMessage.value = '' }, 3000)
  } catch (error) {
    proposalError.value = error?.message || 'Không thể lưu đề xuất đáp ứng.'
  } finally {
    isSavingProposal.value = false
  }
}

async function loadFieldProposals() {
  isFieldProposalLoading.value = true
  fieldProposalError.value = ''

  try {
    const payload = await schoolPortalService.getFieldProposals()
    fieldProposals.value = Array.isArray(payload?.proposals) ? payload.proposals : []
  } catch (error) {
    fieldProposals.value = []
    fieldProposalError.value = error?.message || 'Không tải được các đề xuất theo lĩnh vực.'
  } finally {
    isFieldProposalLoading.value = false
  }
}

async function loadPortalData() {
  if (!activeSchool.value) {
    await router.replace('/map')
    return
  }

  isCatalogLoading.value = true
  catalogError.value = ''

  isFieldProposalLoading.value = true
  const [catalogsResult, capacitiesResult, proposalsResult] = await Promise.allSettled([
    supplyDemandService.getCatalogs(),
    schoolPortalService.getCapacities(),
    schoolPortalService.getFieldProposals(),
  ])

  if (catalogsResult.status === 'fulfilled') {
    const catalogs = catalogsResult.value || {}
    majorOptions.value = Array.isArray(catalogs.training_majors) ? catalogs.training_majors : []
    levelOptions.value = Array.isArray(catalogs.training_levels) && catalogs.training_levels.length ? catalogs.training_levels : fallbackLevels
    periodOptions.value = Array.isArray(catalogs.reporting_periods) && catalogs.reporting_periods.length ? catalogs.reporting_periods : fallbackPeriods
  } else {
    levelOptions.value = fallbackLevels
    periodOptions.value = fallbackPeriods
  }

  if (!majorOptions.value.length) {
    try {
      majorOptions.value = await schoolService.getTrainingMajors()
    } catch (error) {
      majorOptions.value = []
    }
  }

  if (capacitiesResult.status === 'fulfilled') {
    const payload = capacitiesResult.value || {}
    if (payload.school) {
      activeSchool.value = payload.school
      portalRoleService.setSchool(payload.school)
    }
    capacityRows.value = Array.isArray(payload.capacities) ? payload.capacities.map(withSchool) : []
  } else {
    const message = capacitiesResult.reason?.message || 'Không tải được dữ liệu năng lực của cơ sở.'
    if (message.includes('Chưa chọn cơ sở')) {
      portalRoleService.clearSchool()
      await router.replace('/map')
      return
    }
    catalogError.value = message
  }

  if (proposalsResult.status === 'fulfilled') {
    fieldProposals.value = Array.isArray(proposalsResult.value?.proposals) ? proposalsResult.value.proposals : []
  } else {
    fieldProposals.value = []
    fieldProposalError.value = proposalsResult.reason?.message || 'Không tải được các đề xuất theo lĩnh vực.'
  }

  if (!majorOptions.value.length && !catalogError.value) {
    catalogError.value = 'Danh mục ngành/nghề chưa tải được. Hãy bảo đảm máy chủ Laravel đang chạy.'
  }

  isCatalogLoading.value = false
  isFieldProposalLoading.value = false
}

onMounted(loadPortalData)
</script>
