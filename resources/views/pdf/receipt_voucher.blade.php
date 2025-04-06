<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        <title>{{trans_choice('main.receipt_voucher',1)}}</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta charset="utf-8">
        <style type="text/css" >

            @font-face
            {
                font-family: 'cairo';
                font-style: normal;
                font-weight: normal;
                src: local('Cairo-Regular'), local('Cairo-Regular'), url("{{asset('fonts/Cairo-Regular.ttf')}}"), format('truetype')
            }
            * {
                font-family: 'cairo' !important; direction: rtl;text-align:right;
            }
            html {
                font-family:  'cairo', sans-serif;
                line-height: 1.15;
                margin: 0;
                direction: rtl
            }

            body {
                font-family: 'cairo';
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
        <x-school-header/>
        <hr>
        {{-- receipt info --}}
        <h5 class="text-uppercase" style="text-align: center;margin:auto;font-family: 'cairo';">
            <strong style="direction: rtl;font-size:18px;font-weight:bold">{{ trans('main.receipt_voucher_info')}}</strong>
        </h5>
        <table class=" mt-5" style="width: 100%">
            <tbody>
                <tr>
                    <td class="border-0 pl-0" style="border: none"  colspan="2">
                        {{ trans('main.student_name') }} : <span style="">{{ $receipt?->student?->username ??  $receipt?->studentAttached?->username }}</span> <br><br>                       
                    </td>
                    <td class="border-0 pl-0" style="border: none"  colspan="2">
                        {{ trans('main.registration_number') }} : <span style="">{{ $receipt?->student?->registration_number ??  $receipt?->studentAttached?->registration_number }}</span> <br><br>                       
                    </td>
                    
                    
                    <td class="border-0 pl-0"  style="border: none"  colspan="2"> 
                        {{ trans('main.national_id_n') }} : <span style="">{{ $receipt?->student?->user?->national_id ??  $receipt?->studentAttached?->user?->national_id}}</span> <br><br>
                    </td>
                    
                </tr>
                <tr>
                    <td class="border-0 pl-0" colspan="2" style="border: none" >
                        {{ trans('main.receipt_number') }} : <span style="">{{ $receipt->id }}</span> <br><br>
                    </td>
                    <td class="border-0 pl-0" colspan="2" style="border: none" >
                        {{ trans('main.date') }} : <span style="">{{ date('Y-m-d',strtotime($receipt->payment_date)) }}</span> <br>
                    </td>
                    <td class="border-0 pl-0" colspan="2" style="border: none" >
                        {{ trans('main.reference_number') }} : <span style="">{{ $receipt->refrence_number }}</span> <br>
                    </td>
                </tr>
                <tr>
                    <td class="border-0 pl-0" style="border: none"  colspan="2">
                        {{ trans('main.value') }} : <span style="">{{ number_format($receipt->value, 2, '.', ',') }}{{trans("main.".env('DEFAULT_CURRENCY')."")}}</span> <br><br>                       
                    </td>
                    <td class="border-0 pl-0" style="border: none"  colspan="2">
                        @php
                            $numberToWords = new \NumberToWords\NumberToWords();
                            // build a new number transformer using the RFC 3066 language identifier
                            $numberTransformer = $numberToWords->getNumberTransformer('ar');
                            $value_in_alphabetic = $numberTransformer->toWords($receipt->value);
                        @endphp
                        {{ trans('main.value_in_alphabetic') }} : <span style="">{{ $value_in_alphabetic  }}{{trans("main.".env('DEFAULT_CURRENCY')."")}}</span> <br>
                    </td>
                    
                    <td class="border-0 pl-0"  style="border: none"  colspan="2">
                        {{ trans_choice('main.payment_method',1) }} : <span style="">{{ $receipt?->paymentMethod?->name == "transfer" ? trans('main.transfer') : $receipt?->paymentMethod?->name }}</span> <br><br>
                    </td>
                </tr>
                
            </tbody>
        </table>
        <hr>
        <br> <br> <br> <br>
        <table style="width: 100%;border-collapse: collapse;">
            <tbody>
                <tr style="border:none">
                    
                    <th style="border:none;text-align:left" colspan="3">
                        <img style="margin:auto;text-align:center;margin-left:2rem;display:block" src="{{url(asset("storage/$settings->stamp"))}}"  alt="logo" height="75">
                    </th>
                </tr>
            </tbody>
        </table>

    </body>
</html>