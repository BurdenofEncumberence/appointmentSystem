<div id="kymnet-page-loader" class="kymnet-loader-overlay" aria-live="polite" aria-busy="true">
    <div class="kymnet-loader-box">
        <div class="kymnet-loader-icon">
            <div class="kymnet-loader-ball"></div>
        </div>
        <div class="kymnet-loader-title">KYMNET</div>
        <div id="kymnet-loader-msg" class="kymnet-loader-subtitle">Loading arena...</div>
        <div class="kymnet-loader-bar" role="progressbar" aria-label="Loading">
            <div class="kymnet-loader-bar-fill"></div>
        </div>
    </div>
</div>

<script>
    (function () {
        const loader = document.getElementById('kymnet-page-loader');
        const loaderMsg = document.getElementById('kymnet-loader-msg');

        function hideLoader() {
            if (!loader) return;
            loader.classList.add('is-hidden');
            loader.setAttribute('aria-busy', 'false');
            setTimeout(() => {
                if (loader.classList.contains('is-hidden')) {
                    loader.style.display = 'none';
                }
            }, 400);
        }

        function showLoader(msg) {
            if (!loader) return;
            if (msg && loaderMsg) {
                loaderMsg.textContent = msg;
            }
            loader.style.display = 'flex';
            // Force reflow
            void loader.offsetWidth;
            loader.classList.remove('is-hidden');
            loader.setAttribute('aria-busy', 'true');
        }

        window.showKymnetLoader = showLoader;
        window.hideKymnetLoader = hideLoader;

        // Auto hide once window is ready, with safety fallback
        if (document.readyState === 'complete') {
            setTimeout(hideLoader, 250);
        } else {
            window.addEventListener('load', function () {
                setTimeout(hideLoader, 250);
            });
            // Max fallback: don't block user for more than 1.5 seconds if slow network assets stall
            setTimeout(hideLoader, 1500);
        }

        // Optional: show loader when forms with data-show-loader or standard booking forms submit
        document.addEventListener('submit', function (e) {
            const form = e.target;
            if (form && (form.hasAttribute('data-show-loader') || form.getAttribute('action')?.includes('book'))) {
                showLoader('Securing your court...');
            }
        });
    })();
</script>
