<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script type="application/javascript" src="js/jquery-1.11.1.min.js"></script>
        <script type="application/javascript" src="js/bootstrap.min.js"></script>
        <script type="application/javascript" src="js/app.js"></script>

        <title>Raspicam live</title>

        <!-- Bootstrap core CSS -->
        <link href="css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <a class="sr-only sr-only-focusable" href="#content">Skip to main content</a>

        <!-- Docs master nav -->
        <header class="navbar navbar-static-top bs-docs-nav" id="top" role="banner">
            <div class="container">
                <div class="navbar-header">
                    <a href="/" class="navbar-brand">RaspiCam Live</a>
                </div>
                <nav class="collapse navbar-collapse bs-navbar-collapse">
                    <ul class="nav navbar-nav">
                        <li<?php if ($_SERVER["REQUEST_URI"] == "/") echo ' class="active"'; ?>>
                            <a href="/">Capture</a>
                        </li>
                        <li<?php if ($_SERVER["REQUEST_URI"] == "/storage.php") echo ' class="active"'; ?>>
                            <a href="/?action=storage">Storage</a>
                        </li>
                        <li<?php if ($_SERVER["REQUEST_URI"] == "/settings.php") echo ' class="active"'; ?>>
                            <a href="/?action=settings">Settings</a>
                        </li>
                    </ul>
                    <!-- 
                                        <ul class="nav navbar-nav navbar-right">
                                            <li><a href="http://www.tmplab.org/wiki/index.php/Streaming_Video_With_RaspberryPi">RaspiCam Live Website</a></li>
                                        </ul>
                    -->
                </nav>
            </div>
        </header>
        <div id="notifications-wrapper">
            <div id="notifications"></div>
        </div>
        <div class="container bs-docs-container">

            <?= $__action_output ?>
        </div>
    </body>
</html>
