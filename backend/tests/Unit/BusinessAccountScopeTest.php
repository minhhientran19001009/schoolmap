<?php

namespace Tests\Unit;

use App\Models\Employer;
use App\Models\User;
use Tests\TestCase;

class BusinessAccountScopeTest extends TestCase
{
    public function test_business_account_can_manage_only_its_employer(): void
    {
        $user = new User([
            'role' => 'BUSINESS_ADMIN',
            'employer_id' => 10,
            'is_active' => true,
        ]);

        $ownEmployer = new Employer(['name' => 'Doanh nghiệp A']);
        $ownEmployer->id = 10;
        $otherEmployer = new Employer(['name' => 'Doanh nghiệp B']);
        $otherEmployer->id = 11;

        $this->assertTrue($user->isBusinessAdmin());
        $this->assertTrue($user->canManageEmployer($ownEmployer));
        $this->assertFalse($user->canManageEmployer($otherEmployer));
    }

    public function test_account_types_are_separated_between_all_panels(): void
    {
        $business = new User(['role' => 'BUSINESS_ADMIN', 'employer_id' => 10, 'is_active' => true]);
        $school = new User(['role' => 'SCHOOL_ADMIN', 'school_id' => 'school-main', 'is_active' => true]);
        $admin = new User(['role' => 'SUPER_ADMIN', 'is_active' => true]);

        $this->assertTrue($business->canAccessPanel(app('filament')->getPanel('business')));
        $this->assertFalse($business->canAccessPanel(app('filament')->getPanel('admin')));
        $this->assertFalse($business->canAccessPanel(app('filament')->getPanel('school')));
        $this->assertFalse($school->canAccessPanel(app('filament')->getPanel('business')));
        $this->assertFalse($admin->canAccessPanel(app('filament')->getPanel('business')));
    }
}
