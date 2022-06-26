<!DOCTYPE html>
<html lang="de_CH">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{$title}}</title>
</head>
<body>
    <p>Guten Tag {{$firstname}} {{$name}},</p>
    <br>
    <p>Im angehängten PDF findest du die wichtigsten Informationen zu deinem baldigen Aufenthalt im Ferienhaus Itelfingen und die Parkkarte für das Ferienhaus Itelfingen.</p>
    <p>Schauen auch auf unserer <a href="{{route('faq')}}">FAQ-Seite</a> nach.</p>
    <p>Der Code für die Türe ist {{$code}}.</p>
    <p>Bei Notfällen erreichst du uns direkt: Lukas Affolter (079 810 30 05), Jérôme Sigg (079 587 56 51) oder Matthias Bächler (079 386 73 20).</p>
    <br>
    <p>Freundliche Grüsse,</p>
    <p>Das Ferienhaus Itelfingen</p>
</body>
</html>
