<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Property;
use App\Models\RentalContract;
use App\Models\LoanContract;
use App\Models\Transaction;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function index() {
        // สถิติพื้นฐาน
        $customers = Customer::count();
        $properties = Property::count();
        
        // สัญญาเช่า
        $rentals = RentalContract::where('status', 'active')->count();
        $rentalsClosed = RentalContract::where('status', 'closed')->count();
        
        // สัญญาขายฝาก/จำนอง
        $loans = LoanContract::where('status', 'อยู่ในสัญญา')->count();
        
        // รายรับเดือนนี้
        $income = Transaction::whereMonth('payment_date', now()->month)
                             ->whereYear('payment_date', now()->year)->sum('amount');
        
        // สัญญาใกล้ครบกำหนด 60 วัน (ทั้งเช่าและขายฝาก)
        $expiringRentals = RentalContract::whereRaw("julianday(date(start_date, '+' || '1 year')) - julianday('now') <= 60")
            ->where('status', 'active')->count();
            
        $expiringLoans = LoanContract::whereRaw("julianday(date(contract_date, '+' || CASE 
            WHEN duration = '6 เดือน' THEN '6 months' 
            WHEN duration = '1 ปี' THEN '1 year' 
            ELSE '2 years' END)) - julianday('now') <= 60")
            ->where('status', 'อยู่ในสัญญา')->count();
        
        $expiringTotal = $expiringRentals + $expiringLoans;
        
        // บ้านเช่าว่าง (ค้างชำระ)
        $vacantProperties = Property::where('status', 'vacant')->count();
        
        // สัญญาเช่าค้างชำระ (จำนวนอาจต้องมี field overdue_amount หรือใช้ transaction history)
        $overdueRentals = RentalContract::where('status', 'active')->count(); // สำหรับตอนนี้ใช้ active แล้วตรวจสอบ transactions
        
        // สัญญาขายฝากค้างชำระ
        $overdueLoans = LoanContract::where('status', 'อยู่ในสัญญา')->count(); // สำหรับตอนนี้ใช้ active

        return view('dashboard', compact(
            'customers', 'properties', 'rentals', 'rentalsClosed', 'loans', 
            'income', 'expiringTotal', 'expiringRentals', 'expiringLoans',
            'vacantProperties', 'overdueRentals', 'overdueLoans'
        ));
    }

    public function backup() {
        return response()->download(database_path('database.sqlite'), 'backup_'.date('Y-m-d').'.sqlite');
    }

    public function restore(Request $request) {
        try {
            $request->validate([
                'backup_file' => 'required|file|mimes:sqlite'
            ]);
            
            $file = $request->file('backup_file');
            
            // สำรองฐานข้อมูลปัจจุบันก่อน
            $dbPath = database_path('database.sqlite');
            $backupPath = database_path('backups');
            
            if (!file_exists($backupPath)) {
                mkdir($backupPath, 0755, true);
            }
            
            copy($dbPath, $backupPath . '/database_' . date('Y-m-d_H-i-s') . '.sqlite');
            
            // นำเข้าไฟล์ใหม่
            $file->storeAs('', 'database.sqlite', ['disk' => 'database_path']);
            
            return back()->with('success', '✅ Restore ข้อมูลสำเร็จ! ระบบจะโหลดข้อมูลใหม่ในการเข้าถึงครั้งต่อไป');
        } catch (\Exception $e) {
            return back()->with('error', '❌ Restore ไม่สำเร็จ: ' . $e->getMessage());
        }
    }
}