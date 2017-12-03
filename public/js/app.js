$(document).ready(function () {

    //blink link stuff
    var colors = [
        "#ff8d00",
        "#00f2f9",
        "#15f600",
        "#ff5454",
        "#f284f9",
        "#9a5aff",
        "#0061fc"];


    var interval;

    $('.blinkLink').hover(function () {

        var link = $(this);

        interval = window.setInterval(function () {
            var color = colors.shift();
            colors.push(color);
            link.css({
                "color": color
            });
        }, 130); //set the interval for image to change while hovered.

    }, function () {
        window.clearInterval(interval); //clear the interval on mouseOut.


            $(this).css({
                "color": "#333"
            });

    });

});