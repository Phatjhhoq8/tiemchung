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
