<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div>
        {{-- {{ $getState() }} --}}
        
       @if($getState() != "") <a href="{{$getState()}}" target="_blink" class="underline " style="color:blue">{{trans('main.view_document')}}</a> 
       @else 
         <strong>{{trans('main.no_document_found')}}</strong> 
       @endif
    </div>
</x-dynamic-component>
