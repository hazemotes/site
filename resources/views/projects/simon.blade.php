<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Simon</title>
    <script type="text/javascript" src="{{ asset('js/jquery-3.2.1.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('js/app.js')}}"></script>
</head>

<style>
    @import url('https://fonts.googleapis.com/css?family=Quicksand');

    body {
        font-family: 'Quicksand', sans-serif;
    }

    #face {
        height: 100%;
    }

    #leftEye {
        height: 30px;
        width: 30px;
        background-color: black;
        margin-top: 100px;
        margin-right: 150px;
        border-radius: 50%;
        display: inline-block;
        float: right;
    }

    #rightEye {
        height: 30px;
        width: 30px;
        background-color: black;
        margin-top: 100px;
        margin-left: 150px;
        border-radius: 50%;
        display: inline-block;
    }

    #frown {
        /*   display: inline-block; */
        /*   background-color: black; */
        border-radius: 50%/100px 100px 0 0;
        height: 150px;
        width: 200px;
        margin-top: 200px;
        margin-left: 145px;
        border: solid 5px;
        border-color: #000 transparent transparent transparent;
    }

    #menu {
        float: right;
        margin-right: 150px;
        display: block;
    }

    li {
        list-style: none;
        border: 1px solid black;
        padding: 10px;
        display: inline-block;
        margin-right: 15px;
        width: 60px;
        text-align: center;
    }

    #colors {
        background-color: black;
        height: 500px;
    }

    #start {
        line-height: 500px;
        font-size: 2em;
        margin: auto;
        display: block;
        text-align: center;
    }

    #board {
        border-radius: 50%;
        width: 500px;
        height: 500px;
        overflow: hidden;
        margin: auto;
        border: 1px solid black;
    }

    #green {
        background-color: #00FF48;
        width: 50%;
        height: 50%;
        display: inline-block;
        float: left;
    }

    #red {
        background-color: #FF0000;
        width: 50%;
        height: 50%;
        float: right;
        display: inline-block;
    }

    #yellow {
        width: 50%;
        height: 50%;
        float: left;
        background-color: #FFC800;
        display: inline-block;
    }

    #blue {
        width: 50%;
        height: 50%;
        background-color: #0018FF;
        display: inline-block;
        float: right;
    }

    .highlight {
        color: white;
        background-color: black;
    }

    h1 {
        text-align: center;
        color: transparent;
        background-image: linear-gradient(270deg, #000000, #00FF48, #FF0000, #FFC800, #0018FF, #000000);
        background-clip: text;
        -webkit-background-clip: text;
        background-size: 200% 200%;
        animation: text 100s linear infinite alternate;
    }

    @keyframes text {
        0% {
            background-position: 0%
        }
        100% {
            background-position: 100%
        }
    }

    a:hover {
        text-decoration: none;
    }


</style>

<body>
<h1>Simon</h1>
<div id="board">
    <span id="start">Start</span>
    <div id="face">
        <div id="leftEye" class="eye"></div>
        <div id="rightEye"class="eye"></div>
        <div id="frown"></div>
    </div>
    <div id="colors">
        <div id="green"></div>
        <div id="red"></div>
        <div id="yellow"></div>
        <div id="blue"></div>
    </div>
</div>

<div id="menu">
    <ul>
        <li id="reset">Reset</li>
        <li id="sound">Sound</li>
    </ul>
</div>
<a href="/projects" class="blinkLink">Back</a>
</body>
</html>

<script>
    $(document).ready(function () {
        var started = false;
        var sound = true;
        var guesses = [];

        $("#colors").hide();
        $("#face").hide();

        $("#board").click(function () {
            if (!started) {
                $("#start").hide();
                $("#colors").show();
                round();
                started = true;
                $(this).css("border", "1px solid white");
            }
        });

        var sequence = [];

        function playSound(freq) {
            if (sound) {
                var context = new (window.AudioContext || window.webkitAudioContext)();
                var osc = context.createOscillator(); // instantiate an oscillator
                osc.type = 'square'; // sawtooth
                osc.frequency.value = freq;
                osc.connect(context.destination); // connect it to the destination
                osc.start(); // start the oscillator
                osc.stop(context.currentTime + .2); // stop .2 seconds after the current time
            }
        }


        function round() {
            var current = rand();
            sequence.push(current);
            var pause;

            if (sequence.length > 10) {
                pause = 800;
            } else if (sequence.length > 4) {
                pause = 1100
            } else {
                pause = 1500;
            }

            var tempSequence = sequence.slice();
            lightMe(tempSequence);

            function lightMe(n) {
                setTimeout(function () {
                    if (n.length > 0) {
                        var c = n.shift();

                        light(c);
                        lightMe(n);
                    }
                }, pause);
            }

        }

        function loss() {
            $("#colors").hide();
            $("#face").show();
            $("#board").css("border", "1px solid black");

            if (sound) {
                setTimeout(function () {
                    var context = new (window.AudioContext || window.webkitAudioContext)();
                    var osc = context.createOscillator(); // instantiate an oscillator
                    osc.type = 'sawtooth'; // sawtooth
                    osc.frequency.value = 100;
                    osc.connect(context.destination); // connect it to the destination
                    osc.start(); // start the oscillator
                    osc.stop(context.currentTime + .5); // stop 2 seconds after the current time
                }, 300);
            }
        }

        function check() {

            if (guesses.length > sequence.length) {
                loss();
                return 0;
            }
            for (var i = 0; i < sequence.length; i++) {
                if (guesses[i]) {
                    if (guesses[i] != sequence[i]) {
                        loss();
                        return 0;
                    }
                } else {
                    return 0;
                }
            }

            guesses = [];
            round();
        }

        $("#green").mousedown(function () {
            light(1);
            guesses.push(1);
            check();
        });

        $("#red").mousedown(function () {
            light(2);
            guesses.push(2);
            check();
        });

        $("#yellow").mousedown(function () {
            light(3);
            guesses.push(3);
            check();
        });

        $("#blue").mousedown(function () {
            light(4);
            guesses.push(4);
            check();
        });

        function rand() {
            return Math.floor(Math.random() * 4) + 1;
        }

        function light(num) {
            if (num == 4) {
                $("#blue").css("background-color", "#99a3ff");
                playSound(390);
                setTimeout(function () {
                    $("#blue").css("background-color", "#0018FF");
                }, 300);
            }

            if (num == 2) {
                playSound(450);
                $("#red").css("background-color", "#ff9999");
                setTimeout(function () {
                    $("#red").css("background-color", "#FF0000");
                }, 300);
            }

            if (num == 3) {
                playSound(420);
                $("#yellow").css("background-color", "#ffe999");
                setTimeout(function () {
                    $("#yellow").css("background-color", "#FFC800");
                }, 300);
            }

            if (num == 1) {
                playSound(480);
                $("#green").css("background-color", "#99ffb6");
                setTimeout(function () {
                    $("#green").css("background-color", "#00FF48");
                }, 300);
            }
        }

        $("#reset").on("click", function () {
            $("#colors").show();
            $("#board").css("border", "1px solid white");
            $("#face").hide();
            $("#start").hide();
            sequence = [];
            guesses = [];
            round();

            $("#reset").addClass("highlight");

            setTimeout(function () {
                $("#reset").removeClass("highlight");
            }, 300);
        });

        $("#sound").on("click", function () {

            if(sound) {
                sound = false;
                $(this).addClass("highlight");
                $(this).html("Off");
            } else {
                sound = true;
                $(this).html("Sound");
                $(this).removeClass("highlight");

            }
        });

        var colorArr = ["#00FF48", "#FFC800", "#FF0000", "#0018FF"];
        colorShift();

        function colorShift(){
            setTimeout(function(){
                var c = colorArr.shift();
                $(".eye").css("background-color", c);
                $("#frown").css("border-color",c + " transparent transparent transparent" );
                colorArr.push(c);
                colorShift();
            }, 1000);
        }

    });

</script>