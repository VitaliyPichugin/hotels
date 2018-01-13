<?php
/*
  Перенос информации о запросах для CRM и HOTELS
  Author:  alexdzyaba
  Version: 1.0
 */
 
function all_temp () {
// Зарегистрирован ли пользователь
	if ( is_user_logged_in() ) {
		global $wpdb;
	// Если есть запрос на заполнение website_orders_info
		if ($_GET['hotels_return']) {
			if( !current_user_can('edit_pages') ) {
				echo '<p>У Вас нет прав на проведение данной операции</p>';
			}
			else {
				$hotels_old = $wpdb->get_results( "SELECT * FROM all_hotels_old ORDER BY hotel_id" );
				for ($i = 0; $i < count($hotels_old); $i++) {
					$hotel_id = $hotels_old[$i]->hotel_id;
					$region_id = $hotels_old[$i]->region_id;
					$hotel_title = $hotels_old[$i]->hotel_title;
					$hotel_stars = $hotels_old[$i]->hotel_stars;
					echo '___'.$hotel_id;
					$wpdb->update( 'all_hotels',
                                array(
                                    'region_id' => $region_id,
                                    'hotel_title' => $hotel_title,
                                    'hotel_stars' => $hotel_stars
                                ),
                                array ('hotel_id' => $hotel_id)
                            );
				}
			}
		}
	}
}
?>

