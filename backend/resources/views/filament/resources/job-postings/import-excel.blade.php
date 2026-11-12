<form id="job-posting-excel-import-form" method="POST" action="{{ route('admin.job-postings.import-excel') }}" enctype="multipart/form-data" class="space-y-5">
    @csrf
    <x-filament-forms::field-wrapper id="job-posting-excel-file" label="Tệp Excel tin việc làm (.xlsx, .xls)" required>
        <x-filament::input.wrapper>
            <x-filament::input id="job-posting-excel-file" name="excel_file" type="file" accept=".xlsx,.xls" required />
        </x-filament::input.wrapper>
    </x-filament-forms::field-wrapper>
    <label class="flex items-center gap-3">
        <input type="checkbox" name="dry_run" value="1" checked class="rounded border-gray-300" />
        <span>Chỉ kiểm tra dữ liệu, chưa ghi vào hệ thống</span>
    </label>
</form>
