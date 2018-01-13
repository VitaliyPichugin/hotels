<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 16.08.2017
 * Time: 10:10
 */

function print_arr($arr){
    echo "<pre>";
    print_r($arr);
    echo "</pre>";
}

function get_curl($query, &$return){

    $header = array();
    $header[] = 'Authorization: '.TOKEN;

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_HTTPHEADER , $header);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl, CURLOPT_URL, $query);

    $return = json_decode(trim(curl_exec($curl)), TRUE);
    curl_close($curl);

    return $return;
}

function test_api(){
/*    $con = new mysqli("a103.mysql.ukraine.com.ua", "a103_hotels", "5wpgtw3e", "a103_hotels");

    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
    $con->set_charset("utf8");

    $res = $con->query("SELECT * FROM all_hotels, all_currency, all_countries, all_from_city WHERE m");

    $country_id = array();
    $row = array();
    if(!empty($res)) {
        while ($row = mysqli_fetch_assoc($res)) {
            $country_id[] = $row;
        }
    }else echo 'Result is empty';
    $con->close();*/

   // $arr9 = multi_curl();
   // print_arr($arr9);
    $date_from = date('d.m.y', strtotime("+1 days"));
    //echo $date_from;
    $date_till = date('d.m.y', strtotime("+12 days"));
    //https://api.ittour.com.ua/module/search-list?type=1&country=338&adult_amount=2&child_amount=0&hotel_rating=4:78&night_from=6&night_till=8&date_from=26.01.16&date_till=03.02.17&page=1
    $meal_type = "https://api.ittour.com.ua/module/search?type=1&country=434&adult_amount=2&child_amount=0&hotel_rating=4:78&night_from=6&night_till=7&date_from=".$date_from."&date_till=".$date_till."&items_per_page=25&from_city=2014";
    //$q_search = "https://api.ittour.com.ua/module/params/318?entity=hotel:meal_type:from_city";
    get_curl($meal_type, $q_rating);
  // $http =  htmlspecialchars( $q_rating['online_booking_form']);
  // echo $http;

   // preg_match( '/((http)\:\/\/)?([a-z0-9]{1})((\.[a-z0-9-])|([a-z0-9-]))*\.([a-z]{2,4})\/$/', $http, $forLink, PREG_PATTERN_ORDER ); // находим ссылки
   // print_arr($forLink);

    //$keywords = preg_split('/ /', $http);
  //  preg_match('/((http)\:\/\/)?([a-z0-9]{1})((\.[a-z0-9-])|([a-z0-9-]))*\.([a-z]{2,4})\/$/',  $keywords[2], $found);

    //$arr[$i][$k] = str_replace($found3[0], " <", $arr[$i][$k])

   print_arr($q_rating);

    if(isset($q_rating['online_booking_form'])){
        $res = file_get_contents("http://online2.sonata-travel.com");
       // echo $res;
       // echo iconv('windows-1251', 'utf-8', $res);
    }
    //print_arr($q_rating);
    //print_arr($arr9);

}
?>