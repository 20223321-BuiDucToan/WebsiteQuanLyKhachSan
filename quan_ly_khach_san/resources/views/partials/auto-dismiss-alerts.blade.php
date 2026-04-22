<script>
    (() => {
        const initAutoDismissAlerts = () => {
            document.querySelectorAll('.js-auto-dismiss-alert[data-auto-dismiss]').forEach((alertElement) => {
                const delay = Number.parseInt(alertElement.dataset.autoDismiss, 10) || 5000;

                window.setTimeout(() => {
                    alertElement.style.transition = 'opacity 0.25s ease, transform 0.25s ease';
                    alertElement.style.opacity = '0';
                    alertElement.style.transform = 'translateY(-8px)';

                    window.setTimeout(() => {
                        alertElement.remove();
                    }, 250);
                }, delay);
            });
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initAutoDismissAlerts, { once: true });
            return;
        }

        initAutoDismissAlerts();
    })();
</script>
