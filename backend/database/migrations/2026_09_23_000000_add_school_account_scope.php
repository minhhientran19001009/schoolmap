<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->ensureLegacyUserColumns();

        if (! Schema::hasColumn('users', 'school_id')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->string('school_id', 50)->nullable()->after('district_id');
                $table->foreign('school_id', 'fk_user_school')
                    ->references('id')
                    ->on('schools')
                    ->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('users', 'must_change_password')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->boolean('must_change_password')->default(false)->after('is_active');
            });
        }

        if (! Schema::hasColumn('users', 'password_changed_at')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->timestamp('password_changed_at')->nullable()->after('must_change_password');
            });
        }

        if (! Schema::hasColumn('users', 'locked_at')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->timestamp('locked_at')->nullable()->after('last_login_at');
            });
        }

        if (! $this->hasIndex('users', 'users_school_id_unique')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->unique('school_id', 'users_school_id_unique');
            });
        }

        $this->addSchoolAdminRole();
    }

    /**
     * The production database predates Laravel's default users migration and
     * already contains several application-specific columns. Keep a fresh
     * install and the existing VPS schema compatible before adding account
     * ownership fields.
     */
    private function ensureLegacyUserColumns(): void
    {
        $columns = Schema::getColumnListing('users');

        if (! in_array('username', $columns, true)) {
            Schema::table('users', function (Blueprint $table): void {
                $table->string('username', 50)->nullable()->unique();
            });
        }

        if (! in_array('password_hash', $columns, true)) {
            Schema::table('users', function (Blueprint $table): void {
                $table->string('password_hash')->nullable();
            });
        }

        if (! in_array('full_name', $columns, true)) {
            Schema::table('users', function (Blueprint $table): void {
                $table->string('full_name', 100)->nullable();
            });
        }

        if (! in_array('role', $columns, true)) {
            Schema::table('users', function (Blueprint $table): void {
                $table->string('role', 30)->default('VIEWER')->index();
            });
        }

        if (! in_array('district_id', $columns, true)) {
            Schema::table('users', function (Blueprint $table): void {
                $table->string('district_id', 50)->nullable();
            });
        }

        if (! in_array('is_active', $columns, true)) {
            Schema::table('users', function (Blueprint $table): void {
                $table->boolean('is_active')->default(true)->index();
            });
        }

        if (! in_array('last_login_at', $columns, true)) {
            Schema::table('users', function (Blueprint $table): void {
                $table->timestamp('last_login_at')->nullable();
            });
        }
    }

    private function addSchoolAdminRole(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        $column = collect(DB::select("SHOW COLUMNS FROM users WHERE Field = 'role'"))->first();
        $type = (string) ($column->Type ?? '');

        if ($type === '' || ! str_contains($type, "'SCHOOL_ADMIN'")) {
            DB::statement(
                "ALTER TABLE users MODIFY role ENUM('SUPER_ADMIN','DISTRICT_ADMIN','VIEWER','SCHOOL_ADMIN') NOT NULL DEFAULT 'VIEWER'"
            );
        }
    }

    private function hasIndex(string $table, string $index): bool
    {
        return collect(Schema::getIndexes($table))->contains(
            fn (array $definition): bool => ($definition['name'] ?? null) === $index,
        );
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'school_id')) {
            if ($this->hasIndex('users', 'users_school_id_unique')) {
                Schema::table('users', function (Blueprint $table): void {
                    $table->dropUnique('users_school_id_unique');
                });
            }

            Schema::table('users', function (Blueprint $table): void {
                $table->dropForeign('fk_user_school');
                $table->dropColumn('school_id');
            });
        }

        foreach (['must_change_password', 'password_changed_at', 'locked_at'] as $column) {
            if (Schema::hasColumn('users', $column)) {
                Schema::table('users', fn (Blueprint $table) => $table->dropColumn($column));
            }
        }
    }
};
