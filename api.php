<?php
include('PrayTime.php');
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
date_default_timezone_set('Asia/Kuala_Lumpur');
$today = getdate();
$arr = array();

$response = array(
    'data' => [],
    'messages' => 'RALAT: Waktu solat ini adalah anggaran sahaja. Mungkin terdapat perbezaan 3 - 4 minit dengan waktu yang dikeluarkan oleh pihak berkuasa agama negeri. Sila rujuk pihak berkaitan untuk pengesahan.',
);

$places = [
    'kl' => [3.142602, 101.686167],
    'jb' => [1.493221, 103.743247],
    'mersing' => [2.430819, 103.834831],
    'kangar' => [6.446986, 100.221099],
    'melaka' => [2.193331, 102.244984],
    'kb' => [6.117706, 102.273441],
    'kt' => [5.332485, 103.130166],
    'kuantan' => [3.813715, 103.328503],
    'ipoh' => [4.597804, 101.088413],
    'seremban' => [2.723762, 101.942944],
    'alor star' => [6.123753, 100.363874],
    'p pinang' => [5.412111, 100.336098],
];

function get_docs() {
    global $places;
    $output = [
        "Available places: " . implode(",", array_keys($places)),
    ];
    return implode("\n", $output);
}

function prepare_data($date, $endDate, $latitude, $longitude, $timeZone, $prayTime, &$arr) {
    while ($date < $endDate)
    {
        $times = $prayTime->getPrayerTimes($date, $latitude, $longitude, $timeZone);
        $day = date('M d', $date);
        array_push($arr, array('today' => $day),array('Subuh' => $times[0]),array('Syuruk' => $times[1]),array('Zuhur' => $times[2]),array('Asar' => $times[3]),array('Terbenam Matahari' => $times[4]),array('Maghrib' => $times[5]),array('Isyak' => $times[6]));
        $date += 24* 60* 60;  // next day
    }

}

$place = isset($_GET['place']) ? $_GET['place'] : null;
if (is_null($place) || $place == '') {
    $response['messages'] = get_docs();
    echo json_encode($response);
    die();
}

if (!array_key_exists($place, $places)) {
    $response['messages'] = get_docs();
    echo json_encode($response);
    die();
}

$latlong = $places[$place];
list($method, $year, $latitude, $longitude, $timeZone) = array(5, $today['year'], $latlong[0], $latlong[1], 8);
$prayTime = new PrayTime($method);

if(isset($_GET['monthly']) && $_GET['monthly'] != "" && $_GET['monthly'] == 1){
  
    $date = strtotime($year.'-'.$today['mon'].'-1');
    $endDate = strtotime($year.'-'.($today['mon']+1).'-1');
    prepare_data($date, $endDate, $latitude, $longitude, $timeZone, $prayTime, $arr);
}
elseif(isset($_GET['anually']) && $_GET['anually'] != "" && $_GET['anually'] == 1){

    $date = strtotime($year.'-1-1');
    $endDate = strtotime(($year+1).'-1-1');
    prepare_data($date, $endDate, $latitude, $longitude, $timeZone, $prayTime, $arr);
}
else{
    
    $date = strtotime($year.'-'.$today['mon'].'-'.$today['mday']);
    $endDate = strtotime($year.'-'.$today['mon'].'-'.($today['mday']+1));
    prepare_data($date, $endDate, $latitude, $longitude, $timeZone, $prayTime, $arr);
}

$response['data'] = $arr;
echo json_encode($response);
