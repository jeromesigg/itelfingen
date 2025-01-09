@aware(['model'])

@if($label != '')<label for="{{ $name }}">{{ $label }}</label>@endif
<input type="{{ $type ?? 'text' }}" name="{{ $name }}" id="{{ $id }}" 
    @if($type === 'checkbox')
        value="{{$value ?? ($model ? data_get($model, $name) : '')}}" {{ $attributes }} {{$value ? 'checked' : ''}}
    @else
        value="{{$value ?? ($model ? data_get($model, $name) : '')}}" {{ $attributes->merge(['class' => 'form-control']) }} 
    @endif
    {{ $required ? 'required' : ''}}>