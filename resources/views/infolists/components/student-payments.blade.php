<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">

    <style>
        td{color: black;font-weight: 600}

    </style>
    
    @if($getState() != null && count($getState()))

    <div class=" relative overflow-x-auto shadow-md sm:rounded-lg">
        {{-- <div class="py-2 flex justify-end mb-2">
            {{ $getAction('editPartitions','aùo,e') }}
        </div> --}}
        <x-html-print>
            <div class=" flex items-center justify-end gap-2">
                {{-- <div class="print:hidden relative mb-2 top-3 right-4 hover:opacity-0">
                    {{ ($this->printAllPayments)([]) }} --}}
                    {{-- <button type="button" id="pirnt-btn"
                     x-on:click="printDiv(e)"  
                     
                     class="bg-opacity-50 text-gray-700 shadow-sm border px-2 py-1 rounded-md bg-white flex items-center">
                     <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-1 h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>{{trans('main.print')}}</button> --}}
                {{-- </div> --}}

                {{-- <x-filament::button  
                    class="mb-2"
                    href="{{route('filament.admin.resources.receipt-vouchers.create')}}"
                    tag="a"
                >
                    {{trans('main.new_receipt_payment')}}
                </x-filament::button> --}}
            </div>
        <table class="w-full text-sm text-right ltr:text-left text-gray-500 dark:text-gray-400 border" id="payment_list">
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
                    <th scope="col" class="px-6 py-3 border">
                        {{trans('main.action')}}
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
                            {{$payment->value}} {{trans("main.".env('DEFAULT_CURRENCY'))}}
                        </td>
                        
                        @php
                            $total+=$payment->value;
                        @endphp
                        <td class="px-2">
                            {{ ($this->printReceipt)(['payment_id' => $payment->id]) }}
                            {{ ($this->viewReceipt)(['payment_id' => $payment->id]) }}

                            @if(auth()->user()->student == null && auth()->user()->parent == null)
                            {{ ($this->editReceipt)(['payment_id' => $payment->id]) }}
                            {{ ($this->deleteReceipt)(['payment_id' => $payment->id]) }}
                            @endif
                            {{-- <a href="{{route("filament.admin.resources.receipt-vouchers.edit",['record'=>$payment->id])}}">edit</a> --}}
                        </td>
                    </tr> 
                @empty 
                    <td colspan="4">{{trans('main.no_payment_found')}}</td>
                @endforelse
                {{-- total sum --}}
                <tr>
                    <td class="px-6 py-4 border" colspan="3" >{{trans('main.total')}}</td>
                    <td class="px-6 py-4 border">
                       {{$total}} {{trans("main.".env('DEFAULT_CURRENCY')."")}}
                    </td>
                </tr>
            </tbody>
        </table>
       
           
          </x-html-print>

        <x-filament-actions::modals />
    </div>
    @else 
        <h4>{{trans('main.no_operation_found')}}</h4>
    @endif
    </x-dynamic-component>
    