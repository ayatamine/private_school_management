<table class=" mt-5" style="width: 100%;border:0">
    <tbody>
        <tr>
            
            <td class="border-0 pl-0" colspan="2" style="text-align: right;font-size:14px;border:none">
                <strong style="font-size: 18px;font-weight:bold"> {{ $settings->title }}</strong> <br>
               {{ trans('main.permit_number_2') }} : <span style="margin:0 5px;font-weight:bold">{{ $settings->permit_number }}</span> 
               {{ trans('main.commercial_register_number_2') }} : <span style="font-weight:bold">{{ $settings->commercial_register_number }}</span> <br>
               {{ trans('main.tax_number_2') }} : <span style="font-weight:bold">{{ $settings->added_value_tax_number }}</span> <br>
                <span >{{ $settings->address }}</span> <br>
               {{ trans('main.email') }} : <span style="margin:0 5px;font-weight:bold">{{ $settings->email }}</span> 
               {{ trans('main.phone_number') }} : <span style="margin:0 5px;font-weight:bold">{{ $settings->phone_number }}</span> 
           </td>
           @if($settings->logo)
           <td class="border-0 pl-0" style="border: none;text-align:left" colspan="2" >
               <img style="margin:1rem;text-align:left" src="{{ url(asset("storage/$settings->logo")) }}"  alt="logo" height="90">
           </td>
           @endif
        </tr>
    </tbody>
</table>