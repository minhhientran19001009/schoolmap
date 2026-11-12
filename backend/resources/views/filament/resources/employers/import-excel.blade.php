<form id="employer-excel-import-form" method="POST" action="{{ route('admin.employers.import-excel') }}" enctype="multipart/form-data" class="space-y-5">
    @csrf
    <x-filament-forms::field-wrapper id="employer-excel-file" label="Tệp Excel doanh nghiệp (.xlsx, .xls)" required>
        <x-filament::input.wrapper>
            <x-filament::input id="employer-excel-file" name="excel_file" type="file" accept=".xlsx,.xls,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel" required />
        </x-filament::input.wrapper>
    </x-filament-forms::field-wrapper>
    <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-gray-200 p-4 dark:border-white/10">
        <input type="checkbox" name="dry_run" value="1" checked class="mt-1 rounded border-gray-300 text-primary-600 focus:ring-primary-500" />
        <span><span class="block text-sm font-semibold text-gray-950 dark:text-white">Chỉ kiểm tra dữ liệu, chưa ghi vào hệ thống</span><span class="mt-1 block text-xs text-gray-500">Bỏ chọn sau khi tệp đã được kiểm tra thành công.</span></span>
    </label>
</form>
