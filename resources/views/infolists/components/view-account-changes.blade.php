<div class="w-full">
    <style>
    th,td{padding:0.5rem}
    th{text-align: start}
    </style>
    <h5 class="px-3 mb-3 text-base lg:text-2xl font-bold underline underline-offset-2">{{trans_choice('main.transfer_operation',2)}}</h5>
    <br>
    <div class="p-3">
        <table class="border w-full">
            <thead>
                <tr>
                    <th class="border">
                        {{trans('main.operation_type')}}
                    </th>
                    <th class="border">{{trans('main.from_account_id')}}</th>
                    <th class="border">{{trans('main.to_account_id')}}</th>
                    <th class="border">{{trans('main.amount')}}</th>
                    <th class="border">{{trans('main.date')}}</th>
                </tr>
            </thead>
            <tbody>
                @forelse(\App\Models\Transfer::where('from_account_id',$getRecord()->id)->orWhere('to_account_id',$getRecord()->id) as $transfer)
                <tr class="w-full mb-1">
                    <td class="border">
                        {{$transfer->from_account_id == $getRecord()->id ? trans('main.out') : trans('main.in')}}
                    </td>
                    <td  class="border">
                        {{$transfer->from_account_id == $getRecord()->id ? trans('main.current_account') : $transfer->fromAccount?->name }}
                    </td>
                    <td class="border">
                        {{$transfer->to_account_id == $getRecord()->id ? trans('main.current_account') : $transfer->toAccount?->name }}
                    </td>
                    <td>{{number_format($transfer->amount, 2, '.', ',')}}</td>
                    <td>{{\Carbon\Carbon::createFromTimestamp($transfer->transfer_date)->isoFormat('Y-M-D')}}</td>
                
                </tr>
                @empty 
                 <tr>
                    <td colspan="5" class="text-center">{{trans('main.no_transfer_found')}}</td>
                 </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <br><br>
    <h5 class="px-3 mb-3 text-base lg:text-2xl font-bold underline underline-offset-2">{{trans_choice('main.payments',2)}}</h5>
    <div class="p-3">
        <table class="border w-full">
            <thead>
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
                    
                    $payment_methods = $getRecord()->paymentMethods()->pluck('id');
                @endphp
                @forelse (\App\Models\ReceiptVoucher::whereIn('payment_method_id',$payment_methods)->get() as $payment)
                
                    <tr class="">
                        <td scope="row" class="px-6 py-4 border ">
                        {{$payment->id}}
                        </td>
                        <td class="px-6 py-4  border">
                            {{\Carbon\Carbon::createFromDate($payment->payment_date)->isoFormat('Y-M-DD')}}
                        </td>
                        <td class="px-6 py-4 border">
                            {{$payment->paymentMethod->name == "transfer" ? trans('main.transfer') : $payment->paymentMethod->name }}
                        </td>
                        
                        <td class="px-6 py-4 border" >
                            {{number_format(floatval($payment->value), 2, '.', ',')}} {{trans("main.".env('DEFAULT_CURRENCY'))}}
                        </td>
                        
                    </tr> 
                    
                @empty 
                    <tr>
                        <td colspan="4" style="border-left: 1px solid #262729">{{trans('main.no_payment_found')}}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <br><br>
    <h5 class="px-3 mb-3 text-base lg:text-2xl font-bold underline underline-offset-2">{{trans_choice('main.income',2)}}</h5>
    <div class="p-3">
        <table class="border w-full">
            <thead>
                <tr>
                    <th scope="col" class="px-6 py-3 border">
                        {{trans('main.id')}}
                     </th>
                     <th scope="col" class="px-6 py-3 border">
                         {{trans('main.income_name')}}
                     </th>
                     <th scope="col" class="px-6 py-3 border">
                         {{trans('main.value')}}
                     </th>
                    
                     <th scope="col" class="px-6 py-3 border">
                         {{trans('main.created_at')}}
                     </th>
                </tr>
            </thead>
            <tbody>
                @php
                    
                    $payment_methods = $getRecord()->paymentMethods()->pluck('id');
                @endphp
                @forelse (\App\Models\Income::whereIn('payment_method_id',$payment_methods)->get() as $income)
                
                    <tr class="">
                        <td scope="row" class="px-6 py-4 border ">
                         {{$income->id}}
                        </td>
                        <td class="px-6 py-4  border">
                            {{$income?->transactionCategory?->name}}
                        </td>
                        
                        <td class="px-6 py-4 border" >
                            {{number_format(floatval($income->value), 2, '.', ',')}} {{trans("main.".env('DEFAULT_CURRENCY'))}}
                        </td>
                        <td class="px-6 py-4  border">
                            {{\Carbon\Carbon::createFromDate($income->created_at)->isoFormat('Y-M-DD')}}
                        </td>
                    </tr> 
                    
                @empty 
                    <tr>
                        <td colspan="4" style="border-left: 1px solid #262729">{{trans('main.no_payment_found')}}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <br><br>
    <h5 class="px-3 mb-3 text-base lg:text-2xl font-bold underline underline-offset-2">{{trans_choice('main.expense',2)}}</h5>
    <div class="p-3">
        <table class="border w-full">
            <thead>
                <tr>
                    <th scope="col" class="px-6 py-3 border">
                        {{trans('main.registration_number')}}
                     </th>
                     <th scope="col" class="px-6 py-3 border">
                         {{trans('main.expense_name')}}
                     </th>
                     <th scope="col" class="px-6 py-3 border">
                         {{trans_choice('main.payment_method',1)}}
                     </th>
                     <th scope="col" class="px-6 py-3 border">
                         {{trans('main.value')}}
                     </th>
                    
                     <th scope="col" class="px-6 py-3 border">
                         {{trans('main.created_at')}}
                     </th>
                </tr>
            </thead>
            <tbody>
                @php
                    
                    $payment_methods = $getRecord()->paymentMethods()->pluck('id');
                @endphp
                @forelse (\App\Models\Expense::whereIn('payment_method_id',$payment_methods)->get() as $expense)
                
                    <tr class="">
                        <td scope="row" class="px-6 py-4 border ">
                         {{$expense->id}}
                        </td>
                        <td class="px-6 py-4  border">
                            {{$expense?->transactionCategory?->name}}
                        </td>
                        <td class="px-6 py-4  border">
                            {{$expense?->paymentMethod->name}}
                        </td>
                        
                        <td class="px-6 py-4 border" >
                            {{number_format(floatval($expense->value), 2, '.', ',')}} {{trans("main.".env('DEFAULT_CURRENCY'))}}
                        </td>
                        <td class="px-6 py-4  border">
                            {{\Carbon\Carbon::createFromDate($expense->created_at)->isoFormat('Y-M-DD')}}
                        </td>
                    </tr> 
                    
                @empty 
                    <tr>
                        <td colspan="4" style="border-left: 1px solid #262729">{{trans('main.no_payment_found')}}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>