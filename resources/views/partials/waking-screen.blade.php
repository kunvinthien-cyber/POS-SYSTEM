<div id="waking-overlay"
    class="waking-overlay"
    role="status"
    aria-live="polite"
    aria-label="កំពុងដាស់ Server">
    <div class="waking-card">
        <div class="spinner" aria-hidden="true"></div>
        <h2>កំពុងដាស់ Server...</h2>
        <p>Free hosting ត្រូវការពេលប្រហែល 15-30 វិនាទី ដើម្បីដំណើរការ។</p>
    </div>
</div>

<style>
.waking-overlay {
    position: fixed;
    inset: 0;
    background: #0f172a;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    opacity: 1;
    transition: opacity 0.5s ease;
}

.waking-overlay.is-hidden {
    opacity: 0;
    pointer-events: none;
}

.waking-card {
    text-align: center;
    color: white;
    padding: 2rem;
    max-width: 400px;
}

.spinner {
    width: 48px;
    height: 48px;
    border: 4px solid rgba(248, 250, 252, 0.25);
    border-top-color: #f8fafc;
    border-radius: 50%;
    margin: 0 auto 1.5rem;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.waking-card h2 {
    font-size: 1.25rem;
    margin-bottom: 0.5rem;
}

.waking-card p {
    color: #cbd5e1;
    font-size: 0.9rem;
    line-height: 1.6;
    margin: 0.25rem 0;
}
</style>

<script>
(function () {
    const overlay = document.getElementById('waking-overlay');
    const healthUrl = '{{ url('/health') }}';
    const checkIntervalMs = 2000;
    const fallbackTimeoutMs = 20000;
    let checkTimer;
    let fallbackTimer;

    function hideOverlay() {
        clearInterval(checkTimer);
        clearTimeout(fallbackTimer);
        overlay.classList.add('is-hidden');
    }

    function checkServer() {
        fetch(healthUrl, {
            headers: { Accept: 'application/json' },
            cache: 'no-store',
        })
            .then((response) => response.json().then((data) => ({ response, data })))
            .then(({ response, data }) => {
                if (response.ok && data.status === 'ok') {
                    hideOverlay();
                }
            })
            .catch(() => {});
    }

    checkTimer = setInterval(checkServer, checkIntervalMs);
    fallbackTimer = setTimeout(hideOverlay, fallbackTimeoutMs);
    checkServer();
})();
</script>
