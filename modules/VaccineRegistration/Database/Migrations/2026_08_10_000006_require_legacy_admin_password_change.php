<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->whereIn('role', ['super_admin', 'branch_admin'])
            ->whereNull('password_changed_at')
            ->update(['must_change_password' => true]);
    }

    public function down(): void
    {
        // Không tự động bỏ yêu cầu đổi mật khẩu khi rollback.
    }
};
