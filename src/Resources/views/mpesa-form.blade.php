@inject('mpesaPayment', 'Webkul\Mpesa\Payment\Mpesa')

{!! view_render_event('bagisto.shop.checkout.payment.mpesa.before') !!}

<!-- Prevent zooming on mobile -->
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

<div class="mpesa-payment-container">
    <div class="mpesa-header">
        <div class="mpesa-logo">
            <img src="{{ asset('vendor/mpesa/images/mpesa-logo.png') }}" alt="M-Pesa Logo" onerror="this.onerror=null; this.src='{{ asset('vendor/mpesa/images/mpesa-text-logo.svg') }}'; if(this.src.indexOf('mpesa-text-logo.svg') !== -1 && this.naturalWidth === 0) { this.style.display='none'; this.insertAdjacentHTML('afterend', '<div style=\'background-color: #43B02A; color: white; padding: 8px 15px; border-radius: 4px; font-weight: bold; font-size: 18px;\'>M-PESA</div>'); }">
        </div>
        <h3 class="mpesa-title">{{ __('M-Pesa Mobile Money') }}</h3>
    </div>

    <div class="mpesa-body">
        <form method="POST" action="{{ url('/mpesa/process') }}" id="mpesa-payment-form">
            @csrf
            <div class="mpesa-form-group">
                <label for="mpesa_phone">
                    {{ __('Phone Number') }} <span class="required">*</span>
                    <span class="mpesa-hint">(e.g., 07XXXXXXXX)</span>
                </label>

                <div class="mpesa-input-wrapper">
                    <input
                        type="tel"
                        id="mpesa_phone"
                        name="mpesa_phone"
                        class="mpesa-input"
                        placeholder="Enter your M-Pesa phone number"
                        required
                        value="{{ cart()->getCart() && cart()->getCart()->billing_address ? cart()->getCart()->billing_address->phone : '' }}"
                        pattern="(0|254|\+254)[17][0-9]{8}"
                        inputmode="numeric"
                    />
                </div>

                <div class="mpesa-error hidden" id="phone-error">
                    <span class="mpesa-error-icon">!</span>
                    {{ __('Please enter a valid phone number') }}
                </div>
            </div>

            <div class="mpesa-info-box">
                <div class="mpesa-info-row">
                    <span class="mpesa-info-label">{{ __('Amount to Pay:') }}</span>
                    <span class="mpesa-info-value">{{ core()->currency(cart()->getCart()->grand_total) }}</span>
                </div>
                <div class="mpesa-info-row">
                    <span class="mpesa-info-label">{{ __('Order Reference:') }}</span>
                    <span class="mpesa-info-value">#{{ cart()->getCart()->id }}</span>
                </div>
            </div>

            <div class="mpesa-instructions">
                <p class="mpesa-instructions-title">{{ __('How it works:') }}</p>
                <ol class="mpesa-steps">
                    <li>Enter your M-Pesa phone number</li>
                    <li>Click "Pay with M-Pesa" button</li>
                    <li>You'll receive a prompt on your phone</li>
                    <li>Enter your M-Pesa PIN to complete payment</li>
                </ol>
            </div>

            <div class="mpesa-actions">
                <button type="submit" id="mpesa-submit-btn" class="mpesa-button">
                    {{ __('Pay with M-Pesa') }}
                </button>
            </div>
        </form>
    </div>
</div>

<style>
/* Mobile-first approach - base styles are for mobile */
.mpesa-payment-container {
    background-color: #ffffff;
    border-radius: 0;
    box-shadow: none;
    margin: 0;
    overflow: hidden;
    border: none;
    width: 100%;
    max-width: 100%;
    font-size: 18px; /* Larger base font size */
}

.mpesa-header {
    background-color: #f8f9fa;
    padding: 12px 15px;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    align-items: center;
}

.mpesa-logo {
    margin-right: 10px;
}

.mpesa-logo img {
    height: 32px;
    width: auto;
}

.mpesa-title {
    font-size: 18px; /* Slightly smaller for better fit */
    font-weight: 700; /* Bolder text */
    color: #333;
    margin: 0;
}

.mpesa-body {
    padding: 15px;
}

.mpesa-form-group {
    margin-bottom: 15px;
}

.mpesa-form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
    color: #333;
    font-size: 18px; /* Slightly smaller for better fit */
}

.mpesa-hint {
    font-weight: normal;
    font-size: 16px; /* Larger hint text */
    color: #666;
    display: block;
    margin-top: 4px;
}

.required {
    color: #e53e3e;
}

.mpesa-input-wrapper {
    position: relative;
}

.mpesa-input {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 20px; /* Slightly smaller for better fit */
    transition: all 0.2s;
    box-sizing: border-box;
    -webkit-appearance: none; /* Removes iOS styling */
    height: 60px; /* Slightly shorter for better fit */
    max-width: 100%;
    margin-bottom: 5px;
}

.mpesa-input:focus {
    border-color: #43B02A;
    outline: none;
    box-shadow: 0 0 0 3px rgba(67, 176, 42, 0.15);
}

.mpesa-error {
    color: #e53e3e;
    font-size: 18px; /* Larger error text */
    margin-top: 10px;
    display: flex;
    align-items: center;
}

.mpesa-error-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 24px; /* Larger icon */
    height: 24px; /* Larger icon */
    background-color: #e53e3e;
    color: white;
    border-radius: 50%;
    font-size: 16px; /* Larger icon text */
    font-weight: bold;
    margin-right: 10px;
    flex-shrink: 0;
}

.mpesa-info-box {
    background-color: #f9fafb;
    border-radius: 8px;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #e5e7eb;
}

.mpesa-info-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 5px;
    font-size: 16px; /* Slightly smaller for better fit */
}

.mpesa-info-row:last-child {
    margin-bottom: 0;
}

.mpesa-info-label {
    color: #4b5563;
    font-weight: 600; /* Bolder text */
}

.mpesa-info-value {
    color: #111827;
    font-weight: 700; /* Bolder text */
}

.mpesa-instructions {
    display: none; /* Hide instructions on mobile to save space */
}

.mpesa-steps li:last-child {
    margin-bottom: 0;
}

.mpesa-actions {
    display: flex;
    justify-content: center;
    padding: 0 15px;
    margin-top: 15px;
    margin-bottom: 15px;
}

.mpesa-button {
    background-color: #43B02A;
    color: white;
    border: none;
    border-radius: 8px;
    padding: 15px;
    font-size: 22px; /* Slightly smaller for better fit */
    font-weight: 700; /* Bolder text */
    cursor: pointer;
    transition: background-color 0.2s;
    width: 100%;
    -webkit-appearance: none; /* Removes iOS styling */
    text-align: center;
    height: 65px; /* Slightly shorter for better fit */
    max-width: 100%;
    touch-action: manipulation; /* Improves touch response */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15); /* More prominent shadow */
    letter-spacing: 0.5px; /* Better text readability */
}

.mpesa-button:hover {
    background-color: #3a9824;
}

.mpesa-button:active {
    transform: translateY(1px);
}

.mpesa-button:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.hidden {
    display: none;
}

/* Spinner animation */
@keyframes mpesa-spin {
    to { transform: rotate(360deg); }
}

.mpesa-spinner {
    display: inline-block;
    width: 16px;
    height: 16px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    border-top-color: white;
    animation: mpesa-spin 0.8s linear infinite;
    margin-right: 8px;
    vertical-align: middle;
}

/* Tablet and desktop styles */
@media (min-width: 768px) {
    .mpesa-payment-container {
        margin: 30px auto;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        max-width: 700px; /* Wider container */
        font-size: 16px; /* Reset base font size for desktop */
    }

    .mpesa-header {
        padding: 20px;
    }

    .mpesa-logo img {
        height: 45px;
    }

    .mpesa-title {
        font-size: 20px;
    }

    .mpesa-body {
        padding: 30px;
    }

    .mpesa-form-group label {
        font-size: 18px;
        margin-bottom: 10px;
    }

    .mpesa-hint {
        font-size: 14px;
    }

    .mpesa-input {
        padding: 15px;
        height: 60px;
        font-size: 18px;
    }

    .mpesa-error {
        font-size: 16px;
    }

    .mpesa-info-box {
        padding: 20px;
        margin-bottom: 25px;
    }

    .mpesa-info-row {
        font-size: 16px;
    }

    .mpesa-instructions {
        display: block; /* Show instructions on desktop */
        padding: 20px;
        margin-bottom: 25px;
        background-color: #f0f9ff;
        border-radius: 8px;
        border: 1px solid #e0f2fe;
    }

    .mpesa-instructions-title {
        font-weight: 600;
        color: #0369a1;
        margin-top: 0;
        margin-bottom: 10px;
        font-size: 18px;
    }

    .mpesa-steps {
        margin: 0;
        padding-left: 25px;
        font-size: 16px;
    }

    .mpesa-steps li {
        margin-bottom: 10px;
        color: #334155;
    }

    .mpesa-actions {
        justify-content: flex-end;
        margin-top: 40px;
    }

    .mpesa-button {
        width: auto;
        min-width: 250px;
        padding: 15px 30px;
        height: auto;
        font-size: 18px;
    }
}
</style>

{!! view_render_event('bagisto.shop.checkout.payment.mpesa.after') !!}

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('mpesa-payment-form');
    if (form) {
        const phoneInput = document.getElementById('mpesa_phone');
        const phoneError = document.getElementById('phone-error');
        const submitBtn = document.getElementById('mpesa-submit-btn');

        form.addEventListener('submit', function(e) {
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
            submitBtn.innerHTML = '<span class="mpesa-spinner"></span> {{ __("Processing...") }}';

            // Submit form via AJAX
            fetch('{{ url("/mpesa/process") }}', {
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
                    // Redirect to the waiting page
                    window.location.href = data.redirect_url;
                } else {
                    // Show error message
                    const errorMsg = data.message || 'Payment failed. Please try again.';

                    // Create a styled error notification
                    const notification = document.createElement('div');
                    notification.className = 'mpesa-notification mpesa-notification-error';
                    notification.innerHTML = `
                        <div class="mpesa-notification-icon">✕</div>
                        <div class="mpesa-notification-content">
                            <div class="mpesa-notification-title">Payment Failed</div>
                            <div class="mpesa-notification-message">${errorMsg}</div>
                        </div>
                    `;

                    // Add the notification to the page
                    document.body.appendChild(notification);

                    // Remove the notification after 5 seconds
                    setTimeout(() => {
                        notification.classList.add('mpesa-notification-hide');
                        setTimeout(() => {
                            document.body.removeChild(notification);
                        }, 300);
                    }, 5000);

                    // Reset the button
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '{{ __("Pay with M-Pesa") }}';
                }
            })
            .catch(error => {
                console.error('Error:', error);

                // Create a styled error notification
                const notification = document.createElement('div');
                notification.className = 'mpesa-notification mpesa-notification-error';
                notification.innerHTML = `
                    <div class="mpesa-notification-icon">✕</div>
                    <div class="mpesa-notification-content">
                        <div class="mpesa-notification-title">Error</div>
                        <div class="mpesa-notification-message">An error occurred. Please try again.</div>
                    </div>
                `;

                // Add the notification to the page
                document.body.appendChild(notification);

                // Remove the notification after 5 seconds
                setTimeout(() => {
                    notification.classList.add('mpesa-notification-hide');
                    setTimeout(() => {
                        document.body.removeChild(notification);
                    }, 300);
                }, 5000);

                // Reset the button
                submitBtn.disabled = false;
                submitBtn.innerHTML = '{{ __("Pay with M-Pesa") }}';
            });
        });
    }
});
</script>

<style>
/* Notification styles */
.mpesa-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    display: flex;
    align-items: flex-start;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    padding: 16px;
    z-index: 10000;
    max-width: 400px;
    animation: mpesa-notification-slide-in 0.3s ease-out forwards;
}

.mpesa-notification-error {
    border-left: 4px solid #e53e3e;
}

.mpesa-notification-success {
    border-left: 4px solid #43B02A;
}

.mpesa-notification-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    margin-right: 12px;
    flex-shrink: 0;
}

.mpesa-notification-error .mpesa-notification-icon {
    background-color: #e53e3e;
    color: white;
    font-weight: bold;
}

.mpesa-notification-success .mpesa-notification-icon {
    background-color: #43B02A;
    color: white;
}

.mpesa-notification-content {
    flex: 1;
}

.mpesa-notification-title {
    font-weight: 600;
    font-size: 16px;
    margin-bottom: 4px;
    color: #111827;
}

.mpesa-notification-message {
    font-size: 14px;
    color: #4b5563;
}

.mpesa-notification-hide {
    animation: mpesa-notification-slide-out 0.3s ease-in forwards;
}

@keyframes mpesa-notification-slide-in {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

@keyframes mpesa-notification-slide-out {
    from { transform: translateX(0); opacity: 1; }
    to { transform: translateX(100%); opacity: 0; }
}
</style>