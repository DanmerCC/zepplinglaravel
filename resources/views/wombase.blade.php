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
        <br>
           <div class="header">
                <div class="logo">
                    <img src="/assets/logo.png">
                </div>
           </div>
            <div id="app">
                <view-jobs></view-jobs>
            </div>
            <script src="{{ mix('/js/app.js') }}"></script>
	</body>
</html>
