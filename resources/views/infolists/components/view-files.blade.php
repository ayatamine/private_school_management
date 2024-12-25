<x-filament::section collapsible collapsed>
    <x-slot name="heading">
        {{trans('main.files')}}
    </x-slot>
 
    <div class="bg-white p-3 px-4">
        @if(count($getRecord()->files))
        
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @php
                    function isImage($filePath) {
                        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp','svg'];
                        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                        return in_array($extension, $allowedExtensions);
                    }
                @endphp
                @foreach ($getRecord()->files as $file)
                   
                    @if(isImage($file->file))
                       <div class="flex items-start gap-3">
                        <div style="" class="flex-2 shadow-lg border flex justify-center items-center">
                            <a href="{{asset('storage/'.$file->file)}}" target="_blink" style="color:blue;" class="text-sm underline">
                                <img src="{{asset('storage/'.$file->file)}}" alt="{{$file->file}}"  style="height:68px;width:68px">
                            </a>
                           
                        </div>
                        <p class="flex-1">{{$file->description}}</p>
                       </div>
                    @else
                        <div class="flex items-start gap-3">
                            <div style="" class="flex-2 shadow-lg border flex       justify-center items-center">
                                <a href="{{asset('storage/'.$file->file)}}" target="_blink" style="color:blue;" class="text-sm underline">
                                    <img src="{{asset('storage/file.svg')}}" alt="{{$file->file}}" style="height: 50px">
                                </a>
                                
                            </div>
                            <p class="flex-1">{{$file->description}}</p>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
</x-filament::section>