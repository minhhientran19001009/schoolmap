<div id="fl-progress-container" aria-hidden="true">
    <div id="fl-progress-bar"></div>
</div>

<style>
#fl-progress-container {
    pointer-events: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 999999;
}
#fl-progress-bar {
    position: fixed;
    top: 0;
    left: 0;
    height: 3px;
    width: 0%;
    background: linear-gradient(90deg, #3b82f6, #6366f1, #8b5cf6, #3b82f6);
    background-size: 200% 100%;
    box-shadow: 0 0 10px rgba(99, 102, 241, 0.7), 0 0 4px rgba(59, 130, 246, 0.5);
    transition: width 0.2s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s ease;
    opacity: 0;
    animation: flGradientMove 1.5s linear infinite;
}
@keyframes flGradientMove {
    0% { background-position: 0% 50%; }
    100% { background-position: 200% 50%; }
}
</style>

<script>
(function() {
    let progressTimer = null;
    let currentProgress = 0;
    let activeRequests = 0;

    function getProgressBar() {
        return document.getElementById('fl-progress-bar');
    }

    function startProgress() {
        const bar = getProgressBar();
        if (!bar) return;

        activeRequests++;
        bar.style.opacity = '1';
        currentProgress = Math.max(currentProgress, 15);
        bar.style.width = currentProgress + '%';

        clearInterval(progressTimer);
        progressTimer = setInterval(() => {
            if (currentProgress < 85) {
                currentProgress += (85 - currentProgress) * 0.15;
                bar.style.width = currentProgress + '%';
            }
        }, 150);
    }

    function finishProgress() {
        activeRequests = Math.max(0, activeRequests - 1);
        if (activeRequests > 0) return;

        const bar = getProgressBar();
        if (!bar) return;

        clearInterval(progressTimer);
        currentProgress = 100;
        bar.style.width = '100%';

        setTimeout(() => {
            bar.style.opacity = '0';
            setTimeout(() => {
                bar.style.width = '0%';
                currentProgress = 0;
            }, 300);
        }, 200);
    }

    // Initial page load
    startProgress();
    if (document.readyState === 'complete') {
        finishProgress();
    } else {
        window.addEventListener('load', () => finishProgress(), { once: true });
    }

    // Livewire hooks
    function setupLivewireHooks() {
        if (typeof window.Livewire === 'undefined') {
            document.addEventListener('livewire:init', registerHooks, { once: true });
        } else {
            registerHooks();
        }

        function registerHooks() {
            if (!window.Livewire) return;

            // Livewire request hook (form actions, table search/filter/pagination)
            window.Livewire.hook('request', ({ uri, options, payload, respond, succeed, fail }) => {
                startProgress();
                respond(() => {
                    finishProgress();
                });
                fail(() => {
                    finishProgress();
                });
            });

            // Livewire commit hook
            window.Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
                respond(() => {
                    setTimeout(() => finishProgress(), 50);
                });
            });
        }
    }

    setupLivewireHooks();

    // Livewire SPA navigation events
    document.addEventListener('livewire:navigating', () => {
        startProgress();
    });
    document.addEventListener('livewire:navigated', () => {
        finishProgress();
    });

    // Handle standard beforeunload
    window.addEventListener('beforeunload', () => {
        startProgress();
    });
})();
</script>
