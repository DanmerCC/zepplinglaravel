<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>MENU ID</title>
		<link rel="stylesheet" type="text/css" href="{{ mix('css/app.css') }}">
		<link rel="stylesheet" type="text/css" href="estilo.css">

	</head>
    <style>
        body {
            background-image: 'assets/wave2.png' no-repeat
        }
    </style>
	<body>
            <ul class="menu">
                <li><a href="#">INICIO</a></li>
                <li><a href="http://10.40.111.88:8080/#/notebook/2GZRVSJZV">CONSULTA</a></li>
                <li><a href="historial.html">HISTORIAL DE BUSQUEDA</a></li>
                <li><a href="contacto.html">CONTACTO</a></li>
                <li><a href="index.html">CERRAR SESION</a></li>
			</ul>
            <div id="app">
                <view-jobs></view-jobs>
            </div>
            <script src="{{ mix('/js/app.js') }}"></script>
	</body>
</html>
