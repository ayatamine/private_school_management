<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        <title>{{trans_choice('main.student_transportation',1)}}</title>
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
            table {
                    padding:0.4rem;color: #212529;
                    border-left: 0.01em solid #262729;
                    border-right: 0;
                    border-top: 0.01em solid #262729;
                    border-bottom: 0;
                    border-collapse: collapse;
                }
                table td:not(:last-child),
                table th:not(:last-child) {
                    padding: 0.4rem;text-align: center;
                    font-size: 13px;
                    border-left: 0;
                    border-right: 0.01em solid #262729;
                    border-top: 0;
                    border-bottom: 0.01em solid #262729;
                }

            /* .table th,
            .table td {
                vertical-align: top; border-top: 1px solid #3f4143;padding: 0.3rem;
                font-size: 14px !important;
            } */

            table td ,table th{
                vertical-align: middle;
                /* border: 1px solid #262729; */
                padding: 0.3rem;
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
        <table class=" mt-5" style="width: 100%;border:none">
            <tbody>
                <tr>
                    @if($settings->logo)
                    <td class="border-0 pl-0" style="border: none" colspan="2" >
                        <img style="margin:auto;text-align:center" src="{{ url("storage/$settings->logo") }}" alt="logo" height="100">
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
        <br>
        <br>
    
        <h5 class="text-uppercase cool-gray">
            <strong style="text-align: right;direction: rtl">{{ trans_choice('main.student_transportation',2)}}</strong>
        </h5>
         {{-- school info --}}
         <h5 class="text-uppercase">
            <strong style="text-align: right;direction: rtl">{{ trans('main.date')}}: </strong> <span style="text-size:12px">{{date('d-m-Y')}}</span>
        </h5>
        <table class="w-ful" style="width: 100%" id="payment_list">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 border-b">

            
                <tr>
                    <th scope="col" class="px-6 py-3 border">
                       {{trans('main.st_registration_number')}}
                    </th>
                    <th scope="col" class="px-6 py-3 border">
                       {{trans_choice('main.student',1)}}
                    </th>
                    <th scope="col" class="px-6 py-3 border">
                        {{trans('main.national_id')}}
                    </th>
                    <th scope="col" class="px-6 py-3 border">
                        {{trans('main.course_enrolled')}}
                    </th>
                    <th scope="col" class="px-6 py-3 border">
                        {{trans_choice('main.bus_name',1)}}
                    </th>
                    <th scope="col" class="px-6 py-3 border">
                        {{trans_choice( 'main.transport_fee',1)}}
                    </th>
                    <th scope="col" class="px-6 py-3 border">
                        {{trans('main.registration_date')}}
                    </th>
                   
                    <th scope="col" class="px-6 py-3 border">
                        {{trans('main.status')}}
                    </th>
                </tr>
            </thead>
            <tbody>
               
           
           
      
                @forelse ($students_transportation as $transport)
                    <tr class="">
                        <td class="px-6 py-4  border ">
                            {{$transport->student?->registration_number}}
                        </td>
                        <td class="px-6 py-4  border ">
                            {{$transport->student?->username}}
                        </td>
                        <td class="px-6 py-4  border ">
                            {{$transport->student?->user?->national_id}}
                        </td>
                        <td class="px-6 py-4  border ">
                            {{$transport?->student?->semester?->academicYear?->name .' '.$transport?->student?->semester?->course?->name}}
                        </td>
                        <td class="px-6 py-4  border">
                            {{$transport->vehicle?->car_name}}
                        </td>
                        <td class="px-6 py-4  border">
                            {{$transport->transportFee?->name}}
                        </td>
                        <td class="px-6 py-4  border">
                        {{date('Y-m-d',strtotime($transport->created_at))}}
                        </td>
                        <td class="px-6 py-4  border">
                            {{!$transport?->termination_date ? trans('main.transportation_active') 
                                : trans('main.transportation_inactive')}}
                        </td>
                        
                    </tr> 
                @empty 
                    <tr>
                        <td colspan="6" style="border-left: 1px solid #262729">{{trans('main.no_operation_found')}}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <br>
   

    </body>
</html>