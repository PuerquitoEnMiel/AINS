document.addEventListener('DOMContentLoaded', () => {
    // ─── IMAGE FALLBACK CHECKER ───
    const handleBrokenImage = (img) => {
        img.style.display = 'none';
        const next = img.nextElementSibling;
        if (next && (next.classList.contains('resource-card__thumbnail-placeholder') || next.classList.contains('resource-show-thumb-placeholder'))) {
            next.style.display = 'flex';
        }
    };

    document.querySelectorAll('img').forEach((img) => {
        if (img.complete) {
            if (img.naturalWidth === 0) {
                handleBrokenImage(img);
            }
        } else {
            img.addEventListener('error', () => {
                handleBrokenImage(img);
            });
        }
    });

    // ─── PAGE TRANSITIONS ───
    const mainContent = document.getElementById('main-workspace-content');
    if (mainContent) {
        mainContent.classList.add('page-enter');
    }

    // Intercept same-origin link clicks for smooth fade-out
    document.body.addEventListener('click', (e) => {
        const link = e.target.closest('a');
        if (link && link.href) {
            // Check targets, downloads, or external links
            if (link.target === '_blank' || link.hasAttribute('download') || link.getAttribute('href').startsWith('#') || link.getAttribute('href').startsWith('javascript:')) {
                return;
            }

            try {
                const url = new URL(link.href);
                if (url.origin === window.location.origin && url.pathname !== window.location.pathname) {
                    // Prevent default click and transition
                    e.preventDefault();
                    if (mainContent) {
                        mainContent.classList.remove('page-enter');
                        mainContent.classList.add('page-exit');
                    }
                    setTimeout(() => {
                        window.location.href = link.href;
                    }, 200);
                }
            } catch (err) {
                // Ignore URL parsing errors
            }
        }
    });

    // ─── COUNT-UP STATS ANIMATION ───
    const statsElements = document.querySelectorAll('.animate-stat-count');
    statsElements.forEach(el => {
        const textVal = el.innerText.trim();
        const targetVal = parseInt(el.getAttribute('data-target') || textVal, 10);
        if (isNaN(targetVal) || targetVal <= 0) return;

        el.innerText = '0';
        let current = 0;
        const duration = 600; // ms
        const startTime = performance.now();

        const updateCount = (timestamp) => {
            const progress = Math.min((timestamp - startTime) / duration, 1);
            current = Math.floor(progress * targetVal);
            el.innerText = current;

            if (progress < 1) {
                requestAnimationFrame(updateCount);
            } else {
                el.innerText = targetVal;
            }
        };
        requestAnimationFrame(updateCount);
    });
});
