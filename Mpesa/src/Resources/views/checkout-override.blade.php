@once
@push('scripts')
<script type="text/javascript">
    (() => {
        // Function to override the place order button
        function overridePlaceOrderButton() {
            // Find the place order button
            const placeOrderBtn = document.querySelector('.primary-button');

            if (placeOrderBtn) {
                // Store the original click handler
                const originalClickHandler = placeOrderBtn.onclick;

                // Override the click handler
                placeOrderBtn.onclick = function(event) {
                    // Check if M-Pesa is selected
                    const mpesaRadio = document.querySelector('input[name="payment[method]"][value="mpesa"]:checked');

                    if (mpesaRadio) {
                        // Prevent the default action
                        event.preventDefault();
                        event.stopPropagation();

                        // Open the M-Pesa modal
                        if (typeof window.openMpesaModal === 'function') {
                            window.openMpesaModal();
                        } else {
                            console.error('M-Pesa modal function not found');
                            // Fall back to original behavior
                            if (originalClickHandler) {
                                originalClickHandler.call(this, event);
                            }
                        }

                        return false;
                    } else {
                        // For other payment methods, use the original handler
                        if (originalClickHandler) {
                            return originalClickHandler.call(this, event);
                        }
                    }
                };
            }
        }

        // Function to check for the place order button periodically
        function checkForPlaceOrderButton() {
            if (document.querySelector('.primary-button')) {
                overridePlaceOrderButton();
                return true;
            }
            return false;
        }

        // Wait for DOM to be fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Try immediately
            if (!checkForPlaceOrderButton()) {
                // If not found, set up an interval to check
                const buttonCheckInterval = setInterval(function() {
                    if (checkForPlaceOrderButton()) {
                        clearInterval(buttonCheckInterval);
                    }
                }, 500);

                // Stop checking after 10 seconds
                setTimeout(function() {
                    clearInterval(buttonCheckInterval);
                }, 10000);
            }

            // Also set up a mutation observer to detect when the button might be added
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.addedNodes && mutation.addedNodes.length > 0) {
                        for (let i = 0; i < mutation.addedNodes.length; i++) {
                            const node = mutation.addedNodes[i];
                            if (node.nodeType === 1 && (node.classList.contains('primary-button') || node.querySelector('.primary-button'))) {
                                overridePlaceOrderButton();
                                break;
                            }
                        }
                    }
                });
            });

            // Start observing the document body for changes
            observer.observe(document.body, { childList: true, subtree: true });
        });
    })();
</script>
@endpush
@endonce