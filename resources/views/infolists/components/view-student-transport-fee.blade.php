<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <style>
        td{color: black;font-weight: 600}
    </style>
     @if($getState() != null && count($getState()))
    <div class=" relative overflow-x-auto shadow-md sm:rounded-lg">
        {{-- <div class="py-2 flex justify-end mb-2">
            {{ $getAction('editPartitions','aùo,e') }}
        </div> --}}
        <table class="w-full text-sm text-right ltr:text-left text-gray-500 dark:text-gray-400 border">
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
                    @if($getRecord()->nationality != "saudian")
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
                    <th scope="col" class="px-6 py-3 border">
                        {{trans('main.action')}}
                    </th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total =[];
                @endphp
                @foreach ($getState() as $fee)
                 @if(count($fee->payment_partition))
                  @foreach ($fee->payment_partition as $i=> $partition)
                   {{-- if student transport has been terminated after due date --}}
                    @if( $getRecord()->transport && ($getRecord()->transport->termination_date == null || ($getRecord()->transport->termination_date > $partition['due_date'])) )
                    
                    <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                        <td scope="row" class="px-6 py-4 border">
                           {{trans_choice('main.transport_fee',1)}} {{$fee->academicYear?->name}}
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
                                        ->where('feeable_type', 'App\Models\TransportFee')
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
                         
                        @if($getRecord()->nationality != "saudian")
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
                        <td  class="px-6 py-4 border">{{ ($this->editTransportFeePartitions)(['fee_id' => $fee->id,'partition' => $i,'feeable_type'=>"App\Models\TransportFee"]) }}</td>
                    </tr> 
                    @endif
                  @endforeach
                 @endif
                @endforeach
                {{-- total sum --}}
                <tr>
                    <td class="px-6 py-4 border" @if($getRecord()->nationality != "saudian") colspan="9" @else colspan="7" @endif>{{trans('main.total')}}</td>
                    <td class="px-6 py-4 border">
                        {{array_sum($total)}} {{trans("main.".env('DEFAULT_CURRENCY')."")}}
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
    