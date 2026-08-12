<style>
    .app-dialog-overlay {
        position: fixed;
        inset: 0;
        z-index: 10000000;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(15, 23, 42, 0.62);
    }
    .app-dialog {
        width: min(100%, 430px);
        padding: 24px;
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 24px 70px rgba(15, 23, 42, 0.28);
        color: #0f172a;
    }
    .app-dialog h2 { margin: 0 0 10px; font-size: 20px; line-height: 1.35; }
    .app-dialog p { margin: 0; color: #475569; line-height: 1.6; white-space: pre-wrap; }
    .app-dialog-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px; }
    .app-dialog-button { min-height: 42px; padding: 9px 18px; border: 0; border-radius: 8px; font: inherit; font-weight: 700; cursor: pointer; }
    .app-dialog-cancel { background: #e2e8f0; color: #334155; }
    .app-dialog-confirm { background: var(--primary-color, #c8102e); color: #fff; }
    .app-dialog-button:focus-visible { outline: 3px solid rgba(0, 75, 143, 0.35); outline-offset: 2px; }
    .app-toast-region { position: fixed; top: 20px; right: 20px; z-index: 10000001; display: grid; gap: 10px; width: min(380px, calc(100vw - 32px)); pointer-events: none; }
    .app-toast { display: flex; align-items: flex-start; gap: 10px; padding: 13px 15px; border-radius: 10px; box-shadow: 0 12px 35px rgba(15, 23, 42, 0.2); background: #10b981; color: #fff; pointer-events: auto; }
    .app-toast-error { background: #ef4444; }
    .app-toast-info { background: #3b82f6; }
    .app-toast span { flex: 1; line-height: 1.45; }
    .app-toast button { padding: 0; border: 0; background: transparent; color: inherit; font-size: 20px; line-height: 1; cursor: pointer; }
    @media (max-width: 540px) {
        .app-dialog { padding: 20px; }
        .app-dialog-actions { flex-direction: column-reverse; }
        .app-dialog-button { width: 100%; }
        .app-toast-region { top: 12px; right: 16px; }
    }
</style>
<script>
    (function () {
        if (window.AppDialog) return;

        let activeDialog = null;
        let dialogId = 0;

        function openDialog(options) {
            const settings = Object.assign({
                title: options.type === 'confirm' ? 'Xác nhận' : 'Thông báo',
                message: '',
                confirmText: options.type === 'confirm' ? 'Xác nhận' : 'Đóng',
                cancelText: 'Hủy bỏ'
            }, options);

            if (activeDialog) activeDialog(false);

            return new Promise(function (resolve) {
                const previousFocus = document.activeElement;
                const overlay = document.createElement('div');
                const titleId = 'app-dialog-title-' + (++dialogId);
                const messageId = 'app-dialog-message-' + dialogId;
                overlay.className = 'app-dialog-overlay';
                overlay.innerHTML = '<div class="app-dialog" role="dialog" aria-modal="true" aria-labelledby="' + titleId + '" aria-describedby="' + messageId + '">' +
                    '<h2 id="' + titleId + '"></h2><p id="' + messageId + '"></p><div class="app-dialog-actions"></div></div>';

                const dialog = overlay.firstElementChild;
                const actions = dialog.querySelector('.app-dialog-actions');
                dialog.querySelector('h2').textContent = settings.title;
                dialog.querySelector('p').textContent = settings.message;

                if (settings.type === 'confirm') {
                    const cancelButton = document.createElement('button');
                    cancelButton.type = 'button';
                    cancelButton.className = 'app-dialog-button app-dialog-cancel';
                    cancelButton.textContent = settings.cancelText;
                    actions.appendChild(cancelButton);
                    cancelButton.addEventListener('click', function () { close(false); });
                }

                const confirmButton = document.createElement('button');
                confirmButton.type = 'button';
                confirmButton.className = 'app-dialog-button app-dialog-confirm';
                confirmButton.textContent = settings.confirmText;
                actions.appendChild(confirmButton);

                function close(result) {
                    if (!overlay.isConnected) return;
                    overlay.remove();
                    document.removeEventListener('keydown', handleKeydown);
                    activeDialog = null;
                    if (previousFocus && typeof previousFocus.focus === 'function') previousFocus.focus();
                    resolve(result);
                }

                function handleKeydown(event) {
                    if (event.key === 'Escape') {
                        event.preventDefault();
                        close(false);
                    }
                    if (event.key === 'Tab') {
                        const buttons = Array.from(dialog.querySelectorAll('button'));
                        const first = buttons[0];
                        const last = buttons[buttons.length - 1];
                        if (event.shiftKey && document.activeElement === first) {
                            event.preventDefault();
                            last.focus();
                        } else if (!event.shiftKey && document.activeElement === last) {
                            event.preventDefault();
                            first.focus();
                        }
                    }
                }

                confirmButton.addEventListener('click', function () { close(true); });
                overlay.addEventListener('click', function (event) {
                    if (event.target === overlay) close(false);
                });
                document.addEventListener('keydown', handleKeydown);
                document.body.appendChild(overlay);
                activeDialog = close;
                confirmButton.focus();
            });
        }

        function toast(message, type) {
            let region = document.querySelector('.app-toast-region');
            if (!region) {
                region = document.createElement('div');
                region.className = 'app-toast-region';
                region.setAttribute('aria-live', 'polite');
                document.body.appendChild(region);
            }
            const item = document.createElement('div');
            item.className = 'app-toast app-toast-' + (type || 'success');
            item.setAttribute('role', type === 'error' ? 'alert' : 'status');
            const text = document.createElement('span');
            const closeButton = document.createElement('button');
            text.textContent = message;
            closeButton.type = 'button';
            closeButton.setAttribute('aria-label', 'Đóng thông báo');
            closeButton.textContent = '×';
            closeButton.addEventListener('click', function () { item.remove(); });
            item.append(text, closeButton);
            region.appendChild(item);
            window.setTimeout(function () { item.remove(); }, 4500);
        }

        window.AppDialog = {
            alert: function (message, options) { return openDialog(Object.assign({ type: 'alert', message: message }, options)); },
            confirm: function (message, options) { return openDialog(Object.assign({ type: 'confirm', message: message }, options)); },
            toast: toast
        };

        document.addEventListener('submit', async function (event) {
            const form = event.target;
            if (!(form instanceof HTMLFormElement) || !form.dataset.confirm) return;
            if (form.dataset.appDialogConfirmed === 'true') {
                delete form.dataset.appDialogConfirmed;
                return;
            }
            event.preventDefault();
            const submitter = event.submitter;
            if (await window.AppDialog.confirm(form.dataset.confirm)) {
                form.dataset.appDialogConfirmed = 'true';
                form.requestSubmit(submitter && !submitter.disabled ? submitter : undefined);
            }
        }, true);
    }());
</script>
