@if($patients->isEmpty())
    <p style="color:var(--text-muted); text-align:center; padding:30px 0;">Không tìm thấy hồ sơ bệnh nhân nào.</p>
@else
    <div class="table-responsive-modern">
        <table class="table-modern">
            <thead>
                <tr>
                    <th style="width: 50px; text-align: center;">STT</th>
                    <th>Họ và tên</th>
                    <th>Ngày sinh</th>
                    <th>Giới tính</th>
                    <th>Số điện thoại</th>
                    <th>Số CCCD / Định danh</th>
                    <th style="text-align:center;">Lượt đặt lịch</th>
                    <th style="text-align:center;">Mũi đã tiêm</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($patients as $patient)
                    <tr>
                        <td style="text-align: center; color:var(--text-muted); font-weight:600;">{{ $patients->firstItem() ? ($patients->firstItem() + $loop->index) : $loop->iteration }}</td>
                        <td style="font-weight:700; color:var(--accent-color);">{{ $patient->full_name }}</td>
                        <td>{{ $patient->dob ? $patient->dob->format('d/m/Y') : 'Chưa rõ' }}</td>
                        <td>{{ $patient->gender === 'male' ? 'Nam' : ($patient->gender === 'female' ? 'Nữ' : $patient->gender) }}</td>
                        <td>{{ \Modules\VaccineRegistration\Support\PhoneNormalizer::display($patient->phone) }}</td>
                        <td>{{ $patient->identity_card ?? 'Chưa cập nhật' }}</td>
                        <td style="text-align:center; font-weight:600;">{{ $patient->registrations_count }}</td>
                        <td style="text-align:center; font-weight:600; color:var(--primary-color);">{{ $patient->administered_doses_count }}</td>
                        <td style="text-align:right;">
                            <a href="{{ route('admin.patients.show', $patient->id) }}" class="btn-action-sm">Xem hồ sơ</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div style="display:flex; justify-content:center; margin-top:20px;">
        {{ $patients->links() }}
    </div>
@endif
