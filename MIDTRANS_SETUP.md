# 💳 Midtrans Payment Gateway - Setup & Configuration

**Status:** ✅ CONFIGURED  
**Merchant ID:** M465598278  
**Date:** June 26, 2026

---

## 📋 Informasi Midtrans Anda

### **Merchant Details**
- **Merchant ID:** M465598278
- **Server Key:** Mid-server-trMqZDeb4F7yguxUTQ5IgbSW
- **Client Key:** Mid-client-2ifCLwnq_OAHXHP-
- **Status:** ✅ Configured

### **Configuration File**
- **Location:** `config/midtrans.php`
- **Status:** ✅ Updated with your credentials
- **Environment:** Sandbox (Testing) - Set `$isProduction = false`

---

## ✅ What's Configured

### **1. Server Key** ✅
```php
Server Key: Mid-server-trMqZDeb4F7yguxUTQ5IgbSW
Purpose: Backend transaction processing & verification
```

### **2. Client Key** ✅
```php
Client Key: Mid-client-2ifCLwnq_OAHXHP-
Purpose: Frontend Snap payment widget
```

### **3. Environment** ✅
```php
$isProduction = false;  // Sandbox/Testing mode
// Change to true when moving to production
```

---

## 🚀 Next Steps

### **Step 1: Setup Webhook URL** 🔴 REQUIRED

In Midtrans Dashboard:
1. Login to [Midtrans Dashboard](https://dashboard.midtrans.com)
2. Go to **Settings > Configuration**
3. Find **Notification URL** section
4. Add your webhook URL:

```
Production:  https://yourdomain.com/tirtasanita/webhook/midtrans.php
Testing:     http://localhost/tirtasanita/webhook/midtrans.php (for localhost)
```

⚠️ For local testing, you can use ngrok to expose localhost:
```bash
ngrok http 80
# Then use: https://[your-ngrok-url].ngrok.io/tirtasanita/webhook/midtrans.php
```

### **Step 2: Enable Notifications** 🔴 REQUIRED

In Midtrans Dashboard:
1. **Settings > Webhooks**
2. Make sure these events are enabled:
   - ✅ Payment Complete (settlement)
   - ✅ Payment Failed
   - ✅ Payment Pending
   - ✅ Payment Cancelled

### **Step 3: Test Transaction** ✅ RECOMMENDED

Test the payment flow:

1. **Go to homepage:** http://localhost/tirtasanita
2. **Browse packages** and create a reservation
3. **Select online payment** (Midtrans)
4. **Redirect to Midtrans Snap page**
5. **Use test card:** 4811 1111 1111 1114
6. **Expiry:** 12/25
7. **CVV:** 123
8. **Success!**

---

## 🔧 Configuration Details

### **File: `config/midtrans.php`**

```php
// Your credentials are already set
\Midtrans\Config::$serverKey = 'Mid-server-trMqZDeb4F7yguxUTQ5IgbSW';
\Midtrans\Config::$clientKey = 'Mid-client-2ifCLwnq_OAHXHP-';
\Midtrans\Config::$isProduction = false;  // Sandbox mode

// Security settings
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;
```

### **Helper Functions Available**

```php
// Generate unique transaction ID
generateTransactionId($reservationId)

// Create Snap token for payment page
createSnapToken($paymentData)

// Check transaction status
checkTransactionStatus($transactionId)

// Cancel transaction
cancelTransaction($transactionId)
```

---

## 📊 Payment Flow

```
Customer Browse Package
    ↓
Create Reservation
    ↓
Select Payment Method (Midtrans)
    ↓
Generate Snap Token
    ↓
Redirect to Midtrans Snap
    ↓
Customer Enter Payment Details
    ↓
Payment Processed
    ↓
Midtrans sends Webhook Notification
    ↓
webhook/midtrans.php processes notification
    ↓
Update Payment & Reservation Status
    ↓
Send Email Confirmation
    ↓
✅ Success!
```

---

## 🧪 Testing Credentials

### **Midtrans Sandbox Test Cards**

| Card Type | Card Number | Status |
|-----------|-------------|--------|
| Visa | 4811 1111 1111 1114 | ✅ Success |
| Visa Declined | 4911 1111 1111 1114 | ❌ Declined |
| Mastercard | 5555 5555 5555 4444 | ✅ Success |

**Expiry:** Any future date (e.g., 12/25)  
**CVV:** Any 3 digits (e.g., 123)

---

## 📱 Sandbox vs Production

### **Sandbox Mode** (Current)
```php
$isProduction = false;
// Use for testing
// Money is not charged
// Test with fake card numbers
```

### **Production Mode** (When Live)
```php
$isProduction = true;
// Real transactions
// Money is charged
// Use real payment methods
```

**To switch to production:**
1. Update `config/midtrans.php`:
   ```php
   $isProduction = getenv('MIDTRANS_IS_PRODUCTION') ?: true;
   ```
2. Or set environment variable in `.env`:
   ```
   MIDTRANS_IS_PRODUCTION=true
   ```

---

## 🔐 Security Notes

### **For Production:**

1. **Move credentials to .env file** (don't hardcode)
   ```php
   // In .env
   MIDTRANS_SERVER_KEY=Mid-server-trMqZDeb4F7yguxUTQ5IgbSW
   MIDTRANS_CLIENT_KEY=Mid-client-2ifCLwnq_OAHXHP-
   ```

2. **Don't commit credentials to Git**
   ```
   # In .gitignore
   .env
   config/midtrans.php
   ```

3. **Use HTTPS only in production**
   ```php
   // Secure cookie settings
   ini_set('session.cookie_secure', 1);
   ini_set('session.cookie_httponly', 1);
   ```

4. **Verify webhook authenticity**
   ```php
   // Midtrans sends a signature
   // Always verify before processing
   ```

---

## 🐛 Troubleshooting

### **Error: "Call to undefined function \Midtrans\Snap"**
- **Cause:** Composer dependencies not installed
- **Fix:** Run `composer install`

### **Error: "Invalid transaction ID"**
- **Cause:** Transaction ID not unique or incorrect format
- **Fix:** Use `generateTransactionId()` helper function

### **Webhook Not Received**
- **Cause:** Webhook URL not configured or firewall blocked
- **Fix:** 
  1. Check webhook URL in Midtrans Dashboard
  2. Use ngrok for localhost testing
  3. Check server firewall settings

### **Payment Pending Status**
- **Cause:** Payment method requires additional verification
- **Status:** Normal, customer may complete payment later
- **Fix:** Monitor webhook notifications

### **Cannot Access Midtrans Snap**
- **Cause:** Client Key invalid or network issue
- **Fix:** Verify Client Key in `config/midtrans.php`

---

## 📞 Midtrans Support

- **Website:** https://midtrans.com/
- **Dashboard:** https://dashboard.midtrans.com/
- **Documentation:** https://docs.midtrans.com/
- **Support:** support@midtrans.com

---

## ✅ Configuration Checklist

### **Setup Complete:**
- [x] Server Key configured
- [x] Client Key configured
- [x] Sandbox mode enabled
- [x] Helper functions added

### **Before Production:**
- [ ] Switch to production mode
- [ ] Move credentials to .env
- [ ] Setup webhook URL in dashboard
- [ ] Enable all webhook events
- [ ] Test with real payment methods
- [ ] Verify SSL/HTTPS working
- [ ] Load testing completed

---

## 🎯 What Works Now

✅ **Payment Gateway Integration**
- Midtrans Snap payment widget
- Multiple payment methods
- Real-time transaction tracking
- Webhook notifications

✅ **Payment Methods Available**
- Bank Transfer (BCA, BNI, Mandiri, etc.)
- E-Wallet (GoPay, OVO, Dana, etc.)
- Credit Card (Visa, Mastercard)
- QRIS
- And more...

✅ **Features Implemented**
- `generateTransactionId()` - Unique transaction IDs
- `createSnapToken()` - Payment page tokens
- `checkTransactionStatus()` - Status checking
- `cancelTransaction()` - Cancellation handling

---

## 📝 Configuration Summary

| Item | Value |
|------|-------|
| **Merchant ID** | M465598278 |
| **Server Key** | Mid-server-trMqZDeb4F7yguxUTQ5IgbSW |
| **Client Key** | Mid-client-2ifCLwnq_OAHXHP- |
| **Mode** | Sandbox (Testing) |
| **Config File** | config/midtrans.php |
| **Status** | ✅ Ready |

---

## 🚀 Ready to Process Payments!

Your Midtrans integration is now **✅ READY**!

**Next steps:**
1. Setup webhook URL in Midtrans Dashboard
2. Test with sample payment cards
3. Verify email confirmations working
4. When live: Switch to production mode

---

**Questions?** Check:
- Midtrans Documentation: https://docs.midtrans.com/
- Your Midtrans Dashboard: https://dashboard.midtrans.com/
- Project Documentation: See TECHNICAL_REVIEW.md

---

**Last Updated:** June 26, 2026  
**Status:** ✅ Configuration Complete

