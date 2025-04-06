<?php

use App\Models\User;
use App\Models\Course;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Student;
use App\Models\Employee;
use App\Models\Transport;
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
                $payment_method_id = $queryParams['tableFilters']['payment_method']['value'] ?? null;
                $finance_account_id = $queryParams['tableFilters']['finance_account']['value'] ?? null;
                $receipt_vouchers = ReceiptVoucher::oldest()->whereNull('added_by')
                ->when(
                    $date_from, 
                    fn ($query) => $query->whereDate('payment_date', '>=', $date_from),
                )
                ->when(
                    $date_to, 
                    fn ($query) => $query->whereDate('payment_date', '<=', $date_to),
                )
                ->when(
                    $payment_method_id, 
                    fn ($query) => $query->wherePaymentMethodId($payment_method_id),
                )
                ->when(
                    $finance_account_id, 
                    fn ($query) => $query->whereHas('paymentMethod',function($query) use ($finance_account_id){
                        $query->whereFinanceAccountId($finance_account_id);
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
                // return view('pdf.invoice',$data);
            break;
        case 'all_payments':
                $record = Student::findOrFail($id);
                $data = ['student' => $record,'settings'=>SchoolSetting::first()];
                $view = "all_payments";
                $file_name = "قائمة المدفوعات $record->id.pdf";
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
        case 'students_transportation':
                $url =url()->previous();
                $parsedUrl = parse_url($url);
                if(array_key_exists('query',$parsedUrl))    parse_str($parsedUrl['query'], $queryParams);
                $transport_fee_id = $queryParams['tableFilters']['transport_fee_id']['value'] ?? null;
                $termination_date = $queryParams['tableFilters']['termination_date']['value'] ?? null;

                $students_transportation = Transport::with('student')->with('transportFee')->with('vehicle')
                ->when(
                    $transport_fee_id, 
                    fn ($query) => $query->whereTransportFeeId($transport_fee_id),
                )
                ->when(
                    $termination_date == 1, 
                    fn ($query) => $query->whereTerminationDate(null) ,
                )
                ->get();
                $data = ['students_transportation' => $students_transportation,'settings'=>SchoolSetting::first()];
                $view = "students_transportation";
                $file_name = "قائمة المواصلات.pdf";
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
            case 'student_termination':
                //remove the super admin
                    $students = Student::whereHas('termination')->latest()->get();
                    $data = ['students' => $students,'settings'=>SchoolSetting::first()];
                    $view = "student_termination";
                    $file_name = "قائمة الطلاب منتهي القيد.pdf";
                break;
            case 'newest_students':
                //remove the super admin
                $url =url()->previous();
                $parsedUrl = parse_url($url);
                if(array_key_exists('query',$parsedUrl))    parse_str($parsedUrl['query'], $queryParams);
               
                // Extract the date values
                $course_id = $queryParams['tableFilters']['course_id']['value'] ?? null;
                $status = $queryParams['tableFilters']['status']['value'] ?? null;
                
                $students = Student::whereDoesntHave('termination')
                ->when($course_id, function($query) use ($course_id){
                    return $query->whereHas('semester', function ($q) use ($course_id) {
                        return $q->where('course_id', $course_id);
                    });
                })
                ->when($status, function($query) use ($status){
                    return $query->where('status', $status);
                })
                ->latest()
                ->get();
                $data = ['students' => $students,'settings'=>SchoolSetting::first(),'course_id'=>$course_id,'status'=>$status];
                $view = "newest_students";
                $file_name = "قائمة تسجيل الطلاب.pdf";
                break;
            case 'students':
                //remove the super admin
                $url =url()->previous();
                $parsedUrl = parse_url($url);
                if(array_key_exists('query',$parsedUrl))    parse_str($parsedUrl['query'], $queryParams);
               
                // Extract the date values
                $academic_stage_id = $queryParams['tableFilters']['academic_stage_id']['value'] ?? null;
                $course_id = $queryParams['tableFilters']['course_id']['value'] ?? null;
                $nationality = $queryParams['tableFilters']['nationality']['value'] ?? null;
                
                $students = Student::whereDoesntHave('termination')
                ->where('status','approved')
                ->when($academic_stage_id, function($query) use ($academic_stage_id){
                    $courses = Course::whereHas('academicStage', function ($query) use ($academic_stage_id) {
                        return $query->where('academic_stage_id', $academic_stage_id);
                    })->pluck('id');
                    return $query->whereHas('semester', function ($query) use ($courses) {
                        return $query->whereIn('course_id', $courses);
                    });
                    return $query->whereAcademicStageId($academic_stage_id);
                })
                ->when($course_id, function($query) use ($course_id){
                    return $query->whereHas('semester', function ($q) use ($course_id) {
                        return $q->where('course_id', $course_id);
                    });
                })
                ->when($nationality, fn ($query) => $nationality == 'saudian' ? $query->where('nationality', 'saudian') : $query->where('nationality', '!=', 'saudian'))
                ->latest()
                ->get();
                $data = ['students' => $students,'settings'=>SchoolSetting::first(),
                'academic_stage_id'=>$academic_stage_id,'course_id'=>$course_id,'nationality'=>$nationality];
                $view = "students";
                $file_name = "قائمة الطلاب.pdf";
                break;
            case 'tuition_fees_reports':
                //remove the super admin
                $url =url()->previous();
                $parsedUrl = parse_url($url);
                if(array_key_exists('query',$parsedUrl))    parse_str($parsedUrl['query'], $queryParams);
               
                // Extract the date values
                $academic_year_id = $queryParams['tableFilters']['academic_year_id']['value'] ?? null;
                $academic_stage_id = $queryParams['tableFilters']['academic_stage_id']['value'] ?? null;
                $semester_id = $queryParams['tableFilters']['semester_id']['value'] ?? null;
                $course_id = $queryParams['tableFilters']['course_id']['value'] ?? null;
                $nationality = $queryParams['tableFilters']['nationality']['value'] ?? null;
                
                $students = Student::whereDoesntHave('termination')
                ->where('status','approved')
                ->when($academic_year_id, function($query) use ($academic_year_id){
                    return $query->whereHas('tuitionFees', function ($q) use ($academic_year_id) {
                        return $q->where('academic_year_id', $academic_year_id);
                    });
                })
                ->when($academic_stage_id, function($query) use ($academic_stage_id){
                    $courses = Course::whereHas('academicStage', function ($query) use ($academic_stage_id) {
                        return $query->where('academic_stage_id', $academic_stage_id);
                    })->pluck('id');
                    return $query->whereHas('semester', function ($query) use ($courses) {
                        return $query->whereIn('course_id', $courses);
                    });
                    return $query->whereAcademicStageId($academic_stage_id);
                })
                ->when($course_id, function($query) use ($course_id){
                    return $query->whereHas('semester', function ($q) use ($course_id) {
                        return $q->where('course_id', $course_id);
                    });
                })
                ->when($semester_id, function($query) use ($semester_id){
                    return $query->where('semester_id', $semester_id);
                })
                ->when($nationality, fn ($query) => $nationality == 'saudian' ? $query->where('nationality', 'saudian') : $query->where('nationality', '!=', 'saudian'))
                ->latest()
                ->get();
                $data = ['students' => $students,'settings'=>SchoolSetting::first(),'academic_year_id'=>$academic_year_id,
                'academic_stage_id'=>$academic_stage_id,'course_id'=>$course_id,'nationality'=>$nationality];
                $view = "tuition_fees_reports";
                $file_name = "تقرير الرسوم الدراسية.pdf";
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
