<form method="{{ $spoofMethod ? 'POST' : $method }}" {!! $attributes !!}>
    @unless(in_array($method, ['HEAD', 'GET', 'OPTIONS']))
        @csrf
    @endunless

    @if($spoofMethod)
        @method($method)
    @endif
    @if($scope)
        <input type="hidden" name="_form_scope" value="{{ $scope }}">
    @endif

        {!! $slot !!}
</form>
