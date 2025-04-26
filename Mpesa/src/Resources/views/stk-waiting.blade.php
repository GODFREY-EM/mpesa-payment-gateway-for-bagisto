<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>M-Pesa Payment Processing</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f7f7f7;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }
        
        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 90%;
            box-sizing: border-box;
        }
        
        .logo {
            margin-bottom: 30px;
        }
        
        h1 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }
        
        p {
            margin-bottom: 15px;
            font-size: 16px;
        }
        
        .spinner {
            display: inline-block;
            width: 50px;
            height: 50px;
            border: 5px solid rgba(0, 65, 255, 0.1);
            border-radius: 50%;
            border-top-color: #0041ff;
            animation: spin 1s ease-in-out infinite;
            margin: 20px 0;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .status-message {
            font-size: 18px;
            font-weight: 500;
            margin: 20px 0 10px 0;
        }

        .waiting-time-container {
            display: inline-block;
            background-color: #f0f9ff;
            border: 1px solid #e0f2fe;
            border-radius: 20px;
            padding: 5px 15px;
            margin-bottom: 20px;
            color: #0369a1;
            font-size: 14px;
        }

        .waiting-label {
            font-weight: 500;
            margin-right: 5px;
        }

        #waiting-time {
            font-family: monospace;
            font-weight: 600;
        }
        
        .instructions {
            background-color: #f9fafb;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
            text-align: left;
        }
        
        .instructions h2 {
            font-size: 18px;
            margin-top: 0;
        }
        
        .instructions ol {
            margin-left: 20px;
            padding-left: 0;
        }
        
        .instructions li {
            margin-bottom: 10px;
        }
        
        .btn {
            display: inline-block;
            background-color: #0041ff;
            color: white;
            padding: 12px 24px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 500;
            margin-top: 20px;
            transition: background-color 0.2s;
        }
        
        .btn:hover {
            background-color: #0037d9;
        }
        
        .hidden {
            display: none;
        }

        .sandbox-notice {
            margin-top: 20px;
            padding: 15px;
            background-color: #fff8e6;
            border: 1px solid #ffeeba;
            border-radius: 6px;
            color: #856404;
        }

        .sandbox-notice strong {
            display: block;
            margin-bottom: 10px;
            font-size: 16px;
            color: #856404;
        }

        .sandbox-notice p {
            margin-bottom: 10px;
            font-size: 14px;
        }

        .sandbox-notice ul {
            margin: 0;
            padding-left: 20px;
            font-size: 14px;
        }

        .sandbox-notice li {
            margin-bottom: 5px;
        }

        /* Responsive styles for smaller screens */
        @media (max-width: 768px) {
            .container {
                padding: 20px;
                margin: 30px auto;
            }

            h1 {
                font-size: 20px;
            }

            p {
                font-size: 14px;
            }

            .instructions {
                padding: 12px;
            }

            .instructions h2 {
                font-size: 16px;
            }

            .instructions li {
                font-size: 14px;
            }

            .btn {
                padding: 10px 20px;
                font-size: 14px;
            }

            .sandbox-notice {
                padding: 12px;
            }

            .sandbox-notice strong {
                font-size: 14px;
            }

            .sandbox-notice p, .sandbox-notice li {
                font-size: 13px;
            }
        }

        /* Extra small devices */
        @media (max-width: 480px) {
            .container {
                padding: 15px;
                margin: 20px auto;
                width: 95%;
            }

            .logo img {
                width: 120px;
            }

            .spinner {
                width: 40px;
                height: 40px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="{{ asset('vendor/mpesa/images/mpesa-logo.svg') }}" alt="M-Pesa Logo" width="150">
        </div>
        
        <h1>M-Pesa Payment Processing</h1>
        
        <div id="processing-state">
            <div class="spinner"></div>

            <p class="status-message">Please check your phone for the M-Pesa prompt</p>

            <div class="waiting-time-container">
                <span class="waiting-label">Waiting time:</span>
                <span id="waiting-time">0:00</span>
            </div>

            <div class="instructions">
                <h2>Instructions:</h2>
                <ol>
                    <li>You will receive a prompt on your phone to enter your M-Pesa PIN</li>
                    <li>Enter your PIN and press OK</li>
                    <li>Once payment is complete, this page will automatically update</li>
                    <li>Do not close this page until the process is complete</li>
                </ol>

                @if(config('mpesa.sandbox', false))
                <div class="sandbox-notice">
                    <strong>SANDBOX MODE</strong>
                    <p>This is running in sandbox/test mode. For the STK prompt to appear on your phone:</p>
                    <ul>
                        <li>Your phone number must be registered in the Safaricom sandbox environment</li>
                        <li>Contact Safaricom Developer Support to register your phone number</li>
                        <li>Use a test phone number provided by Safaricom (e.g., 254708374149)</li>
                    </ul>
                </div>
                @endif
            </div>

            <p>Order Reference: #{{ $orderId }}</p>
        </div>
        
        <div id="success-state" class="hidden">
            <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="#4CAF50" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
            
            <h1>Payment Successful!</h1>
            
            <p>Your payment has been processed successfully.</p>
            
            <a href="{{ url('/checkout/success') }}" class="btn">Continue to Order Confirmation</a>
        </div>
        
        <div id="error-state" class="hidden">
            <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="#F44336" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="15" y1="9" x2="9" y2="15"></line>
                <line x1="9" y1="9" x2="15" y2="15"></line>
            </svg>
            
            <h1>Payment Failed</h1>
            
            <p id="error-message">There was a problem processing your payment.</p>
            
            <a href="{{ url('/checkout/onepage') }}" class="btn">Return to Checkout</a>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const processingState = document.getElementById('processing-state');
            const successState = document.getElementById('success-state');
            const errorState = document.getElementById('error-state');
            const errorMessage = document.getElementById('error-message');
            const statusMessage = document.querySelector('.status-message');
            const waitingTime = document.getElementById('waiting-time');

            let secondsWaited = 0;
            let checkCount = 0;

            // Update the waiting time every second
            const waitingInterval = setInterval(() => {
                secondsWaited++;
                if (waitingTime) {
                    const minutes = Math.floor(secondsWaited / 60);
                    const seconds = secondsWaited % 60;
                    waitingTime.textContent = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
                }
            }, 1000);

            // Check payment status every 5 seconds
            const checkStatus = () => {
                checkCount++;

                // Update status message based on how long we've been waiting
                if (checkCount === 3) {
                    statusMessage.textContent = "Waiting for you to enter your M-Pesa PIN...";
                } else if (checkCount === 6) {
                    statusMessage.textContent = "Still waiting for your confirmation...";
                } else if (checkCount === 12) {
                    statusMessage.textContent = "This is taking longer than usual. Please check your phone.";
                }

                fetch('{{ url("/mpesa/status/" . $orderId) }}')
                    .then(response => response.json())
                    .then(data => {
                        console.log('Status check response:', data);

                        if (data.success) {
                            // Payment successful
                            processingState.classList.add('hidden');
                            successState.classList.remove('hidden');
                            errorState.classList.add('hidden');

                            // Clear intervals
                            clearInterval(statusInterval);
                            clearInterval(waitingInterval);

                            // Redirect after 3 seconds
                            setTimeout(() => {
                                window.location.href = '{{ url("/checkout/success") }}';
                            }, 3000);
                        } else if (data.redirect) {
                            // Payment failed with redirect
                            processingState.classList.add('hidden');
                            successState.classList.add('hidden');
                            errorState.classList.remove('hidden');

                            if (data.message) {
                                errorMessage.textContent = data.message;
                            }

                            // Clear intervals
                            clearInterval(statusInterval);
                            clearInterval(waitingInterval);
                        } else if (data.status && data.status.ResultCode === "1032") {
                            // User cancelled the payment
                            processingState.classList.add('hidden');
                            successState.classList.add('hidden');
                            errorState.classList.remove('hidden');
                            errorMessage.textContent = "Payment cancelled. You cancelled the M-Pesa payment request.";

                            // Clear intervals
                            clearInterval(statusInterval);
                            clearInterval(waitingInterval);
                        }
                        // If still processing, continue checking
                    })
                    .catch(error => {
                        console.error('Error checking status:', error);
                    });
            };

            // Check immediately and then every 5 seconds
            checkStatus();
            const statusInterval = setInterval(checkStatus, 5000);

            // Stop checking after 2 minutes (in case of timeout)
            setTimeout(() => {
                clearInterval(statusInterval);
                clearInterval(waitingInterval);

                // If still on processing state, show error
                if (!processingState.classList.contains('hidden')) {
                    processingState.classList.add('hidden');
                    successState.classList.add('hidden');
                    errorState.classList.remove('hidden');
                    errorMessage.textContent = 'Payment request timed out. Please try again.';
                }
            }, 120000); // 2 minutes
        });
    </script>
</body>
</html>