<!-- M-Pesa Payment Modal -->
<!-- Prevent zooming on mobile -->
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

<div id="mpesa-payment-modal" class="mpesa-modal" style="display: none;">
    <div class="mpesa-modal-content">
        <span class="mpesa-modal-close">&times;</span>
        <div class="mpesa-modal-header">
            <h2>M-Pesa Payment</h2>
        </div>
        <div class="mpesa-modal-body">
            <div class="mpesa-logo-container" style="text-align: center; margin-bottom: 20px;">
                <img src="{{ asset('vendor/mpesa/images/mpesa-logo.png') }}" alt="M-Pesa Logo"
                    style="height: 50px; margin-bottom: 10px;"
                    onerror="this.onerror=null; this.src='{{ asset('vendor/mpesa/images/mpesa-text-logo.svg') }}'; if(this.src.indexOf('mpesa-text-logo.svg') !== -1 && this.naturalWidth === 0) { this.style.display='none'; this.insertAdjacentHTML('afterend', '<div style=\'background-color: #43B02A; color: white; padding: 8px 15px; border-radius: 4px; font-weight: bold; font-size: 18px; display: inline-block; margin-bottom: 10px;\'>M-PESA</div>'); }">
                <p style="color: #666; font-size: 14px; margin: 0;">Fast, Secure Mobile Money Payments</p>
            </div>

            <form id="mpesa-payment-form" method="POST" action="{{ url('/mpesa/process') }}">
                @csrf
                <div class="form-group">
                    <label for="mpesa_phone">Phone Number <span
                            style="color: #999; font-weight: normal; font-size: 14px;">(e.g., 07XXXXXXXX)</span></label>
                    <input type="tel" id="mpesa_phone" name="mpesa_phone" class="form-control" required
                        placeholder="Enter your M-Pesa phone number"
                        value="{{ $cart->billing_address ? $cart->billing_address->phone : '' }}"
                        pattern="(0|254|\+254)[17][0-9]{8}" inputmode="numeric">
                    <div id="phone-error" class="error-message" style="display: none;">
                        Please enter a valid phone number
                    </div>
                </div>

                <div class="payment-details">
                    <div class="detail-row">
                        <span>Amount:</span>
                        <span>{{ core()->currency($cart->grand_total) }}</span>
                    </div>
                    <div class="detail-row">
                        <span>Order Reference:</span>
                        <span>#{{ $cart->id }}</span>
                    </div>
                </div>

                <div style="font-size: 14px; color: #666; margin-bottom: 20px; line-height: 1.5;">
                    <p style="margin: 0 0 10px 0;">After clicking "Pay Now":</p>
                    <ol style="margin: 0; padding-left: 20px;">
                        <li>You'll receive an M-Pesa prompt on your phone</li>
                        <li>Enter your M-Pesa PIN to complete the payment</li>
                    </ol>
                </div>

                <div class="button-container">
                    <button type="button" id="mpesa-cancel-btn" class="btn-cancel">Cancel</button>
                    <button type="submit" id="mpesa-submit-btn" class="btn-submit">Pay Now</button>
                </div>
            </form>

            <div id="mpesa-processing" style="display: none;">
                <div class="processing-message">
                    <div class="spinner"></div>
                    <p>Processing your payment...</p>
                    <p class="small">Please check your phone for the M-Pesa prompt</p>
                    <p class="small">Do not close this window</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Mobile-first approach - base styles are for mobile */
    .mpesa-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: #fff;
        display: flex;
        align-items: flex-start;
        justify-content: center;
        z-index: 10000;
        padding: 0;
        box-sizing: border-box;
    }

    .mpesa-modal-content {
        background-color: #fff;
        border-radius: 0;
        width: 100%;
        max-width: 100%;
        box-shadow: none;
        position: relative;
        animation: modalFadeIn 0.3s ease-out;
        overflow: auto;
        height: 100vh;
        /* Full height */
        font-size: 16px;
    }

    @keyframes modalFadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .mpesa-modal-close {
        position: absolute;
        top: 10px;
        right: 15px;
        font-size: 24px;
        cursor: pointer;
        color: #666;
        transition: color 0.2s;
        line-height: 1;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1;
    }

    .mpesa-modal-close:hover {
        color: #333;
    }

    .mpesa-modal-header {
        padding: 15px;
        border-bottom: 1px solid #eee;
        background-color: #f8f9fa;
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
    }

    .mpesa-modal-header h2 {
        margin: 0;
        font-size: 18px;
        color: #333;
        font-weight: 600;
    }

    .mpesa-modal-body {
        padding: 20px 15px;
    }

    /* Form Styles */
    .form-group {
        margin-bottom: 25px;
    }

    .form-group label {
        display: block;
        margin-bottom: 10px;
        font-weight: 500;
        color: #333;
        font-size: 16px;
    }

    .form-control {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 18px;
        /* Larger font size to prevent zoom */
        transition: all 0.2s;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        box-sizing: border-box;
        -webkit-appearance: none;
        /* Removes iOS styling */
        height: 56px;
        /* Taller input for better touch targets */
        max-width: 100%;
    }

    .form-control:focus {
        border-color: #43B02A;
        outline: none;
        box-shadow: 0 0 0 3px rgba(67, 176, 42, 0.15);
    }

    .error-message {
        color: #e53e3e;
        font-size: 14px;
        margin-top: 8px;
        font-weight: 500;
    }

    /* Payment Details */
    .payment-details {
        background-color: #f9fafb;
        border-radius: 6px;
        padding: 15px;
        margin-bottom: 20px;
        border: 1px solid #eee;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        font-size: 15px;
    }

    .detail-row:last-child {
        margin-bottom: 0;
        font-weight: 600;
    }

    .detail-row span:first-child {
        color: #555;
    }

    .detail-row span:last-child {
        color: #333;
    }

    /* Button Styles */
    .button-container {
        display: flex;
        flex-direction: column;
        gap: 15px;
        margin-top: 30px;
        margin-bottom: 30px;
        padding: 0 15px;
    }

    .btn-cancel {
        padding: 14px 20px;
        background-color: #f3f4f6;
        color: #374151;
        border: none;
        border-radius: 6px;
        font-weight: 500;
        cursor: pointer;
        font-size: 16px;
        transition: background-color 0.2s;
        order: 2;
        /* Show cancel button below submit button on mobile */
        height: 50px;
        /* Taller button for better touch targets */
        touch-action: manipulation;
        /* Improves touch response */
        text-align: center;
    }

    .btn-cancel:hover {
        background-color: #e5e7eb;
    }

    .btn-cancel:active {
        transform: translateY(1px);
    }

    .btn-submit {
        padding: 16px 20px;
        background-color: #43B02A;
        color: white;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        font-size: 18px;
        transition: background-color 0.2s;
        box-shadow: 0 2px 4px rgba(67, 176, 42, 0.2);
        -webkit-appearance: none;
        /* Removes iOS styling */
        text-align: center;
        order: 1;
        /* Show submit button above cancel button on mobile */
        height: 60px;
        /* Taller button for better touch targets */
        touch-action: manipulation;
        /* Improves touch response */
    }

    .btn-submit:hover {
        background-color: #3a9824;
    }

    .btn-submit:active {
        transform: translateY(1px);
    }

    /* Processing State */
    .processing-message {
        text-align: center;
        padding: 20px 0;
    }

    .processing-message p {
        margin: 10px 0;
        font-size: 16px;
        color: #333;
    }

    .processing-message .small {
        font-size: 14px;
        color: #666;
        margin-top: 6px;
    }

    .spinner {
        display: inline-block;
        width: 40px;
        height: 40px;
        border: 4px solid rgba(67, 176, 42, 0.1);
        border-radius: 50%;
        border-top-color: #43B02A;
        animation: spin 1s ease-in-out infinite;
        margin-bottom: 15px;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    /* Tablet and desktop styles */
    @media (min-width: 768px) {
        .mpesa-modal {
            background-color: rgba(0, 0, 0, 0.5);
            align-items: center;
            padding: 0 10px;
        }

        .mpesa-modal-content {
            width: 90%;
            max-width: 500px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            height: auto;
            max-height: 90vh;
        }

        .mpesa-modal-close {
            top: 15px;
            right: 20px;
            font-size: 28px;
        }

        .mpesa-modal-header {
            padding: 20px;
        }

        .mpesa-modal-header h2 {
            font-size: 22px;
        }

        .mpesa-modal-body {
            padding: 25px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            margin-bottom: 10px;
            font-size: 16px;
        }

        .form-control {
            padding: 12px 15px;
            height: auto;
        }

        .payment-details {
            padding: 18px;
            margin-bottom: 25px;
        }

        .detail-row {
            margin-bottom: 10px;
        }

        .button-container {
            flex-direction: row;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 25px;
        }

        .btn-cancel {
            order: 1;
            /* Restore normal order on desktop */
            padding: 12px 20px;
            height: auto;
        }

        .btn-submit {
            order: 2;
            /* Restore normal order on desktop */
            padding: 12px 24px;
            min-width: 120px;
            height: auto;
        }

        .processing-message {
            padding: 30px 0;
        }

        .processing-message p {
            margin: 12px 0;
            font-size: 17px;
        }

        .spinner {
            width: 50px;
            height: 50px;
            margin-bottom: 20px;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get modal elements
        const modal = document.getElementById('mpesa-payment-modal');
        const closeBtn = document.querySelector('.mpesa-modal-close');
        const cancelBtn = document.getElementById('mpesa-cancel-btn');
        const form = document.getElementById('mpesa-payment-form');
        const phoneInput = document.getElementById('mpesa_phone');
        const phoneError = document.getElementById('phone-error');
        const submitBtn = document.getElementById('mpesa-submit-btn');
        const processingDiv = document.getElementById('mpesa-processing');

        // Close modal when clicking the X or Cancel button
        closeBtn.addEventListener('click', closeModal);
        cancelBtn.addEventListener('click', closeModal);

        // Close modal when clicking outside the content
        window.addEventListener('click', function(event) {
            if (event.target === modal) {
                closeModal();
            }
        });

        // Handle form submission
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            // Validate phone number
            const phone = phoneInput.value.trim();
            const phoneRegex = /^(0|254|\+254)[17][0-9]{8}$/;

            if (!phoneRegex.test(phone)) {
                phoneError.style.display = 'block';
                return;
            }

            phoneError.style.display = 'none';

            // Show processing state
            form.style.display = 'none';
            processingDiv.style.display = 'block';

            // Submit form via AJAX
            fetch('{{ url('/mpesa/process') }}', {
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
                        form.style.display = 'block';
                        processingDiv.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                    form.style.display = 'block';
                    processingDiv.style.display = 'none';
                });
        });

        // Function to close the modal
        function closeModal() {
            modal.style.display = 'none';
            form.style.display = 'block';
            processingDiv.style.display = 'none';
        }

        // Function to open the modal (will be called from outside)
        window.openMpesaModal = function() {
            modal.style.display = 'flex';
        };
    });
</script>
