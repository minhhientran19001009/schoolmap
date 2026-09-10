<?php

use App\Models\School;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/api/schools', function () {
    $schools = School::with('educationLevels')->orderBy('name', 'asc')->get();
    
    // Map education level name for backward compatibility with frontend
    $data = $schools->map(function ($s) {
        $arr = $s->toArray();
        $firstLevel = $s->educationLevels->first();
        $arr['education_level'] = $firstLevel?->id ?? $s->education_level_id ?? 'cao_dang';
        $arr['education_level_name'] = $firstLevel?->name ?? 'Giáo dục';
        $arr['education_levels_list'] = $s->educationLevels->pluck('name')->toArray();
        return $arr;
    });

    return response()->json($data)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, OPTIONS');
});

Route::get('/api/schools/download-template', function (\App\Services\SchoolExcelService $excelService) {
    return $excelService->downloadTemplate();
});

Route::get('/api/schools/export-excel', function (\App\Services\SchoolExcelService $excelService) {
    return $excelService->exportCurrentSchools();
});

Route::get('/api/schools/{id}', function ($id) {
    $school = School::with('educationLevels')->where('id', $id)->orWhere('code', $id)->first();
    if (!$school) {
        return response()->json(['error' => 'School not found'], 404);
    }
    $arr = $school->toArray();
    $firstLevel = $school->educationLevels->first();
    $arr['education_level'] = $firstLevel?->id ?? $school->education_level_id ?? 'cao_dang';
    $arr['education_level_name'] = $firstLevel?->name ?? 'Giáo dục';
    $arr['education_levels_list'] = $school->educationLevels->pluck('name')->toArray();

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

Route::post('/api/schools', function (\Illuminate\Http\Request $request) {
    $data = $request->all();
    if (empty($data['id'])) {
        $data['id'] = 'NB-' . strtoupper($data['education_level'] ?? 'SCH') . '-' . rand(1000, 9999);
    }
    if (empty($data['code'])) {
        $data['code'] = (string) rand(10000, 99999);
    }
    $levelId = $data['education_level'] ?? $data['education_level_id'] ?? 'cao_dang';
    $data['education_level_id'] = $levelId;
    $data['geom'] = \Illuminate\Support\Facades\DB::raw("Point(" . ($data['lng'] ?? 105.9745) . ", " . ($data['lat'] ?? 20.2506) . ")");
    
    $school = School::create($data);
    $school->educationLevels()->sync([$levelId]);
    return response()->json($school, 201)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});

Route::put('/api/schools/{id}', function ($id, \Illuminate\Http\Request $request) {
    $school = School::where('id', $id)->orWhere('code', $id)->first();
    if (!$school) {
        return response()->json(['error' => 'School not found'], 404);
    }
    $data = $request->all();
    if (isset($data['lat']) && isset($data['lng'])) {
        $data['geom'] = \Illuminate\Support\Facades\DB::raw("Point({$data['lng']}, {$data['lat']})");
    }
    $levelId = $data['education_level'] ?? $data['education_level_id'] ?? null;
    if ($levelId) {
        $data['education_level_id'] = $levelId;
        $school->educationLevels()->sync([$levelId]);
    }
    $school->update($data);
    return response()->json($school)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});

Route::delete('/api/schools/{id}', function ($id) {
    $school = School::where('id', $id)->orWhere('code', $id)->first();
    if ($school) {
        $school->educationLevels()->detach();
        $school->delete();
    }
    return response()->json(['success' => true])
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});

Route::options('/api/{any}', function () {
    return response('', 200)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
})->where('any', '.*');

