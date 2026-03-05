<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="icon" type="image/x-icon" href="https://directorioasambleasvzla.com/iconos/icon2-8.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Derechos de Autor - Directorio de Asambleas</title>

    <!-- Fuente Sansation (si la tienes local omite esto) -->
    <link href="https://fonts.cdnfonts.com/css/sansation" rel="stylesheet">

    <style>
        /* Estilos generales */
        body {
            font-family: 'Sansation', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            padding: 20px;
            position: relative;
            text-align: center;
            color: #192E2F;
            z-index: 1;
        }

        body::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('/iconos/Fonfo_Mapa_Color.png');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: top;
            opacity: 0.7;
            z-index: -1;
        }

        .contenido {
            background: transparent;
            padding: 30px;
            border-radius: 15px;
            max-width: 800px;
        }

        h1, h2 {
            margin-top: 0;
            color: #192E2F;
        }

        p, li {
            color: #192E2F;
        }

        ul {
            list-style: none;
            padding-left: 0;
        }

        ul li::before {
            content: "✔️ ";
            margin-right: 5px;
        }

        footer {
            margin-top: 2em;
            font-size: 0.9em;
            color: #192E2F;
        }

        /* Botón de retorno */
        .return-button {
            position: absolute;
            top: 20px;
            left: 30px;
            width: 30px;
            height: 30px;
            background-image: url('https://directorioasambleasvzla.com/iconos/Retorno.png'); /* Asegúrate que esté en esa ruta */
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            border: none;
            cursor: pointer;
            z-index: 1003;
            background-color: transparent;
        }

        /* Responsive */
        @media (max-width: 768px) {
            body {
                background-image: url('Fondo_Mapa_Tlf.png');
                background-size: cover;
                background-repeat: no-repeat;
                background-position: center;
                flex-direction: column;
                padding: 0;
                height: auto;
                overflow-y: auto;
            }

            .contenido {
                width: 90%;
                max-width: 100%;
                margin: 20px 0;
                padding: 15px;
                border-radius: 10px;
                font-size: 14px;
            }

            h1 {
                font-size: 20px;
            }

            h2 {
                font-size: 16px;
            }

            p, li {
                font-size: 14px;
                line-height: 1.4em;
            }

            ul {
                padding-left: 1em;
            }

            .return-button {
                top: 10px;
                left: 10px;
                width: 24px;
                height: 24px;
            }
        }

    </style>
</head>

<body>
    <!-- Botón de retorno -->
    <button onclick="location.href='index.php'" class="return-button" title="Volver"></button>

    <div class="contenido">
        <h1>Acerca de esta aplicación</h1>
        <p>Esta aplicación web fue desarrollada por <strong>John Malavé</strong> como parte del trabajo de grado para optar al título de <strong>Licenciado en Computación</strong> en la <strong>Universidad del Zulia</strong>, año <strong>2025</strong>.</p>

        <p>El diseño visual y la estructura gráfica de la página web fueron realizados por <strong>Alexa Malavé</strong>, colaborando para ofrecer una interfaz clara, atractiva y funcional.</p>

        <p>Su objetivo es ofrecer una versión digital del <strong>Directorio de Asambleas de Hermanos en Venezuela</strong>, con funciones como:</p>
        <ul>
            <li>Acceso a direcciones y horarios de reuniones</li>
            <li>Cartelera de eventos</li>
            <li>Salas de chat</li>
            <li>Área de lectura y estudios bíblicos</li>
        </ul>

        <p>La aplicación está orientada a facilitar la comunicación y organización dentro del grupo de iglesias cristianas, respetando la privacidad y datos personales de sus miembros.</p>

        <h2>Política de Privacidad</h2>
        <p>Esta aplicación almacena únicamente los datos necesarios para su funcionamiento, como nombres de usuario, mensajes de chat y horarios de eventos. Ninguna información personal es compartida con terceros ni usada con fines comerciales.</p>

        <p>Los datos se manejan de forma segura y son accesibles solo para los fines establecidos en el uso de esta plataforma. Al utilizar la aplicación, usted acepta estas condiciones.</p>

        <h2>Agradecimientos</h2>
        <p>Se agradece especialmente a <strong>Andrés Padrón</strong> por su valiosa colaboración al facilitar la base de datos del <strong>Himnario</strong>, contribuyendo significativamente al desarrollo y funcionalidad de esta aplicación.</p>

        <footer>
            © 2025 John Malavé. Todos los derechos reservados.<br>
            Prohibida su reproducción total o parcial sin autorización previa.<br>
            Versión 1.0 | Última actualización: november de 2025
        </footer>
    </div>



</body>
</html>
