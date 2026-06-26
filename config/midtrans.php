<?php
/**
 * Midtrans Payment Gateway Configuration
 * Tirta Sanita Outbound
 * 
 * IMPORTANT: Get credentials from Midtrans Dashboard
 * Settings > Access Keys > Copy Server Key and Client Key
 * 
 * Setup URL di Midtrans Dashboard:
 * Settings > Configuration > Point of Sale URL: https://yourdomain.com/tirtasanita
 * Settings > Webhooks > Notification URL: https://yourdomain.com/tirtasanita/webhook/midtrans.php
 */

// Autoload Midtrans SDK dari Composer
require_once dirname(__FILE__) . '/../vendor/autoload.php';

// ========================================
// KONFIGURASI MIDTRANS
// ========================================

// Midtrans credentials
// ⚠️ IMPORTANT: Add your actual credentials here
// Get from Midtrans Dashboard: Settings > Access Keys
// Use .env.local or environment variables instead
$serverKey = getenv('MIDTRANS_SERVER_KEY') ?: 'YOUR_SERVER_KEY_HERE';

// Client Key untuk frontend payment page
$clientKey = getenv('MIDTRANS_CLIENT_KEY') ?: 'YOUR_CLIENT_KEY_HERE';

// Environment: false untuk sandbox/testing, true untuk production
$isProduction = getenv('MIDTRANS_IS_PRODUCTION') ?: false;

// ========================================
// SET KONFIGURASI KE MIDTRANS SDK
// ========================================

\Midtrans\Config::$serverKey = $serverKey;
\Midtrans\Config::$clientKey = $clientKey;
\Midtrans\Config::$isProduction = $isProduction;
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

// ========================================
// HELPER FUNCTIONS
// ========================================

/**
 * Generate unique transaction ID
 * @param int $reservationId
 * @return string
 */
function generateTransactionId($reservationId) {
    return 'TIRTA-' . date('YmdHis') . '-' . $reservationId;
}

/**
 * Create Snap token for payment
 * @param array $paymentData
 * @return string|null
 */
function createSnapToken($paymentData) {
    try {
        $snapToken = \Midtrans\Snap::getSnapToken($paymentData);
        return $snapToken;
    } catch (\Exception $e) {
        error_log('Snap Token Error: ' . $e->getMessage());
        return null;
    }
}

/**
 * Check transaction status
 * @param string $transactionId
 * @return array|null
 */
function checkTransactionStatus($transactionId) {
    try {
        $status = \Midtrans\Transaction::status($transactionId);
        return $status;
    } catch (\Exception $e) {
        error_log('Transaction Status Error: ' . $e->getMessage());
        return null;
    }
}

/**
 * Cancel transaction
 * @param string $transactionId
 * @return array|null
 */
function cancelTransaction($transactionId) {
    try {
        $cancel = \Midtrans\Transaction::cancel($transactionId);
        return $cancel;
    } catch (\Exception $e) {
        error_log('Cancel Transaction Error: ' . $e->getMessage());
        return null;
    }
}

?>
