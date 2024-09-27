<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
<style>
    td{color: black;font-weight: 600}
</style>
<div class=" relative overflow-x-auto shadow-md sm:rounded-lg">
    <div class="py-2 flex justify-end mb-2">
        {{ $getAction('editPartitions') }}
    </div>
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
                @if($fee->dicounts)
                <th scope="col" class="px-6 py-3 border">
                    {{trans('main.discount_value')}}
                </th>
                <th scope="col" class="px-6 py-3 border">
                    {{trans('main.value_after_discount')}}
                </th>
                @endif
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
            </tr>
        </thead>
        <tbody>
            @foreach ($getState() as $fee)
             @if(count($fee->payment_partition))
              @foreach ($fee->payment_partition as $partition)
              
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
                    @if($fee->dicounts)
                    <td class="px-6 py-4 border">
                        $2999
                    </td>
                    <td class="px-6 py-4 border">
                        $2999
                    </td>
                    @endif
                    <td class="px-6 py-4 border">
                        $2999
                    </td>
                    <td class="px-6 py-4 border">
                        $2999
                    </td>
                    @endif
                    <td class="px-6 py-4 border">
                        {{$partition['due_date']}}
                    </td>
                    <td class="px-6 py-4 border">
                        $2999
                    </td>
                </tr> 
              @endforeach
             @endif
            @endforeach
        </tbody>
    </table>
</div>

</x-dynamic-component>
