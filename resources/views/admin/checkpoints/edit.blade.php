@extends('layouts.admin')
@section('content')

    <header>
        <h3 class="text-3xl font-bold dark:text-white">Checkpunkt bearbeiten</h3>
    </header>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-testcolor">
        <div class="col-span-2">
            <x-forms.form :action="route('checkpoints.update', $checkpoint)" enctype="multipart/form-data" accept-charset="UTF-8" method="PATCH" :model="$checkpoint">
                <x-forms.container>
                    <x-forms.text label="Name:" name="name"/>
                </x-forms.container>
                <x-forms.container>
                    <x-forms.text-area label="Beschreibung:" name="description" rows=15/>
                </x-forms.container>
                <x-forms.container>
                    <x-forms.select label="Raum:" name="room_id" required=true :collection="$rooms_select"/>
                </x-forms.container>
                <x-forms.container>
                    <x-forms.button type="submit" class="btn btn-primary">
                        Checkpunkt aktualisieren
                    </x-forms.button>
                </x-forms.container>
            </x-forms.form>
        </div>
    </div>
    <div class="row">
        @include('includes.form_error')
    </div>
@endsection

@push('scripts')
    <script type="module">
        const editor = Jodit.make('#description', {
            buttons: ['bold', 'italic', 'underline', '|', 'ul', 'ol'],
            height: 400,
            allowResizeY: true
        });
    </script>
@endpush