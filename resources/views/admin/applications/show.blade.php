@extends('layouts.admin')
@section('content')
    <div>
        <div class="container-fluid">
            <header>
                <h3 class="text-3xl font-bold dark:text-white">Bewerbung anzeigen</h3>
            </header>
            <table>
                <tr>
                    <th width="150px">Anrede</th>
                    <td>{{$application->salutation ? $application->salutation['name'] : ''}}</td>
                </tr>
                <tr>
                    <th>Vorname</th>
                    <td> {{$application['firstname']}}</td>
                </tr>
                <tr>
                    <th>Nachname</th>
                    <td> {{$application['name']}}</td>
                </tr>
                <tr>
                    <th>Organisation</th>
                    <td>{{$application['organisation']}}</td>
                </tr>
                <tr>
                    <th>Strasse</th>
                    <td>{{$application['street']}}</td>
                </tr>
                <tr>
                    <th>Strassen-Nr.</th>
                    <td>{{$application['house_number']}}</td>
                </tr>
                <tr>
                    <th>PLZ / Ort</th>
                    <td>{{$application['zipcode']}} {{$application['city']}}</td>
                </tr>
                <tr>
                    <th>E-Mail</th>
                    <td>{{$application['email']}}</td>
                </tr>
                <tr>
                    <th>Telefon</th>
                    <td>{{$application['telephone']}}</td>
                </tr>
                <tr>
                    <th>Wieso</th>
                    <td>{{$application['why']}}</td>
                </tr>
                <tr>
                    <th>Bemerkung</th>
                    <td> {{$application['comment']}}</td>
            </table>
        </div>
    </div>
@endsection
