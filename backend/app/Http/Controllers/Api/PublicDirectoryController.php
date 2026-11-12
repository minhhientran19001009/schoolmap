<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CareerField;
use App\Models\Employer;
use App\Models\JobPosting;
use Illuminate\Http\JsonResponse;

class PublicDirectoryController extends Controller
{
    public function index(): JsonResponse
    {
        $employers = Employer::query()->where('status', 'ACTIVE')->orderBy('name')->get();
        $jobs = JobPosting::query()->where('status', 'PUBLISHED')->whereHas('employer', fn ($query) => $query->where('status', 'ACTIVE'))->with('employer')->orderBy('id')->get();

        return response()->json([
            'jobs' => $jobs->map(fn ($job) => $job->publicData())->values(),
            'career_fields' => CareerField::publicRows(onlyActive: true)->values(),
            'employers' => $employers->map(fn ($employer) => array_replace($employer->public_profile ?? [], [
                'id' => $employer->public_id ?: 'employer-'.$employer->id,
                'employer_id' => $employer->id, 'name' => $employer->name,
                'tax_code' => $employer->tax_code, 'address' => $employer->address,
                'phone' => $employer->phone, 'email' => $employer->email,
                'website' => $employer->website, 'employee_count' => $employer->employee_count,
                'industry_name' => $employer->industry, 'region_name' => $employer->region,
                'is_verified' => $employer->is_verified,
            ]))->values(),
        ])->header('Cache-Control', 'no-store');
    }
}
