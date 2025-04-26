@if (isset($cart) && $cart && $cart->payment_method == 'mpesa')
    <div id="mpesa-payment-form-container" class="mt-4">
        {!! view_render_event('bagisto.shop.checkout.payment.mpesa.before') !!}

        @include('mpesa::mpesa-form')

        {!! view_render_event('bagisto.shop.checkout.payment.mpesa.after') !!}
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Make sure the form is visible when the page loads if M-Pesa is selected
            const mpesaFormContainer = document.getElementById('mpesa-payment-form-container');
            if (mpesaFormContainer) {
                mpesaFormContainer.style.display = 'block';
            }
        });
    </script>
@endif