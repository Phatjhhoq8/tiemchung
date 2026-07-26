/**
 * News Articles Single-Page Application (SPA) Engine - Medicare Cờ Đỏ
 * Handles smooth AJAX transitions for categories, tag clouds, search, and pagination.
 */
document.addEventListener('DOMContentLoaded', function () {
    const newsContainer = document.querySelector('.news-catalog-page');
    if (!newsContainer) return;

    // Helper: Initialize Lucide icons if available
    function refreshIcons() {
        if (typeof lucide !== 'undefined' && typeof lucide.createIcons === 'function') {
            lucide.createIcons();
        }
    }

    // Helper: Smooth transition container update
    function updateNewsContent(url, pushState = true) {
        const listSection = document.querySelector('.news-horizontal-list');
        const paginationContainer = document.querySelector('.news-pagination');
        const heroSection = document.querySelector('.news-hero-section');

        if (!listSection) {
            window.location.href = url;
            return;
        }

        // Add opacity fade-out class
        listSection.style.transition = 'opacity 0.2s ease, transform 0.2s ease';
        listSection.style.opacity = '0.4';
        listSection.style.transform = 'translateY(6px)';

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            const newListSection = doc.querySelector('.news-horizontal-list');
            const newPagination = doc.querySelector('.news-pagination');
            const newHeroSection = doc.querySelector('.news-hero-section');
            const newNavTabs = doc.querySelector('.news-nav-tabs');
            const newTypeCloud = doc.querySelector('.news-type-cloud');

            // Update Nav Tabs Active State
            if (newNavTabs && document.querySelector('.news-nav-tabs')) {
                document.querySelector('.news-nav-tabs').innerHTML = newNavTabs.innerHTML;
            }

            // Update Type Cloud Active State
            if (newTypeCloud && document.querySelector('.news-type-cloud')) {
                document.querySelector('.news-type-cloud').innerHTML = newTypeCloud.innerHTML;
            }

            // Update Hero Section if page 1 vs other pages
            if (heroSection) {
                if (newHeroSection) {
                    heroSection.style.display = 'flex';
                    heroSection.innerHTML = newHeroSection.innerHTML;
                } else {
                    heroSection.style.display = 'none';
                }
            }

            // Replace Horizontal Article List
            if (newListSection && listSection) {
                listSection.innerHTML = newListSection.innerHTML;
            }

            // Replace Pagination
            if (newPagination && paginationContainer) {
                paginationContainer.innerHTML = newPagination.innerHTML;
            }

            // Animate Back In
            listSection.style.opacity = '1';
            listSection.style.transform = 'translateY(0)';

            // Refresh icons and re-bind event listeners
            refreshIcons();
            bindNewsSpaEvents();

            // Smooth scroll up to category nav bar if below it
            const navBar = document.querySelector('.news-nav-bar-container');
            if (navBar && window.scrollY > navBar.offsetTop - 80) {
                window.scrollTo({
                    top: navBar.offsetTop - 80,
                    behavior: 'smooth'
                });
            }

            // Push History State for back/forward browser support
            if (pushState) {
                history.pushState({ newsSpaUrl: url }, '', url);
            }
        })
        .catch(error => {
            console.error('News SPA fetch error:', error);
            window.location.href = url;
        });
    }

    // Bind click events on all SPA links
    function bindNewsSpaEvents() {
        // Nav tabs (Categories)
        document.querySelectorAll('.news-nav-tab').forEach(tab => {
            tab.removeEventListener('click', handleSpaClick);
            tab.addEventListener('click', handleSpaClick);
        });

        // Tag cloud items
        document.querySelectorAll('.news-cloud-item').forEach(item => {
            item.removeEventListener('click', handleSpaClick);
            item.addEventListener('click', handleSpaClick);
        });

        // Pagination links
        document.querySelectorAll('.news-pagination nav a').forEach(link => {
            link.removeEventListener('click', handleSpaClick);
            link.addEventListener('click', handleSpaClick);
        });
    }

    function handleSpaClick(e) {
        const href = this.getAttribute('href');
        if (href && href !== '#' && !href.startsWith('javascript:')) {
            e.preventDefault();
            updateNewsContent(href, true);
        }
    }

    // Bind Search Bar Form Submission via AJAX
    const searchForm = document.querySelector('.catalog-search-box .search-form');
    if (searchForm) {
        searchForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const action = this.getAttribute('action') || window.location.pathname;
            const formData = new FormData(this);
            const params = new URLSearchParams(formData).toString();
            const url = action + (params ? '?' + params : '');
            updateNewsContent(url, true);
        });
    }

    // Handle Browser PopState (Back/Forward buttons)
    window.addEventListener('popstate', function (e) {
        if (e.state && e.state.newsSpaUrl) {
            updateNewsContent(e.state.newsSpaUrl, false);
        } else {
            updateNewsContent(window.location.href, false);
        }
    });

    // Initial binding
    bindNewsSpaEvents();
});
