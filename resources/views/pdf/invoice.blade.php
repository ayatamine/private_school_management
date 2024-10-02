<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        <title>{{ $invoice->name }}</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta charset="utf-8">
        <style type="text/css" media="screen">

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

            h4 { direction: rtl;text-align: right;
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

            table {
                direction: ltr;
            width: 100%;
            text-align: right; border-collapse: collapse;
            font-family: 'DejaVu Sans', 'Roboto', 'Montserrat', 'Open Sans', sans-serif;
            }

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
                color: #212529;
            }

            .table th,
            .table td {
                padding: 0.75rem;
                vertical-align: top;
            }

            .table.table-items td {
                border-top: 1px solid #dee2e6;
            }

            .table thead th {
                vertical-align: bottom;
                border-bottom: 2px solid #dee2e6;
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
            .border-0 {
                border: none !important;
            }
            .cool-gray {
                color: #6B7280;
            }
            tr{
                direction: rtl;text-align:right;
            }
            th,td{color: black;font-weight: 600;border: 1px solid #999;padding: 0.75rem;vertical-align: top;}
            th{background: #dcd9d9}
            hr{border-top: 1px solid #212529}
            
        </style>
        
    </head>

    <body>
        {{-- Header --}}

       
        {{-- school info --}}
        {{-- <h5 class="text-uppercase cool-gray">
            <strong style="text-align: right;direction: rtl">{{ trans('main.school_info')}}</strong>
        </h5> --}}
        <table class="table mt-5" style="width: 100%">
            <tbody>
                <tr>
                    @if($settings->logo)
                    <td class="border-0 pl-0" colspan="2" >
                        <img style="margin:auto;text-align:center" src="{{ url("storage/$settings->logo") }}" alt="logo" height="100">
                    </td>
                    @endif
                    <td class="border-0 pl-0" colspan="2" style="text-align: left;font-size:14px">
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
        <h5 class="text-uppercase cool-gray">
            <strong style="text-align: right;direction: rtl">{{ trans('main.invoice_info')}}</strong>
        </h5>
        <table class="table mt-5">
            <tbody>
                <tr>
                    <td class="border-0 pl-0" >
                        {{ trans('main.name') }} : <span style="">{{ $invoice->name }}</span> <br>
                        {{ trans('main.invoice_number') }} : <span style="">{{ $invoice->number }}</span> <br>
                       
                    </td>
                    <td class="border-0 pl-0">
                            <h4 class="text-uppercase cool-gray">
                                <strong style="text-align: right;direction: rtl">{{ $invoice->name }}</strong>
                            </h4>
                    </td>
                    <td class="border-0 pl-0" >
                        <h4 class="text-uppercase">
                            <strong style="text-align: right;direction: rtl">{{ trans('main.invoice_number') }}</strong>
                        </h4>
                    </td>
                    <td class="border-0 pl-0">
                            <h4 class="text-uppercase cool-gray">
                                <strong style="text-align: right;direction: rtl">#{{ $invoice->number }}</strong>
                            </h4>
                    </td>
                </tr>
                <tr>
                    <td class="border-0 pl-0" >
                        <h4 class="text-uppercase">
                            <strong style="text-align: right;direction: rtl">{{ trans('main.release_date') }}</strong>
                        </h4>
                    </td>
                    <td class="border-0 pl-0">
                            <h4 class="text-uppercase cool-gray">
                                <strong style="text-align: right;direction: rtl">{{ date('Y-m-d',strtotime($invoice->created_at)) }}</strong>
                            </h4>
                    </td>
                    <td class="border-0 pl-0" width="70%">
                        <h4 class="text-uppercase">
                            <strong style="text-align: right;direction: rtl">{{ trans_choice('main.academic_year',1) }}</strong>
                        </h4>
                    </td>
                    <td class="border-0 pl-0">
                            <h4 class="text-uppercase cool-gray">
                                <strong style="text-align: right;direction: rtl">{{ $invoice->academicYear?->name }}</strong>
                            </h4>
                    </td>
                </tr>
            </tbody>
        </table>
        <hr>
        {{-- invoice info --}}
        <h5 class="text-uppercase cool-gray">
            <strong style="text-align: right;direction: rtl">{{ trans('main.student_info')}}</strong>
        </h5>
        <table class="table mt-5">
            <tbody>
                <tr>
                    <td class="border-0 pl-0" width="70%">
                        <h4 class="text-uppercase">
                            <strong style="text-align: right;direction: rtl">{{ trans('main.name') }}</strong>
                        </h4>
                    </td>
                    <td class="border-0 pl-0">
                        <strong style="text-align: right;direction: rtl">{{ $invoice?->student->username }}</strong>
                    </td>
                    <td class="border-0 pl-0" width="70%">
                        <h4 class="text-uppercase">
                            <strong style="text-align: right;direction: rtl">{{ trans('main.nationality') }}</strong>
                        </h4>
                    </td>
                    <td class="border-0 pl-0">
                        <strong style="text-align: right;direction: rtl">{{ $invoice->nationality }}</strong>
                    </td>
                </tr>
                <tr>
                    <td class="border-0 pl-0" width="70%">
                        <h4 class="text-uppercase">
                            <strong style="text-align: right;direction: rtl">{{ trans('main.registration_number') }}</strong>
                        </h4>
                    </td>
                    <td class="border-0 pl-0">
                        <strong style="text-align: right;direction: rtl">{{ $invoice?->student->registration_number }}</strong>
                    </td>
                    <td class="border-0 pl-0" width="70%">
                        <h4 class="text-uppercase">
                            <strong style="text-align: right;direction: rtl">{{ trans_choice('main.course',1) }}</strong>
                        </h4>
                    </td>
                    <td class="border-0 pl-0">
                        <strong style="text-align: right;direction: rtl">{{ $invoice->academic_course?->name }}</strong>
                    </td>
                </tr>
            </tbody>
        </table>
        <hr>
        <h5 class="text-uppercase cool-gray">
            <strong style="text-align: right;direction: rtl">{{ trans('main.tuition_fee')}}</strong>
        </h5>
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400 border">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 border-b">
               
                <tr>
                    <th scope="col" class="px-6 py-3 border">
                       {{trans('main.fee_name')}}
                    </th>
                    <th scope="col" class="px-6 py-3 border">
                        {{trans('main.partition_name')}}
                    </th>
                    <th scope="col" class="px-6 py-3 border">
                        {{trans('main.value')}}
                    </th>
                   
                    <th scope="col" class="px-6 py-3 border">
                        {{trans('main.discount_value')}}
                    </th>
                    <th scope="col" class="px-6 py-3 border">
                        {{trans('main.value_after_discount')}}
                    </th>
                    @if($invoice->student->nationality != "saudian")
                    <th scope="col" class="px-6 py-3 border">
                        {{trans('main.tax_percentage')}}
                    </th>
                    <th scope="col" class="px-6 py-3 border">
                        {{trans('main.tax_value')}}
                    </th>
                    @endif
                    <th scope="col" class="px-6 py-3 border">
                        {{trans('main.due_date')}}
                    </th>
                    <th scope="col" class="px-6 py-3 border">
                        {{trans('main.total')}}
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->student->tuitionFees as $fee)
                 @if(count($fee->payment_partition))
                  @foreach ($fee->payment_partition as $i=> $partition)
                  
                    <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                        <td scope="row" class="px-6 py-4 border">
                           {{trans_choice('main.tuition_fee',1)}} {{$fee->academicYear?->name}}
                        </td>
                        <td class="px-6 py-4 border ">
                            {{$partition['partition_name']}}
                        </td>
                        <td class="px-6 py-4 border">
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
                        <td class="px-6 py-4 border" >
                            {{$decodedDiscounts[0]['discount_value']}} @if($decodedDiscounts[0]['discount_type'] == 'percentage')% @endif
                        </td>
                        <td class="px-6 py-4 border">
                            @php
                                if($decodedDiscounts[0]['discount_type'] == 'percentage')
                                {
                                     $value_after_discount =$partition['value'] * (1 - ($decodedDiscounts[0]['discount_value'] / 100));
                                }
                                else{
                                    $value_after_discount = $partition['value'] - $decodedDiscounts[0]['value'];
                                }
                                   
                            @endphp
                            
                            {{$value_after_discount}}
                        </td>
                         @else 
    
                         <td class="px-6 py-4 border" >
                            0
                         </td>
                         <td class="px-6 py-4 border" >
                            0
                         </td>
                         @endif
                         
                        @if($invoice->student->nationality != "saudian")
                        @php
                            $vat = \App\Models\ValueAddedTax::first();
                        @endphp
                        
                        <td class="px-6 py-4 border">
                            {{$vat->percentage}} %
                        </td>
                        <td class="px-6 py-4 border">
                            {{-- here you can check if the orginal value or value_after_discount is with vat or not  --}}
                            @php
                                $value_after_tax = ($vat->percentage / 100) * ($value_after_discount ?? $partition['value'])
                            @endphp
                            {{$value_after_tax}}
                        </td>
                        @endif
                        <td class="px-6 py-4 border">
                            {{$partition['due_date']}}
                        </td>
                        <td class="px-6 py-4 border">
                            @php
                                $total[$i] = ($value_after_discount ?? $partition['value']) + ($value_after_tax ?? 0);
                            @endphp
                            {{$total[$i]}}
                        </td>
                    </tr> 
                  @endforeach
                 @endif
                @endforeach
                {{-- total sum --}}
                <tr>
                    <td class="px-6 py-4 border" @if($invoice->student->nationality != "saudian") colspan="8" @else colspan="6" @endif>{{trans('main.total')}}</td>
                    <td class="px-6 py-4 border">
                        {{array_sum($total)}} {{trans("main.".env('DEFAULT_CURRENCY'))}}
                    </td>
                </tr>
            </tbody>
        </table>
       
        <table>
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td class="title">

                            </td>

                            <td>
                                فاتورة #: 123<br />
                                الانشاء : يناير 1, 2015<br />
                                تاريخ : فبراير 1, 2015
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>


            <tr class="heading">
                <td>العنصر </td>

                <td>السعر </td>
            </tr>

            <tr class="item">
                <td>تصميم موقع </td>

                <td>$300.00</td>
            </tr>

            <tr class="item">
                <td>استضافة (3 أشهر )</td>

                <td>$75.00</td>
            </tr>

            <tr class="item ">
                <td>نطاق (1 عام )</td>

                <td>$10.00</td>
            </tr>

            <tr class="total last">
                <td>الإجمالي : </td>

                <td>$385.00 </td>
            </tr>
        </table>

    </body>
</html>