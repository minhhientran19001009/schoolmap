<?php

namespace Tests\Unit;

use App\Models\School;
use App\Models\User;
use Tests\TestCase;

class SchoolAccountScopeTest extends TestCase
{
    public function test_school_account_can_manage_its_main_school_and_direct_campuses_only(): void
    {
        $user = new User([
            'role' => 'SCHOOL_ADMIN',
            'school_id' => 'school-main',
            'is_active' => true,
        ]);

        $main = new School([
            'id' => 'school-main',
            'campus_type' => 'MAIN',
        ]);

        $campus = new School([
            'id' => 'school-campus',
            'campus_type' => 'CAMPUS',
            'parent_school_id' => 'school-main',
        ]);

        $otherSchool = new School([
            'id' => 'other-school',
            'campus_type' => 'MAIN',
        ]);

        $this->assertTrue($user->isSchoolAdmin());
        $this->assertTrue($user->canManageSchool($main));
        $this->assertTrue($user->canManageSchool($campus));
        $this->assertFalse($user->canManageSchool($otherSchool));
    }

    public function test_inactive_or_locked_school_account_cannot_access_the_school_panel(): void
    {
        $inactive = new User([
            'role' => 'SCHOOL_ADMIN',
            'school_id' => 'school-main',
            'is_active' => false,
        ]);

        $locked = new User([
            'role' => 'SCHOOL_ADMIN',
            'school_id' => 'school-main',
            'is_active' => true,
            'locked_at' => now(),
        ]);

        $this->assertFalse($inactive->canAccessPanel(app('filament')->getPanel('school')));
        $this->assertFalse($locked->canAccessPanel(app('filament')->getPanel('school')));
    }

    public function test_account_types_are_separated_between_the_two_panels(): void
    {
        $admin = new User([
            'role' => 'SUPER_ADMIN',
            'is_active' => true,
        ]);

        $school = new User([
            'role' => 'SCHOOL_ADMIN',
            'school_id' => 'school-main',
            'is_active' => true,
        ]);

        $adminPanel = app('filament')->getPanel('admin');
        $schoolPanel = app('filament')->getPanel('school');

        $this->assertTrue($admin->canAccessPanel($adminPanel));
        $this->assertFalse($admin->canAccessPanel($schoolPanel));
        $this->assertFalse($school->canAccessPanel($adminPanel));
        $this->assertTrue($school->canAccessPanel($schoolPanel));
    }
}
