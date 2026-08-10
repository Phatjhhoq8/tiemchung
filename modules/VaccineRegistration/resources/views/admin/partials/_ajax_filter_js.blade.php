<style>
    #table-container {
        position: relative;
        transition: opacity 0.2s ease-in-out;
    }
    #table-container.loading {
        opacity: 0.45;
        pointer-events: none;
    }
    .table-loading-spinner {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 10;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.9);
        padding: 12px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border: 1px solid var(--border-color, #e2e8f0);
        gap: 10px;
    }
    .spin-medicare {
        border: 3px solid #f3f3f3;
        border-top: 3px solid var(--primary-color, #c8102e);
        border-radius: 50%;
        width: 22px;
        height: 22px;
        animation: spin-ajax 0.8s linear infinite;
    }
    @keyframes spin-ajax {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tableContainer = document.getElementById('table-container');
    if (!tableContainer) return;

    const filterForm = document.querySelector('form.vaccine-filter-form') || tableContainer.closest('.card-modern')?.querySelector('form') || document.querySelector('form[action]');

    let currentController = new AbortController();
    let debounceTimer = null;

    function showLoading() {
        tableContainer.classList.add('loading');
        if (!tableContainer.querySelector('.table-loading-spinner')) {
            const spinner = document.createElement('div');
            spinner.className = 'table-loading-spinner';
            spinner.innerHTML = '<div class="spin-medicare"></div><span style="font-size:13px; font-weight:600; color:var(--primary-color, #c8102e);">Đang tải...</span>';
            tableContainer.appendChild(spinner);
        }
    }

    function hideLoading() {
        tableContainer.classList.remove('loading');
        const spinner = tableContainer.querySelector('.table-loading-spinner');
        if (spinner) {
            spinner.remove();
        }
    }

    async function fetchData(url, pushState = true) {
        currentController.abort();
        currentController = new AbortController();

        showLoading();

        try {
            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                signal: currentController.signal
            });

            if (!response.ok) {
                throw new Error('HTTP error ' + response.status);
            }

            const data = await response.json();
            if (data.success && data.html !== undefined) {
                tableContainer.innerHTML = data.html;
                if (pushState) {
                    window.history.pushState({ url: url }, '', url);
                }
                if (window.lucide && typeof window.lucide.createIcons === 'function') {
                    window.lucide.createIcons();
                }
            }
        } catch (err) {
            if (err.name !== 'AbortError') {
                console.error('AJAX Filter Fetch Error:', err);
            }
        } finally {
            hideLoading();
        }
    }

    function submitFilter(page = 1) {
        if (!filterForm) return;
        const formData = new FormData(filterForm);
        const params = new URLSearchParams();

        for (const [key, value] of formData.entries()) {
            if (value !== null && value.trim() !== '') {
                params.append(key, value.trim());
            }
        }
        if (page > 1) {
            params.set('page', page);
        } else {
            params.delete('page');
        }

        const baseUrl = filterForm.action.split('?')[0];
        const queryString = params.toString();
        const url = queryString ? baseUrl + '?' + queryString : baseUrl;
        fetchData(url);
    }

    if (filterForm) {
        filterForm.addEventListener('submit', function (e) {
            e.preventDefault();
            submitFilter(1);
        });

        const searchInput = filterForm.querySelector('input[name="search"]');
        if (searchInput) {
            searchInput.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    submitFilter(1);
                }, 300);
            });
        }

        filterForm.querySelectorAll('select, input[type="date"], input[type="number"]').forEach(el => {
            el.addEventListener('change', function () {
                submitFilter(1);
            });
        });
    }

    tableContainer.addEventListener('click', function (e) {
        const link = e.target.closest('.pagination a, a.pagination-btn, a.page-link');
        if (link && link.href && !link.href.startsWith('javascript:')) {
            e.preventDefault();
            fetchData(link.href);
        }
    });

    window.addEventListener('popstate', function (e) {
        const targetUrl = (e.state && e.state.url) ? e.state.url : window.location.href;
        fetchData(targetUrl, false);

        if (filterForm) {
            const urlParams = new URLSearchParams(window.location.search);
            filterForm.querySelectorAll('input, select').forEach(input => {
                if (!input.name) return;
                const paramValue = urlParams.get(input.name) || '';
                input.value = paramValue;
            });
        }
    });
});
</script>
