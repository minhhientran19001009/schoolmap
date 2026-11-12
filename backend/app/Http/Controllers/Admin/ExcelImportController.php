<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\EmployerExcelService;
use App\Services\JobPostingExcelService;
use App\Services\WorkforceDemandExcelService;
use Filament\Notifications\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExcelImportController extends Controller
{
    public function jobs(Request $request, JobPostingExcelService $service): RedirectResponse
    {
        return $this->import($request, $service, 'filament.admin.resources.job-postings.index', 'tin việc làm');
    }

    public function employers(Request $request, EmployerExcelService $service): RedirectResponse
    {
        return $this->import($request, $service, 'filament.admin.resources.employers.index', 'doanh nghiệp');
    }

    public function workforceDemands(Request $request, WorkforceDemandExcelService $service): RedirectResponse
    {
        return $this->import($request, $service, 'filament.admin.resources.workforce-demands.index', 'nhu cầu nhân lực');
    }

    private function import(Request $request, object $service, string $redirectRoute, string $subject): RedirectResponse
    {
        abort_unless($request->user()?->isSystemAdmin(), 403);
        $validator = Validator::make($request->all(), [
            'excel_file' => ['required', 'file', 'extensions:xlsx,xls', 'max:40960'],
            'dry_run' => ['nullable', 'boolean'],
        ], [
            'excel_file.required' => 'Vui lòng chọn tệp Excel cần nhập.',
            'excel_file.file' => 'Tệp tải lên không hợp lệ.',
            'excel_file.extensions' => 'Chỉ chấp nhận tệp Excel định dạng .xlsx hoặc .xls.',
            'excel_file.max' => 'Tệp Excel không được lớn hơn 40 MB.',
        ]);
        if ($validator->fails()) {
            Notification::make()->title('Không thể tải tệp Excel')->body($validator->errors()->first())->danger()->send();

            return redirect()->route($redirectRoute);
        }

        $dryRun = $request->boolean('dry_run');
        $result = $service->importFromFile($request->file('excel_file')->getRealPath(), $dryRun);
        if (! $result['success']) {
            Notification::make()->title('Không thể đọc tệp Excel')->body($result['message'] ?? 'Tệp không đúng định dạng.')->danger()->send();

            return redirect()->route($redirectRoute);
        }
        if ($result['errors'] !== []) {
            $preview = collect($result['errors'])->take(3)->implode("\n");
            $more = count($result['errors']) > 3 ? "\n… và ".(count($result['errors']) - 3).' lỗi khác.' : '';
            Notification::make()
                ->title('Tệp có dữ liệu chưa hợp lệ')
                ->body("Không có dữ liệu nào được ghi. {$preview}{$more}")
                ->danger()
                ->persistent()
                ->send();

            return redirect()->route($redirectRoute);
        }

        $message = "Tạo mới {$result['created']} {$subject}, cập nhật {$result['updated']} {$subject}.";
        if ($result['skipped'] > 0) {
            $message .= " Bỏ qua {$result['skipped']} dòng trống.";
        }
        if ($dryRun) {
            Notification::make()->title('Kiểm tra tệp thành công — chưa ghi dữ liệu')->body($message)->info()->send();
        } else {
            Notification::make()->title('Nhập Excel thành công')->body($message)->success()->send();
        }

        return redirect()->route($redirectRoute);
    }
}
