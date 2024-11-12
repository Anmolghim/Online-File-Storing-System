<?php


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Storage</title>
    <style>
        body{
            display: flex;
            align-items: center;
        }
        .circle{
            height: 500px;
            width: 600px;
            border-radius: 50%;
            background-color: green;
            display: flex;
            align-items: center; 
            justify-content: center;
            position: relative;
            left: 550px;
            top: 200px;
            border: 2px solid red;

        }
        .v-shape {
            width: 0;
            height: 0;
            border-left: 50px solid transparent;
            border-right: 50px solid transparent;
            border-top: 100px solid blue;
            transform: rotate(0deg);
        }
    </style>
</head>
<body>
    <div class="circle">
        <!-- <div class="image">

        </div>

        <div class="video">

        </div>

        <div class="document">

        </div>

        <div class="audio">

        </div> -->
        <div class="v-shape">

        </div>
    </div>
</body>
</html>
