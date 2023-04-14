<?php

    function _checkFileExists($url) {
        $headers = @get_headers($url);

        if($headers[0] == 'HTTP/1.1 404 Not Found') {
            return false;
        } else {
            return true;
        }
    }

    function country(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){

            global $data, $names;

            $code = $data->city->country;

            if($code!=''){
                echo $data->city->name.', '.$names->$code;
            } else {
                echo $data->city->name;
            }
        }
    }

    function weatherimg(){

        global $desc, $time;
        
        if ($desc == 'shower rain') {
            echo "<img src='../images/rain.jpg'>";
        }
        elseif ($desc == 'light rain') {
            echo "<img src='../images/light-rain.jpg'>";
        }
        elseif ($desc == 'clear sky' and $time == 'd') {
            echo "<img src='../images/clear.jpg'>";
        }
        elseif ($desc == 'clear sky' and $time == 'n') {
            echo "<img src='../images/moon.jpg'>";
        }
        elseif ($desc == 'broken clouds') {
            echo "<img src='../images/overcast.jpg'>";
        }
        elseif ($desc == 'scattered clouds') {
            echo "<img src='../images/overcast.png'>";
        }
        elseif ($desc == 'few clouds') {
            echo "<img src='../images/clouds.png'>";
        }
        
    }

    function desc() {
        global $desc;
        echo ucfirst($desc);
    }

    function temp() {

        global $data;

        $temp = $data->list[0]->main->temp;
        echo $temp;
    }

    function feel() {
        global $data;
        $feel = $data->list[0]->main->feels_like;
        echo $feel;
    }

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/style.css">
    <title>Weather App</title>
</head>
<body>
    <nav class="navbar nav py-3 justify-content-between">
    <div class="title">
        <img class="icon" src="../images/icon.png" alt="icon">
        <h1 class="navbar-brand p-2"> 5-Day Weather</h1>
    </div>
        <form class="form-inline form" method="post">
            <input type="search" class="form-control mr-2 search" placeholder="Search for a city" name="city">
            <button class="btn my-2 search" type="submit">Search</button>

        </form>
    </nav>

    <div class="container-fluid my-3 py-5">
        <div class="row main justify-content-center align-items-center">

            <div class="col-sm-12 top text-center">
               <?php 

                    if($_SERVER['REQUEST_METHOD'] == 'POST'){

                        $city = $_POST['city'];
                        $url = "https://api.openweathermap.org/data/2.5/forecast?q=".$city."&lang=en&appid=ee44b86316a1bbdd065dd1f88122f834&units=metric";

                        if(_checkFileExists($url) == false){
                            echo '<h1>'.'City not found. Did you misspell?'.'</h1>';
                            die();
                        }

                        $all = file_get_contents($url);
                        $data = json_decode($all);

                        $names = json_decode(file_get_contents("http://country.io/names.json"));

                    }

                    else {
                        echo '<h1>'.'Enter a city in the searchbar!'.'</h1>';
                        die();
                    }

                ?>
            </div>
        </div>

        <div class="row main2">

            <div class="col-sm-6 weatherimg justify-content-center align-items-center">
                <div class="locate justify-self-flex-start">
                    <h1>
                        <?php country() ?>
                    </h1>
                </div>
                <div class="sky-temp">
                    <div class="sky">
                        <div><?php
                            $weather = $data->list[0]->weather[0];
                            $desc = $weather->description;
                            $time = $weather->icon[2];
                            weatherimg();
                        ?></div>
                        <div class="temp">
                            <h1>
                                <?php temp() ?>°
                            </h1>
                            <h2>
                                Feels like <?php feel() ?>°
                            </h2>
                        </div>    
                    </div>
                    <div class="desc">
                        <h2>
                            <?php desc() ?>
                        </h2>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <h1>Random text and I'm rambling right now and also testing out my keyboard skills which are bunnies</h1>
            </div>
        </div>
        
        


        <!--<?php
            /*for ($i=0; $i < 40; $i++) {

                $weather = $data->list[$i]->weather[0];
                $curr = $weather->main;
                $desc = $weather->description;
                $time = $weather->icon[2];

                /*echo '<div class="row">
                        <div class="col-2" data-color="red">'
                            .$desc." ".$data->list[$i]->dt_txt.' '.$time.' '.weatherimg($i).
                        '</div>
                    </div>';
            }*/
        ?>-->
    </div>
</body>
</html>