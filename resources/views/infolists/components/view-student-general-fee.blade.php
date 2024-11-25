<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <style>
        td{color: black;font-weight: 600}
    </style>
    
    <div class=" relative overflow-x-auto shadow-md sm:rounded-lg">
        {{-- <div class="py-2 flex justify-end mb-2">
            {{ $getAction('editPartitions','aùo,e') }}
        </div> --}}
        @if($getState() != null && count($getState()))
        <table class="w-full text-sm text-right rtl:text-left text-gray-500 dark:text-gray-400 border">
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
                    <th scope="col" class="px-6 py-3 border">
                        {{trans('main.tax_percentage')}}
                    </th>
                    <th scope="col" class="px-6 py-3 border">
                        {{trans('main.tax_value')}}
                    </th>
                    <th scope="col" class="px-6 py-3 border">
                        {{trans('main.due_date')}}
                    </th>
                    <th scope="col" class="px-6 py-3 border">
                        {{trans('main.total')}}
                    </th>
                    <th scope="col" class="px-6 py-3 border">
                        {{trans('main.action')}}
                    </th>
                </tr>
            </thead>
            <tbody>
                @php
                     $grand_total=$total =$value_after_discount=$value_after_tax=[];
                @endphp
              
                @foreach ($getState() as $k=> $fee)
               
                 @if(count($fee->payment_partition))
                  @foreach ($fee->payment_partition as $i=> $partition)
                  {{-- if student has been terminated after due date --}}
                    @if($getRecord()->termination_date ==null || $getRecord()->termination_date > $partition['due_date_end_at'])
                    <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                        <td scope="row" class="px-6 py-4 border">
                           {{trans_choice('main.tuition_fee',1)}} {{$fee->academicYear?->name}}
                        </td>
                        <td class="px-6 py-4 border ">
                            {{$partition['partition_name']}}
                        </td>
                        <td class="px-6 py-4 border">
                           {{-- added --}}
                            @php
                                if($getRecord()->approved_at && ($partition['due_date_end_at'] <= \Carbon\Carbon::createFromTimestamp($getRecord()->approved_at)->format('Y-m-d'))) $partition['value'] =  0;
                                if($getRecord()->termination_date && $getRecord()->termination_date <= $partition['due_date']) $partition['value'] =  0;
                            @endphp
                            {{$partition['value']}}
                        </td>
                        @php
                            $discounts = DB::table('student_fee')
                                        ->where('student_id', $getRecord()->id)
                                        ->where('feeable_id', $fee->id)
                                        ->where('feeable_type', 'App\Models\GeneralFee')
                                        ->value('discounts');
                                        $decodedDiscounts = json_decode($discounts, true);
                        @endphp
                        @if(isset($decodedDiscounts[$i]) && array_key_exists('discount_value',$decodedDiscounts[$i]))
                        <td class="px-6 py-4 border" >
                            {{$decodedDiscounts[$i]['discount_value']}} @if($decodedDiscounts[$i]['discount_type'] == 'percentage')% @endif
                        </td>
                        <td class="px-6 py-4 border">
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
                                }else
                                {
                                    $value_after_discount[$i] =$partition['value'];
                                }
                                   
                            @endphp
                            
                            {{$value_after_discount[$i]}}
                        </td>
                         @else 
    
                         <td class="px-6 py-4 border" >
                            0
                         </td>
                         <td class="px-6 py-4 border" >
                            {{$partition['value']}}
                         </td>
                         @endif
                         
                        {{-- @if($getRecord()->nationality != "saudian") --}}
                        @php
                            $vat = null;
                            $payment_due_date = $partition['due_date_end_at'];
                            $vat = \App\Models\ValueAddedTax::whereDate('applies_at',"<=",date('Y-m-d',strtotime($payment_due_date)))->first();   
                            if($vat == null)
                            {
                                $vat = \App\Models\ValueAddedTax::first();
                            }
                        @endphp
                        
                        <td class="px-6 py-4 border">
                            {{$vat?->percentage ?? 0}} %
                        </td>
                        <td class="px-6 py-4 border">
                            {{-- here you can check if the orginal value or value_after_discount is with vat or not  --}}
                            @php
                                $value_after_tax[$i] = (($vat?->percentage ? $vat?->percentage : 0) / 100) * (isset($value_after_discount[$i]) ? $value_after_discount[$i] : $partition['value']) ;                               
                            @endphp
                            {{$value_after_tax[$i]}}
                        </td>
                        {{-- @else 
                        <td class="px-6 py-4 border">
                           0%
                        </td>
                        <td class="px-6 py-4 border">
                            {{$value_after_discount[$i] ?? $partition['value']}}
                        </td>
                        @endif --}}
                        <td class="px-6 py-4 border">
                            {{$partition['due_date']}}
                        </td>
                        <td class="px-6 py-4 border">
                            @php
                            
                                $total[$i] = (isset($value_after_discount[$i]) ? $value_after_discount[$i] : $partition['value']) + (isset($value_after_tax[$i]) ? $value_after_tax[$i] : 0);
                                $total_fees_to_pay[$i] = (now() > $partition['due_date'] ) ?  ((isset($value_after_discount[$i]) ? $value_after_discount[$i] : $partition['value']) + (isset($value_after_tax[$i]) ? $value_after_tax[$i] : 0)) : 0;
                                $grand_total[$k] =$total[$i];
                                
                            @endphp
                            {{$total[$i]}}
                        </td>
                        <td  class="px-6 py-4 border">
                            {{ ($this->editPartitions)(['fee_id' => $fee->id,'partition' => $i,'feeable_type'=>"App\Models\GeneralFee"]) }}
                            {{-- {{ ($this->printReceipt)(['fee_id' => $fee->id]) }} --}}
                        </td>
                    </tr> 
                    @endif
                  @endforeach
                  @php 
                 
                  @endphp
                 @endif
                @endforeach
                {{-- total sum --}}
                <tr>
                    <td class="px-6 py-4 border"  colspan="9">{{trans('main.total')}}</td>
                    <td class="px-6 py-4 border">
                        {{array_sum($grand_total) + array_sum($total_fees_to_pay)}} {{trans("main.".env('DEFAULT_CURRENCY')."")}}
                    </td>
                </tr>
                <tr>
                    <td class="px-6 py-4 border"  colspan="9">{{trans('main.total_fees_to_pay')}}</td>
                    <td class="px-6 py-4 border">
                        {{array_sum($grand_total)}} {{trans("main.".env('DEFAULT_CURRENCY')."")}}
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
    