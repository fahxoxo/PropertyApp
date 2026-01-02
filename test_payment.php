<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Get invoice 10
$invoice = App\Models\Invoice::find(10);

if (!$invoice) {
    echo "Invoice not found\n";
    exit(1);
}

echo "Invoice found: " . $invoice->invoice_number . "\n";
echo "Outstanding balance: " . $invoice->outstanding_balance . "\n";

// Try to record a payment
try {
    $paymentService = new App\Services\PaymentAllocationService();
    $transaction = $paymentService->processPayment(
        invoice: $invoice,
        amountPaid: 500.00,
        paymentMethod: 'cash',
        receiptNumber: 'TEST-' . time()
    );
    
    if ($transaction) {
        echo "✅ Payment recorded successfully\n";
        echo "Transaction ID: " . $transaction->id . "\n";
        
        // Refresh invoice to see updated balance
        $invoice->refresh();
        echo "New outstanding balance: " . $invoice->outstanding_balance . "\n";
    } else {
        echo "❌ Transaction returned null\n";
    }
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
