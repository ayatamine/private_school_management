<x-filament::section collapsible collapsed>
    <x-slot name="heading">
        {{trans('main.files')}}
    </x-slot>
  <style>td{padding:0.5rem}
    /* .remove-file{max-width: 50px;} */
  </style>
    <div class="bg-white p-3">
        @if(count($getRecord()->files))
            @php
                function isImage($filePath) {
                    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp','svg'];
                    $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                    return in_array($extension, $allowedExtensions);
                }
            @endphp
            <table class="border w-full">
                <tbody>
                    @foreach ($getRecord()->files as $file)
                   
                        @if(isImage($file->file))
                        <tr class="w-full mb-1">
                            <td style="width:70px" class="border">
                                <a href="{{asset('storage/'.$file->file)}}" target="_blink" style="color:blue;" class="text-sm underline">
                                    <img src="{{asset('storage/'.$file->file)}}" alt="{{$file->file}}"  style="height:56px;width:56px">
                                </a>
                            
                            </td>
                            <td  class="border">
                                <p class="text-sm">{{$file->description}}</p>
                            </td>
                            <td  class="border remove-file" >
                                {{ ($this->removeFile)(['id' => $file->id]) }}
                            </td>
                        
                        </tr>
                        @else
                            <tr class="w-full mb-1">
                                <td style="width:70px"  class="border">
                                    <a href="{{asset('storage/'.$file->file)}}" target="_blink" style="color:blue;" class="text-sm underline">
                                        <img src="{{asset('storage/file.svg')}}" alt="{{$file->file}}" style="height: 56px;width:56px">
                                    </a>
                                    
                                </td>
                                <td  class="border remove-file">
                                    <p class="text-sm">{{$file->description}}</p>
                                </td>
                                <td >
                                    {{ ($this->removeFile)(['id' => $file->id]) }}
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
   
        @endif
    </div>
</x-filament::section>