<?php

use App\Models\School;
use App\Models\TrainingMajor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

Route::get('/', function () {
    return view('welcome');
});

// Keep the public map payload small. Heavy JSON fields are returned by the
// detail endpoint only after a user opens a school profile.
$mapSchool = static function (School $school, array $districtNames = []) {
    $arr = $school->toArray();
    $firstLevel = $school->educationLevels->first();
    $levelId = $firstLevel?->id ?? $school->education_level_id ?? 'cao_dang';
    $arr['education_level'] = str_replace('-', '_', $levelId);
    $arr['education_level_name'] = $firstLevel?->name ?? 'Giáo dục';
    $arr['education_levels_list'] = $school->educationLevels->pluck('name')->toArray();
    $arr['education_level_ids'] = $school->educationLevels->pluck('id')->map(fn ($id) => str_replace('-', '_', $id))->values()->all();
    $arr['training_major_ids'] = $school->relationLoaded('majors')
        ? $school->majors->pluck('id')->values()->all()
        : [];
    $arr['school_type'] = $school->school_type_id;
    $arr['district_name'] = $districtNames[$school->district_id] ?? null;
    $arr['campus_type'] = $school->campus_type ?: 'MAIN';
    $arr['campus_type_label'] = School::campusTypeLabels()[$arr['campus_type']] ?? 'Cơ sở giáo dục';
    return $arr;
};

$syncSchoolMajors = static function (School $school, ?array $assignments): void {
    if ($assignments === null) {
        return;
    }

    $rows = collect($assignments)
        ->filter(fn ($item) => is_array($item) && ! empty($item['training_major_id']))
        ->map(fn (array $item) => [
            'training_major_id' => (int) $item['training_major_id'],
            'degree_level' => (string) ($item['degree_level'] ?? 'cao_dang'),
            'annual_quota' => max(0, (int) ($item['annual_quota'] ?? 0)),
        ])
        ->unique(fn (array $item) => $item['training_major_id'].'|'.$item['degree_level'])
        ->values()
        ->all();

    $school->majorAssignments()->delete();
    if ($rows !== []) {
        $school->majorAssignments()->createMany($rows);
    }
};

// The public write routes are kept for legacy integrations. Explicitly allow
// only school attributes and validate the location/campus fields before model
// events enforce the relational integrity a second time.
$schoolPayload = static function (\Illuminate\Http\Request $request, bool $creating = false): array {
    $allowed = [
        'code', 'name', 'campus_note', 'campus_type', 'campus_name', 'parent_school_id',
        'education_level', 'education_level_id', 'school_type_id', 'special_type', 'district_id', 'ward',
        'legacy_province', 'address', 'lat', 'lng', 'phone', 'email', 'website', 'principal', 'leaders',
        'training_majors', 'is_national_standard', 'national_standard_level', 'founded_year', 'student_count',
        'annual_enrollment', 'annual_graduates', 'employment_rate', 'teacher_count', 'teacher_quota',
        'teachers_shortage', 'teachers_surplus', 'faculty_rank_1', 'faculty_rank_2', 'faculty_rank_3',
        'faculty_doctors', 'faculty_masters', 'faculty_professors', 'partner_enterprises', 'class_count',
        'classroom_count', 'computer_room_count', 'library', 'lab_count', 'workshops_count', 'campus_area_m2',
        'gallery', 'status', 'last_verified_at',
        'major_assignments',
    ];
    $data = $request->only($allowed);

    $rules = [
        'code' => ['sometimes', 'nullable', 'string', 'max:50'],
        'name' => [$creating ? 'required' : 'sometimes', 'string', 'max:255'],
        'campus_note' => ['sometimes', 'nullable', 'string', 'max:255'],
        'campus_type' => ['sometimes', 'string', Rule::in(array_keys(School::campusTypeLabels()))],
        'campus_name' => ['sometimes', 'nullable', 'string', 'max:150'],
        'parent_school_id' => ['sometimes', 'nullable', 'string', 'max:50'],
        'education_level' => ['sometimes', 'nullable', 'string', 'max:20'],
        'education_level_id' => ['sometimes', 'nullable', 'string', 'max:20'],
        'school_type_id' => ['sometimes', 'nullable', 'string', 'max:20'],
        'district_id' => ['sometimes', 'nullable', 'string', 'max:50'],
        'ward' => ['sometimes', 'nullable', 'string', 'max:150'],
        'address' => ['sometimes', 'nullable', 'string', 'max:255'],
        'lat' => ['sometimes', 'numeric', 'between:-90,90'],
        'lng' => ['sometimes', 'numeric', 'between:-180,180'],
        'phone' => ['sometimes', 'nullable', 'string', 'max:50'],
        'email' => ['sometimes', 'nullable', 'email', 'max:100'],
        'website' => ['sometimes', 'nullable', 'url', 'max:255'],
        'status' => ['sometimes', Rule::in(['VERIFIED', 'NEED_REVIEW', 'UNVERIFIED', 'INACTIVE'])],
        'leaders' => ['sometimes', 'nullable', 'array'],
        'training_majors' => ['sometimes', 'nullable', 'array'],
        'major_assignments' => ['sometimes', 'nullable', 'array'],
        'major_assignments.*.training_major_id' => ['required', 'integer', 'exists:training_majors,id'],
        'major_assignments.*.degree_level' => ['required', 'string', 'max:30'],
        'major_assignments.*.annual_quota' => ['sometimes', 'integer', 'min:0'],
        'partner_enterprises' => ['sometimes', 'nullable', 'array'],
        'gallery' => ['sometimes', 'nullable', 'array'],
    ];

    return Validator::make($data, $rules)->validate();
};

Route::get('/api/training-majors', function (\Illuminate\Http\Request $request) {
    $query = TrainingMajor::query()
        ->select(['id', 'name', 'slug', 'is_active'])
        ->withCount('schools')
        ->orderBy('name');

    if ($request->boolean('active', true)) {
        $query->where('is_active', true);
    }

    if ($request->boolean('used')) {
        $query->has('schools');
    }

    if ($search = trim((string) $request->input('q', ''))) {
        $search = mb_strtolower($search, 'UTF-8');
        $query->whereRaw('LOWER(`name`) COLLATE utf8mb4_bin LIKE ?', ["%{$search}%"]);
    }

    return response()->json($query->get())
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, OPTIONS');
});

Route::get('/api/schools', function () use ($mapSchool) {
    $schools = School::query()
        ->select([
            'id', 'code', 'name', 'education_level_id', 'school_type_id', 'district_id',
            'ward', 'legacy_province', 'address', 'lat', 'lng', 'campus_note', 'campus_type', 'campus_name',
            'phone', 'email', 'website', 'principal',
            'is_national_standard', 'national_standard_level', 'founded_year',
            'student_count', 'teacher_count', 'class_count',
            'classroom_count', 'computer_room_count', 'library', 'lab_count',
            'campus_area_m2', 'status', 'last_verified_at',
        ])
        ->with(['educationLevels:id,name', 'majors:id,name,slug'])
        ->orderBy('name', 'asc')
        ->get();

    $districtNames = DB::table('districts')
        ->whereIn('id', $schools->pluck('district_id')->filter()->unique()->values())
        ->pluck('name', 'id')
        ->all();
    
    // Map education level name for backward compatibility with frontend
    $data = $schools->map(fn ($school) => $mapSchool($school, $districtNames));

    return response()->json($data)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, OPTIONS');
});

Route::get('/api/schools/download-template', function (\App\Services\SchoolExcelService $excelService) {
    return $excelService->downloadTemplate();
})->middleware(['auth', 'throttle:30,1']);

Route::get('/api/schools/export-excel', function (\App\Services\SchoolExcelService $excelService) {
    return $excelService->exportCurrentSchools();
})->middleware(['auth', 'throttle:10,1']);

Route::post('/admin/schools/import-excel', function (\Illuminate\Http\Request $request, \App\Services\SchoolExcelService $excelService) {
    $validator = Validator::make($request->all(), [
        'excel_file' => ['required', 'file', 'extensions:xlsx,xls', 'max:40960'],
    ], [
        'excel_file.required' => 'Vui lòng chọn tệp Excel cần nhập.',
        'excel_file.file' => 'Tệp tải lên không hợp lệ.',
        'excel_file.extensions' => 'Chỉ chấp nhận tệp Excel định dạng .xlsx hoặc .xls.',
        'excel_file.max' => 'Tệp Excel không được lớn hơn 40 MB.',
    ]);

    if ($validator->fails()) {
        \Filament\Notifications\Notification::make()
            ->title('Không thể tải tệp Excel')
            ->body($validator->errors()->first('excel_file'))
            ->danger()
            ->send();

        return redirect()->route('filament.admin.resources.schools.index');
    }

    $uploadedFile = $request->file('excel_file');
    $result = $excelService->importFromFile($uploadedFile->getRealPath());

    if (! $result['success']) {
        \Filament\Notifications\Notification::make()
            ->title('Lỗi khi đọc tệp Excel')
            ->body($result['message'] ?? 'Không thể xử lý tệp Excel.')
            ->danger()
            ->send();

        return redirect()->route('filament.admin.resources.schools.index');
    }

    $message = "Tạo mới {$result['created']} trường, cập nhật {$result['updated']} trường.";
    if ($result['skipped'] > 0) {
        $message .= " Bỏ qua {$result['skipped']} dòng trống.";
    }
    if (! empty($result['errors'])) {
        $message .= ' Có '.count($result['errors']).' dòng cần kiểm tra lại.';
    }

    \Filament\Notifications\Notification::make()
        ->title('Nhập dữ liệu Excel thành công')
        ->body($message)
        ->success()
        ->send();

    return redirect()->route('filament.admin.resources.schools.index');
})->middleware(['auth', 'throttle:10,1'])->name('admin.schools.import-excel');

Route::post('/admin/training-majors/import-excel', function (\Illuminate\Http\Request $request, \App\Services\TrainingMajorExcelService $excelService) {
    $validator = Validator::make($request->all(), [
        'excel_file' => ['required', 'file', 'extensions:xlsx,xls', 'max:40960'],
    ], [
        'excel_file.required' => 'Vui lòng chọn tệp Excel cần nhập.',
        'excel_file.file' => 'Tệp tải lên không hợp lệ.',
        'excel_file.extensions' => 'Chỉ chấp nhận tệp Excel định dạng .xlsx hoặc .xls.',
        'excel_file.max' => 'Tệp Excel không được lớn hơn 40 MB.',
    ]);

    $redirect = fn () => redirect()->route('filament.admin.resources.training-majors.index');

    if ($validator->fails()) {
        \Filament\Notifications\Notification::make()
            ->title('Không thể tải tệp danh mục chuyên ngành')
            ->body($validator->errors()->first('excel_file'))
            ->danger()
            ->send();

        return $redirect();
    }

    $result = $excelService->importFromFile($request->file('excel_file')->getRealPath());

    if (! $result['success']) {
        \Filament\Notifications\Notification::make()
            ->title('Lỗi khi đọc tệp danh mục chuyên ngành')
            ->body($result['message'] ?? 'Không thể xử lý tệp Excel.')
            ->danger()
            ->send();

        return $redirect();
    }

    $message = "Tạo mới {$result['created']} ngành, cập nhật {$result['updated']} ngành.";
    if ($result['skipped'] > 0) {
        $message .= " Bỏ qua {$result['skipped']} dòng trống.";
    }
    if (! empty($result['errors'])) {
        $message .= ' Có ' . count($result['errors']) . ' dòng lỗi cần kiểm tra lại.';
    }

    $notification = \Filament\Notifications\Notification::make()
        ->title(empty($result['errors']) ? 'Nhập danh mục chuyên ngành thành công' : 'Nhập dữ liệu có cảnh báo')
        ->body($message);

    (empty($result['errors']) ? $notification->success() : $notification->warning())->send();

    return $redirect();
})->middleware(['auth', 'throttle:10,1'])->name('admin.training-majors.import-excel');

Route::get('/api/schools/{id}', function ($id) use ($mapSchool) {
    $school = School::with(['educationLevels', 'majors'])->where('id', $id)->orWhere('code', $id)->first();
    if (!$school) {
        return response()->json(['error' => 'School not found'], 404);
    }
    $districtName = DB::table('districts')->where('id', $school->district_id)->value('name');
    $arr = $mapSchool($school, $districtName ? [$school->district_id => $districtName] : []);
    $arr['training_majors'] = $school->relationLoaded('majors') && $school->majors->isNotEmpty()
        ? $school->majors->map(fn ($major) => [
            'id' => $major->id,
            'name' => $major->name,
            'degree_level' => $major->pivot?->degree_level ?? 'cao_dang',
            'annual_quota' => (int) ($major->pivot?->annual_quota ?? 0),
        ])->values()->all()
        : $school->training_majors;

    // Campus members are intentionally fetched only here, not in the map list.
    // This keeps panning responsive even when the database has many locations.
    $rootId = $school->parent_school_id ?: $school->id;
    $root = School::query()
        ->with(['childCampuses' => fn ($query) => $query
            ->select([
                'id', 'code', 'name', 'campus_note', 'campus_type', 'campus_name',
                'parent_school_id', 'ward', 'address', 'lat', 'lng', 'status', 'education_level_id',
            ])
            ->with(['educationLevels:id,name', 'majors:id,name,slug'])])
        ->select([
            'id', 'code', 'name', 'campus_note', 'campus_type', 'campus_name',
            'parent_school_id', 'ward', 'address', 'lat', 'lng', 'status', 'education_level_id',
        ])
        ->with(['educationLevels:id,name', 'majors:id,name,slug'])
        ->find($rootId);

    if ($root) {
        $arr['campus_group'] = [
            'root_id' => $root->id,
            'root_name' => $root->name,
            'items' => collect([$root])
                ->concat($root->childCampuses)
                ->map(function (School $campus) use ($school) {
                    $level = $campus->educationLevels->first();
                    $type = $campus->campus_type ?: 'MAIN';

                    return [
                        'id' => $campus->id,
                        'code' => $campus->code,
                        'name' => $campus->name,
                        'campus_name' => $campus->campus_name,
                        'campus_note' => $campus->campus_note,
                        'campus_type' => $type,
                        'campus_type_label' => School::campusTypeLabels()[$type] ?? 'Cơ sở giáo dục',
                        'education_level' => $level?->id ?? $campus->education_level_id,
                        'education_level_name' => $level?->name,
                        'education_level_ids' => $campus->educationLevels->pluck('id')->map(fn ($id) => str_replace('-', '_', $id))->values()->all(),
                        'training_major_ids' => $campus->majors->pluck('id')->values()->all(),
                        'ward' => $campus->ward,
                        'address' => $campus->address,
                        'lat' => $campus->lat,
                        'lng' => $campus->lng,
                        'status' => $campus->status,
                        'is_current' => $campus->id === $school->id,
                    ];
                })
                ->values(),
        ];
    }

    return response()->json($arr)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, OPTIONS');
});

Route::get('/api/wards', function () {
    $wards = \Illuminate\Support\Facades\DB::table('wards')
        ->select('id', 'code', 'name', 'full_name', 'unit_type', 'area_km2')
        ->orderBy('name', 'asc')
        ->get();

    return response()->json($wards)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, OPTIONS');
});

Route::post('/api/schools', function (\Illuminate\Http\Request $request) use ($schoolPayload, $syncSchoolMajors) {
    $data = $schoolPayload($request, true);
    if (empty($data['code'])) {
        $data['code'] = (string) rand(10000, 99999);
    }
    $levelId = $data['education_level'] ?? $data['education_level_id'] ?? 'cao_dang';
    $data['education_level_id'] = $levelId;
    $majorAssignments = array_key_exists('major_assignments', $data) ? $data['major_assignments'] : null;
    unset($data['major_assignments']);
    $school = School::create($data);
    $school->educationLevels()->sync([$levelId]);
    $syncSchoolMajors($school, $majorAssignments);
    return response()->json($school, 201)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
})->middleware(['auth', 'throttle:60,1']);

Route::put('/api/schools/{id}', function ($id, \Illuminate\Http\Request $request) use ($schoolPayload, $syncSchoolMajors) {
    $school = School::where('id', $id)->orWhere('code', $id)->first();
    if (!$school) {
        return response()->json(['error' => 'School not found'], 404);
    }
    $data = $schoolPayload($request);
    $levelId = $data['education_level'] ?? $data['education_level_id'] ?? null;
    if ($levelId) {
        $data['education_level_id'] = $levelId;
        $school->educationLevels()->sync([$levelId]);
    }
    $majorAssignments = array_key_exists('major_assignments', $data) ? $data['major_assignments'] : null;
    unset($data['major_assignments']);
    $school->update($data);
    $syncSchoolMajors($school, $majorAssignments);
    return response()->json($school)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
})->middleware(['auth', 'throttle:60,1']);

Route::delete('/api/schools/{id}', function ($id) {
    $school = School::where('id', $id)->orWhere('code', $id)->first();
    if ($school) {
        if ($school->childCampuses()->exists()) {
            return response()->json([
                'error' => 'Không thể xóa cơ sở chính khi còn cơ sở trực thuộc hoặc phân hiệu.',
            ], 409);
        }
        $school->educationLevels()->detach();
        $school->majorAssignments()->delete();
        $school->delete();
    }
    return response()->json(['success' => true])
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
})->middleware(['auth', 'throttle:30,1']);

Route::options('/api/{any}', function () {
    return response('', 200)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
})->where('any', '.*');
