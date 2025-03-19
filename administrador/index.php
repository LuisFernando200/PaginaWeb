<!DOCTYPE html>
<html>
<head>
    <title>Login de Empleados</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        header {
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            text-align: center;
        }
        .login-container {
            width: 100%;
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h2 {
            margin: 0 0 20px;
            color: #4CAF50;
        }
        form input[type="email"],
        form input[type="password"],
        form input[type="submit"] {
            width: calc(100% - 20px);
            margin: 10px;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }
        form input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        form input[type="submit"]:hover {
            background-color: #45a049;
        }
        #mensaje {
            width: calc(100% - 20px);
            background: #EFEFEF;
            border-radius: 5px;
            color: #f00;
            font-size: 16px;
            line-height: 25px;
            text-align: center;
            margin: 20px auto 0;
            padding: 5px;
            display: none;
        }
        footer {
            text-align: center;
            padding: 10px;
            background-color: #333;
            color: white;
            margin-top: 20px;
        }
    </style>
    <script src="jquery-3.3.1.min.js"></script>
    <script>
        function login(form) {
            var correo = form.correo.value;
            var pass = form.pass.value;

            var mensajeDiv = document.getElementById('mensaje');
            mensajeDiv.style.display = "none";

            if (correo === "" || pass === "") {
                mensajeDiv.innerHTML = "Faltan campos por llenar";
                mensajeDiv.style.display = "block";
                return false;
            }

            // Depurar valores enviados
            console.log("Correo:", correo, "Contraseña:", pass);

            $.ajax({
                url: 'verificar.php',
                type: 'POST',
                dataType: 'json',
                data: { correo: correo, pass: pass },
                success: function(res) {
                    if (res.existe) {
                        window.location.href = 'bienvenido.php';
                    } else {
                        mensajeDiv.innerHTML = res.message || "Error: Credenciales incorrectas";
                        mensajeDiv.style.display = "block";
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error:", xhr.responseText);
                    mensajeDiv.innerHTML = "Error en la solicitud. Inténtalo de nuevo.";
                    mensajeDiv.style.display = "block";
                }
            });

            return false;
        }
    </script>
</head>
<body>
    <header>
        <h1>Bienvenido a TecnoHub</h1>
    </header>
    <div class="login-container">
        <h2>Iniciar Sesión</h2>
        <form name="loginForm" method="post" onsubmit="return login(this);">
            <input type="email" name="correo" placeholder="Escribe tu correo" /><br>
            <input type="password" name="pass" placeholder="Escribe tu contraseña" /><br>
            <input type="submit" value="Iniciar Sesión" />
        </form>
        <div id="mensaje"></div>
    </div>
    <footer>
        <p>&copy; 2024 TecnoHub. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
