<form
    id="school-excel-import-form"
    method="POST"
    action="{{ route('admin.schools.import-excel') }}"
    enctype="multipart/form-data"
>
    @csrf

    <x-filament-forms::field-wrapper
        id="school-excel-file"
        label="Tệp Excel (.xlsx, .xls)"
        required
    >
        <x-filament::input.wrapper>
            <x-filament::input
                id="school-excel-file"
                name="excel_file"
                type="file"
                accept=".xlsx,.xls,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel"
                required
            />
        </x-filament::input.wrapper>
    </x-filament-forms::field-wrapper>
</form>
