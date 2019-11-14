<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Filevuer Home</title>

    <link href="{{ asset('vendor/filevuer/css/filevuer.css') }}" rel="stylesheet">
</head>
<body>

    <div id="filevuer-main">
        <app :connections='{{ $connections }}' :logged-in='{{ $loggedIn }}' selected='{{ $selected }}'></app>
    </div>

<script>
    window.Filevuer = {
        csrfToken: '{{ csrf_token() }}'
    }
</script>
<script src="{{ asset('vendor/filevuer/js/filevuer.js') }}"></script>

</body>
</html>
