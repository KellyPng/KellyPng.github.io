<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>401 Unauthorized</title>
    
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
    </script>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif&family=Rubik:wght@800&family=Trocchi&display=swap"
        rel="stylesheet">

    <style>
        body {
            background: url('{{ asset('images/security.jpeg') }}') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: 'Rubik', sans-serif;
        }

        .unauthorized-container {
            background: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            max-width: 500px;
            width: 100%;
        }

        .unauthorized-container h1 {
            font-size: 36px;
            margin-top: 20px;
        }

        .unauthorized-container p {
            font-size: 18px;
            margin-top: 10px;
        }

        .unauthorized-container .btn {
            margin-top: 20px;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="unauthorized-container">
        <h1>401! Hold up!</h1>
        <p>Sorry, but you are not authorized to view this page.</p>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
        <button class="btn btn-primary" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            Back to Login Page
        </button>
    </div>
</body>
</html>