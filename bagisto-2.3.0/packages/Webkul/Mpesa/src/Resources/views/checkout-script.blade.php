@once
@push('styles')
<link rel="stylesheet" href="{{ asset('vendor/mpesa/css/mpesa.css') }}">
@endpush

@push('scripts')
<script type="text/javascript">
    (() => {
        // Function to initialize M-Pesa form
        function initMpesaForm() {
            const mpesaForm = document.getElementById('mpesa-payment-form');
            
            if (mpesaForm) {
                const phoneInput = document.getElementById('mpesa_phone');
                const phoneError = document.getElementById('phone-error');
                const submitBtn = document.getElementById('mpesa-submit-btn');
                
                mpesaForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    // Validate phone number
                    const phone = phoneInput.value.trim();
                    const phoneRegex = /^(0|254|\+254)[17][0-9]{8}$/;
                    
                    if (!phoneRegex.test(phone)) {
                        phoneError.classList.remove('hidden');
                        return false;
                    }
                    
                    phoneError.classList.add('hidden');
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner inline-block w-4 h-4 border-2 border-white rounded-full border-t-transparent animate-spin mr-2"></span> Processing...';
                    
                    // Submit form via AJAX
                    fetch('{{ route("mpesa.process") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: new URLSearchParams({
                            'mpesa_phone': phone,
                            '_token': '{{ csrf_token() }}'
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = data.redirect_url;
                        } else {
                            alert(data.message || 'Payment failed. Please try again.');
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = 'Pay with M-Pesa';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred. Please try again.');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = 'Pay with M-Pesa';
                    });
                });
            }
        }
        
        // Function to show/hide M-Pesa form based on payment method selection
        function handlePaymentMethodSelection() {
            // Check if we're on the checkout page
            if (window.location.href.includes('checkout/onepage')) {
                // Find all payment method radio buttons
                const paymentMethodRadios = document.querySelectorAll('input[name="payment[method]"]');

                if (paymentMethodRadios.length > 0) {
                    // Add change event listener to each radio button
                    paymentMethodRadios.forEach(radio => {
                        radio.addEventListener('change', function() {
                            // Check if M-Pesa is selected
                            const isMpesaSelected = this.value === 'mpesa';

                            // Find the M-Pesa form container
                            const mpesaFormContainer = document.getElementById('mpesa-payment-form-container');

                            // Show/hide the form based on selection
                            if (mpesaFormContainer) {
                                mpesaFormContainer.style.display = isMpesaSelected ? 'block' : 'none';
                            }
                        });
                    });

                    // Trigger the change event for the currently selected payment method
                    const selectedPaymentMethod = document.querySelector('input[name="payment[method]"]:checked');
                    if (selectedPaymentMethod) {
                        selectedPaymentMethod.dispatchEvent(new Event('change'));
                    }
                }
            }
        }

        // Wait for DOM to be fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize M-Pesa form if it exists
            initMpesaForm();

            // Handle payment method selection
            handlePaymentMethodSelection();

            // Set up a mutation observer to detect when the M-Pesa form is added to the DOM
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.addedNodes && mutation.addedNodes.length > 0) {
                        for (let i = 0; i < mutation.addedNodes.length; i++) {
                            const node = mutation.addedNodes[i];
                            if (node.nodeType === 1) {
                                // Check if the M-Pesa form was added
                                if (node.querySelector('#mpesa-payment-form')) {
                                    initMpesaForm();
                                }

                                // Check if payment methods were added
                                if (node.querySelector('input[name="payment[method]"]')) {
                                    handlePaymentMethodSelection();
                                }
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