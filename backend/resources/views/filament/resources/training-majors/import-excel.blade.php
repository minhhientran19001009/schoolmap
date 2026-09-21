<form
    id="training-major-excel-import-form"
    method="POST"
    action="{{ route('admin.training-majors.import-excel') }}"
    enctype="multipart/form-data"
>
    @csrf

    <x-filament-forms::field-wrapper
        id="training-major-excel-file"
        label="Tệp Excel danh mục chuyên ngành (.xlsx, .xls)"
        required
    >
        <x-filament::input.wrapper>
            <x-filament::input
                id="training-major-excel-file"
                name="excel_file"
                type="file"
                accept=".xlsx,.xls,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel"
                required
            />
        </x-filament::input.wrapper>
    </x-filament-forms::field-wrapper>
</form>
