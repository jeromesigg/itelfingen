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
    <p>Im angehängten PDF findest du die wichtigsten Informationen zu deinem baldigen Aufenthalt im Ferienhaus Itelfingen.</p>
    <p>Schauen auch auf unserer <a href="{{route('faq')}}">FAQ-Seite</a> nach.</p>
    <p>Der Code für die Türe ist {{$code}}.</p>
    <p>Freundliche Grüsse,</p>
    <p>Das Ferienhaus Itelfingen</p>
</body>
</html>