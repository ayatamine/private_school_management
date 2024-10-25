<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        <title>[{{ trans('main.fees_invoice') }}]</title>
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
                        <img style="margin:auto;text-align:center" src="{{ url("storage/$settings->logo") }}" alt="logo" height="100">
                    </td>
                    @endif
                    <td class="border-0 pl-0" colspan="2" style="text-align: left;font-size:14px;border:none">
                        {{ trans('main.school_title') }} : <span style="">{{ $settings->title }}</span> <br>
                        {{ trans('main.permit_number') }} : <span style="">{{ $settings->permit_number }}</span> <br>
                        {{ trans('main.commercial_register_number') }} : <span style="">{{ $settings->commercial_register_number }}</span> <br>
                        {{ trans('main.tax_number') }} : <span style="">{{ $settings->added_value_tax_number }}</span> <br>

                    </td>
                    
                </tr>
            </tbody>
        </table>
        <hr>
        {{-- invoice info --}}
        {{-- <h5 class="text-uppercase cool-gray">
            <strong style="text-align: right;direction: rtl">{{ trans('main.invoice_info')}}</strong>
        </h5>
        <table class=" mt-5" style="width: 100%">
            <tbody>
                <tr>
                    <td class="border-0 pl-0" colspan="2" style="border: none" >
                        {{ trans('main.name') }} : <span style="">{{ $invoice->name }}</span> <br><br>
                        {{ trans('main.invoice_number') }} : <span style="">{{ $invoice->number }}</span> <br>
                       
                    </td>
                    
                    <td class="border-0 pl-0" style="border: none"  colspan="2">
                        {{ trans('main.release_date') }} : <span style="">{{ date('Y-m-d',strtotime($invoice->created_at)) }}</span> <br><br>
                        {{ trans_choice('main.academic_year',1) }} : <span style="">{{ $invoice->academicYear?->name }}</span> <br>
                    </td>
                </tr>
                
            </tbody>
        </table>
        <hr> --}}
        {{-- invoice info --}}
        <h5 class="text-uppercase cool-gray">
            <strong style="text-align: right;direction: rtl">{{ trans('main.student_info')}}</strong>
        </h5>
        <table class=" mt-5"  style="width: 100%">
            <tbody>
                <tr>
                    <td class="border-0 pl-0" style="border: none"  colspan="2">
                        {{ trans('main.name') }} : <span style="">{{ $student->username }}</span> <br><br>
                        {{ trans('main.nationality') }} : <span style="">{{ $student->nationality }}</span> <br>
                       
                    </td>
                    
                    <td class="border-0 pl-0"  style="border: none"  colspan="2">
                        {{ trans('main.registration_number') }} : <span style="">{{ $student->registration_number }}</span> <br><br>
                        {{ trans_choice('main.academic_course',1) }} : <span style="">{{ $student?->semester?->course?->name }}</span> <br>
                    </td>
                </tr>
                
            </tbody>
        </table>
        <hr>
        <h5 class="text-uppercase cool-gray">
            <strong style="text-align: right;direction: rtl">{{ trans_choice('main.tuition_fee',2)}}</strong>
        </h5>
        @php
            $total_without_taxes =$total_of_taxes = [];
        @endphp
        <table   style="width: 100%;border-collapse: collapse;">
            <thead >
               
                <tr>
                    <th scope="col" colspan="1">
                       {{trans('main.fee_name')}}
                    </th>
                    <th scope="col"  colspan="1">
                        {{trans('main.partition_name')}}
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
                    @if($student->nationality != "saudian")
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
                @foreach ($student->tuitionFees as $fee)
                 @if(count($fee->payment_partition))
                  @foreach ($fee->payment_partition as $i=> $partition)
                  
                    <tr>
                        <td scope="row" >
                           {{trans_choice('main.tuition_fee',1)}} {{$fee->academicYear?->name}}
                        </td>
                        <td class="px-6 py-4 border ">
                            {{$partition['partition_name']}}
                        </td>
                        <td >
                            {{$partition['value']}}
                        </td>
                        @php
                            $discounts = DB::table('student_fee')
                                        ->where('feeable_id', $fee->id)
                                        ->where('feeable_type', 'App\Models\TuitionFee')
                                        ->value('discounts');
                                        $decodedDiscounts = json_decode($discounts, true);
                        @endphp
                        @if(isset($decodedDiscounts[0]))
                        <td  >
                            {{$decodedDiscounts[0]['discount_value']}} @if($decodedDiscounts[0]['discount_type'] == 'percentage')% @endif
                        </td>
                        <td >
                            @php
                                if($decodedDiscounts[0]['discount_type'] == 'percentage')
                                {
                                     $value_after_discount =$partition['value'] * (1 - ($decodedDiscounts[0]['discount_value'] / 100));
                                }
                                else{
                                    $value_after_discount = $partition['value'] - $decodedDiscounts[0]['value'];
                                }
                                   
                                $total_without_taxes[$i]=$value_after_discount;
                            @endphp
                            
                            {{$value_after_discount}}
                        </td>
                         @else 
    
                         <td  >
                            0
                         </td>
                         <td  >
                            0
                         </td>
                         @endif
                         
                        @if($student->nationality != "saudian")
                        @php
                            $vat = \App\Models\ValueAddedTax::first();
                        @endphp
                        
                        <td >
                            {{$vat->percentage}} %
                        </td>
                        <td >
                            {{-- here you can check if the orginal value or value_after_discount is with vat or not  --}}
                            @php
                                $value_after_tax = ($vat->percentage / 100) * ($value_after_discount ?? $partition['value']);

                                $total_of_taxes[$i]=$value_after_tax;
                            @endphp
                            {{$value_after_tax}}
                        </td>
                        @endif
                        <td >
                            {{$partition['due_date']}}
                        </td>
                        <td >
                            @php
                                $total[$i] = ($value_after_discount ?? $partition['value']) + ($value_after_tax ?? 0);
                            @endphp
                            {{$total[$i]}}
                        </td>
                    </tr> 
                  @endforeach
                 @endif
                @endforeach
                {{-- total without taxes --}}
                <tr>
                    <td @if($student->nationality != "saudian") colspan="8" @else colspan="6" @endif>{{trans('main.total_without_taxes')}}</td>
                    <td>
                        {{array_sum($total_without_taxes)}} {{trans("main.".env('DEFAULT_CURRENCY')."")}}
                    </td>
                </tr>
                {{-- total without taxes --}}
                <tr>
                    <td @if($student->nationality != "saudian") colspan="8" @else colspan="6" @endif>{{trans('main.total_of_taxes')}}({{\App\Models\ValueAddedTax::first()->percentage }}%)</td>
                    <td>
                        {{array_sum($total_of_taxes)}}  {{trans("main.".env('DEFAULT_CURRENCY')."")}}
                    </td>
                </tr>
                {{-- total sum --}}
                <tr>
                    <td @if($student->nationality != "saudian") colspan="8" @else colspan="6" @endif>{{trans('main.total')}}</td>
                    <td>
                        {{array_sum($total)}} {{trans("main.".env('DEFAULT_CURRENCY')."")}}
                    </td>
                </tr>
            </tbody>
        </table>
        <h5 class="text-uppercase cool-gray">
            <strong style="text-align: right;direction: rtl">{{ trans_choice('main.transport_fee',2)}}</strong>
        </h5>
        <table   style="width: 100%;border-collapse: collapse;">
            <thead >
               
                <tr>
                    <th scope="col" >
                       {{trans('main.fee_name')}}
                    </th>
                    <th scope="col" >
                        {{trans('main.partition_name')}}
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
                    @if($student->nationality != "saudian")
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
                @foreach ($student->transportFees as $fee)
                 @if(count($fee->payment_partition))
                  @foreach ($fee->payment_partition as $i=> $partition)
                  
                    <tr>
                        <td  >
                           {{trans_choice('main.transport_fee',1)}} {{$fee->academicYear?->name}}
                        </td>
                        <td >
                            {{$partition['partition_name']}}
                        </td>
                        <td >
                            {{$partition['value']}}
                        </td>
                        @php
                            $discounts = DB::table('student_fee')
                                        ->where('feeable_id', $fee->id)
                                        ->where('feeable_type', 'App\Models\TransportFee')
                                        ->value('discounts');
                                        $decodedDiscounts = json_decode($discounts, true);
                        @endphp
                        @if(isset($decodedDiscounts[0]))
                        <td  >
                            {{$decodedDiscounts[0]['discount_value']}} @if($decodedDiscounts[0]['discount_type'] == 'percentage')% @endif
                        </td>
                        <td >
                            @php
                                if($decodedDiscounts[0]['discount_type'] == 'percentage')
                                {
                                     $value_after_discount =$partition['value'] * (1 - ($decodedDiscounts[0]['discount_value'] / 100));
                                }
                                else{
                                    $value_after_discount = $partition['value'] - $decodedDiscounts[0]['value'];
                                }
                                $total_without_taxes[$i]=$value_after_discount;
                            @endphp
                            
                            {{$value_after_discount}}
                        </td>
                         @else 
    
                         <td  >
                            0
                         </td>
                         <td  >
                            0
                         </td>
                         @endif
                         
                        @if($student->nationality != "saudian")
                        @php
                            $vat = \App\Models\ValueAddedTax::first();
                        @endphp
                        
                        <td >
                            {{$vat->percentage}} %
                        </td>
                        <td >
                            {{-- here you can check if the orginal value or value_after_discount is with vat or not  --}}
                            @php
                                $value_after_tax = ($vat->percentage / 100) * ($value_after_discount ?? $partition['value']);

                                $total_of_taxes[$i]=$value_after_tax;
                            @endphp
                            {{$value_after_tax}}
                        </td>
                        @endif
                        <td >
                            {{$partition['due_date']}}
                        </td>
                        <td >
                            @php
                                $total[$i] = ($value_after_discount ?? $partition['value']) + ($value_after_tax ?? 0);
                            @endphp
                            {{$total[$i]}}
                        </td>
                    </tr> 
                  @endforeach
                 @endif
                @endforeach
                 {{-- total without taxes --}}
                 <tr>
                    <td @if($student->nationality != "saudian") colspan="8" @else colspan="6" @endif>{{trans('main.total_without_taxes')}}</td>
                    <td>
                        {{array_sum($total_without_taxes)}} {{trans("main.".env('DEFAULT_CURRENCY')."")}}
                    </td>
                </tr>
                {{-- total without taxes --}}
                <tr>
                    <td @if($student->nationality != "saudian") colspan="8" @else colspan="6" @endif>{{trans('main.total_of_taxes')}}({{\App\Models\ValueAddedTax::first()->percentage }}%)</td>
                    <td>
                        {{array_sum($total_of_taxes)}} {{trans("main.".env('DEFAULT_CURRENCY')."")}}
                    </td>
                </tr>
                {{-- total sum --}}
                <tr>
                    <td  @if($student->nationality != "saudian") colspan="8" @else colspan="6" @endif>{{trans('main.total')}}</td>
                    <td >
                        {{array_sum($total)}} {{trans("main.".env('DEFAULT_CURRENCY')."")}}
                    </td>
                </tr>
            </tbody>
        </table>
        <br><br><br><br>
        {{-- @php
            $qr_content = json_encode([
                'school_name'=>$settings->name,
                'added_value_tax_number'=>$settings->added_value_tax_number,
                // 'invoice_date'=>$created_at,
                'total'=>$student?->totalFees()
            ]);
        @endphp
        <div>
            {{\SimpleSoftwareIO\QrCode\Facades\QrCode::generate($qr_content)}}
        </div> --}}
   

    </body>
</html>