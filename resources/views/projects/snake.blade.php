<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Phaser: Snake</title>
    <style>
        * {
            padding: 0;
            margin: 0;
        }
    </style>
    <script type="text/javascript" src="{{ asset('js/phaser.min.js')}}"></script>

</head>
<body>
<script>

    var game = new Phaser.Game(600, 600, Phaser.AUTO, null, {
        preload: preload, create: create, update: update
    });

    var ball;
    var speed = 20;
    var newFood = true;
    var currentFood;
    var chainGroup;
    var chainLength = 0;
    var direction = "r";

    var scoreText;
    var score = 0;

    var bonusGroup;

    var timer;

    var spider;
    var spiderExists = false;

    function preload() {
        game.scale.scaleMode = Phaser.ScaleManager.SHOW_ALL;
        game.scale.pageAlignHorizontally = true;
        game.scale.pageAlignVertically = true;
        game.stage.backgroundColor = "#eee";

        game.load.image("ball", "/images/ball.png");
        game.load.image("food", "/images/food.png");
        game.load.image("spider", "/images/spider.png");

        game.load.spritesheet('bonus', '/images/bonus.png', 20, 20);

    }

    function create() {

        game.physics.startSystem(Phaser.Physics.ARCADE);

        ball = game.add.sprite(0, 300, "ball");
        game.physics.enable(ball, Phaser.Physics.ARCADE);
        chainGroup = game.add.physicsGroup();
        bonusGroup = game.add.physicsGroup();

        chainGroup.add(ball);
        scoreText = game.add.text(5, 5, "Points: ", {font: '18px Arial', fill: '#0095DD'});

        ball.checkWorldBounds = true;
        ball.events.onOutOfBounds.add(gameOver, this);

    }

    function update() {

        setDirection();

        if ((new Date().getTime() - this.lastUpdate) < 50) {
            return;
        }

        this.lastUpdate = new Date().getTime();

        if (newFood) {
            placeFood();
        }

        if(!spiderExists){
            spider = game.add.sprite(randX(), 0, "spider");
            spiderExists = true;
            game.physics.enable(spider, Phaser.Physics.ARCADE);
            spider.checkWorldBounds = true;
        } else {
            spider.y += 2;
            spider.events.onOutOfBounds.add(function(){
                spiderExists = false;
            }, this);

        }

        game.physics.arcade.collide(ball, spider, gameOver);
        game.physics.arcade.collide(ball, currentFood, eat);
        game.physics.arcade.collide(ball, bonusGroup, eatBonus);

        if (game.physics.arcade.collide(chainGroup, chainGroup)) {
            gameOver();
        }

        for (var i = chainLength; i > 0; i--) {

            chainGroup.children[i].y = chainGroup.children[i - 1].y;
            chainGroup.children[i].x = chainGroup.children[i - 1].x;
        }

        if (direction === "l") {
            ball.x -= speed;
        } else if (direction === "r") {
            ball.x += speed;
        } else if (direction === "d") {
            ball.y += speed;
        } else if (direction === "u") {
            ball.y -= speed;
        }

        scoreText.setText('Points: ' + score);
    }

    function placeFood() {

        currentFood = game.add.sprite(randX(), randY(), "food");
        newFood = false;

        game.physics.enable(currentFood, Phaser.Physics.ARCADE);

    }

    function eat(ball, food) {
        food.kill();
        newFood = true;

        chainLength++;

        var n = game.add.sprite(-100, -100, "food");
        chainGroup.add(n);

        score++;

        placeBonus();

    }

    function setDirection() {
        if (game.input.keyboard.isDown(Phaser.Keyboard.LEFT) && direction !== "r") {
            direction = 'l';
        }
        else if (game.input.keyboard.isDown(Phaser.Keyboard.RIGHT) && direction !== "l") {
            direction = 'r';

        } else if (game.input.keyboard.isDown(Phaser.Keyboard.DOWN) && direction !== "u") {
            direction = 'd';

        } else if (game.input.keyboard.isDown(Phaser.Keyboard.UP) && direction !== "d") {
            direction = 'u';
        }
    }

    function placeBonus() {

        var r = Math.floor(Math.random() * (4)) + 1;
        if (r === 4) {

            var b = game.add.sprite(randX(), randY(), "bonus");
            b.animations.add("countdown", [0, 1, 2, 3, 4], 1);
            b.animations.play("countdown");
            bonusGroup.add(b);
            b.points = 5;

            timer = game.time.create(false);
            //  Set a TimerEvent to occur after 2 seconds
            timer.loop(1000, function () {
                b.points = b.points - 1;
                if (b.points < 1) b.kill();
            }, this);

            timer.start();
        }

    }

    function gameOver() {
        alert("Game over");
        location.reload();
    }

    function randX() {
        return Math.floor(Math.random() * ((game.world.width - 20) - 20)) + 20;
    }

    function randY() {
        return Math.floor(Math.random() * ((game.world.height - 20) - 20)) + 20;
    }

    function eatBonus(ball, bonus) {
        bonus.kill();
        score += bonus.points;

        var pointsText = game.add.text(bonus.x, bonus.y, "+" + bonus.points , {font: '24px Arial', fill: '#0095DD'});

        var killTween = game.add.tween(pointsText.scale);
        killTween.to({x:0,y:0}, 400, Phaser.Easing.Linear.None);
        killTween.onComplete.addOnce(function(){
            pointsText.kill();
        }, this);
        killTween.start();
    }

</script>
</body>
</html>