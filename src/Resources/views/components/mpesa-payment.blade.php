@once
@push('scripts')
<script type="text/x-template" id="mpesa-payment-template">
    <form @submit.prevent="submitMpesaPayment" class="mpesa-payment-form">
        <div class="form-group">
            <label for="phone_number" class="required">Phone Number</label>
            <input 
                type="text" 
                id="phone_number" 
                v-model="phoneNumber" 
                class="control" 
                required
                placeholder="254XXXXXXXXX"
                pattern="^254[0-9]{9}$"
                title="Please enter a valid Safaricom number starting with 254"
            >
        </div>

        <button type="submit" class="theme-btn" :disabled="processing">
            @{{ processing ? 'Processing...' : 'Pay with M-Pesa' }}
        </button>
    </form>
</script>

<script type="text/javascript">
    (() => {
        app.component('mpesa-payment', {
            template: '#mpesa-payment-template',

            data() {
                return {
                    phoneNumber: '',
                    processing: false
                }
            },

            methods: {
                async submitMpesaPayment() {
                    this.processing = true;

                    try {
                        const response = await this.$axios.post("{{ route('mpesa.process') }}", {
                            phone_number: this.phoneNumber
                        });

                        if (response.data.success) {
                            this.$emit('success', response.data);
                        } else {
                            this.$emit('error', response.data.message || 'Payment failed');
                        }
                    } catch (error) {
                        this.$emit('error', error.response?.data?.message || 'Payment failed');
                    } finally {
                        this.processing = false;
                    }
                }
            }
        });
    })();
</script>

<style>
.mpesa-payment-form {
    padding: 20px;
}

.mpesa-payment-form .form-group {
    margin-bottom: 20px;
}

.mpesa-payment-form label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
}

.mpesa-payment-form label.required::after {
    content: "*";
    color: #ff0000;
    margin-left: 2px;
}

.mpesa-payment-form .control {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.mpesa-payment-form .control:focus {
    border-color: #0041ff;
    outline: none;
}

.mpesa-payment-form button {
    width: 100%;
    padding: 10px;
    background: #0041ff;
    color: white;
    border: none;
    border-radius: 4px;
    font-weight: 600;
    cursor: pointer;
}

.mpesa-payment-form button:disabled {
    background: #ccc;
    cursor: not-allowed;
}
</style>
@endpush
