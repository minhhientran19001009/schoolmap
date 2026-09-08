<?php

namespace App\Filament\Widgets;

use App\Models\EducationLevel;
use App\Models\School;
use App\Models\Ward;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalSchools = School::count();
        $stdCount = School::where('is_national_standard', true)->count();
        $totalLevels = EducationLevel::count();
        $totalWards = Ward::count();
        $phuongCount = Ward::where('unit_type', 'Phường')->count();
        $xaCount = Ward::where('unit_type', 'Xã')->count();
        $totalArea = round(Ward::sum('area_km2'), 1);

        return [
            Stat::make('Danh sách Trường học', number_format($totalSchools) . ' Trường')
                ->description("Đạt chuẩn QG: {$stdCount} trường")
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('primary'),

            Stat::make('Danh mục Cấp học', "{$totalLevels} Cấp học / Hệ đào tạo")
                ->description('Mầm non, Tiểu học, THCS, THPT, CĐ, ĐH...')
                ->descriptionIcon('heroicon-m-bookmark-square')
                ->color('info'),

            Stat::make('Phường / Xã', "{$totalWards} Đơn vị")
                ->description("{$phuongCount} Phường đô thị • {$xaCount} Xã nông thôn")
                ->descriptionIcon('heroicon-m-map-pin')
                ->color('success'),

            Stat::make('Tổng Diện tích Tự nhiên', number_format($totalArea, 1) . ' km²')
                ->description('Tỉnh Ninh Bình (Mã tỉnh GSO: 37)')
                ->descriptionIcon('heroicon-m-globe-americas')
                ->color('warning'),
        ];
    }
}
