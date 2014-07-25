$( document ).ready(function() {

    /* --- DATA --- */

    var db;
    var musique = new Audio();
    var chargment = false;
    var duration;
    var playlist = new Array();
    var currentMusique;
    var config = new Array();

    /* --- FIN DATA --- */



    /* --- MUSIQUE DATA --- */

    var dataDE = new Object();
    dataDE.src = 'default';
    dataDE.duration = 0;
    dataDE.title = "Titre";

    /*var audio = "<?php //echo $audio ?>";
    var audio2 = "<?php //echo $audio2 ?>";*/

    /* --- FIN MUSIQUE DATA --- */



    /* --- LOAD CONFIG --- */

    $.get('js/bersi-player/config/global-config.xml', function(data) {
        config['template'] = $(data).find("template name").text();
        loadTemplate();
    });

    /* --- FIN LOAD CONFIG --- */



    /* --- LOAD TEMPLATE PLAYER --- */

    function loadTemplate() {
        $.get('js/bersi-player/template/' + config['template'] + '/html/template.html', function(data) {
            init(data);
        });
        $("head").append(
            $(document.createElement("link")).attr({rel:"stylesheet", type:"text/css", href:"js/bersi-player/template/" + config['template'] + "/css/main.css"})
        );
    }

    /* --- FIN LOAD TEMPLATE PLAYER --- */

    function init(data) {
        $("#bersi").html(data);
        $("#volume").slider({
            slide: function(event, ui) {
                setVolume(ui.value/100);
            },
            value: 50
        });
    }

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

    musique.addEventListener('ended',function(){
        if (currentMusique < playlist.length - 1){
            nextMusique();
        } else {
            setSrc(dataDE,-1);
            chargementPlaylist();
        }
    });

    /* --- FIN AUDIO EVENT --- */



    /* --- AUDIO CONTROLER --- */

    function playPause(){
        if (musique.getAttribute('src') == null || musique.getAttribute('src') == 'default') {
            if (playlist[0] != undefined) {
                setSrc(playlist[0],0);
            } else {
                return
            }
        }
        var bouton  = document.querySelector('#play');
        if (musique.paused){
            musique.play();
            bouton.innerHTML = 'Pause';
        } else {
            musique.pause();
            bouton.innerHTML = 'Play';
        }
        chargementPlaylist();
    }

    function stop(){
        if (musique.getAttribute('src') == null) return;
        musique.currentTime = 0;
        musique.pause();
        document.querySelector('#play').innerHTML = 'Play';
        document.querySelector('#progress').style.width = '1px' ;
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

    function setSrc(data, id) {
        currentMusique = id;
        musique.src = data.src;
        duration = data.duration;
        document.querySelector('#progressData').style.width = '0px' ;
        document.querySelector('#progress').style.width = '0px' ;
        document.querySelector('#play').innerHTML = 'Play';
        document.querySelector('#time').innerHTML = '0:00 : ' + formatTime(duration);
        document.querySelector('#titre').innerHTML = data.title;
    }

    function addPlaylist(data){
        musiqueData = new Object();
        musiqueData.src = data.id + '.mp3';
        musiqueData.duration = data.getAttribute('duration');
        musiqueData.title = data.title;
        playlist.push(musiqueData);
        chargementPlaylist();
    }

    function chargementPlaylist(){
        var htmlPlaylist = "";
        var style = "";
        for (var i = 0; i < playlist.length; i++) {
            style = (currentMusique == i) ? "color:blue" : "" ;
            htmlPlaylist = htmlPlaylist + "<li id='" + i + "' style='border-bottom:solid 1px black;margin-bottom:10px;" + style + "' draggable='true'><span>" + playlist[i].title + "</span> <span style='float: right'>X</span></li>";
        };
        document.querySelector('#playlist').innerHTML = htmlPlaylist;
    }

    function nextMusique(){
        if (currentMusique < playlist.length - 1) {
            setSrc(playlist[currentMusique + 1],  currentMusique + 1);
            playPause();
        }
    }


    $.fn.live = function(eventAction,functionAction) {
        $("body").on(eventAction,this.selector,functionAction);
    };

    $("#chargementMusique a").live('click',function(){
        addPlaylist(this);
    });

    $("#next").live('click',function(){
        nextMusique();
    });

    $("#previous").live('click',function(){
        if (currentMusique > 0) {
            setSrc(playlist[currentMusique - 1], currentMusique - 1);
            playPause();
        }
    });

    $("#playlist li span:last-child").live('click', function(){
        var key = this.parentNode.getAttribute('id');
        if(key != currentMusique || playlist.length == 1){
            playlist.splice(key, 1);
            if(playlist.length == 0) {
                setSrc(dataDE,-1);
            }
            if (key < currentMusique) {
                currentMusique = currentMusique - 1;
            }
            chargementPlaylist();
        }
    });

    $("#playlist li span:first-child").live('click', function(){
        var key = this.parentNode.getAttribute('id');
        setSrc(playlist[key], key);
        playPause();
    });

    $('#playlist li').live({
        dragstart: function(e) {
            $this = $(this);
            i = $this.index();
            $this.css('opacity', '0.5');
        },
        dragenter: function(e) {
            if (i !== $(this).index()) {
                $(this).animate({
                    fontSize: '20px'
                }, 'fast');
            }
        },
        dragleave: function() {
            if (i !== $(this).index()) {
                $(this).animate({
                    fontSize: '17px'
                }, 'fast');
            }
        },
        dragover: function(e) {
            e.preventDefault();
        },
        drop: function(e) {
            if (i !== $(this).index()) {
                $depart = $this.attr('id');
                $arrive = $(this).attr('id');
                $tmp = playlist[$arrive];
                playlist[$arrive] = playlist[$depart];
                playlist[$depart] = $tmp;
                if ($depart == currentMusique){
                    currentMusique = parseInt($arrive);
                } else if ($arrive == currentMusique) {
                    currentMusique = parseInt($depart);
                }
                chargementPlaylist();
            }
            $(this).animate({
                fontSize: '17px'
            }, 'fast');
        },
        dragend: function() {
            $(this).css('opacity', '1');
        }
    });

    $('#progress').live('click',function(){
        selectCurrentTime(event)
    });

    $('#progressData').live('click',function(){
        selectCurrentTime(event)
    });

    $("#play").live('click',playPause);

    $("#stop").live('click',stop);

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

    // window.indexedDB = window.indexedDB || window.mozIndexedDB || window.    webkitIndexedDB || window.msIndexedDB;
    // if (!window.indexedDB) {
    //     window.alert("Your browser doesn't support a stable version of IndexedDB.    Such and such feature will not be available.");
    // }
    // var request = window.indexedDB.deleteDatabase("MyTestDatabase");


    // const customerData = [
    //     { ssn: "1", name: "LP", data: audio },
    //     { ssn: "2", name: "CLASSIC", data: audio2 }
    // ];

    // window.indexedDB = window.indexedDB || window.mozIndexedDB || window.    webkitIndexedDB || window.msIndexedDB;
    // if (!window.indexedDB) {
    //     window.alert("Your browser doesn't support a stable version of IndexedDB.    Such and such feature will not be available.");
    // }

    // var request = window.indexedDB.open("MyTestDatabase",1);

    // request.onerror = function(event) {
    //     alert('erreur ouvert base');
    // };

    // request.onsuccess = function (evt) {
    //     db = this.result;
    //     chargment = true;
    // };

    // request.onupgradeneeded = function(event) {
    //     var db = event.target.result;
    //     var objectStore = db.createObjectStore("musique", { keyPath: "ssn" });
    //     objectStore.createIndex("name", "name", { unique: true });
    //     objectStore.createIndex("data", "data", { unique: true });
    //     objectStore.transaction.oncomplete = function(event) {
    //         var customerObjectStore = db.transaction("musique", "readwrite").    objectStore("musique");
    //         for (var i in customerData) {
    //             customerObjectStore.add(customerData[i]);
    //         }
    //     }
    // };

    // $("#chargementLP").click(function(){getMusique('1')});
    // $("#chargementCL").click(function(){getMusique('2')});

    // function getMusique($id){
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
    //     var objectStore  = db.transaction("musique", "readwrite").objectStore(   "musique");
    //     var request = objectStore.get("1");
    //     request.onerror = function(event) {
    //         alert('no ok');
    //     };
    //     request.onsuccess = function(event) {
    //         var data = request.result;
    //         data.data = "<?php //echo $audio ?>";
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