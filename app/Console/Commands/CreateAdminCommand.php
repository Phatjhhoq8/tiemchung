<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Support\AdminPasswordPolicy;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\VaccineRegistration\Models\Center;

class CreateAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create
                            {--name= : Họ và tên quản trị viên}
                            {--username= : Tên đăng nhập}
                            {--email= : Địa chỉ email}
                            {--password= : Mật khẩu tạm (có thể lộ trong lịch sử shell; nên nhập tương tác)}
                            {--role= : Vai trò (super_admin hoặc branch_admin)}
                            {--center_id= : ID trung tâm tiêm chủng (cho branch_admin)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tạo tài khoản quản trị viên mới với kiểm tra độ an toàn mật khẩu và quyền hạn.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->option('name');
        $username = $this->option('username');
        $email = $this->option('email');
        $password = $this->option('password');
        $role = $this->option('role');
        $centerId = $this->option('center_id');

        // Interactive prompts if options are missing
        if (blank($name)) {
            $name = $this->ask('Nhập họ và tên admin');
        }

        if (blank($username)) {
            $username = $this->ask('Nhập tên đăng nhập (username)');
        }

        if (blank($email)) {
            $email = $this->ask('Nhập địa chỉ email');
        }

        if (blank($password)) {
            $password = $this->secret('Nhập mật khẩu tạm (ít nhất 12 ký tự, gồm chữ hoa, chữ thường, số và ký hiệu)');
        }

        if (blank($role)) {
            $roleChoice = $this->choice(
                'Chọn vai trò admin',
                ['super_admin' => 'Super Admin (Toàn quyền hệ thống)', 'branch_admin' => 'Branch Admin (Quản trị chi nhánh)'],
                'super_admin'
            );
            $role = ($roleChoice === 'Branch Admin (Quản trị chi nhánh)' || $roleChoice === 'branch_admin') ? 'branch_admin' : 'super_admin';
        }

        if ($role === 'branch_admin' && blank($centerId)) {
            $centers = Center::select('id', 'name')->get();
            if ($centers->isNotEmpty()) {
                $choices = [];
                foreach ($centers as $center) {
                    $choices[$center->id] = "{$center->name} (ID: {$center->id})";
                }
                $selected = $this->choice('Chọn trung tâm tiêm chủng phân công', $choices);
                foreach ($choices as $id => $label) {
                    if ($label === $selected) {
                        $centerId = $id;
                        break;
                    }
                }
            } else {
                $centerId = $this->ask('Nhập ID trung tâm tiêm chủng');
            }
        }

        if ($role === 'super_admin') {
            $centerId = null;
        }

        // Validate inputs
        $validator = Validator::make([
            'name' => $name,
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'role' => $role,
            'center_id' => $centerId,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:191', Rule::unique('users', 'username')],
            'email' => ['required', 'string', 'email', 'max:191', Rule::unique('users', 'email')],
            'password' => ['required', 'string', AdminPasswordPolicy::rule()],
            'role' => ['required', 'string', Rule::in(['super_admin', 'branch_admin'])],
            'center_id' => [
                Rule::requiredIf($role === 'branch_admin'),
                'nullable',
                Rule::exists('centers', 'id'),
            ],
        ], [
            'name.required' => 'Họ và tên không được để trống.',
            'username.required' => 'Tên đăng nhập không được để trống.',
            'username.unique' => 'Tên đăng nhập đã tồn tại trên hệ thống.',
            'email.required' => 'Email không được để trống.',
            'email.email' => 'Email không hợp lệ.',
            'email.unique' => 'Email đã tồn tại trên hệ thống.',
            'password.required' => 'Mật khẩu không được để trống.',
            'role.in' => 'Vai trò không hợp lệ (phải là super_admin hoặc branch_admin).',
            'center_id.required' => 'Tài khoản Branch Admin bắt buộc phải chọn trung tâm tiêm chủng.',
            'center_id.exists' => 'Trung tâm tiêm chủng được chọn không tồn tại.',
        ]);

        if ($validator->fails()) {
            $this->error('Tạo tài khoản thất bại do dữ liệu không hợp lệ:');
            foreach ($validator->errors()->all() as $error) {
                $this->error(" - {$error}");
            }

            return Command::FAILURE;
        }

        $user = User::create([
            'name' => $name,
            'username' => $username,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => $role,
            'center_id' => $role === 'branch_admin' ? (int) $centerId : null,
            'is_active' => true,
            'status' => 'active',
            'must_change_password' => true,
            'password_changed_at' => null,
        ]);

        $this->info("Đã tạo thành công tài khoản admin: {$user->username} ({$user->role})");
        if ($user->center_id) {
            $this->info("Trung tâm gán: ID {$user->center_id}");
        }

        return Command::SUCCESS;
    }
}
