<div id="waking-overlay" class="waking-overlay">
    <div class="waking-card">
        <div class="spinner"></div>
        <h2>កំពុងដាស់ Server...</h2>
        <p>Free hosting server ត្រូវការពេលប្រហែល 15-30 វិនាទីដើម្បី wake ឡើងវិញ</p>
        <p class="sub-text">សូមអត់ធ្មត់មួយភ្លែត ⏳</p>
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
    transition: opacity 0.3s ease;
}

.waking-overlay.hidden {
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
    border: 4px solid rgba(255,255,255,0.2);
    border-top-color: #3b82f6;
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
    margin: 0.25rem 0;
}

.waking-card .sub-text {
    font-size: 0.8rem;
    color: #64748b;
    margin-top: 1rem;
}
</style>

<script>
(function () {
    const overlay = document.getElementById('waking-overlay');
    const healthUrl = '/health';
    const timeoutMs = 20000;
    const startTime = Date.now();

    function hideOverlay() {
        overlay.classList.add('hidden');
        setTimeout(() => overlay.remove(), 300);
    }

    function checkServer() {
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 5000);

        fetch(healthUrl, { signal: controller.signal, cache: 'no-store' })
            .then((response) => {
                clearTimeout(timeoutId);
                if (response.ok) {
                    hideOverlay();
                } else {
                    retryOrFail();
                }
            })
            .catch(() => {
                clearTimeout(timeoutId);
                retryOrFail();
            });
    }

    function retryOrFail() {
        const elapsed = Date.now() - startTime;
        if (elapsed < timeoutMs) {
            setTimeout(checkServer, 2000);
        } else {
            // Server ប្រហែលជា sleep យូរពេក - នៅតែបង្ហាញ overlay
            // ឬអាច redirect ទៅ error page
            hideOverlay(); // fallback: ទុកឲ្យ user ឃើញ page ធម្មតា
        }
    }

    checkServer();
})();
</script>
