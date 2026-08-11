<!-- SPA Registration Drawer / Modal -->
<style>
    .spa-register-grid {
        display: grid;
        grid-template-columns: 1fr 280px;
        gap: 24px;
        box-sizing: border-box;
    }
    .spa-form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
        box-sizing: border-box;
    }
    #spaRegisterBody,
    #spaRegisterModal,
    .spa-modal-card,
    .spa-modal-overlay {
        scrollbar-width: none !important;
        -ms-overflow-style: none !important;
    }
    #spaRegisterBody::-webkit-scrollbar,
    #spaRegisterModal::-webkit-scrollbar,
    .spa-modal-card::-webkit-scrollbar,
    .spa-modal-overlay::-webkit-scrollbar {
        display: none !important;
        width: 0 !important;
        height: 0 !important;
    }
    @media (max-width: 960px) {
        .spa-register-grid {
            grid-template-columns: 1fr !important;
            gap: 20px;
        }
        .spa-register-grid aside {
            width: 100% !important;
            min-width: 100% !important;
        }
    }
    @media (max-width: 600px) {
        .spa-form-row {
            grid-template-columns: 1fr !important;
            gap: 10px;
        }
    }
</style>
<div id="spaRegisterModal" class="spa-modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.7); backdrop-filter: blur(5px); z-index: 999999; align-items: center; justify-content: center; padding: 20px; overflow-y: auto; scrollbar-width: none; -ms-overflow-style: none;">
    <div class="spa-modal-card" style="background: var(--bg-card, #ffffff); border-radius: 16px; max-width: 900px; width: 100%; box-shadow: 0 25px 60px rgba(0,0,0,0.3); overflow: hidden; position: relative; animation: modalSlideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1); border: 1px solid var(--border-color, #e2e8f0); max-height: 90vh; display: flex; flex-direction: column;">
        
        <!-- Header Modal -->
        <div style="padding: 20px 28px; border-bottom: 1px solid var(--border-color, #e2e8f0); display: flex; align-items: center; justify-content: space-between; background: var(--bg-main, #f8fafc);">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(200,16,46,0.1); color: var(--primary-color, #c8102e); display: flex; align-items: center; justify-content: center; font-weight: 800;">
                    <i data-lucide="calendar-plus" style="width: 22px; height: 22px; color: var(--primary-color);"></i>
                </div>
                <div style="text-align: left;">
                    <h3 style="font-size: 18px; font-weight: 800; color: #1e293b; margin: 0; text-align: left;">Đăng Ký Tiêm Chủng Trực Tuyến</h3>
                    <span style="font-size: 13px; color: #64748b; text-align: left;" id="spaRegisterSubtitle">Hệ thống Trung tâm Tiêm chủng Medicare — Xử lý tức thì</span>
                </div>
            </div>
            <button onclick="closeSpaRegisterModal()" style="width: 36px; height: 36px; border-radius: 50%; background: #ffffff; border: 1px solid #e2e8f0; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='#ffffff'">
                <i data-lucide="x" style="width: 20px; height: 20px; color: #64748b;"></i>
            </button>
        </div>

        <!-- Body Scrollable Content -->
        <div id="spaRegisterBody" style="padding: 28px 28px 80px 28px; overflow-y: auto; overflow-x: hidden; flex-grow: 1; scrollbar-width: none; -ms-overflow-style: none;">
            <!-- Form loader / content inserted by JS -->
            <div style="text-align: center; padding: 40px;">
                <i data-lucide="loader-2" style="width: 36px; height: 36px; color: var(--primary-color); animation: spin 1s linear infinite;"></i>
                <p style="margin-top: 12px; color: #64748b;">Đang chuẩn bị thông tin giỏ hàng & địa điểm tiêm...</p>
            </div>
        </div>

    </div>
</div>
