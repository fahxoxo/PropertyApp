<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Check invoices table schema
$schema = DB::getSchemaBuilder();
$columns = $schema->getColumnListing('invoices');

echo "Columns in invoices table:\n";
print_r($columns);

// Check a sample invoice
$invoice = DB::table('invoices')->first();
if($invoice) {
    echo "\nSample invoice:\n";
    print_r($invoice);
} else {
    echo "\nNo invoices found\n";
}
