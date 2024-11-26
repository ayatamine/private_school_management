<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        <title>{{ $invoice->name }}</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta charset="utf-8">
        <style type="text/css" >

            @font-face
            {
                font-family: 'noto kufi arabic';
                font-style: normal;
                font-weight: normal;
                src: local('noto kufi arabic'), local('noto kufi arabic'), url("{{asset('fonts/NotoKufiArabic-VariableFont_wght.ttf')}}"), format('truetype')
            }
            * {
                font-family: DejaVu Sans !important; direction: rtl;text-align:right;
            }
            html {
                font-family:  DejaVu Sans, sans-serif;
                line-height: 1.15;
                margin: 0;
                direction: rtl
            }

            body {
                font-family: 'DejaVu Sans', 'Roboto', 'Montserrat', 'Open Sans', sans-serif;
                line-height: 1.5;
                color: #212529;
                text-align: left;
                background-color: #fff;
                font-size: 16px;
                margin: 36pt;
            }

            h4 { 
                direction: rtl;text-align: right;
                margin-top: 0;
                margin-bottom: 0.5rem;
            }

            p {
                direction: rtl;text-align: right;
                margin-top: 0;
                margin-bottom: 1rem;
            }

            strong {
                font-weight: bolder;
            }

            img {
                vertical-align: middle;
                border-style: none;
            }

            /* table {
                direction: rtl;
            width: 100%;
            text-align: right; border-collapse: collapse;
            font-family: 'DejaVu Sans', 'Roboto', 'Montserrat', 'Open Sans', sans-serif;
            } */

            th {
                text-align: inherit;
            }

            h4, .h4 {
                margin-bottom: 0.5rem;
                font-weight: 500;
                line-height: 1.2;
            }

            h4, .h4 {
                font-size: 19px;direction: rtl;text-align:right;
            }
            h5{font-size: 17px;font-weight: 500;line-height: 1.1;direction: rtl;text-align:right;}
            .table {
                direction: ltr;text-align:right;
                width: 100%;
                margin-bottom: 1rem;
                color: #212529;border-collapse: collapse; 
            }

            .table th,
            .table td {
                vertical-align: top; border-top: 1px solid #3f4143;padding: 0.3rem;
                font-size: 14px !important;
            }

            table td ,table th{
                vertical-align: middle;
                border: 1px solid #262729;padding: 0.3rem;
            }

            .mt-5 {
                margin-top: 3rem !important;
            }

            .pr-0,
            .px-0 {
                padding-right: 0 !important;
            }

            .pl-0,
            .px-0 {
                padding-left: 0 !important;
            }

            .text-right {
                text-align: right !important;
            }

            .text-center {
                text-align: center !important;
            }

            .text-uppercase {
                text-transform: uppercase !important;
            }
            * {
                font-family: "DejaVu Sans";
            }
            body, h1, h2, h3, h4, h5, h6, table, th, tr, td, p, div {
                line-height: 1.1;
            }
            .party-header {
                font-size: 1.5rem;
                font-weight: 400;
            }
            .total-amount {
                font-size: 12px;
                font-weight: 700;
            }
            .border {
                border: 1px solid #999 !important;
            }
            .border-0 {
                border: none !important;
            }
            .cool-gray {
                color: #6B7280;
            }
            tr{
                direction: rtl;text-align:right;
            }
            /* th,td{color: black;font-weight: 600;border: 1px solid #999 !important;padding: 0.75rem;vertical-align: top;display: block}
            th{background: #dcd9d9} */
            hr{border-top: 1px solid #212529}
            XML{
                display:none;
            }
        </style>
        
    </head>

    <body>
        {{-- Header --}}

       
        {{-- school info --}}
        {{-- <h5 class="text-uppercase cool-gray">
            <strong style="text-align: right;direction: rtl">{{ trans('main.school_info')}}</strong>
        </h5> --}}
        <table class=" mt-5" style="width: 100%">
            <tbody>
                <tr>
                    @if($settings->logo)
                    <td class="border-0 pl-0" style="border: none" colspan="2" >
                        <img style="margin:auto;text-align:center" src="data:image/png;base64,{{ base64_encode(file_get_contents( "storage/$settings->logo" )) }}"  alt="logo" height="90">
                    </td>
                    @endif
                    <td class="border-0 pl-0" colspan="2" style="text-align: left;font-size:14px;border:none">
                         <span style="">{{ $settings->title }}</span> <br>
                        {{ trans('main.permit_number_2') }} : <span style="">{{ $settings->permit_number }}</span> 
                        {{ trans('main.commercial_register_number_2') }} : <span style="">{{ $settings->commercial_register_number }}</span> <br>
                        {{ trans('main.tax_number_2') }} : <span style="">{{ $settings->added_value_tax_number }}</span> <br>
                         <span style="">{{ $settings->address }}</span> <br>
                        {{ $settings->email }} {{ trans('main.phone_number') }} : <span style="">{{ $settings->phone_number }}</span> 
                    </td>
                    
                </tr>
            </tbody>
        </table>
        <hr>
        {{-- invoice info --}}
        <h5 class="text-uppercase cool-gray">
            <strong style="text-align: right;direction: rtl">{{ trans('main.invoice_info')}}</strong>
        </h5>
        <table class=" mt-5" style="width: 100%">
            <tbody>
                <tr>
                    <td class="border-0 pl-0" colspan="2" style="border: none" >
                        <span style="margin-left:0.5rem">{{ trans('main.invoice_name_first') }} </span> <span style="">{{ $invoice->name }}</span> 
                        {{-- {{ trans('main.name') }} : <span style="margin-left:4rem">{{ $invoice?->student->username }}</span> 
                        {{ trans('main.nationality') }} : <span style="margin-left:4rem">{{  $invoice?->student->nationality =="saudian" ? trans('main.saudian') : $invoice?->student->nationality }}</span>
                        {{ trans('main.registration_number') }} : <span style="margin-left:4rem">{{ $invoice?->student->registration_number }}</span> --}}
                        <br><br>
                       
                    </td>
                    
                    <td class="border-0 pl-0" style="border: none"  colspan="2">
                        {{ trans('main.invoice_number') }} : <span style="margin-left:2rem">{{ $invoice->number }}</span> {{ trans('main.release_date') }} : <span style="">{{ date('Y-m-d',strtotime($invoice->created_at)) }}</span><br><br>
                         {{-- {{ trans_choice('main.academic_year',1) }} : <span style="">{{ $invoice->academicYear?->name }}</span> <br> <br> --}}
                    </td>
                </tr>
                <tr>
                    <td class="border-0 pl-0" colspan="4" style="border: none" >
                        {{ trans('main.name') }} : <span style="margin-left:4rem">{{ $invoice?->student->username }}</span> 
                        {{ trans('main.nationality') }} : <span style="margin-left:4rem">{{  $invoice?->student->nationality =="saudian" ? trans('main.saudian') : $invoice?->student->nationality }}</span>
                        {{ trans('main.registration_number') }} : <span style="margin-left:4rem">{{ $invoice?->student->registration_number }}</span>
                        {{ trans_choice('main.academic_course',1) }} : <span style="margin-left:4rem">{{ $invoice->student?->semester?->course?->name }}</span> <br> <br>
                        <br><br>
                       
                    </td>
                    
                    {{-- <td class="border-0 pl-0" style="border: none"  colspan="2">
                         {{ trans_choice('main.academic_year',1) }} : <span style="">{{ $invoice->academicYear?->name }}</span> <br> <br>
                    </td> --}}
                </tr>
                
            </tbody>
        </table>
        <hr>
        {{-- invoice info --}}
        {{-- <h5 class="text-uppercase cool-gray">
            <strong style="text-align: right;direction: rtl">{{ trans('main.student_info')}}</strong>
        </h5>
        <table class=" mt-5"  style="width: 100%">
            <tbody>
                <tr>
                    <td class="border-0 pl-0" style="border: none"  colspan="2">
                        {{ trans('main.name') }} : <span style="">{{ $invoice?->student->username }}</span> <br><br>
                        {{ trans('main.nationality') }} : <span style="">{{  $invoice?->student->nationality =="saudian" ? trans('main.saudian') : $invoice?->student->nationality }}</span> <br>
                       
                    </td>
                    
                    <td class="border-0 pl-0"  style="border: none"  colspan="2">
                        {{ trans('main.registration_number') }} : <span style="">{{ $invoice?->student->registration_number }}</span> <br><br>
                        {{ trans_choice('main.academic_course',1) }} : <span style="">{{ $invoice?->student?->semester?->course?->name }}</span> <br>
                    </td>
                </tr>
                
            </tbody>
        </table>
        <hr> --}}
        <h5 class="text-uppercase cool-gray">
            <strong style="text-align: right;direction: rtl">{{ trans_choice('main.tuition_fee',2)}}</strong>
        </h5>
        @php
            $applied_vat =null;
          $total_tuition_without_taxes =$total_of_tuition_taxes = $tuition_total = $tuituion_value_after_discount =[]; $vat = null;
        @endphp
        <table   style="width: 100%;border-collapse: collapse;">
            <thead >
               
                <tr>
                    {{-- <th scope="col" colspan="1">
                       {{trans('main.fee_name')}}
                    </th> --}}
                    <th scope="col"  colspan="1">
                        {{trans('main.partition_name2')}}
                    </th>
                    <th scope="col"  colspan="1">
                        {{trans('main.value')}}
                    </th>
                   
                    <th scope="col" >
                        {{trans('main.discount_value')}}
                    </th>
                    <th scope="col" >
                        {{trans('main.value_after_discount')}}
                    </th>
                    @if($invoice->student->nationality != "saudian")
                    <th scope="col" >
                        {{trans('main.tax_percentage')}}
                    </th>
                    <th scope="col" >
                        {{trans('main.tax_value')}}
                    </th>
                    @endif
                    <th scope="col" >
                        {{trans('main.due_date')}}
                    </th>
                    <th scope="col" >
                        {{trans('main.total')}}
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->student->tuitionFees as $k=> $fee)
                 @if(count($fee->payment_partition))
                  @foreach ($fee->payment_partition as $i=> $partition)
                  
                    <tr>
                        {{-- <td scope="row" >
                           {{trans_choice('main.tuition_fee',1)}} {{$fee->academicYear?->name}}
                        </td> --}}
                        <td class="px-6 py-4 border ">
                            {{$partition['partition_name']}}
                        </td>
                        @php
                            if($invoice->student->approved_at && ($partition['due_date_end_at'] < \Carbon\Carbon::createFromTimestamp($invoice->student->approved_at )->format('Y-m-d'))) $partition['value'] =  0;
                            if($invoice->student->termination_date && $invoice->student->termination_date < $partition['due_date']) $partition['value'] =  0;
                        @endphp
                        <td >
                            {{$partition['value']}}
                        </td>
                        @php
                            $discounts = DB::table('student_fee')
                                        ->where('feeable_id', $fee->id)
                                        ->where('feeable_type', 'App\Models\TuitionFee')
                                        ->where('student_id', $invoice->student->id)
                                        ->value('discounts');
                                        $decodedDiscounts = json_decode($discounts, true);
                                        
                        @endphp
                         
                        @if(isset($decodedDiscounts[$i]) && array_key_exists('discount_value',$decodedDiscounts[$i]))
                        <td  >
                            {{$decodedDiscounts[$i]['discount_value']}} @if($decodedDiscounts[$i]['discount_type'] == 'percentage')% @endif
                        </td>
                        <td >
                            @php
                            if(array_key_exists('discount_type',$decodedDiscounts[$i]))
                            {
                                if(isset($decodedDiscounts[$i]['discount_type']) && $decodedDiscounts[$i]['discount_type'] == 'percentage')
                                {
                                     $tuituion_value_after_discount[$i] =$partition['value'] * (1 - ($decodedDiscounts[$i]['discount_value'] / 100));
                                }
                                else{
                                    $tuituion_value_after_discount[$i] = $partition['value'] - $decodedDiscounts[$i]['discount_value'];
                                }
                            }else
                            {
                                $tuituion_value_after_discount[$i] =$partition['value'];
                            }      
                                $total_transport_without_taxes[$i]=$tuituion_value_after_discount[$i];
                            @endphp
                            
                            {{$tuituion_value_after_discount[$i]}}
                        </td>
                         @else 
    
                         <td  >
                            0
                         </td>
                         <td  >
                            0
                         </td>
                         @endif
                        @if($invoice->student->nationality != "saudian")
                        @php
                            $payment_due_date = $partition['due_date_end_at'];
                            $vat = \App\Models\ValueAddedTax::whereDate('applies_at',"<=",date('Y-m-d',strtotime($payment_due_date)))->first();   
                            if($vat == null)
                            {
                                $vat = \App\Models\ValueAddedTax::first();
                            }
                            $applied_vat = $vat;
                        @endphp
                        
                        <td >
                            {{isset($vat?->percentage) ? $vat?->percentage : 0}} %
                        </td>
                        <td >
                            {{-- here you can check if the orginal value or tuituion_value_after_discount[$i] is with vat or not  --}}
                            @php
                                $value_after_tax[$i] = (($vat?->percentage ? $vat?->percentage : 0) / 100) * (isset($tuituion_value_after_discount[$i]) ? $tuituion_value_after_discount[$i] :  $partition['value']);

                                $total_of_tuition_taxes[$i]=$value_after_tax[$i];
                            @endphp
                            {{$value_after_tax[$i]}}
                        </td>
                        @endif
                        <td >
                            {{$partition['due_date']}}
                        </td>
                        <td >
                            @php
                                $tuition_total[$i] = (isset($tuituion_value_after_discount[$i]) ? $tuituion_value_after_discount[$i] : $partition['value']) + (isset($value_after_tax[$i]) ? $value_after_tax[$i] : 0);
                                
                            @endphp
                            {{$tuition_total[$i]}}
                        </td>
                    </tr> 
                  @endforeach
                  @php
                            $total_of_tuition_taxes_ii[$k]=array_sum($total_of_tuition_taxes);
                            $total_of_tuition_taxes =[];
                            $tuition_total_ii[$k]=array_sum($tuition_total);
                            $tuition_total =[];
                    @endphp
                 @endif
                @endforeach
               
            </tbody>
        </table>
        <h5 class="text-uppercase cool-gray">
            <strong style="text-align: right;direction: rtl">{{ trans_choice('main.transport_fee',2)}}</strong>
        </h5>
        @php
            $total_transport_without_taxes =$total_of_transport_taxes = $transport_total = $transport_value_after_discount = [];
        @endphp
        <table   style="width: 100%;border-collapse: collapse;">
            <thead >
               
                <tr>
                    {{-- <th scope="col" >
                       {{trans('main.fee_name')}}
                    </th> --}}
                    <th scope="col" >
                        {{trans('main.partition_name2')}}
                    </th>
                    <th scope="col" >
                        {{trans('main.value')}}
                    </th>
                   
                    <th scope="col" >
                        {{trans('main.discount_value')}}
                    </th>
                    <th scope="col" >
                        {{trans('main.value_after_discount')}}
                    </th>
                    <th scope="col" >
                        {{trans('main.tax_percentage')}}
                    </th>
                    <th scope="col" >
                        {{trans('main.tax_value')}}
                    </th>
                    <th scope="col" >
                        {{trans('main.due_date')}}
                    </th>
                    <th scope="col" >
                        {{trans('main.total')}}
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->student->transportFees as $k=>$fee)
                 @if(count($fee->payment_partition))
                  @foreach ($fee->payment_partition as $i=> $partition)
                  
                    <tr>
                        {{-- <td  >
                           {{trans_choice('main.transport_fee',1)}} {{$fee->academicYear?->name}}
                        </td> --}}
                        <td >
                            {{$partition['partition_name']}}
                        </td>
                        @php
                            if(\App\Models\Transport::whereStudentId($invoice?->student?->id)?->first()?->created_at > $partition['due_date_end_at'] ) $partition['value'] =  0;
                            if($invoice->student->termination_date && $invoice->student->termination_date < $partition['due_date']) $partition['value'] =  0;
                        @endphp
                        <td >
                            {{$partition['value']}}
                        </td>
                        @php
                            $discounts = DB::table('student_fee')
                                        ->where('feeable_id', $fee->id)
                                        ->where('feeable_type', 'App\Models\TransportFee')
                                        ->where('student_id', $invoice->student->id)
                                        ->value('discounts');
                                        $decodedDiscounts = json_decode($discounts, true);
                        @endphp
                        @if(isset($decodedDiscounts[$i])  && array_key_exists('discount_value',$decodedDiscounts[$i]))
                        <td  >
                            {{isset($decodedDiscounts[$i]['discount_value']) ? $decodedDiscounts[$i]['discount_value'] : 0}}
                             @if(isset($decodedDiscounts[$i]['discount_type'] ) && $decodedDiscounts[$i]['discount_type'] == 'percentage')% @endif
                        </td>
                        <td >
                            @php
                            if(array_key_exists('discount_type',$decodedDiscounts[$i]))
                            {
                                if(isset($decodedDiscounts[$i]['discount_type']) && $decodedDiscounts[$i]['discount_type'] == 'percentage')
                                {
                                     $transport_value_after_discount[$i] =$partition['value'] * (1 - ($decodedDiscounts[$i]['discount_value'] / 100));
                                }
                                else{
                                    $transport_value_after_discount[$i] = $partition['value'] - $decodedDiscounts[$i]['value'];
                                }
                            }
                            else
                            {
                                $transport_value_after_discount[$i][$i] =$partition['value'];
                            }
                                $total_transport_without_taxes[$i]=$transport_value_after_discount[$i];
                            @endphp
                            
                            {{$transport_value_after_discount[$i]}}
                        </td>
                         @else 
    
                         <td  >
                            0
                         </td>
                         <td  >
                            0
                         </td>
                         @endif
                         
                        
                        @php
                            $payment_due_date = $partition['due_date_end_at'];
                            $vat = \App\Models\ValueAddedTax::whereDate('applies_at',"<=",date('Y-m-d',strtotime($payment_due_date)))->first();   
                            if($vat == null)
                            {
                                $vat = \App\Models\ValueAddedTax::first();
                            }
                            $applied_vat = $vat;
                        @endphp
                        <td >
                            {{isset($vat?->percentage) ? $vat?->percentage : 0}} %
                        </td>
                        <td >
                            {{-- here you can check if the orginal value or transport_value_after_discount[$i] is with vat or not  --}}
                            @php
                                $value_after_tax[$i] = (($vat?->percentage ? $vat?->percentage : 0) / 100) * (isset($transport_value_after_discount[$i]) ? $transport_value_after_discount[$i] :  $partition['value']);

                                $total_of_transport_taxes[$i]=$value_after_tax[$i];
                            @endphp
                            {{$value_after_tax[$i]}}
                        </td>
              
                        <td >
                            {{$partition['due_date']}}
                        </td>
                        <td >
                            @php
                                $transport_total[$i] = (isset($transport_value_after_discount[$i]) ? $transport_value_after_discount[$i] : $partition['value']) + (isset($value_after_tax[$i]) ? $value_after_tax[$i] : 0);
                            @endphp
                            {{$transport_total[$i]}}
                        </td>
                    </tr> 
                  @endforeach
                   @php
                            $total_of_transport_taxes_ii[$k]=array_sum($total_of_transport_taxes);
                            $total_of_transport_taxes =[];
                            $transport_total_ii[$k]=array_sum($transport_total);
                            $transport_total =[];
                    @endphp
                 @endif
                @endforeach
                 
            </tbody>
        </table>
        
        
        <h5 class="text-uppercase cool-gray">
            <strong style="text-align: right;direction: rtl">{{ trans_choice('main.general_fee',2)}}</strong>
        </h5>
        @php
         $total_other_without_taxes =$total_of_other_taxes = $other_total =[];
        @endphp
        <table   style="width: 100%;border-collapse: collapse;">
            <thead >
               
                <tr>
                    {{-- <th scope="col" colspan="1">
                       {{trans('main.fee_name')}}
                    </th> --}}
                    <th scope="col"  colspan="1">
                        {{trans('main.partition_name2')}}
                    </th>
                    <th scope="col"  colspan="1">
                        {{trans('main.value')}}
                    </th>
                   
                    <th scope="col" >
                        {{trans('main.discount_value')}}
                    </th>
                    <th scope="col" >
                        {{trans('main.value_after_discount')}}
                    </th>
                    
                    <th scope="col" >
                        {{trans('main.tax_percentage')}}
                    </th>
                    <th scope="col" >
                        {{trans('main.tax_value')}}
                    </th>
                    
                    <th scope="col" >
                        {{trans('main.due_date')}}
                    </th>
                    <th scope="col" >
                        {{trans('main.total')}}
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->student->otherFees as $k=> $fee)
                 @if(count($fee->payment_partition))
                  @foreach ($fee->payment_partition as $i=> $partition)
                  
                    <tr>
                        {{-- <td scope="row" >
                           {{trans_choice('main.tuition_fee',1)}} {{$fee->academicYear?->name}}
                        </td> --}}
                        <td class="px-6 py-4 border ">
                            {{$partition['partition_name']}}
                        </td>
                        @php
                            if($invoice->student->approved_at && ($partition['due_date_end_at'] <= \Carbon\Carbon::createFromTimestamp($invoice->student->approved_at)->format('Y-m-d'))) $partition['value'] =  0;
                            if($invoice->student->termination_date && $invoice->student->termination_date <= $partition['due_date']) $partition['value'] =  0;
                        @endphp
                        <td >
                            {{$partition['value']}}
                            @php   $total_other_without_taxes[$i] = intval($partition['value']);  @endphp
                        </td>
                        @php
                            $discounts = DB::table('student_fee')
                                        ->where('feeable_id', $fee->id)
                                        ->where('feeable_type', 'App\Models\GeneralFee')
                                        ->where('student_id', $invoice->student->id)
                                        ->value('discounts');
                                        $decodedDiscounts = json_decode($discounts, true);
                        @endphp
                        @if(isset($decodedDiscounts[$i]) && array_key_exists('discount_value',$decodedDiscounts[$i]))
                        <td  >
                            {{isset($decodedDiscounts[$i]['discount_value']) ? $decodedDiscounts[$i]['discount_value'] : 0}}
                             @if($decodedDiscounts[$i]['discount_type'] == 'percentage')% @endif
                        </td>
                        <td >
                            @php
                            if(array_key_exists('discount_type',$decodedDiscounts[$i]))
                            {
                                if($decodedDiscounts[$i]['discount_type'] == 'percentage')
                                {
                                     $value_after_discount[$i] =$partition['value'] * (1 - ($decodedDiscounts[$i]['discount_value'] / 100));
                                }
                                else{
                                    $value_after_discount[$i] = $partition['value'] - $decodedDiscounts[$i]['discount_value'];
                                }
                            }
                            else
                            {
                                 $value_after_discount[$i] =$partition['value'];
                            }    
                            $total_other_without_taxes[$i]=$value_after_discount[$i];
                            @endphp
                            
                            {{$value_after_discount[$i]}}
                        </td>
                         @else 
                           
                         <td  >
                            0
                         </td>
                         <td  >
                            0
                         </td>
                         @endif
                       
                        @php
                            
                            $payment_due_date = $partition['due_date_end_at'];
                            $vat = \App\Models\ValueAddedTax::whereDate('applies_at',"<=",date('Y-m-d',strtotime($payment_due_date)))->first();   
                            if($vat == null)
                            {
                                $vat = \App\Models\ValueAddedTax::first();
                            }
                            $applied_vat = $vat;
                        @endphp
                         
                        <td >
                            {{$vat?->percentage}} %
                        </td>
                        <td >
                            {{-- here you can check if the orginal value or value_after_discount[$i] is with vat or not  --}}
                            @php
                                $value_after_tax[$i] = ((isset($vat?->percentage) ? $vat?->percentage : 0) / 100) * (isset($value_after_discount[$i]) ? $value_after_discount[$i] : $partition['value']);

                                $total_of_other_taxes[$i]=$value_after_tax[$i];
                            @endphp
                            {{$value_after_tax[$i]}}
                           
                        </td>
                      
                        <td >
                            {{$partition['due_date']}}
                        </td>
                        <td >
                            @php
                                $other_total[$i] = (isset($value_after_discount[$i]) ? $value_after_discount[$i] : $partition['value']) + (isset($value_after_tax[$i]) ? $value_after_tax[$i] : 0);
                            @endphp
                            {{$other_total[$i]}}
                        </td>
                    </tr> 
                  @endforeach
                   @php
                            $total_of_other_taxes_ii[$k]=array_sum($total_of_other_taxes);
                            $total_of_other_taxes =[];
                            $other_total_ii[$k]=array_sum($other_total);
                            $other_total=[];
                    @endphp
                 @endif
                @endforeach
                
            </tbody>
        </table>
        <br>

        <table style="width: 100%;border-collapse: collapse;">
            <tbody>
                @php
                    $total_without_tax =  ($invoice->student->nationality == "saudian") ?  array_sum($tuition_total_ii) : 0 ;
                    $total_of_tax = array_sum($total_of_tuition_taxes_ii) + array_sum($total_of_transport_taxes_ii) + array_sum($total_of_other_taxes_ii) ;
                    $total_with_tax = 0;
                    if($invoice->student->nationality != "saudian")
                    {
                        $total_with_tax+=  array_sum($tuition_total_ii) ;
                    }
                    $total_with_tax+=   array_sum($transport_total_ii) + array_sum($other_total_ii);
                    $total_with_tax=  $total_with_tax / (1 + ($vat->percentage / 100));
                    $total =$total_without_tax + $total_of_tax + $total_with_tax;
                @endphp
                                {{-- total without taxes --}}
                                <tr>
                                    <td>{{trans('main.total_without_taxes')}}</td>
                                    <td>
                                        {{$total_without_tax}} {{trans("main.".env('DEFAULT_CURRENCY')."")}}
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{trans('main.total_with_tax')}}</td>
                                    <td>
                                        {{$total_with_tax}} {{trans("main.".env('DEFAULT_CURRENCY')."")}}
                                    </td>
                                </tr>
                                {{-- total without taxes --}}
                                <tr>
                                    <td>{{trans('main.total_of_taxes')}}
                                        {{-- @php
                                            $vat = null;
                                            if(\App\Models\ValueAddedTax::count() == 1)
                                            {
                                                $vat = \App\Models\ValueAddedTax::first();
                                            }
                                        @endphp --}}
                                       
                                        ({{$applied_vat?->percentage }}%)
                                    </td>
                                    <td>
                                        {{$total_of_tax}}  {{trans("main.".env('DEFAULT_CURRENCY')."")}}
                                    </td>
                                </tr>
                                {{-- total sum --}}
                                <tr>
                                    <td>{{trans('main.total_of_total_with_tax')}} ({{$applied_vat?->percentage }}%)</td>
                                    <td>
                                        {{$total}} {{trans("main.".env('DEFAULT_CURRENCY')."")}}
                                    </td>
                                </tr>
            </tbody>
        </table>
        <br><br><br><br>
        @php
            $qr_content = json_encode([
                'school_name'=>$settings->name,
                'student_registeration_number'=>$invoice->student->registration_number,
                'added_value_tax_number'=>$settings->added_value_tax_number,
                'invoice_date'=>$invoice->created_at,
                'total_without_tax'=>$total_without_tax,
                'total_without_tax'=>$total_with_tax,
                'total'=>$total
            ]);
        @endphp
        <table style="width: 100%;border-collapse: collapse;">
            <tbody>
                <tr style="border:none">
                    
                    <th style="border:none;text-align:left" colspan="3">
                        <img style="margin:auto;text-align:center;margin-left:2rem;display:block" src="data:image/png;base64,{{ base64_encode(file_get_contents( "storage/$settings->stamp" )) }}"  alt="logo" height="75">
                    </th>
                    <th style="border:none;text-align:right;margin-right:4rem;display:block" colspan="3">
                        <div style="border:none;float:left:margin-right:3rem" colspan="3">
                            @php
                                $qr_code =\SimpleSoftwareIO\QrCode\Facades\QrCode::generate($qr_content);
                                $code = (string)$qr_code;
                                 echo substr($code,38);
                            @endphp     
                            {{-- {{\SimpleSoftwareIO\QrCode\Facades\QrCode::generate($qr_content)}} --}}
                        </div>
                    </th>
                </tr>
            </tbody>
        </table>
        {{-- <div style="text-align: left;border:none;float:left:margin-right:3rem" colspan="3">
            @php
                $qr_code =\SimpleSoftwareIO\QrCode\Facades\QrCode::generate($qr_content);
                $code = (string)$qr_code;
                 echo substr($code,38);
            @endphp     
        </div> --}}
    </body>
</html>