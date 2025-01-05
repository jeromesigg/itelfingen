@aware(['model'])

<label for="{{$name}}">{{$label}}</label>
<select name="{{$name}}"  id="{{ $id }}" {{$attributes->merge(['class' => 'form-control']) }} {{ $required ? 'required' : ''}}>
    @foreach ($collection as $key => $value)
        <option value="{{ $key }}"
        @if ($key == ($model ? data_get($model, $name) : 0))
            selected="selected"
        @endif
        >{{ $value }}</option>
    @endforeach
</select>