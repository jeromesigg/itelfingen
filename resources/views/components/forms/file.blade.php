@aware(['model'])

@if($label != '')<label for="{{ $name }}">{{ $label }} </label>@endif
<input type="file" id="{{ $id }}" name="{{ $name }}" {{ $required ? 'required' : ''}} {{$attributes}}>