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
                @if($getRecord()->nationality = "saudian")
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
            @foreach ($getState() as $fee)
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
                        $discounts = DB::select('select discounts from student_fee where feeable_id = ? AND feeable_type = ?',[$fee->id,"App\Models\TuitionFee"]);
                    @endphp
                    @if(isset($discounts[0]))
                    <td class="px-6 py-4 border" >
                        {{dd((array)$discounts[0])}}
                    </td>
                    <td class="px-6 py-4 border">
                     
                    </td>
                     @else 

                     <td class="px-6 py-4 border" >
                        02
                     </td>
                     <td class="px-6 py-4 border" >
                        0
                     </td>
                     @endif
                    @if($getRecord()->nationality != "saudian")
                    <td class="px-6 py-4 border">
                        $2999
                    </td>
                    <td class="px-6 py-4 border">
                        $2999
                    </td>
                    @else 
                    <td class="px-6 py-4 border">
                        0%
                    </td>
                    <td class="px-6 py-4 border">
                       0
                    </td>
                    @endif
                    <td class="px-6 py-4 border">
                        {{$partition['due_date']}}
                    </td>
                    <td class="px-6 py-4 border">
                        $2999
                    </td>
                    <td>{{ ($this->editPartitions)(['fee_id' => $fee->id,'partition' => $i,'feeable_type'=>"App\Models\TuitionFee"]) }}</td>
                </tr> 
              @endforeach
             @endif
            @endforeach
        </tbody>
    </table>
   
 
    <x-filament-actions::modals />
</div>

</x-dynamic-component>
