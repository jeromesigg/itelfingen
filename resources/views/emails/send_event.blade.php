<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Ihre Buchung für das Ferienhaus Itelfingen</title>
</head>
<body>
    <h1>Ihre Buchung für das Ferienhaus Itelfingen</h1>
    <table class="table">
        <tbody>
            <tr>
                <th scope="row" style="text-align:left">Datum</th>
                <td>{{Carbon\Carbon::parse($start_date)->format('d.m.Y')}} bis {{Carbon\Carbon::parse($end_date)->format('d.m.Y')}}</td>
            </tr>
            <tr>
                <th scope="row" style="text-align:left">Anzahl Übernachtungen</th>
                <td>{{$total_days}}<td>
            </tr>
            <tr>
                <th scope="row" style="text-align:left">Name</th>
                <td>{{$firstname}} {{$name}}</td>
            </tr>
            <tr>
                <th scope="row" style="text-align:left">E-Mail</th>
                <td>{{$email}}</td>
            </tr>
            <tr>
                <th scope="row" style="text-align:left">Anlass / Gruppe</th>
                <td>{{$group_name}}</td>
            </tr>
            <tr>
                <th scope="row" style="text-align:left">Strasse</th>
                <td>{{$street}}</td>
            </tr>
            <tr>
                <th scope="row" style="text-align:left">PlZ / Ort</th>
                <td>{{$plz}} {{$city}}</td>
            </tr>
            <tr>
                <th scope="row" style="text-align:left">Telefon</th>
                <td>{{$telephone}}<td>
            </tr>
            @foreach ($positions as $position)
                @if($position['bexio_code'] >= 50 && $position['amount'] >= 1)
                    <tr>
                        <th scope="row" style="text-align:left">{{$position['name']}}</th>
                        <td>{{$position['amount']}}<td>
                    </tr>
                @endif
            @endforeach
            <tr>
                <th scope="row" style="text-align:left">Voraussichtliches Total:</th>
                <td>CHF {{$total_amount}}.-<td>
            </tr>
            <tr>
                <th scope="row" style="text-align:left">Bemerkung</th>
                <td>{{$comment}}<td>
            </tr>
        </tbody>
    </table>
    Sobald die Buchung überprüft wurde, erhalten Sie von unserem Hausverwalter eine E-Mail mit dem Vertrag und allen weiteren Dokumenten.
</body>
</html>
