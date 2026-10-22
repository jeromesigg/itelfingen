@aware(['model', 'scope' => null])

@if($label != '')<label for="{{ $id }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ $label }}</label>@endif
<input type="{{ $type ?? 'text' }}" name="{{ $name }}" id="{{ $id }}"
    @if($type === 'checkbox')
        {{-- Tailwind 4: bg-gray-100 (Ebene utilities) überdeckt sonst den blauen Hintergrund von Flowbite (Ebene base), der weisse Haken wäre unsichtbar --}}
        value="{{$value ?? ($model ? data_get($model, $name) : '')}}" {{ $attributes }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600 checked:bg-blue-600 checked:border-transparent dark:checked:bg-blue-600" {{$value ? 'checked' : ''}}
    @else
        {{-- Nach einem Validierungsfehler die Eingabe wieder anzeigen (nur aus dem eigenen Formular, siehe OldInput) --}}
        value="{{ \App\View\Components\Forms\OldInput::value($name, $value ?? ($model ? data_get($model, $name) : ''), $scope) }}" {{ $attributes->merge(['class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500']) }}
    @endif
    {{ $required ? 'required' : ''}}>
