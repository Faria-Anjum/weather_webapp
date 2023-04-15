<?php

    function _checkFileExists($url) {
        $headers = @get_headers($url);

        if($headers[0] == 'HTTP/1.1 404 Not Found') {
            return false;
        } else {
            return true;
        }
    }

    function today() {

        global $data;

        $date = strtotime($data->list[0]->dt_txt);
        $tz = $data->city->timezone;
        $day = date('d', $date+$tz);
        $count = 0;

        for ($i=0; $i < 8; $i++) {
            if (date('d', (strtotime($data->list[$i]->dt_txt))+($data->city->timezone))==$day) {
                $count+=1;
            }
        }

        for ($i=0; $i < $count; $i++) {

            $main = $data->list[$i]->main;
            $weather = $data->list[$i]->weather[0];

            $date = strtotime($data->list[$i]->dt_txt);
            $tz = $data->city->timezone;
            $hour = hournow(date('H', $date+$tz));

            // $date = ($data->list[$i]->dt);
            // $hour = hournow(date('H', $date));

            // $date = ($data->list[$i]->dt);
            // $dt = new DateTime('@' . $date);
            // $dt->setTimezone(new DateTimeZone($data->city->timezone));
            // $hour = hournow($dt->format('H'));

            //$date = strtotime($data->list[$i]->dt_txt);
            //$hour = hournow(date('H', $date));
            $temp = round($main->temp);
            $curr = $weather->main;
            $desc = $weather->description;
            $time = $weather->icon[2];
            $weatherimg = weatherimg($curr, $desc, $time);
    
            echo '<div class="row weatherimg">
                    <div class="locate-sky">
                        <div class="locate">
                            <h3>'
                                .$hour.
                            '</h3>
                        </div>
                        <div class="sky">'
                            .$weatherimg.
                        '</div>
                        <h4>'
                            .ucfirst($desc).
                        '</h4>
                        <h3>'
                            .$temp.'°C
                        </h3> 
                    </div>
                </div>';
        }
    }

    function daily() {

        global $data;

        $date = strtotime($data->list[0]->dt_txt);
        $tz = $data->city->timezone;
        $day = date('d', $date+$tz);
        $list = array(0);

        for ($i=0; $i < 40; $i++) {

            if (date('d', (strtotime($data->list[$i]->dt_txt))+($data->city->timezone))!=$day) {
                $day = date('d', (strtotime($data->list[$i]->dt_txt))+($data->city->timezone));
                array_push($list, $i);
            }
            //echo date('d', (strtotime($data->list[$i]->dt_txt))+($data->city->timezone));
            // if (date('d', (strtotime($data->list[$i]->dt_txt))+($data->city->timezone))==($day+$x)%31) {
            //     array_push($list, $i);
            //     $x+=1;
            // }
        }

        $len = count($list);

        for ($i=0; $i < 5; $i++) {

            $main = $data->list[$list[$i]]->main;
            $weather = $data->list[$list[$i]]->weather[0];

            $date = strtotime($data->list[$list[$i]]->dt_txt);
            $tz = $data->city->timezone;
            $day = date('d', $date+$tz);
            $month = date('M', $date+$tz);

            $low = intval(low($day));
            $high = intval(high($day));
            
            $curr = $weather->main;
            $desc = $weather->description;
            $time = $weather->icon[2];
            $weatherimg = weatherimg($curr, $desc, $time);
    
            echo '<div class="row weatherimg">
                    <div class="locate-sky">
                        <div class="locate">
                            <h3>'
                                .$day.' '.$month.
                            '</h3>
                        </div>
                        <div class="sky">'
                            .$weatherimg.
                        '</div>
                        <h4>'
                            .ucfirst($desc).
                        '</h4>
                        <h3>'
                            .$low.'°/'.$high.'°C'.
                        '</h3> 
                    </div>
                </div>';
        }
    }

    function low($day){

        global $data;
        $temps = array();

        for ($i=0; $i < 40; $i++) {

            $date = strtotime($data->list[$i]->dt_txt);
            $tz = $data->city->timezone;
            $currday = date('d', $date+$tz);

            if ($currday == $day){

                $main = $data->list[$i]->main;
                $temp = $main->temp;
                array_push($temps, $temp);
            }
        }
        return min($temps);
    }

    function high($day){

        global $data;
        $temps = array();

        for ($i=0; $i < 40; $i++) {

            $date = strtotime($data->list[$i]->dt_txt);
            $tz = $data->city->timezone;
            $currday = date('d', $date+$tz);

            if ($currday == $day){

                $main = $data->list[$i]->main;
                $temp = $main->temp;
                array_push($temps, $temp);
            }
        }
        return max($temps);
    }

    function country(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){

            global $data, $names;

            $code = $data->city->country;

            if($code!='' and $names->$code!=$data->city->name){
                echo $data->city->name.', '.$names->$code;
            } else {
                echo $data->city->name;
            }
        }
    }

    function weatherimg($curr, $desc, $time){

        if ($desc == 'shower rain') {
            return "<img src='../images/rain.jpg'>";
        }
        elseif ($desc == 'light rain' and $time == 'd') {
            return "<img src='../images/light-rain.jpg'>";
        }
        elseif ($desc == 'light rain' and $time == 'n') {
            return "<img src='../images/light-rain-n.png'>";
        }
        elseif ($desc == 'clear sky' and $time == 'd') {
            return "<img src='../images/clear.jpg'>";
        }
        elseif ($desc == 'clear sky' and $time == 'n') {
            return "<img src='../images/moon.jpg'>";
        }
        elseif ($desc == 'broken clouds') {
            return "<img src='../images/overcast.png'>";
        }
        elseif ($desc == 'scattered clouds') {
            return "<img src='../images/overcast.png'>";
        }
        elseif ($desc == 'few clouds') {
            return "<img src='../images/clouds.png'>";
        }
        elseif ($curr == 'Clouds') {
            return "<img src='../images/overcast.png'>";
        }
        elseif ($curr == 'Rain') {
            return "<img src='../images/rain.jpg'>";
        }
        
    }

    function desc() {
        global $desc;
        echo ucfirst($desc);
    }

    function temp($i) {

        global $data;

        $temp = $data->list[$i]->main->temp;
        echo $temp;
    }

    function feel($i) {
        global $data;
        $feel = $data->list[$i]->main->feels_like;
        echo $feel;
    }

    function hournow($hour) {
        if ($hour>12) {
            return ($hour-12).' PM';
        }
        elseif ($hour==12) {
            return '12 PM';
        }
        elseif ($hour==0) {
            return '12 AM';
        }
        elseif ($hour<12) {
            return $hour.' AM';
        }
    }

    function humid() {
        global $data;
        $humid = $data->list[0]->main->humidity;
        echo $humid.'%';
    }

    function wind() {
        global $data;
        $wind = $data->list[0]->wind->speed;
        echo $wind.' m/s';
    }

    function air() {
        global $data;
        $air = $data->list[0]->main->pressure;
        echo $air.' hPa';
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
        <div class="row justify-content-center align-items-center">

            <div class="col-sm-12 top text-center">
               <?php 

                    if($_SERVER['REQUEST_METHOD'] == 'POST'){

                        $city = $_POST['city'];

                        if($city == '') {
                            echo '<h1>'.'Enter a city in the searchbar!'.'</h1>';
                            die();
                        }


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

        <div class="row main justify-content-center align-items-center">

            <div class="row weatherimg">
                <div class="locate-sky">
                    <div class="locate">
                        <h1>
                            <?php country() ?>
                        </h1>
                    </div>
                    <div class="sky">
                        <?php
                            $weather = $data->list[0]->weather[0];
                            $curr = $weather->main;
                            $desc = $weather->description;
                            $time = $weather->icon[2];
                            $img = weatherimg($curr, $desc, $time);
                            echo $img;
                        ?>
                    </div>
                </div>
                <div class="desc-temp">
                    <h2>
                        <?php desc() ?>
                    </h2>
                    <h1>
                        <?php temp(0) ?>°C
                    </h1>
                    <h2>
                        Feels like <?php feel(0) ?>°C
                    </h2>   
                </div>
                
            </div>
            <!--<div class="row details">
                <h4>
                    Humidity: <?php humid() ?>
                </h4>
                <h4>
                    Wind: <?php wind() ?>
                </h4>
                <h4>
                    Air pressure: <?php air() ?>
                </h4>
            </div>-->
            
        </div>
        <div class="row row3 justify-content-center align-items-center">
            <h1>
                Today
            </h1>
            <div class="row today">
                <?php
                    today();
                ?>
            </div>
        </div>

        <div class="row row3 justify-content-center align-items-center">
            <h1>
                Daily
            </h1>
            <div class="row today">
                <?php
                    daily();
                ?>
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