<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <style>
        td{color: black;font-weight: 600}
    </style>
    
    <div class=" relative overflow-x-auto shadow-md sm:rounded-lg">
        {{-- <div class="py-2 flex justify-end mb-2">
            {{ $getAction('editPartitions','aùo,e') }}
        </div> --}}
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400 border">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 border-b">

                <tr>
                    <th scope="col" class="px-6 py-3 border">
                       {{trans('main.receipt_number')}}
                    </th>
                    <th scope="col" class="px-6 py-3 border">
                        {{trans('main.payment_date')}}
                    </th>
                    <th scope="col" class="px-6 py-3 border">
                        {{trans('main.payment_method')}}
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
                @forelse ($getState() as $payment)
                
                    <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                        <td scope="row" class="px-6 py-4 border">
                           {{$payment->id}}
                        </td>
                        <td class="px-6 py-4 border ">
                            {{$payment->payment_date}}
                        </td>
                        <td class="px-6 py-4 border">
                            {{$payment->paymentMethod->name == "transfer" ? trans('main.transfer') : $payment->paymentMethod->name }}
                        </td>
                        
                        <td class="px-6 py-4 border" >
                            {{$payment->value}}
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
                    <td class="px-6 py-4 border" colspan="3" >{{trans('main.total')}}</td>
                    <td class="px-6 py-4 border">
                       {{$total}}
                    </td>
                </tr>
            </tbody>
        </table>
       
     
        <x-filament-actions::modals />
    </div>
    
    </x-dynamic-component>
    