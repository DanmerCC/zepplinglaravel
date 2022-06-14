<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>MENU ID</title>
		<link rel="stylesheet" type="text/css" href="{{ mix('css/app.css') }}">
		<link rel="stylesheet" type="text/css" href="estilo.css">
        <link rel="icon" href="favicon.ico" type="image/x-icon">
        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">

	</head>
    <style>
        body {
            background-image: 'assets/wave2.png' no-repeat
        }
        .end {
            width: 75%;
            justify-items: end
        }
    </style>
	<body>
        <br>
           <div class="header">
                <div class="logo">
                    <img src="/assets/logo.png">
                </div>
                @guest

                @else
                <div class="end d-flex align-items-end flex-column">
                    <a href="/logout">Salir</a>
                </div>

                @endguest
           </div>
            <div id="app">
                <view-jobs></view-jobs>
            </div>
            <script src="{{ mix('/js/app.js') }}"></script>
	</body>
</html>
