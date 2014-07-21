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
    </head>
    <body>
        <div class="row">
            <header>
                <h1>musique</h1>
            </header>
        </div>
        <div class="row">
            <div class="large-8 medium-8 columns">
                <audio id="musique1" src="http://www.hulkshare.com/dl/sk5mrksy19ib/hulkshare.mp3?d=1" preload="none"></audio>
                <div style="float: left;padding-right: 15px">
                    <span id="titre">Titre </span> / <span id="time">-- : --</span>
                </div>
                <div style="float: left">
                    <div id="progressTotal" style="width: 200px;background-color: darkgray; height: 20px"></div>
                    <div id="progressData" style="width: 0px;background-color: blue; height: 20px; margin-top: -20px"></div>
                    <div id="progress" style="width: 0px;background-color: red; height: 20px; margin-top: -20px"></div>
                </div>
                <div style="clear: both"></div>
                <br>
                <div style="float: left">
                    <button id="play" style="width: 110px">play</button>
                    <button id="stop">stop</button>
                    <span id="volumeValue" style="padding-left: 15px">50</span>
                </div>
                <div id="volume" style="width: 100px; float: left;margin: 17px 0 0 10px"></div>
                <br>
                <div id="progressB" style="clear: both;background-color: red;height: 20px; width: 1px"></div>
                <ul>
                    <li><a id="chargementLP">chargement LP</a></li>
                    <li><a id="chargementCL">chargement CLASSIC</a></li>
                </ul>
            </div>
        </div>
        <?php
        // $contents = file_get_contents('1.mp3');
        // $base64 = base64_encode( $contents );
        // $audio = 'data:audio/mp3;base64,' . $base64;
        // $contents = file_get_contents('2.mp3');
        // $base64 = base64_encode( $contents );
        // $audio2 = 'data:audio/mp3;base64,' . $base64;
        ?>
        <script type="text/javascript">

            //@TODO faire un systeme de playlist
            //@TODO durée de la chanson dans la base
            //@TODO boutton effacer les data hors ligne
            //@TODO boutton charger une data hors ligne

        $( document ).ready(function() {

            /* --- DATA --- */

            var db;
            var musique = new Audio();
            var chargment = false;
            var duration;

            /* --- DATA --- */



            /* --- MUSIQUE DATA --- */

            var dataCL = new Object();
            dataCL.src = "2.mp3";
            dataCL.duration = 171;
            dataCL.title = "Classic";

            var dataLP = new Object();
            dataLP.src = "1.mp3";
            dataLP.duration = 481;
            dataLP.title = "Linkin Park";

            /*var audio = "<?php //echo $audio ?>";
            var audio2 = "<?php //echo $audio2 ?>";*/

            /* --- FIN MUSIQUE DATA --- */



            /* --- CHANGEMENT MUSIQUE --- */

            $("#chargementLP").click(function(){setSrc(dataLP)});
            $("#chargementCL").click(function(){setSrc(dataCL)});

            function setSrc(data) {
                musique.src = data.src;
                duration = data.duration;
                document.querySelector('#progressData').style.width = '0px' ;
                document.querySelector('#progress').style.width = '0px' ;
                document.querySelector('#play').innerHTML = 'Play';
                document.querySelector('#time').innerHTML = '0:00 : ' + formatTime(duration);
                document.querySelector('#titre').innerHTML = data.title;
            }

            /* --- FIN CHANGEMENT MUSIQUE --- */



            /* --- AUDIO EVENT --- */

            musique.addEventListener('timeupdate',function(){
                document.querySelector('#time').innerHTML = formatTime(musique.currentTime) + ' : ' + formatTime(duration) ;
                if (musique.currentTime == 0) {
                    document.querySelector('#progress').style.width = musique.currentTime ;
                } else {
                    var purcent = round2((musique.currentTime / musique.duration)*100);
                    if (purcent == 0){
                        var avancement = 0;
                    } else {
                        var tailleTotale = document.querySelector('#progressTotal').offsetWidth;
                        var avancement = round2((purcent / 100) * tailleTotale) + 'px';
                    }
                    document.querySelector('#progress').style.width = avancement ;
                }
            });

            musique.addEventListener('progress',function(){
                var duration = musique.duration;
                if (musique.buffered.length > 0) {
                    var end = musique.buffered.end(0);
                    var progressValue = (end/duration)*100;
                    var tailleTotale = document.querySelector('#progressTotal').offsetWidth;
                    var avancement = round2((progressValue / 100) * tailleTotale) + 'px';
                    document.querySelector('#progressData').style.width = avancement ;
                } else {
                    document.querySelector('#progressData').style.width = '1px' ;
                }
            });

            /* --- FIN AUDIO EVENT --- */



            /* --- AUDIO CONTROLER --- */

            function playPause(){
                if (musique.getAttribute('src') == null) return;
                var bouton  = document.querySelector('#play');
                if (musique.paused){
                    musique.play();
                    bouton.innerHTML = 'Pause';
                } else {
                    musique.pause();
                    bouton.innerHTML = 'Play';
                }
            }

            function stop(){
                if (musique.getAttribute('src') == null) return;
                musique.currentTime = 0;
                musique.pause();
                document.querySelector('#play').innerHTML = 'Play';
                document.querySelector('#progressB').style.width = '1px' ;
            }

            function setVolume(value){
                $("#volume").slider("option", "value", value*100);
                musique.volume = value;
                document.querySelector('#volumeValue').innerHTML = Math.round(value*100);

            }

            function selectCurrentTime(event){
                var leftProgressData = getPosition(document.querySelector('#progressTotal')).x;
                var tailleTotale = document.querySelector('#progressTotal').offsetWidth;
                var leftMouse = event.clientX;
                var purcent = ((leftMouse - leftProgressData) / tailleTotale) * 100;
                var time = (musique.duration * purcent)/100;
                musique.currentTime = time;
            }

            $('#progress').click(function(){
                selectCurrentTime(event)
            });

            $('#progressData').click(function(){
                selectCurrentTime(event)
            });

            $("#play").on('click',playPause);


            $("#stop").on('click',stop);

            $("#volume").slider({
                slide: function(event, ui) {
                    var volume = ui.value/100;
                    setVolume(volume);
                },
                value: 50
            });

            /* --- FIN AUDIO CONTROLER --- */



            /* --- UTILS FUNCTIONS --- */

            function getPosition(element){
                var top = 0, left = 0;
                while (element) {
                    left   += element.offsetLeft;
                    top    += element.offsetTop;
                    element = element.offsetParent;
                }
                return { x: left, y: top };
            }

            function round2(nb) {
                return (Math.round(nb*100))/100;
            }

            function formatTime(time) {
                var hours = Math.floor(time / 3600);
                var mins  = Math.floor((time % 3600) / 60);
                var secs  = Math.floor(time % 60);
                if (secs < 10) {
                    secs = "0" + secs;
                }
                if (hours) {
                    if (mins < 10) {
                        mins = "0" + mins;
                    }
                    return hours + ":" + mins + ":" + secs; // hh:mm:ss
                } else {
                    return mins + ":" + secs; // mm:ss
                }
            }

            /* --- FIN UTILS FUNCTIONS --- */



            /* --- INDEXED DB --- */

            // window.indexedDB = window.indexedDB || window.mozIndexedDB || window.webkitIndexedDB || window.msIndexedDB;
            // if (!window.indexedDB) {
            //     window.alert("Your browser doesn't support a stable version of IndexedDB. Such and such feature will not be available.");
            // }
            // var request = window.indexedDB.deleteDatabase("MyTestDatabase");


            // const customerData = [
            //     { ssn: "1", name: "LP", data: audio },
            //     { ssn: "2", name: "CLASSIC", data: audio2 }
            // ];

            // window.indexedDB = window.indexedDB || window.mozIndexedDB || window.webkitIndexedDB || window.msIndexedDB;
            // if (!window.indexedDB) {
            //     window.alert("Your browser doesn't support a stable version of IndexedDB. Such and such feature will not be available.");
            // }

            // var request = window.indexedDB.open("MyTestDatabase",1);

            // request.onerror = function(event) {
            //     alert('erreur ouvert base');
            // };

            // request.onsuccess = function (evt) {
            //     db = this.result;
            //     chargment = true;
            //     console.debug('ok chargement ');
            // };

            // request.onupgradeneeded = function(event) {
            //     var db = event.target.result;
            //     var objectStore = db.createObjectStore("musique", { keyPath: "ssn" });
            //     objectStore.createIndex("name", "name", { unique: true });
            //     objectStore.createIndex("data", "data", { unique: true });
            //     objectStore.transaction.oncomplete = function(event) {
            //         var customerObjectStore = db.transaction("musique", "readwrite").objectStore("musique");
            //         for (var i in customerData) {
            //             customerObjectStore.add(customerData[i]);
            //         }
            //     }
            // };

            // $("#chargementLP").click(function(){getMusique('1')});
            // $("#chargementCL").click(function(){getMusique('2')});

            // function getMusique($id){
            //     console.debug();
            //     if (!chargment) return;
            //     var transaction = db.transaction("musique");
            //     var objectStore = transaction.objectStore("musique");
            //     var request = objectStore.get($id);
            //     request.onerror = function(event) {
            //         alert('no ok');
            //     };
            //     request.onsuccess = function(event) {
            //         musique.pause();
            //         document.querySelector('#titre').innerHTML = request.result.name;
            //         musique.setAttribute('src',request.result.data);
            //         document.querySelector('#play').innerHTML = 'Play';
            //     };
            // }
            //
            // $("#chargementBase").click(function() {
            //     var objectStore  = db.transaction("musique", "readwrite").objectStore("musique");
            //     var request = objectStore.get("1");
            //     request.onerror = function(event) {
            //         alert('no ok');
            //     };
            //     request.onsuccess = function(event) {
            //         var data = request.result;
            //         data.data = "<?php echo $audio ?>";
            //         var requestUpdate = objectStore.put(data);
            //         requestUpdate.onerror = function(event) {
            //             alert('no ok');
            //         };
            //         requestUpdate.onsuccess = function(event) {
            //             alert('ok');
            //         };
            //     };
            // });

            /* --- FIN INDEXED DB --- */
        });
        </script>
        <script>
            $(document).foundation();
        </script>
    </body>
</html>
