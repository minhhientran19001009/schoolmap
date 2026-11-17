<form
    id="{{ ($trainingScale ?? false) ? 'school-scale-excel-import-form' : 'school-excel-import-form' }}"
    method="POST"
    action="{{ route('admin.schools.import-excel') }}"
    enctype="multipart/form-data"
>
    @csrf
    @if ($trainingScale ?? false)
        <input type="hidden" name="import_type" value="training_scale">
    @endif

    <x-filament-forms::field-wrapper
        id="{{ ($trainingScale ?? false) ? 'school-scale-excel-file' : 'school-excel-file' }}"
        label="Tệp Excel (.xlsx, .xls)"
        required
    >
        <x-filament::input.wrapper>
            <x-filament::input
                id="{{ ($trainingScale ?? false) ? 'school-scale-excel-file' : 'school-excel-file' }}"
                name="excel_file"
                type="file"
                accept=".xlsx,.xls,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel"
                required
            />
        </x-filament::input.wrapper>
    </x-filament-forms::field-wrapper>
    @if ($trainingScale ?? false)
        <label class="mt-4 flex items-center gap-2 text-sm">
            <x-filament::input.checkbox name="dry_run" value="1" checked />
            Chỉ kiểm tra, chưa ghi dữ liệu
        </label>
    @endif
</form>
