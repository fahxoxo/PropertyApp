<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Check transactions table schema
$schema = DB::getSchemaBuilder();
$columns = $schema->getColumnListing('transactions');

echo "Columns in transactions table:\n";
print_r($columns);

// Check if there are any transactions
$transactions = DB::table('transactions')->get();
echo "\nTotal transactions: " . count($transactions) . "\n";

if(count($transactions) > 0) {
    echo "Sample transaction:\n";
    print_r($transactions[0]);
}
