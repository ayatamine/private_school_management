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
                        {{trans('main.id_ne')}}
                     </th>
                     <th scope="col" class="px-6 py-3 border">
                        {{trans_choice('main.academic_year',1)}}
                    </th>
                    
                    
                    
                    <th scope="col" class="px-6 py-3 border">
                        {{trans_choice('main.academic_stage',1)}}
                    </th>
                    <th scope="col" class="px-6 py-3 border">
                        {{trans_choice('main.academic_course',1)}}
                    </th>
                  
                    <th scope="col" class="px-6 py-3 border">
                        {{trans_choice('main.semester',1)}}
                    </th>
                    <th scope="col" class="px-6 py-3 border">
                        {{trans('main.approved_at')}}
                    </th>
                    @if(auth()->user()->student == null && auth()->user()->parent == null)
                    <th scope="col" class="px-6 py-3 border">
                        {{trans('main.action')}}
                    </th>
                    @endif
                </tr>
            </thead>
            <tbody>
                  @foreach ($getState() as $k=> $semester)
                  
                  {{-- if student has been terminated after due date --}}
                    {{-- {{dd($semester->semester->academicYear)}} --}}
                    <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                        <td scope="row" class="px-6 py-4 border">
                            {{$semester->id}}
                         </td>
                         <td class="px-6 py-4 border">
                            {{$semester->semester?->academicYear?->name}}
                        </td>
                        <td class="px-6 py-4 border">
                            {{$semester->semester?->course?->academicStage?->name}}
                        </td>
                        
                        <td class="px-6 py-4 border">
                            {{$semester->semester?->course?->name}}
                        </td>
                        
                        
                        
                        <td class="px-6 py-4 border">
                            {{$semester->semester?->name}}
                        </td>
                        <td class="px-6 py-4 border">
                            {{date('Y-m-d', strtotime($semester->enrollment_date))}}
                        </td>
                        @if(auth()->user()->student == null && auth()->user()->parent == null)
                        <td  class="px-6 py-4 border">{{ ($this->showFees)(['fee_id' => $semester->id]) }} {{ ($this->printInvoice)(['fee_id' => $semester->id,'academic_year_id' => $semester->semester?->academicYear?->id]) }}</td>
                        @endif
                    </tr> 
                  @endforeach
            </tbody>
        </table>
       
     
        <x-filament-actions::modals />
    </div>
    @else 
    <h4>{{trans('main.not_registered_yet')}}</h4>
    @endif
    </x-dynamic-component>
    