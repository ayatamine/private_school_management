<?php

use App\Models\Invoice;
use App\Models\Student;
use App\Models\SchoolSetting;
use App\Models\ReceiptVoucher;
use Illuminate\Support\Facades\Route;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as MPDF;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::view('/', 'welcome')->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('print-pdf/{type}/{id?}',function($type,$id){
    
    switch ($type) {
        case 'receipt_voucher':
                $record = ReceiptVoucher::findOrFail($id);
                $data = ['receipt' => $record,'settings'=>SchoolSetting::first()];
                $view = "receipt_voucher";
                $file_name = "fee_payment_receipt_$record->id.pdf";
            break;
        case 'invoice':
                $record = Invoice::findOrFail($id);
                $data = ['invoice' => $record,'settings'=>SchoolSetting::first()];
                $view = "invoice";
                $file_name = "invoice_$record->id.pdf";
            break;
        case 'all_payments':
                $record = Student::findOrFail($id);
                $data = ['student' => $record,'settings'=>SchoolSetting::first()];
                $view = "all_payments";
                $file_name = "student_payments_$record->id.pdf";
            break;
        case 'all_fees':
                $record = Student::findOrFail($id);
                $data = ['student' => $record,'settings'=>SchoolSetting::first()];
                $view = "all_fees";
                $file_name = "فاتورة_الرسوم_$record->username.pdf";
            break;
        
        default:
            # code...
            break;
    }
    
    $pdf = MPDF::loadView("pdf.$view", $data);
    $pdf->simpleTables = true;

    $pdf->download($file_name);
    header("Refresh:0");
})
->middleware(['auth'])
->name('print_pdf');


require __DIR__.'/auth.php';
