<?php

use App\Models\User;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Student;
use App\Models\Employee;
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

Route::get('print-pdf/{type}/{id?}',function($type,$id=null){
    
    switch ($type) {
        case 'receipt_voucher':
                $record = ReceiptVoucher::findOrFail($id);
                $data = ['receipt' => $record,'settings'=>SchoolSetting::first()];
                $view = "receipt_voucher";
                $file_name = "سند دفع $record->id.pdf";
            break;
        case 'receipt_voucher_list':
                $url =url()->previous();
                $parsedUrl = parse_url($url);
                if(array_key_exists('query',$parsedUrl))    parse_str($parsedUrl['query'], $queryParams);
               
                // Extract the date values
                $date_from = $queryParams['tableFilters']['payment_date']['created_from'] ?? null;
                $date_to = $queryParams['tableFilters']['payment_date']['created_until'] ?? null;
                $payment_method_id = $queryParams['tableFilters']['payment_method_id']['value'] ?? null;
                $finance_account = $queryParams['tableFilters']['finance_account']['value'] ?? null;

                $receipt_vouchers = ReceiptVoucher::oldest()->whereNull('added_by')
                ->when(
                    $date_from, // Check if $date_from is not null or empty
                    fn ($query) => $query->whereDate('created_at', '>=', $date_from),
                )
                ->when(
                    $date_to, 
                    fn ($query) => $query->whereDate('created_at', '<=', $date_to),
                )
                ->when(
                    $payment_method_id, 
                    fn ($query) => $query->wherePaymentMethodId($payment_method_id),
                )
                ->when(
                    $finance_account, 
                    fn ($query) => $query->whereHas('paymentMethod',function($query) use ($finance_account){
                        $query->whereFinanceAccountId($finance_account);
                    }),
                )
                ->get();
                $data = ['receipt_vouchers' => $receipt_vouchers,'settings'=>SchoolSetting::first(),'date_from'=>$date_from,'date_to'=>$date_to];
                $view = "receipt_voucher_list";
                $file_name = "سداد الرسوم.pdf";
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
        case 'employees':
            //remove the super admin
                $employees = Employee::whereNot('user_id',User::whereHas('roles', function($query) {
                    $query->where('name', 'super_admin')->orWhere('id',1);
                })->first()?->id)->latest()->get();
                $data = ['employees' => $employees,'settings'=>SchoolSetting::first()];
                $view = "employees";
                $file_name = "قائمة العاملين.pdf";
            break;
        case 'expenses':
                $url =url()->previous();
                $parsedUrl = parse_url($url);
                if(array_key_exists('query',$parsedUrl))    parse_str($parsedUrl['query'], $queryParams);
               
                // Extract the date values
                $date_from = $queryParams['tableFilters']['created_at']['created_from'] ?? null;
                $date_to = $queryParams['tableFilters']['created_at']['created_until'] ?? null;
                $payment_method_id = $queryParams['tableFilters']['payment_method_id']['value'] ?? null;
                $is_tax_included = $queryParams['tableFilters']['is_tax_included']['value'] ?? null;
                $transaction_category_id = $queryParams['tableFilters']['transaction_category_id']['value'] ?? null;

                $expenses = Expense::oldest()->where('is_cancelled',false)
                ->when(
                    $date_from, // Check if $date_from is not null or empty
                    fn ($query) => $query->whereDate('created_at', '>=', $date_from),
                )
                ->when(
                    $date_to, 
                    fn ($query) => $query->whereDate('created_at', '<=', $date_to),
                )
                ->when(
                    $payment_method_id, 
                    fn ($query) => $query->wherePaymentMethodId($payment_method_id),
                )
                ->when(
                    $transaction_category_id, 
                    fn ($query) => $query->whereTransactionCategoryId($transaction_category_id),
                )
                ->when(
                    $is_tax_included == 1, 
                    fn ($query) => $query->whereIsTaxIncluded(true),
                )
                ->get();
                $data = ['settings'=>SchoolSetting::first(),'expenses'=>$expenses,'date_from'=>$date_from,'date_to'=>$date_to];
                $view = "expenses";
                $file_name = trans('main.expense_list').'_'.date('Y-m-d').".pdf";
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
