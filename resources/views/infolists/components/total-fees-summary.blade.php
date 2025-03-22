<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <style>
        td{color: black;font-weight: 600}
    </style>
    
    <div class=" relative overflow-x-auto shadow-md sm:rounded-lg">
        {{-- <div class="py-2 flex justify-end mb-2">
            {{ $getAction('editPartitions','aùo,e') }}
        </div> --}}
        @if(session()->get('total_summary') != null)
        <table class="w-full text-sm text-right rtl:text-left text-gray-500 dark:text-gray-400 border">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 border-b">
               
                <tr>
                    <th scope="col" class="px-6 py-3 border">
                       {{trans_choice('main.academic_year',1)}}
                    </th>
                    <th scope="col" class="px-6 py-3 border">
                        {{trans('main.account_ballance_actual')}}
                    </th>
                    <th scope="col" class="px-6 py-3 border">
                        {{trans('main.total_fees_to_pay')}}
                    </th>
                   
                </tr>
            </thead>
            <tbody>
               
              
                    @php
                        $total_summary=session()->get('total_summary');
                    @endphp
               
                    <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                        <td scope="row" class="px-6 py-4 border">
                            {{$total_summary->academic_year}}
                        </td>
                        <td class="px-6 py-4 border ">
                            {{$total_summary->total}} {{trans("main.".env('DEFAULT_CURRENCY'))}}
                        </td>
                        
                        <td class="px-6 py-4 border">
                            {{$total_summary->total_fees_to_pay}} {{trans("main.".env('DEFAULT_CURRENCY'))}}
                        </td>
                        
                    </tr> 
                
            </tbody>
        </table>
       
     
        <x-filament-actions::modals />
    </div>
    @else 
    <h4>{{trans('main.not_registered_yet')}}</h4>
    @endif
    </x-dynamic-component>
    