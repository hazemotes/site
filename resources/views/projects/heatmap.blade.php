<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script type="text/javascript" src="{{ asset('js/d3.v4.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('js/jquery-3.2.1.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('js/jquery-ui.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('js/app.js')}}"></script>
    <link href="{{ asset('css/jquery-ui.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet" type="text/css">

    <title>Course-Building Populations</title>

    <style>
        h1 {
            text-align: center;
            font-size: 1.5em;
            margin-bottom: 15px;
            margin-top: 15px;
        }

        #coursebuildingpop-chart {
            width: 1100px;
            margin: auto;
        }

        .tooltip {
            background: white;
            border: 1px solid black;
            border-radius: 5px;
            /*display: block;*/
            position: absolute;
            padding: 3px;
        }

        a, a:visited {
            color: black;
        }
    </style>
</head>
<body>
<h1>Campus Building Populations by Time</h1>
<div style="width: 80%; margin: auto; font-family: sans-serif">
    <p>Use the slider below. This was built for a project at work. Users could select a time and date and view the
        populations of campus buildings relative to each other. In production, the circle node locations were determined
        by GPS coordinates in the database and translated into coordinates on the image. (The data used on this site is
        randomly generated and
        does
        not reflect actual populations.) </p></div>
<br>
<div id="timePickerContainer" style="text-align: center;">
    <input type="text" id="datepicker" placeholder="Date" class="form-group" style="text-align: center"><br><br>
    <div id="slider" style="width: 50%; display: inline-block"></div>
    <br><span id="timeValue" style="font-size: 1.2em">8:00am</span>
    <br><br>
</div>
<div id="coursebuildingpop-chart">
</div>
<a href="/projects" class="blinkLink">Back</a>

<script>

    $(document).ready(function () {

        var data = []; //name, pop, x, y

        var coords = {
            "Fretwell": {
                "x": 890,
                "y": 432
            },

            "Atkins": {
                "x": 718,
                "y": 455
            },
            "Woodward": {
                "x": 555,
                "y": 350
            },
            "Cone": {
                "x": 680,
                "y": 480
            },
            "Friday": {
                "x": 840,
                "y": 415
            },
            "CHHS": {
                "x": 668,
                "y": 350
            },
            "Duke": {
                "x": 270,
                "y": 68
            },
            "COED": {
                "x": 633,
                "y": 340
            },
            "Colvard": {
                "x": 748,
                "y": 510
            },
            "Burson": {
                "x": 718,
                "y": 350
            }

        };

        var buildings = ["Fretwell", "Atkins", "Woodward", "Cone", "Friday", "CHHS", "Duke", "COED", "Colvard", "Burson"];

        for (var i = 0; i < 10; i++) {

            var obj = {};

            obj.name = buildings[i];
            obj.pop = Math.floor(Math.random() * (1350 - 1)) + 1;
            obj.x = coords[obj.name]["x"];
            obj.y = coords[obj.name]["y"];

            data.push(obj);
        }
        var time = 8;
        $("#datepicker").datepicker({
            dateFormat: "yy-mm-dd",
            minDate: "2017-01-09"
        }).datepicker("option", "disabled", true);
        $("#datepicker").datepicker("setDate", new Date()); //Default to today
        $("#slider").slider({
            animate: "fast",
            max: 17,
            min: 8,
            change: function (event, ui) {
                var value = $("#slider").slider("value");
                time = value;
                if (value > 12) {
                    value = "0" + (value % 12) + ":00pm"
                } else if (value === 12) {
                    value = "12:00pm"
                } else {
                    value = value + ":00am";
                }
                $("#timeValue").text(value);
                draw(data);
            }
        });
        var width = 1100;
        var height = 660;
        draw(data);

        function draw(data) {
            $("#chartSVG").remove();
            for (var i = 0; i < data.length; i++) {
                data[i].pop = Math.floor(Math.random() * (1350 - 1)) + 1;
            }
            var svg = d3.select("#coursebuildingpop-chart")
                .append("svg")
                .attr("id", "chartSVG")
                .attr("width", width)
                .attr("height", height);

            svg.append('image')
                .attr('xlink:href', '/images/coop-campus-map.jpg')
                .attr("width", width)
                .attr("height", height)
                .attr("x", 0)
                .attr("y", 0)
                .style({
                    "position": "relative"
                });
            var circles = svg.selectAll("circle")
                .data(data)
                .enter()
                .append("circle");

            var circleAttributes = circles
                .attr("cx", function (d) { //coordinates
                    return d.x;
                })
                .attr("cy", function (d) {
                    return d.y;
                })
                .attr("r", function (d) {
                    if (d.pop >= 1250) {
                        return 35;
                    } else if (d.pop >= 1001) {
                        return 30;
                    } else if (d.pop >= 751) {
                        return 25;
                    } else if (d.pop >= 501) {
                        return 20;
                    } else if (d.pop >= 251) {
                        return 15;
                    } else if (d.pop >= 51) {
                        return 10;
                    } else if (d.pop >= 1) {
                        return 5;
                    }
                })
                .attr('opacity', .7)
                .attr('id', function (d) {
                    return d.name;
                })
                .style("fill", function (d) {
                    if (d.pop >= 1250) {
                        return "#de0000";
                    } else if (d.pop >= 1001) {
                        return "#fb3500";
                    } else if (d.pop >= 751) {
                        return "#fc6700";
                    } else if (d.pop >= 501) {
                        return "#fd9800";
                    } else if (d.pop >= 251) {
                        return "#f0a404";
                    } else if (d.pop >= 51) {
                        return "#feca00";
                    } else {
                        return "#fffb00";
                    }
                })
                .style({
                    "z-index": "100"
                })
                .on("mouseover", function (d) {
                    div.transition()
                        .duration(200)
                        .style("opacity", .9);
                    div.html(d.name + ": " + d.pop)
                        .style("left", (d3.event.pageX) + "px")
                        .style("top", (d3.event.pageY - 28) + "px");
                })
                .on("mouseout", function (d) {
                    div.transition()
                        .duration(500)
                        .style("opacity", 0);
                });
        }

        var div = d3.select("body").append("div")
            .attr("class", "tooltip")
            .style("opacity", 0);
    });
</script>
</body>
</html>