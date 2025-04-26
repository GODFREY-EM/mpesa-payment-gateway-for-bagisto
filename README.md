```markdown
# MPesa Payment Gateway for Bagisto

[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

A custom **MPesa Payment Gateway** integration package for **Bagisto 2.3.x**, built with Laravel 11 standards.  
It provides a **secure**, **easy**, and **reliable** payment solution for businesses across **East Africa**, especially **Tanzania** and **Kenya**.

---

## 📦 Installation

Install via Composer:

```bash
composer require godfrey-em/mpesa-payment-gateway-for-bagisto
```

Publish package assets:

```bash
php artisan vendor:publish --provider="Webkul\Mpesa\Providers\MpesaServiceProvider"
```

---

## ⚙️ Configuration

After installation:

1. Log into Bagisto Admin Panel.
2. Go to **Configure** > **Sales** > **Payment Methods**.
3. Find and **enable** the "MPesa" payment method.
4. Fill the required fields:
    - API Key
    - API Secret
    - Shortcode
    - Passkey
    - Callback URLs
    - Set environment (**sandbox** or **live**)

> **Tip:** Make sure your M-Pesa credentials are valid and approved for transactions.

---

## ✨ Features

- 🔹 STK Push Payment (Customer receives a payment prompt on their phone)
- 🔹 Admin Dashboard for Transactions
- 🔹 Transaction Reconciliation (Auto match with M-Pesa records)
- 🔹 Email Notifications (Admin and Customer)
- 🔹 Secure API Integration with M-Pesa
- 🔹 Developer Friendly (Well structured and easy to extend)

---

## 📂 Directory Structure

```
Mpesa/
├── composer.json
├── publishable/
│   ├── assets/
│   │   ├── css/
│   │   ├── images/
│   │   └── js/
├── src/
│   ├── Config/
│   ├── Exports/
│   ├── Http/
│   ├── Jobs/
│   ├── Lib/
│   ├── Payment/
│   ├── Providers/
│   └── Resources/
├── tests/
└── README.md
```

---

## 👨‍💻 Author

**Godfrey Ernest Mapunda**

- GitHub: [@GODFREY-EM](https://github.com/GODFREY-EM)
- Email: [godfreymapunda112@gmail.com](mailto:godfreymapunda112@gmail.com)

---

## 📜 License

This project is licensed under the [MIT License](LICENSE).

---

## 🌍 Support

If you encounter any issues, please [open an issue](https://github.com/GODFREY-EM/mpesa-payment-gateway-for-bagisto/issues).
