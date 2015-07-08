<?php
include('PrayTime.php');
header('Access-Control-Allow-Origin: *');
date_default_timezone_set('Asia/Kuala_Lumpur');
$today = getdate();
$arr = array();

$places = [
    'kl' => [3.142602, 101.686167],
    'jb' => [1.493221, 103.743247],
    'kangar' => [6.446986, 100.221099],
    'melaka' => [2.193331, 102.244984],
];

$place = isset($_GET['place']) ? $_GET['place'] : null;
if (is_null($place) || $place == '') {
    echo "<h1>Waktu Solat Api Documentation</h1>";
    echo "<p>Usage:</p>";
    echo "<p>For Help</p><em>http://localhost/dev/solatapi/api.php</em>";
    echo "<p>For Daily</p><em>http://localhost/dev/solatapi/api.php?place=[location]</em>";
    echo "<p>For Monhtly</p><em>http://localhost/dev/solatapi/api.php?place=[location]&monthly=1</em>";
    echo "<p>For Anually</p><em>http://localhost/dev/solatapi/api.php?place=[location]&anually=1</em>";
    echo "<p>Hosting your own api <a href='https://github.com/Treakyidea/solatapi'>Git</a></p>";
    die();
}

if (!array_key_exists($place, $places)) {
    echo "<h1>Waktu Solat Api Documentation</h1>";
    echo "<p>Usage:</p>";
    echo "<p>For Help</p><em>http://localhost/dev/solatapi/api.php</em>";
    echo "<p>For Daily</p><em>http://localhost/dev/solatapi/api.php?place=[location]</em>";
    echo "<p>For Monhtly</p><em>http://localhost/dev/solatapi/api.php?place=[location]&monthly=1</em>";
    echo "<p>For Anually</p><em>http://localhost/dev/solatapi/api.php?place=[location]&anually=1</em>";
    echo "<p>Hosting your own api <a href='https://github.com/Treakyidea/solatapi'>Git</a></p>";
    echo "<p>Available places: " . implode(",", array_keys($places)) . "</p>";
    die();

}

$latlong = $places[$place];
list($method, $year, $latitude, $longitude, $timeZone) = array(5, $today['year'], $latlong[0], $latlong[1], 8);
$prayTime = new PrayTime($method);

if(isset($_GET['monthly']) && $_GET['monthly'] != "" && $_GET['monthly'] == 1){
  
    $date = strtotime($year.'-'.$today['mon'].'-1');
    $endDate = strtotime($year.'-'.($today['mon']+1).'-1');
    while ($date < $endDate)
    {
        $times = $prayTime->getPrayerTimes($date, $latitude, $longitude, $timeZone);
        $day = date('M d', $date);
        array_push($arr,array('data' => array('today' => $day),array('Fajr' => $times[0]),array('Sunrise' => $times[1]),array('Dhuhr' => $times[2]),array('Asr' => $times[3]),array('Sunset' => $times[4]),array('Maghrib' => $times[5]),array('Isha' => $times[6])));
        $date += 24* 60* 60;  // next day
    }

}
elseif(isset($_GET['anually']) && $_GET['anually'] != "" && $_GET['anually'] == 1){

    $date = strtotime($year.'-1-1');
    $endDate = strtotime(($year+1).'-1-1');
    while ($date < $endDate)
    {
        $times = $prayTime->getPrayerTimes($date, $latitude, $longitude, $timeZone);
        $day = date('M d', $date);
        array_push($arr,array('data' => array('today' => $day),array('Fajr' => $times[0]),array('Sunrise' => $times[1]),array('Dhuhr' => $times[2]),array('Asr' => $times[3]),array('Sunset' => $times[4]),array('Maghrib' => $times[5]),array('Isha' => $times[6])));
        $date += 24* 60* 60;  // next day
    }
}
else{
    
    $date = strtotime($year.'-'.$today['mon'].'-'.$today['mday']);
    $endDate = strtotime($year.'-'.$today['mon'].'-'.($today['mday']+1));
    while ($date < $endDate)
    {
        $times = $prayTime->getPrayerTimes($date, $latitude, $longitude, $timeZone);
        $day = date('M d', $date);
        array_push($arr,array('data' => array('today' => $day),array('Fajr' => $times[0]),array('Sunrise' => $times[1]),array('Dhuhr' => $times[2]),array('Asr' => $times[3]),array('Sunset' => $times[4]),array('Maghrib' => $times[5]),array('Isha' => $times[6])));
        $date += 24* 60* 60;  // next day
    }

}
header('Content-Type: application/json');
echo json_encode($arr);
