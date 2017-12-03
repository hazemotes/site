<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CRUD Demo</title>
    <script type="text/javascript" src="{{ asset('js/jquery-3.2.1.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('js/app.js')}}"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        @import url('https://fonts.googleapis.com/css?family=Quicksand');

        body {
            font-family: 'Quicksand', sans-serif;
            color: #333;
            background-color: #ecebf3;
        }

        h2 {
            text-align: center;
        }

        #no-results {
            color: red;
        }

        li {
            line-height: 1.5em;
        }

        a {
            color: #333;
        }

        a:hover {
            text-decoration: none;
        }

        .wrapper {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            grid-gap: 10px;
            grid-auto-rows: minmax(150px, auto);
        }

        .search {
            grid-column: 1;
            grid-row: 1 / 4;
        }

        .list {
            grid-column: 2;
            grid-row: 1 / 4;
        }

        .info {
            grid-column: 3;
            grid-row: 1 / 4;
        }

        .node {
            background-color: white; /*rgba(201, 221, 255, .3); */
            border-radius: 5px;
            padding: 5px;
            border: 2px solid #6d7275;
        }

        .section-title {
            text-align: center;
        }

        .del-row {
            display: inline-block;
            float: right;
            margin-right: 10px;
        }

        .del-row:hover {
            color: red;
            cursor: default;
        }

        label {
            font-weight: bold;
            text-align: right;
            float: left;
            width: 100px;
            margin-right: 10px;
        }

        .movie-details {
            line-height: 2em;
        }

        .movie-detail {
            display: inline-block;
        }

        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgb(0, 0, 0); /* Fallback color */
            background-color: rgba(0, 0, 0, 0.4); /* Black w/ opacity */
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto; /* 15% from the top and centered */
            padding: 20px;
            border: 1px solid #888;
            width: 50%; /* Could be more or less, depending on screen size */
        }

        /* The Close Button */
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .login-input {

        }

        .random-users ul li {
            list-style-type: none;
            display: inline;
            margin-left: 20px;
            text-decoration: underline;
        }

        .random-users ul li:hover {
            text-decoration: none;
            cursor: default;
        }

    </style>
</head>
<body>

<h2>Favorite Movies</h2>

<p>Search for your favorite movies, add them to your list, and save them for others to view!</p>
<p style="text-align: right; font-weight: bold;">
    <span id="login-info" style="display: none">Clay - 4585</span>
    <span id="login-form">
    <input placeholder="Name" class="login-input" style="width: 75px" id="name-input">
    <input placeholder="Key" class="login-input" style="width: 50px" id="key-input">
    <a href="#/" style="color: #394584;" id="login-button">Log in</a>
    </span>
</p>

<div class="wrapper">
    <div class="search node">
        <div class="list-name section-title">Search</div>
        <br>
        <div id="search-bar"><input id="search" placeholder="Type a movie title and press enter"></div>
        <p id="no-results"></p>

        <ul id="search-results">

        </ul>
    </div>
    <div class="list node">
        <div class="list-name section-title">My List</div>

        <ol class="current-list">
        </ol>
        <p style="font-weight: bold;"><a href="#/" id="save">Save</a></p>

    </div>
    <div class="info node">
        <div class="section-title movie-name">Movie Info</div>
        <br>
        <div class="movie-details">

            <label>Title: </label>
            <div class="movie-detail" id="title"></div>
            <br>
            <label>Director: </label>
            <div class="movie-detail" id="director"></div>
            <br>
            <label>Release: </label>
            <div class="movie-detail" id="release"></div>
            <br>
            <label>Overview: </label>
            <div class="movie-detail" id="overview"></div>
            <br>
            <label for="comments">Comments: </label>
            <div class="movie-detail"><input id="comments"></div>
            <br>

        </div>

    </div>
</div>
<div style="text-align: right;"><a href="https://www.themoviedb.org/?language=en">Movie API</a></div>
<div class="random-users">
    <p>View others' lists:</p>
    <ul>
        @foreach($randos as $rando)
            <li data-id="{{$rando->id}}" class="rando">{{$rando->name}}</li>
        @endforeach
    </ul>
</div>


<div id="modal" class="modal">

    <!-- Modal content -->
    <div class="modal-content">
        <span class="close">&times;</span>
        <div class="register-modal-text">
            <p>
                To save your movies, please enter a name. You'll be given a passphrase to access your list later.
            </p>
            <div id="register-form">
                Name: <input title="name" id="register-name">
                <button id="register-button" style="display: inline;">Ok</button>
            </div>
            <p id="register-warning" style="color: red"></p>
            <p id="key-info" style="color: blue"></p>

        </div>
    </div>

</div>
<script>

    $(document).ready(function () {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var currentList = [];
        var user = null;
        var comments = {};
        var activeID;
        var titles = {};

        $(document).keypress(function (e) {
            if (e.which === 13 && $("#search").is(":focus")) {
                clearResults();
                search();
            }

            if (e.which === 13 && $(".login-input").is(":focus")) {
                $("#login-button").trigger("click");
            }

            if (e.which === 13 && $("#register-name").is(":focus")) {
                $("#register-button").trigger("click");
            }
        });

        var modal = $("#modal");

        function search() {

            var term = $("#search").val().replace(" ", "+");

            var url = "https://api.themoviedb.org/3/search/movie?api_key=63b69e61fd51dccdc80fd5a4c3480c43&query=" + term;

            $.ajax({
                url: url,
                data: null,
                dataType: 'jsonp',
                success: function (data) {
                    console.log(data);
                    var numResults = data.total_results;
                    if (numResults === 0) {
                        $("#no-results").html("No results found!");
                    } else {

                        for (var i = 0; i < numResults; i++) {

                            var result = data.results[i];

                            if (result) {

                                $("#search-results").append(
                                    $("<li>").attr("data-id", result.id).attr("data-title", result.title).addClass("add-movie").append("<a href='#'>" + result.title +
                                        " (" + result.release_date + ")</a>"));
                            }
                        }
                    }
                }

            });
        }

        $(document).on("click", ".add-movie", function () {
            var title = $(this).data('title');
            var id = $(this).data('id');

            $(".current-list").append("<li data-id='" + id + "'><a href='#' class='list-movie'>" + title + "</a><div class='del-row'>X</div></li>");

            currentList.push(id);
            titles[id] = title;
            loadDetails(id);

        });

        $(document).on("click", ".del-row", function () {
            var id = $(this).parent().data("id");
            $(this).parent().remove();

            var index = currentList.indexOf(id);
            currentList.splice(index, 1);
        });

        $(document).on("click", ".list-movie", function () {

            var id = $(this).parent().data("id");
            loadDetails(id);
        });

        $(document).on("click", "#save", save);
        $(document).on("click", "#register-button", register);

        $(document).on("click", "#login-button", login);

        $(document).on("click", ".close", function () {
            modal.css("display", "none");
        });

        $(document).on("input", "#comments", function () {

            comments[activeID] = $(this).val();
        });

        $(document).on("click", ".rando", function () {

            var id = $(this).data("id");
            loadList(id);
        });

        function clearResults() {
            $("#no-results").html("");
            $("#search-results").html("");
        }

        function loadDetails(id) {

            activeID = id;

            console.log(comments);
            $("#comments").prop("disabled", false);
            $("#comments").val(comments[id]);

            var url = "https://api.themoviedb.org/3/movie/" + id + "?api_key=63b69e61fd51dccdc80fd5a4c3480c43";
            var url2 = "https://api.themoviedb.org/3/movie/" + id + "/credits?api_key=63b69e61fd51dccdc80fd5a4c3480c43";

            $.ajax({
                url: url,
                data: null,
                dataType: 'jsonp',
                success: function (data) {

                    var title = data.title;
                    var overview = data.overview;
                    var release = data.release_date;

                    $("#title").html(title);
                    $("#overview").html(overview);
                    $("#release").html(release);
                }
            });

            $.ajax({
                url: url2,
                data: null,
                dataType: 'jsonp',
                success: function (data) {

                    var crew = data.crew;

                    for (var i = 0; i < crew.length; i++) {

                        if (crew[i].job === "Director") {
                            $("#director").html(crew[i].name);
                            break;
                        }
                    }

                }
            });
        }

        function save() {

            if (user) {
                $.ajax({
                    url: '/projects/movies/save',
                    data: {
                        'key': user.key,
                        'movies': currentList,
                        'titles': titles,
                        'comments': comments
                    },
                    method: 'POST',
                    success: function (data) {
                        $("#save").html("done!");

                        setTimeout(function () {
                            $("#save").html("save");
                        }, 2000);
                    }
                });


            } else {
                modal.css("display", "block");
            }
        }

        function register() {

            var name = $("#register-name").val();

            $.ajax({
                url: '/projects/movies/register',
                data: {'name': name},
                method: 'POST',
                success: function (data) {

                    var status = data.status;

                    if (status === "success") {

                        user = {
                            username: name,
                            key: data.key
                        };

                        $("#key-info").html("Your key is " + data.key);
                        $("#register-form").hide();
                        hideLogin();

                    } else {
                        $("#register-warning").html(data.message);
                    }
                }
            });

        }

        function login() {

            var name = $("#name-input").val();
            var key = $("#key-input").val();

            $.ajax({
                url: '/projects/movies/login',
                data: {"name": name, "key": key},
                method: 'POST',
                success: function (data) {

                    var status = data.status;

                    if (status === "success") {

                        user = {
                            username: name,
                            key: key,
                            id: data.id
                        };

                        hideLogin();
                        loadList(user.id);

                    } else {
                        alert("That user does not exist");
                    }


                }, error: function (a, c, d) {
                    console.log(a);
                }
            });

        }

        function loadList(id) {

            currentList = [];

            $.ajax({
                url: '/projects/movies/list?id=' + id,
                method: 'GET',
                success: function (data) {
                    data = JSON.parse(data);

                    $(".current-list").html("");

                    if (data) {

                        activeID = data[0].id;

                        for (var i = 0; i < data.length; i++) {

                            var movie = data[i];
                            comments[movie.api_id] = movie.comment;
                            titles[movie.id] = movie.title;
                            currentList.push(movie.id);
                            $(".current-list").append("<li data-id='" + movie.api_id + "'><a href='#' class='list-movie'>" + movie.title + "</a><div class='del-row'>X</div></li>");
                        }

                        loadDetails(data[0].api_id);

                    }


                }, error: function (a, c, d) {
                    console.log(a);
                }
            });
        }

        function hideLogin() {

            $("#login-form").hide();
            $("#login-info").html(user.username + " - " + user.key);
            $("#login-info").css("display", "inline");

            $("#comments").attr("placeholder", "");

        }

    });


</script>
</body>
