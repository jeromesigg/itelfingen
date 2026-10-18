@aware(['model', 'scope' => null])

@if($label != '')<label for="{{ $name }}">{{ $label }}</label>@endif
<input type="hidden" name="{{$name}}" id="{{$id}}" value="{{ \App\View\Components\Forms\OldInput::value($name, $value ?? ($model ? data_get($model, $name) : ''), $scope) }}" {{ $attributes }}/>
