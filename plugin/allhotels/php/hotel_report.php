<?php

wp_enqueue_script('report', plugins_url().'/allhotels/js/report.js', array('jquery'));

function report()
{
    global $wpdb;
    echo '<form method="post"><input type="submit" name="get_report" value="Обновить данные">';
    switch ($_POST['get_report']){
        case 'Обновить данные':{
            update_base();
            update_report();
            break;
        }
        case 'Обновить базу отелей':{
            update_base();
            break;
        }
        default: {
            $fill_table = $wpdb->get_results("SELECT * FROM all_report");
            echo '<h1 style="text-align: center">Таблица отчетов</h1>';
            show_table($fill_table);
            break;
        }
    }
    echo '</form>';
}
//обновить цены ittour
function update_report(){
    global $wpdb;

    $data = $wpdb->get_results("SELECT * FROM all_report WHERE `update` = 0");

    echo '<h1 style="text-align: center">Таблица отчетов</h1>';
    for($i=0; $i<count($data); $i++){
        if ($data[$i]->hotel_stars == '2') {
            $rating = '7';
        } elseif ($data[$i]->hotel_stars == '5') {
            $rating = '78';
        }else{
            $rating = $data[$i]->hotel_stars;
        }
        $night = (int)$data[$i]->date_till;
        $query = 'https://api.ittour.com.ua/module/search-list?type=1&hotel='.$data[$i]->id_ittour.'&country='.$data[$i]->country_ittour_id.'&hotel_rating='.$rating.'&currency='.$data[$i]->currency_ittour_id.'&adult_amount=2&night_from='.$night.'&night_till='.$night.'&date_from='.$data[$i]->date_from.'&date_till='.$data[$i]->date_from.'&from_city='.$data[$i]->from_city_ittour_id.'&items_per_page=1&kind=1&meal_type='.$data[$i]->meal_ittour_id;
        get_curl($query, $return);
        $price_once = round($return['offers'][0]['prices'][$data[$i]->currency_ittour_id] / 2, 0, PHP_ROUND_HALF_DOWN);

        if(!$return['offers']){
            $status = 'Отель не найден';
            $wpdb->update('all_report',
                array(
                    'status' => $status
                ), array(
                    'id_ittour' => $data[$i]->id_ittour
                )
            );

        } else {
            $wpdb->update('all_report',
                array(
                    'update_price' => $price_once,
                    'link_hotel' => 'http://www.t.zp.ua/%D0%BF%D1%80%D0%BE%D1%81%D0%BC%D0%BE%D1%82%D1%80-%D1%82%D1%83%D1%80%D0%B0?id='.$return['offers'][0]['key'].'&search=0',
                    'update' => 1
                ), array(
                    'id_ittour' => $data[$i]->id_ittour,
                    'meal_ittour_id' => $data[$i]->meal_ittour_id,
                    'date_from' => $data[$i]->date_from,
                )
            );
        }
    }
    $fill_table = $wpdb->get_results("SELECT * FROM all_report");
    show_table($fill_table);

}
//прорисовка таблицы
function show_table($data){
    echo '
    <table><tr style="background-color: #777777">
    <td>Название отеля</td>
     <td>Питание</td>
    <td>Дата вылета</td>
    <td>Кол-во ночей</td>
    <td>Город вылета</td>
    <td>Страна</td>
    <td>Старая цена</td>
    <td>Обновленная цена</td>
    <td>Ссылка на отель</td>
    <td>Статус</td>
     <td>Дата последнего обновления</td>
    </tr>';

    foreach ($data as $key=>$val){
        if($val->link_hotel != 'Link is not defined'){
            $link = "<a href='".$val->link_hotel."'>Ссылка на отель(ITtour)</a>";
        } else $link = 'Link is not defined';

        echo '<tr class="row_hotel"> 
        <td >'.$val->hotel_title.' '.$val->hotel_stars.'</td>
        <td>'.$val->meal_name.'</td>
        <td class="hotel_from">'.$val->date_from.'</td>
        <td class="hotel_till">'.$val->date_till.'</td>
        <td>'.$val->from_city_name.'</td>
        <td>'.$val->country_name.'</td>
        <td class="hotel_price">'.$val->price.' '.$val->currency_symbol.'</td>
        <td class="hotel_update_price">'.$val->update_price.' '.$val->currency_symbol.'</td>
         <td class="link_hotel">'.$link.'</td>
        <td class="hotel_status">'.$val->status.'</td>
        <td class="hotel_update">'.$val->date_update.'</td>
         </tr>';
    }

    echo '</table>
    ';
}
//обновить нашу базу отелей
function update_base(){
    global $wpdb;
    $wpdb->query('TRUNCATE TABLE all_report');
    $allhotels = $wpdb->get_results("SELECT ittour_id, hotel_title, hotel_stars, list_start_date, list_days_long, price, currency_symbol, currency_ittour_id, from_city_name, from_city_ittour_id, meal_name, meal_ittour_id, region_name, country_name, country_ittour_id FROM all_hotels  
                                    RIGHT JOIN all_region_list 
                                    ON all_hotels.hotel_id=all_region_list .list_hotel_id
                                    RIGHT JOIN all_currency 
                                    ON all_region_list.currency_id=all_currency .currency_id 
                                    RIGHT JOIN all_from_city
                                    ON all_region_list.from_city_id=all_from_city.from_city_id 
                                    RIGHT JOIN all_meals
                                    ON all_region_list.meal_id=all_meals.meal_id 
                                    RIGHT JOIN all_regions
                                    ON all_region_list.region_id=all_regions.region_id
                                    RIGHT JOIN all_countries
                                    ON all_regions.country_id=all_countries.country_id
                                    WHERE all_hotels.ittour_id IS TRUE");

    for ($i = 0; $i < count($allhotels); $i++) {
        $date_from = $allhotels[$i]->list_start_date;
        $duration = (int)$allhotels[$i]->list_days_long - 1;
        $from = strtotime($date_from);
        $till = strtotime($date_from . "+" . ($duration - 1) . " day");
        $from_new = date("d.m.y", $from);
       // $till_new = date("d.m.y", $till);
        $wpdb->insert(
            'all_report',
            array(
                'id_ittour' => $allhotels[$i]->ittour_id,
                'hotel_title' => $allhotels[$i]->hotel_title,
                'hotel_stars' => $allhotels[$i]->hotel_stars,
                'date_from' => $from_new,
                'date_till' => $duration,
                'price' => $allhotels[$i]->price,
                'update_price' => '',
                'currency_ittour_id' => $allhotels[$i]->currency_ittour_id,
                'currency_symbol' => $allhotels[$i]->currency_symbol,
                'from_city_name' => $allhotels[$i]->from_city_name,
                'from_city_ittour_id' => $allhotels[$i]->from_city_ittour_id,
                'meal_name' => $allhotels[$i]->meal_name,
                'meal_ittour_id' => $allhotels[$i]->meal_ittour_id,
                'country_name' => $allhotels[$i]->country_name,
                'country_ittour_id' => $allhotels[$i]->country_ittour_id,
                'link_hotel' => 'Link is not defined',
                'status' => 'Not updated',

            )
        );
    }

   // $fill_table = $wpdb->get_results("SELECT * FROM all_report");

/*    echo '<h1 style="text-align: center">Таблица отчетов</h1>';

    show_table($fill_table);*/
}
