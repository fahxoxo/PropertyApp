<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Customer;
use App\Models\Property;
use App\Models\RentalContract;
use App\Models\LoanContract;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // สร้างลูกค้า
        $customer = Customer::create([
            'code' => 'CUS-0001',
            'first_name' => 'สมหมาย',
            'last_name' => 'ใจดี',
            'nickname' => 'หมาย',
            'phone' => '0812345678',
            'id_card' => '1234567890123',
        ]);

        // สร้างบ้าน
        $property = Property::create([
            'code' => 'PROP-0001',
            'name' => 'บ้านเดี่ยว 2 ชั้น',
            'address' => '123',
            'moo' => '1',
            'subdistrict' => 'บางนา',
            'district' => 'บางนา',
            'province' => 'กรุงเทพ',
            'price' => 10000,
            'water_meter' => 'W-0001',
            'electric_meter' => 'E-0001',
            'type' => 'โฉนด',
            'gps' => '13.6345,100.7784',
            'status' => 'vacant',
        ]);

        // สร้างสัญญาเช่า
        RentalContract::create([
            'code' => 'RENT-0001',
            'customer_id' => $customer->id,
            'property_id' => $property->id,
            'deposit' => 50000,
            'advance_rent' => 10000,
            'start_date' => '2026-01-01',
            'status' => 'active',
        ]);

        // สร้างสัญญาขายฝาก
        LoanContract::create([
            'code' => 'LOAN-0001',
            'customer_id' => $customer->id,
            'interest_rate' => 2.0,
            'principal' => 1000000,
            'principal_remaining' => 1000000,
            'duration' => '1 ปี',
            'contract_date' => '2026-01-01',
            'status' => 'อยู่ในสัญญา',
            'type' => 'ขายฝาก',
        ]);
    }
}

