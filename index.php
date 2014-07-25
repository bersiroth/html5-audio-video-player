<!doctype html>
<html class="no-js" lang="en">
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <title>Bersi musique</title>
        <link rel="stylesheet" href="css/foundation.css"/>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.11.0/themes/smoothness/jquery-ui.css">
        <script src="js/vendor/modernizr.js"></script>
        <script src="js/vendor/jquery.js"></script>
        <script src="js/foundation.min.js"></script>
        <script src="//code.jquery.com/ui/1.11.0/jquery-ui.js"></script>
        <script src="js/bersi-player/js/main.js"></script>
        <style type="text/css">
            [draggable] {
                -moz-user-select: none;
                -khtml-user-select: none;
                -webkit-user-select: none;
                user-select: none;
            }
        </style>
    </head>
    <body>
        <div id="bersi" class="audio"></div>

        <?php

            // $contents = file_get_contents('1.mp3');
            // $base64 = base64_encode( $contents );
            // $audio = 'data:audio/mp3;base64,' . $base64;
            // $contents = file_get_contents('2.mp3');
            // $base64 = base64_encode( $contents );
            // $audio2 = 'data:audio/mp3;base64,' . $base64;

            //@TODO faire un template responsive avec bootstrape et un template classique avec du css simple
            //@TODO passer le js dans un fichier apart
            //@TODO passer le css dans un fichier et refaire les class et id
            //@TODO bouton repete musique actuelle
            //@TODO bouton repete playlist
            //@TODO créer une base de donnée (musique)
            //@TODO créer une table user
            //@TODO créer une table musique
            //@TODO créer une table album
            //@TODO créer une table artiste
            //@TODO créer une table playlist
            //@TODO creer un systeme de conection
            //@TODO sauvegarder les playlist en base et hors ligne (localstorage + base)
            //@TODO supprimer une playlist (base + localstorage)
            //@TODO boutton charger une data hors ligne
            //@TODO boutton effacer les data hors ligne
            //@TODO upload musique
            //@TODO rendre le css responsive

        ?>

        <script>
            $(document).foundation();
        </script>
    </body>
</html>
