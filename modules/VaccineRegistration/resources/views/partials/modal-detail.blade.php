<!-- Quick View Vaccine Detail Modal (SPA) -->
<div id="vaccineDetailModal" class="spa-modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(4px); z-index: 999999; align-items: center; justify-content: center; padding: 20px; overflow-y: auto;">
    <div class="spa-modal-card" style="background: var(--bg-card, #ffffff); border-radius: 16px; max-width: 800px; width: 100%; box-shadow: 0 20px 50px rgba(0,0,0,0.25); overflow: hidden; position: relative; animation: modalFadeIn 0.25s ease-out; border: 1px solid var(--border-color, #e2e8f0);">
        <!-- Close button -->
        <button onclick="closeVaccineDetailModal()" style="position: absolute; top: 16px; right: 16px; width: 36px; height: 36px; border-radius: 50%; background: rgba(0,0,0,0.06); border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; z-index: 10; transition: all 0.2s;" onmouseover="this.style.background='rgba(0,0,0,0.12)'" onmouseout="this.style.background='rgba(0,0,0,0.06)'">
            <i data-lucide="x" style="width: 20px; height: 20px; color: #64748b;"></i>
        </button>

        <div id="modalDetailContent" style="display: flex; flex-direction: column;">
            <!-- Content will be injected dynamically via JS -->
            <div style="padding: 60px; text-align: center;">
                <i data-lucide="loader-2" style="width: 40px; height: 40px; color: var(--primary-color); animation: spin 1s linear infinite;"></i>
                <p style="margin-top: 12px; color: #64748b; font-weight: 500;">Đang tải thông tin chi tiết vắc xin...</p>
            </div>
        </div>
    </div>
</div>

<!-- Quick View Article Detail Modal (SPA) -->
<div id="articleDetailModal" class="spa-modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(4px); z-index: 999999; align-items: center; justify-content: center; padding: 20px; overflow-y: auto;">
    <div class="spa-modal-card" style="background: var(--bg-card, #ffffff); border-radius: 16px; max-width: 800px; width: 100%; box-shadow: 0 20px 50px rgba(0,0,0,0.25); overflow: hidden; position: relative; animation: modalFadeIn 0.25s ease-out; border: 1px solid var(--border-color, #e2e8f0); display: flex; flex-direction: column; max-height: 90vh;">
        <!-- Close button -->
        <button onclick="closeArticleDetailModal()" style="position: absolute; top: 16px; right: 16px; width: 36px; height: 36px; border-radius: 50%; background: rgba(0,0,0,0.06); border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; z-index: 10; transition: all 0.2s;" onmouseover="this.style.background='rgba(0,0,0,0.12)'" onmouseout="this.style.background='rgba(0,0,0,0.06)'">
            <i data-lucide="x" style="width: 20px; height: 20px; color: #64748b;"></i>
        </button>

        <div id="articleModalDetailContent" style="padding: 32px; overflow-y: auto; text-align: justify;">
            <!-- Content will be injected dynamically via JS -->
            <div style="padding: 60px; text-align: center;">
                <i data-lucide="loader-2" style="width: 40px; height: 40px; color: var(--primary-color); animation: spin 1s linear infinite;"></i>
                <p style="margin-top: 12px; color: #64748b; font-weight: 500;">Đang tải nội dung bài viết...</p>
            </div>
        </div>
    </div>
</div>

<!-- Quick Booking Lookup Modal (SPA) -->
<div id="bookingLookupModal" class="spa-modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(4px); z-index: 999999; align-items: center; justify-content: center; padding: 20px; overflow-y: auto;">
    <div class="spa-modal-card" style="background: var(--bg-card, #ffffff); border-radius: 16px; max-width: 600px; width: 100%; box-shadow: 0 20px 50px rgba(0,0,0,0.25); overflow: hidden; position: relative; animation: modalFadeIn 0.25s ease-out; border: 1px solid var(--border-color, #e2e8f0); display: flex; flex-direction: column; max-height: 90vh;">
        <!-- Close button -->
        <button onclick="closeBookingLookupModal()" style="position: absolute; top: 16px; right: 16px; width: 36px; height: 36px; border-radius: 50%; background: rgba(0,0,0,0.06); border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; z-index: 10; transition: all 0.2s;" onmouseover="this.style.background='rgba(0,0,0,0.12)'" onmouseout="this.style.background='rgba(0,0,0,0.06)'">
            <i data-lucide="x" style="width: 20px; height: 20px; color: #64748b;"></i>
        </button>

        <div style="padding: 28px; overflow-y: auto;">
            <div style="text-align: center; margin-bottom: 24px;">
                <h3 style="font-size: 20px; font-weight: 800; color: var(--primary-color, #c8102e); text-transform: uppercase; margin-bottom: 8px;">Tra Cứu Lịch Hẹn</h3>
                <p style="font-size: 13.5px; color: #64748b; margin: 0;">Nhập số điện thoại đăng ký tiêm để tra cứu trạng thái lịch hẹn.</p>
            </div>

            <!-- Form -->
            <form onsubmit="submitSpaBookingLookup(event)" style="margin-bottom: 20px;">
                <div style="margin-bottom: 12px;">
                    <label style="font-size: 13px; font-weight: 600; color: #475569; display: block; margin-bottom: 6px; text-align: left;">Số điện thoại đã dùng đặt lịch <span style="color: #dc2626;">*</span></label>
                    <input type="tel" id="lookupPhoneInput" placeholder="Ví dụ: 0938xxxxxx" required style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px 14px; font-size: 14.5px; outline: none; box-sizing: border-box;">
                </div>
                <div style="margin-bottom: 16px;">
                    <label style="font-size: 13px; font-weight: 600; color: #475569; display: block; margin-bottom: 6px; text-align: left;">Mã đặt lịch (Để xem thông tin không che - Tùy chọn)</label>
                    <input type="text" id="lookupCodeInput" placeholder="Ví dụ: MC123456" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px 14px; font-size: 14.5px; autocomplete: off; outline: none; box-sizing: border-box;">
                </div>
                <button type="submit" id="lookupSubmitBtn" style="width: 100%; background: var(--primary-color, #c8102e); border: none; color: white; padding: 12px; border-radius: 8px; font-weight: 700; font-size: 14.5px; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#a00d24'" onmouseout="this.style.background='var(--primary-color, #c8102e)'">Tra Cứu Ngay</button>
            </form>

            <!-- Result Box -->
            <div id="lookupResultBox" style="display: none; border-top: 1px solid #f1f5f9; padding-top: 20px;">
                <!-- JS Render -->
            </div>
        </div>
    </div>
</div>
