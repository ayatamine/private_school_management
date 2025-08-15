<?php

namespace App\Filament\Resources\TuitionFeeReportsResource\Widgets;

use App\Models\Student;
use App\Models\Semester;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class TuitionFeesReportSummary extends BaseWidget
{
    
    protected function getStats(): array
    {
        $students = Student::whereDoesntHave('termination')->where('status','approved')->where(function($query){
            $default_academic_year_id = \App\Models\AcademicYear::where('is_global_default', true)->first()->id ?? \App\Models\AcademicYear::where('is_default', true)->first()->id;
            $semesters = Semester::where('academic_year_id', $default_academic_year_id)->pluck('id')->toArray() ;
            return $query->whereHas('semesters', function ($query) use ($semesters) {
                return $query->whereIn('semester_id', $semesters);
            });
          })->get();
        $totalFees = 0;
        $totalPaidFees = 0;
        $currentBalance = 0;
        $needToPayBalance = 0;
        
        foreach($students as $student) {
            $totalFees += calculateAllFees($student)['totals']['grand_total'];
            $totalPaidFees += $student->payments();
            $currentBalance += $student->opening_balance + calculateAllFees($student)['totals']['grand_total'] - $student->payments();
            $needToPayBalance += $student->opening_balance + calculateAllFees($student)['totals']['total_fees_to_pay'] - $student->payments();
        }
        return [
            Stat::make(trans('main.total_fees'), number_format($totalFees,2,',',',')." ".trans('main.'.env('DEFAULT_CURRENCY'))),
            Stat::make(trans('main.total_paid_fees'), number_format($totalPaidFees,2,',',',')." ".trans('main.'.env('DEFAULT_CURRENCY'))),
            Stat::make(trans('main.current_balance'), number_format($currentBalance,2,',',',')." ".trans('main.'.env('DEFAULT_CURRENCY'))),
            Stat::make(trans('main.need_to_pay_balance'), number_format($needToPayBalance,2,',',',')." ".trans('main.'.env('DEFAULT_CURRENCY'))),
        ];
    }
}
