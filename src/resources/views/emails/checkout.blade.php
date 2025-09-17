<!DOCTYPE html>
<html>

<head>
    <title>Gràcies per la seva estada</title>
    <style>
        /* Definición de colores */
        body {
            font-family: "Montserrat", sans-serif;
            background-color: #f9f6f5;
            color: #555;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
        }

        h1 {
            color: #8f5241;
            font-size: 24px;
            text-align: center;
        }

        p {
            color: #555;
            font-size: 16px;
            line-height: 1.5;
            text-align: center;
        }

        a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #b59e98;
            color: #ffffff;
            text-decoration: none;
            font-size: 16px;
            border-radius: 5px;
            text-align: center;
        }

        a:hover {
            background-color: #8f5241;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Gràcies per la seva estada!</h1>
        <p>Ens agradaria saber la seva opinió sobre la seva experiència. Si us plau, feu clic en el següent enllaç per
            deixar la seva ressenya:</p>
        <p><a href="{{ $frontend_url }}/{{ $token }}">Deixar una ressenya</a></p>
    </div>
</body>

</html>
