<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Kontaktformular</title>
</head>    
<body>
    <h1>Kontaktformular</h1>
    <table class="table">
        <tbody>
            <tr>
                <th scope="row" style="text-align:left">Name</th>
                <td>{{$name}}</td>
            </tr>
            <tr>
                <th scope="row" style="text-align:left">E-Mail</th>
                <td>{{$email}}</td>
            </tr>
            <tr>
                <th scope="row" style="text-align:left">Betreff</th>
                <td>{{$subject}}</td>
            </tr>
            <tr>
                <th scope="row" style="text-align:left">Text</th>
                <td>{{$text}}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>