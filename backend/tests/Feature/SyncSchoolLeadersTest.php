<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SyncSchoolLeadersTest extends TestCase
{
    private ?string $createdBackup = null;

    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('schools', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('code');
            $table->string('name');
            $table->string('campus_type')->nullable();
            $table->string('parent_school_id')->nullable();
            $table->string('principal')->nullable();
            $table->text('leaders')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    protected function tearDown(): void
    {
        if ($this->createdBackup !== null) {
            File::delete($this->createdBackup);
        }

        parent::tearDown();
    }

    public function test_preview_does_not_change_any_school(): void
    {
        $this->insertSchool('main', 'Trường Cao đẳng Công nghiệp Nam Định', 'Người cũ');

        $this->artisan('schools:sync-leaders')->assertSuccessful();

        $this->assertSame('Người cũ', DB::table('schools')->where('id', 'main')->value('principal'));
    }

    public function test_apply_changes_only_exact_main_school_and_keeps_others(): void
    {
        $this->insertSchool('main', 'Trường Cao đẳng Công nghiệp Nam Định', 'Người cũ');
        $this->insertSchool('branch', 'Trường Cao đẳng Công nghiệp Nam Định', 'Lãnh đạo cơ sở', 'BRANCH', 'main');
        $this->insertSchool('other', 'Trường Cao đẳng Xây dựng khác', 'Không thay đổi');

        $backupDir = storage_path('app/leader-sync-backups');
        $before = File::isDirectory($backupDir) ? array_map(fn ($file) => $file->getPathname(), File::files($backupDir)) : [];

        $this->artisan('schools:sync-leaders', ['--apply' => true])->assertSuccessful();

        $main = DB::table('schools')->where('id', 'main')->first();
        $this->assertSame('Nguyễn Duy Phấn', $main->principal);
        $this->assertSame([['name' => 'Nguyễn Duy Phấn', 'position' => 'Hiệu trưởng']], json_decode($main->leaders, true));
        $this->assertSame('Lãnh đạo cơ sở', DB::table('schools')->where('id', 'branch')->value('principal'));
        $this->assertSame('Không thay đổi', DB::table('schools')->where('id', 'other')->value('principal'));

        $after = array_map(fn ($file) => $file->getPathname(), File::files($backupDir));
        $newFiles = array_values(array_diff($after, $before));
        $this->assertCount(1, $newFiles);
        $this->createdBackup = $newFiles[0];
        $this->assertSame('Người cũ', json_decode((string) File::get($this->createdBackup), true)[0]['principal']);
    }

    private function insertSchool(string $id, string $name, string $principal, string $campusType = 'MAIN', ?string $parentId = null): void
    {
        DB::table('schools')->insert([
            'id' => $id,
            'code' => 'TEST-'.$id,
            'name' => $name,
            'campus_type' => $campusType,
            'parent_school_id' => $parentId,
            'principal' => $principal,
            'leaders' => json_encode([['name' => $principal, 'position' => 'Lãnh đạo']], JSON_UNESCAPED_UNICODE),
        ]);
    }
}
