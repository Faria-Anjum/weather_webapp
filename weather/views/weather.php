<?php

    function _checkFileExists($url) { //checks if data exists for user-entered city 
        $headers = @get_headers($url); //get header sent by server fpr http request

        if($headers[0] == 'HTTP/1.1 404 Not Found') { //if data for city not found and url is invalid
            return false;
        } else {
            return true;
        }
    }

    function today() {  //function to display individual data every 3 hours for a specific date

        global $data;

        $date = strtotime($data->list[0]->dt_txt);  //get timestamp from date text
        $tz = $data->city->timezone;    //get timezone from city
        $day = date('d', $date+$tz);    //find exact date for city based on city
        $count = 0;

        for ($i=0; $i < 8; $i++) { //look through first 8 dates to find if all are from same day as in today
            if (date('d', (strtotime($data->list[$i]->dt_txt))+($data->city->timezone))==$day) {
                $count+=1;      //if all are, we add to count
            }
        }

        for ($i=0; $i < $count; $i++) { //output all information for current date only

            $main = $data->list[$i]->main;
            $weather = $data->list[$i]->weather[0];

            $date = strtotime($data->list[$i]->dt_txt);
            $tz = $data->city->timezone;
            $hour = hournow(date('H', $date+$tz));

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
                            .ucfirst($desc). //to capitalize description of weather
                        '</h4>
                        <h3>'
                            .$temp.'°C
                        </h3> 
                    </div>
                </div>';
        }
    }

    function daily() {  //function to display average data for each of five days

        global $data;

        $date = strtotime($data->list[0]->dt_txt);
        $tz = $data->city->timezone;
        $day = date('d', $date+$tz);    //setting day to today
        $list = array(0);

        for ($i=0; $i < 40; $i++) { //look through all data to find if data not from current day

            if (date('d', (strtotime($data->list[$i]->dt_txt))+($data->city->timezone))!=$day) {    //if data not from current day
                $day = date('d', (strtotime($data->list[$i]->dt_txt))+($data->city->timezone));     //appending index of day to list
                array_push($list, $i);
            }
        }

        $len = count($list);

        for ($i=0; $i < 5; $i++) {  //printing data of days according to index to make sure data of same day is not repeated

            $main = $data->list[$list[$i]]->main;
            $weather = $data->list[$list[$i]]->weather[0];

            $date = strtotime($data->list[$list[$i]]->dt_txt);
            $tz = $data->city->timezone;
            $day = date('d', $date+$tz);
            $month = date('M', $date+$tz);

            $low = intval(highlow($day, 'l'));
            $high = intval(highlow($day, 'h'));
            
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

    function highlow($day, $hl){ //finding highest and lowest temperature

        global $data;
        $temps = array();

        for ($i=0; $i < 40; $i++) { 

            $date = strtotime($data->list[$i]->dt_txt);
            $tz = $data->city->timezone;
            $currday = date('d', $date+$tz); //finding date of each data

            if ($currday == $day){  //appending temperatures in list if date matches requested date

                $main = $data->list[$i]->main;

                if ($hl == 'l') {
                    $temp = floor($main->temp);
                }
                if ($hl == 'h') {
                    $temp = ceil($main->temp);
                }
                array_push($temps, $temp);
            }
        }

        if ($hl == 'l') {
            return min($temps);
        }
        if ($hl == 'h') {
            return max($temps);
        }
    }

    function country(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){   //if search button clicked

            global $data, $names;

            $code = $data->city->country;   //getting country code of city

            if($code!='' and $names->$code!=$data->city->name){     //if city entered
                echo $data->city->name.', '.$names->$code;
            } else {
                echo $data->city->name;     //if country entered
            }
        }
    }

    function weatherimg($curr, $desc, $time){       //specific images based on curr and desc
        
        if ($desc == 'clear sky' and $time == 'd') {
            return "<img src='../images/clear_sky.png'>";
        }
        elseif ($desc == 'clear sky' and $time == 'n') {
            return "<img src='../images/clear_sky_n.png'>";
        }

        elseif ($desc == 'few clouds' and $time == 'd') {
            return "<img src='../images/few_clouds.png'>";
        }
        elseif ($desc == 'few clouds' and $time == 'n') {
            return "<img src='../images/few_clouds_n.png'>";
        }
        elseif ($desc == 'scattered clouds') {
            return "<img src='../images/scattered_clouds.png'>";
        }
        elseif ($desc == 'broken clouds' or $desc == 'overcast clouds') {
            return "<img src='../images/broken_clouds.png'>";
        }

        elseif ($desc == 'freezing rain') {
            return "<img src='../images/snow.png'>";
        }
        elseif (strpos($desc, 'shower rain') !== false) {
            return "<img src='../images/shower_rain.png'>";
        }
        elseif ($curr == 'Rain' and $time == 'd') {
            return "<img src='../images/rain.png'>";
        }
        elseif ($curr == 'Rain' and $time == 'n') {
            return "<img src='../images/rain_n.png'>";
        }

        elseif ($curr == 'Drizzle') {
            return "<img src='../images/shower_rain.png'>";
        }

        elseif ($desc == 'thunderstorm' or $desc == 'ragged thunderstorm') {
            return "<img src='../images/thunderstorm.png'>";
        }
        elseif ($desc == 'heavy thunderstorm') {
            return "<img src='../images/heavy_thunderstorm.png'>";
        }
        elseif ($curr == 'Thunderstorm') {
            return "<img src='../images/thunderstorm_rain.png'>";
        }
        
        elseif ($curr == 'Snow') {
            return "<img src='../images/snow.png'>";
        }
        
        else {
            return "<img src='../images/mist.png'>";
        }
        
    }

    function desc() {   //return capitalized desc
        global $desc;
        echo ucfirst($desc);
    }

    function temp($i) {     //return curr

        global $data;

        $temp = $data->list[$i]->main->temp;
        echo $temp;
    }

    function feel($i) {     //return feels like temp
        global $data;
        $feel = $data->list[$i]->main->feels_like;
        echo $feel;
    }

    function hournow($hour) {       //return am/pm time
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

                    if($_SERVER['REQUEST_METHOD'] == 'POST'){ //if search button clicked

                        $city = $_POST['city'];     //get city to produce api url

                        if($city == '') {       //if blank value entered (to avoid error)
                            echo '<h1>'.'Enter a city in the searchbar!'.'</h1>';
                            die(); //kill entire program
                        }


                        $url = "https://api.openweathermap.org/data/2.5/forecast?q=".$city."&lang=en&appid=ee44b86316a1bbdd065dd1f88122f834&units=metric";

                        if(_checkFileExists($url) == false){    //if nonexistent city entered
                            echo '<h1>'.'City not found. Did you misspell?'.'</h1>';
                            die();
                        }

                        $all = file_get_contents($url);
                        $data = json_decode($all);

                        $names = json_decode(file_get_contents("http://country.io/names.json"));    //api to get country name from code

                    }

                    else {      //if search button not yet clicked
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
    </div>
</body>
</html>