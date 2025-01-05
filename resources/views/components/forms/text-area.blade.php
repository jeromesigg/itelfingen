@aware(['model'])

<label for="{{ $name }}">{{ $label }}</label>
<textarea name="{{ $name }}" id="{{ $name }}" {{ $attributes->merge(['class' => 'form-control']) }} {{ $required ? 'required' : ''}}>{{$value ?? ($model ? data_get($model, $name) : '')}}</textarea>