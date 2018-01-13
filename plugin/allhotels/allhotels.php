<?php
/*
  Plugin Name: All_hotels
  Description: All_hotels
  Author:  alexdzyaba
  Version: 1.0
 */

// Объявляем стили, скрипты и шорткоды
session_start();
add_shortcode('allmeta', 'all_meta');
add_shortcode('hoteledit', 'hotel_edit');
add_shortcode('hotelstable', 'hotels_table');
add_shortcode('hotellist', 'hotel_list');
add_shortcode('allbanners', 'all_banners');
add_shortcode('testapi', 'test_api');
add_shortcode('report', 'report');

add_shortcode('alltemp', 'all_temp');

// Подключаем PHP файлы
require_once dirname(__FILE__) . '/php/all_meta.php';
require_once dirname(__FILE__) . '/php/hotel_edit.php';
require_once dirname(__FILE__) . '/php/hotels_table.php';
require_once dirname(__FILE__) . '/php/hotel_list.php';
require_once dirname(__FILE__) . '/php/all_banners.php';
require_once dirname(__FILE__) . '/php/test_api.php';
require_once dirname(__FILE__) . '/php/hotel_report.php';

require_once dirname(__FILE__) . '/php/temp.php';

// Преобразование объекта в массив
function object_to_array($data) {
    if (is_object($data))
        $data = get_object_vars($data);
    if (is_array($data))
        return array_map(__FUNCTION__, $data);
    else
        return $data;
}

// Делаем поле в редакторе обязательным к заполнению
function add_required_attribute_to_wp_editor( $editor ) {
    $editor = str_replace( '<textarea', '<textarea required="required"', $editor );
    return $editor;
}
// add_filter( 'the_editor', 'add_required_attribute_to_wp_editor', 10, 1 );   other dates
ini_set('max_execution_time', 300); //300 seconds = 5 minutes

function multi_curl(&$tasks){

    $header = array();
    $header[] = 'Authorization: '.TOKEN;

    // страны, содержимое которых надо получить
    $urls = array("https://api.ittour.com.ua/module/params/338?entity=hotel",
                "https://api.ittour.com.ua/module/params/318?entity=hotel",
                "https://api.ittour.com.ua/module/params/320?entity=hotel",
                "https://api.ittour.com.ua/module/params/372?entity=hotel",
                "https://api.ittour.com.ua/module/params/434?entity=hotel",
                "https://api.ittour.com.ua/module/params/39?entity=hotel",
                "https://api.ittour.com.ua/module/params/16?entity=hotel",
                "https://api.ittour.com.ua/module/params/332?entity=hotel",
                "https://api.ittour.com.ua/module/params/376?entity=hotel",
                "https://api.ittour.com.ua/module/params/378?entity=hotel",
                  "https://api.ittour.com.ua/module/params/334?entity=hotel",
                 "https://api.ittour.com.ua/module/params/23?entity=hotel",
                 "https://api.ittour.com.ua/module/params/60?entity=hotel",
                 "https://api.ittour.com.ua/module/params/321?entity=hotel",
                "https://api.ittour.com.ua/module/params/75?entity=hotel",
                 "https://api.ittour.com.ua/module/params/69?entity=hotel",
                 "https://api.ittour.com.ua/module/params/330?entity=hotel",
                "https://api.ittour.com.ua/module/params/323?entity=hotel",
                "https://api.ittour.com.ua/module/params/76?entity=hotel",
                "https://api.ittour.com.ua/module/params/1082?entity=hotel",
                 "https://api.ittour.com.ua/module/params/9?entity=hotel",
                "https://api.ittour.com.ua/module/params/90?entity=hotel",
                "https://api.ittour.com.ua/module/params/324?entity=hotel",
                 "https://api.ittour.com.ua/module/params/91?entity=hotel",
                 "https://api.ittour.com.ua/module/params/442?entity=hotel",
                //поиск регионов
                 "https://api.ittour.com.ua/module/params"
    );

// инициализируем "контейнер" для отдельных соединений (мультикурл)
    $cmh = curl_multi_init();

// массив заданий для мультикурла
    $tasks = array();
// перебираем наши урлы
    foreach ($urls as $url) {
        // инициализируем отдельное соединение (поток)
        $ch = curl_init($url);
        // если будет редирект - перейти по нему
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        // возвращать результат
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //  возвращать http-заголовок
        curl_setopt($ch, CURLOPT_HTTPHEADER , $header);
        // таймаут соединения
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        // таймаут ожидания
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        // добавляем дескриптор потока в массив заданий
        $tasks[$url] = $ch;
        // добавляем дескриптор потока в мультикурл
        curl_multi_add_handle($cmh, $ch);
    }

// количество активных потоков
    $active = null;
// запускаем выполнение потоков
    do {
        $mrc = curl_multi_exec($cmh, $active);
    }
    while ($mrc == CURLM_CALL_MULTI_PERFORM);

// выполняем, пока есть активные потоки
    while ($active && ($mrc == CURLM_OK)) {
        // если какой-либо поток готов к действиям
        if (curl_multi_select($cmh) != -1) {
            // ждем, пока что-нибудь изменится
            do {
                $mrc = curl_multi_exec($cmh, $active);
                // получаем информацию о потоке
                $info = curl_multi_info_read($cmh);
                // если поток завершился
                if ($info['msg'] == CURLMSG_DONE) {
                    $ch = $info['handle'];
                    // ищем урл страницы по дескриптору потока в массиве заданий
                    $url = array_search($ch, $tasks);
                    // забираем содержимое
                    $tasks[$url] = json_decode(curl_multi_getcontent($ch), true);
                    // удаляем поток из мультикурла
                    curl_multi_remove_handle($cmh, $ch);
                    // закрываем отдельное соединение (поток)
                    curl_close($ch);
                }
            }
            while ($mrc == CURLM_CALL_MULTI_PERFORM);
        }
    }

    // закрываем мультикурл

    curl_multi_close($cmh);

    return $tasks;
}

$date_from = date('d.m.y', strtotime("+1 days"));
$date_till = date('d.m.y', strtotime("+12 days"));

function search_hotel($arr, $region, $id_country){
    $find = ',';
    $hotel_name = array();
    foreach ($arr as $key => $val){
        $hotel_name[$key]['id'] =  $val['id'];
        $hotel_name[$key]['value'] =  $val['name'];
        $hotel_name[$key]['region_id'] =  $val['region_id'];//countries
        $hotel_name[$key]['country_id'] =  $id_country;//countries

    }
    for($i=0; $i<count($hotel_name); $i++) {
        foreach ($region as $key => $val) {
            $preg = stripos($hotel_name[$i]['region_id'], $find);
            if ($preg !== false) {
                $regions = explode(',', $hotel_name[$i]['region_id']);
                for ($k = 0; $k <= count($regions); $k++) {
                    if ($regions[$k] == $val['id']) {
                        $hotel_name[$i]['value'] .= ' (регион - ' . $val['name'] . ')';
                    } else continue;
                }
            }else{
                if ($hotel_name[$i]['region_id'] == $val['id']) {
                    $hotel_name[$i]['value'] .= ' (регион - ' . $val['name'] . ')';
                } else continue;
            }
        }
    }

    return $hotel_name;
}
function search_all_hotel(){
    if($_SERVER['REQUEST_URI'] == "/hotel-edit/" ||  $_GET['action'] == 'edit'){
        $countries = array(
            '338','318','320','372','434','39','16','332','376','378','334','23','60','321',
            '75','69','330','323','76','1082','9','90','324','91','442'
        );
        $param = array();
        $_SESSION['arr'] = multi_curl($res);
        for($i=0; $i<count($countries); $i++){
            $param[$i] = search_hotel(
                $_SESSION['arr']["https://api.ittour.com.ua/module/params/".$countries[$i]."?entity=hotel"]['hotels'],
                $_SESSION['arr']["https://api.ittour.com.ua/module/params"]['regions'],
                $countries[$i]
            );

        }
        $hotel_name = array_merge($param[0], $param[1], $param[2],$param[3],$param[4],$param[5],$param[6],$param[7],$param[8],$param[9],
            $param[10],$param[11],$param[12],$param[13],$param[14],$param[15],$param[16],$param[17],$param[18],$param[19],$param[20],$param[21],
            $param[22],$param[23],$param[24]
        );
        return $hotel_name;
    }else return false;
}

global $wpdb;
$hotel_count = $wpdb->query("SELECT * FROM all_get_ittour_hotels LIMIT 1, 5");
$_SESSION['data'] = array();
if($hotel_count == 0) {
    $data = search_all_hotel();
    if (!empty($wpdb->error)){
        wp_die($wpdb->error);
    }else{
        for ($i = 0; $i <= count($data); $i++) {
            $id = $data[$i]['id'];
            $value = $data[$i]['value'];
            $region_id = $data[$i]['region_id'];
            $country_id = $data[$i]['country_id'];//country_id
            $wpdb->insert(
                'all_get_ittour_hotels',
                array(
                    'id' => $id,
                    'value' => $value,
                    'region_id' => $region_id,
                    'country_id' => $country_id
                )
            );
        }
    }
}
else{
    $res = $wpdb->get_results("SELECT * FROM all_get_ittour_hotels");
    for($i=0; $i<=count($res); $i++){
        $_SESSION['data'][$i]['id'] = $res[$i]->id;
        $_SESSION['data'][$i]['value'] = $res[$i]->value;
        $_SESSION['data'][$i]['region_id'] = $res[$i]->region_id;
        $_SESSION['data'][$i]['country_id'] = $res[$i]->country_id;
    }
}



