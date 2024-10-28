<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        <title>{{trans_choice('main.receipt_voucher',1)}}</title>
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
        {{-- receipt info --}}
        {{-- <h5 class="text-uppercase cool-gray">
            <strong style="text-align: right;direction: rtl">{{ trans('main.receipt_voucher_info')}}</strong>
        </h5>
        <table class=" mt-5" style="width: 100%">
            <tbody>
                <tr>
                    <td class="border-0 pl-0" colspan="2" style="border: none" >
                        {{ trans('main.serial_number') }} : <span style="">{{ $receipt->id }}</span> <br><br>
                        {{ trans('main.name') }} : <span style="">{{ trans_choice('main.receipt_voucher',1)}}</span> <br>
                        {{ trans('main.release_date') }} : <span style="">{{ date('Y-m-d',strtotime($receipt->created_at)) }}</span> <br>

                    </td>
                </tr>
                
            </tbody>
        </table>
        <hr> --}}
        {{-- receipt info --}}
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
        {{-- receipt info --}}
        <h5 class="text-uppercase cool-gray">
            <strong style="text-align: right;direction: rtl">{{ trans('main.financial_infos')}}</strong>
        </h5>
        <table class="w-ful" style="width: 100%" id="payment_list">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 border-b">

                <tr>
                    <th scope="col" class="px-6 py-3 border">
                       {{trans('main.receipt_number')}}
                    </th>
                    <th scope="col" class="px-6 py-3 border">
                        {{trans('main.payment_date')}}
                    </th>
                    <th scope="col" class="px-6 py-3 border">
                        {{trans_choice('main.payment_method',1)}}
                    </th>
                   
                    <th scope="col" class="px-6 py-3 border">
                        {{trans('main.value')}}
                    </th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total=0;
                @endphp
                @forelse ($student->receiptVoucher as $payment)
                
                    <tr class="">
                        <td scope="row" class="px-6 py-4 ">
                           {{$payment->id}}
                        </td>
                        <td class="px-6 py-4  ">
                            {{$payment->payment_date}}
                        </td>
                        <td class="px-6 py-4 ">
                            {{$payment->paymentMethod->name == "transfer" ? trans('main.transfer') : $payment->paymentMethod->name }}
                        </td>
                        
                        <td class="px-6 py-4 " >
                            {{$payment->value}}  {{trans("main.SAR")}}
                        </td>
                        
                        @php
                            $total+=$payment->value;
                        @endphp
                    </tr> 
                @empty 
                    <td colspan="4">{{trans('main.no_payment_found')}}</td>
                @endforelse
                {{-- total sum --}}
                <tr>
                    <td class="px-6 py-4 " colspan="3" >{{trans('main.total')}}</td>
                    <td class="px-6 py-4 ">
                       {{$total}} {{trans("main.SAR")}}
                    </td>
                </tr>
            </tbody>
        </table>

        <br>
   

    </body>
</html>