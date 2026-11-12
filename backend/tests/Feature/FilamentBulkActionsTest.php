<?php

namespace Tests\Feature;

use App\Filament\Business\Resources\ManagedWorkforceDemands\ManagedWorkforceDemandResource;
use App\Filament\Business\Resources\ManagedWorkforceDemands\Pages\ListManagedWorkforceDemands;
use App\Filament\Resources\CareerFields\CareerFieldResource;
use App\Filament\Resources\CareerFields\Pages\ListCareerFields;
use App\Filament\Resources\DemandProposals\DemandProposalResource;
use App\Filament\Resources\DemandProposals\Pages\ListDemandProposals;
use App\Filament\Resources\EducationLevels\EducationLevelResource;
use App\Filament\Resources\EducationLevels\Pages\ListEducationLevels;
use App\Filament\Resources\EmployerAccounts\EmployerAccountResource;
use App\Filament\Resources\EmployerAccounts\Pages\ListEmployerAccounts;
use App\Filament\Resources\Employers\EmployerResource;
use App\Filament\Resources\Employers\Pages\ListEmployers;
use App\Filament\Resources\JobPostings\JobPostingResource;
use App\Filament\Resources\JobPostings\Pages\ListJobPostings;
use App\Filament\Resources\ReportingPeriods\Pages\ListReportingPeriods;
use App\Filament\Resources\ReportingPeriods\ReportingPeriodResource;
use App\Filament\Resources\SchoolAccounts\Pages\ListSchoolAccounts;
use App\Filament\Resources\SchoolAccounts\SchoolAccountResource;
use App\Filament\Resources\Schools\Pages\ListSchools;
use App\Filament\Resources\Schools\SchoolResource;
use App\Filament\Resources\Skills\Pages\ListSkills;
use App\Filament\Resources\Skills\SkillResource;
use App\Filament\Resources\TrainingCapacities\Pages\ListTrainingCapacities;
use App\Filament\Resources\TrainingCapacities\TrainingCapacityResource;
use App\Filament\Resources\TrainingMajors\Pages\ListTrainingMajors;
use App\Filament\Resources\TrainingMajors\TrainingMajorResource;
use App\Filament\Resources\Wards\Pages\ListWards;
use App\Filament\Resources\Wards\WardResource;
use App\Filament\Resources\WorkforceDemands\Pages\ListWorkforceDemands;
use App\Filament\Resources\WorkforceDemands\WorkforceDemandResource;
use App\Filament\School\Resources\TrainingCapacities\Pages\ListTrainingCapacities as ListSchoolTrainingCapacities;
use App\Filament\School\Resources\TrainingCapacities\TrainingCapacityResource as SchoolTrainingCapacityResource;
use Filament\Tables\Table;
use Tests\TestCase;

class FilamentBulkActionsTest extends TestCase
{
    public function test_all_admin_and_deletable_portal_tables_enable_row_selection_checkboxes(): void
    {
        $tables = [
            [CareerFieldResource::class, ListCareerFields::class],
            [DemandProposalResource::class, ListDemandProposals::class],
            [EducationLevelResource::class, ListEducationLevels::class],
            [EmployerAccountResource::class, ListEmployerAccounts::class],
            [EmployerResource::class, ListEmployers::class],
            [JobPostingResource::class, ListJobPostings::class],
            [ReportingPeriodResource::class, ListReportingPeriods::class],
            [SchoolAccountResource::class, ListSchoolAccounts::class],
            [SchoolResource::class, ListSchools::class],
            [SkillResource::class, ListSkills::class],
            [TrainingCapacityResource::class, ListTrainingCapacities::class],
            [TrainingMajorResource::class, ListTrainingMajors::class],
            [WardResource::class, ListWards::class],
            [WorkforceDemandResource::class, ListWorkforceDemands::class],
            [ManagedWorkforceDemandResource::class, ListManagedWorkforceDemands::class],
            [SchoolTrainingCapacityResource::class, ListSchoolTrainingCapacities::class],
        ];

        foreach ($tables as [$resource, $page]) {
            $table = $resource::table(Table::make(app($page)));
            $this->assertNotEmpty($table->getToolbarActions(), "{$resource} should define bulk toolbar actions.");
            $this->assertTrue($table->isSelectionEnabled(), "{$resource} should enable row selection checkboxes.");
        }
    }
}
