<?php
/*
  Таблица всех отелей. Если запрос на добавление отеля GET region_id
  Author:  alexdzyaba
  Version: 1.0
 */

function hotels_table () {
// Зарегистрирован ли пользователь
	if ( is_user_logged_in() ) {
		global $wpdb, $is_iphone;	
		if ($_GET['region_id']) {
			$region_id = $_GET['region_id'];
		}
		if ($_GET['select_region_id']) {
			$select_region_id = $_GET['select_region_id'];
		}	
// Если нет запроса на выборку или это Горящие туры
		if ($select_region_id < 2) {
			echo '<p><big>Таблица отелей</strong></big></p>';
			$hotels_data = object_to_array($wpdb->get_results( "SELECT * FROM all_hotels ORDER BY hotel_id DESC" ));
		}
// Если есть запрос на выборку
		else {
			$select_region = $wpdb->get_row( "SELECT * FROM all_regions WHERE region_id = $select_region_id" );
			echo '<p><big>Таблица отелей. <span style="color: blue;">'.$select_region->region_name.'</span></big></p>';
	// Если регион - не страна и не дубль (например, Хургада)
			if (!$select_region->region_main && !$select_region->region_double) {
				$hotels_data = object_to_array($wpdb->get_results( "SELECT * FROM all_hotels WHERE region_id = $select_region_id ORDER BY hotel_id DESC" ));
			}
			else {
		// Если регион - страна или копия (например, Египет или Египет из Киева)
				$current_country_id = $select_region->country_id;
				$regions_id = $wpdb->get_results( "SELECT region_id FROM all_regions WHERE country_id = $current_country_id AND region_double = FALSE" );
		// Выюорка отелей по основному региону (например, Египет)
				$current_region_id = $regions_id[0]->region_id;
				$hotels_data = object_to_array($wpdb->get_results( "SELECT * FROM all_hotels WHERE region_id = $current_region_id ORDER BY hotel_id DESC" ));
		// Ксли есть дочерние регионы (например, Хургада и Шарм-эль-Шейх)
				if (count($regions_id) > 1) {
					for ($i = 1; $i < count($regions_id); $i++) {
						$current_region_id = $regions_id[$i]->region_id;
						$hotels_region_data = object_to_array($wpdb->get_results( "SELECT * FROM all_hotels WHERE region_id = $current_region_id ORDER BY hotel_id DESC" ));
						$hotels_data = array_merge ($hotels_data, $hotels_region_data);
					}
				}
			}
		}
// Форма выбора региона и поиска
		echo '<div>';
			if ($region_id < 2) {
				$all_countries = $wpdb->get_results( "SELECT * FROM all_regions WHERE region_main = TRUE ORDER BY region_name ASC" );
				echo '<div class="region_select">
						<form action="" method="get">
							<select size="20" multiple name="select_region_id">
								<option value=0>== Все ==</option>';
								for ($i = 0; $i < count($all_countries); $i++) {
									echo '<option value='.$all_countries[$i]->region_id.'>'.$all_countries[$i]->region_name.'</option>';
								}
							echo '</select>
							<input type="hidden" name="region_id" value="'.$region_id.'">
							<button type="submit" class="btn_mail_white" style="display: inline; vertical-align: top; color: #333333;">Выбрать</button>
						</form>
					</div>';
			}
			echo '<div class="hotel_search">
					<form action="" method="post">
						<input type="text" name="hotel_search" value="" placeholder="Название отеля" style="vertical-align: top;">
					</form>
				</div>
		</div>';
	// ВЫВОД ДЛЯ ДЕСКТОПОВ
		if(!$is_iphone) {
			echo '<p>&nbsp;</p>
				  <table class="hotels">
					<tbody>';
					for ($i = 0; $i < count($hotels_data); $i++) {
		// Формируем данные для таблицы
						$hotel_region_id = $hotels_data[$i][region_id];
						$hotel_region = $wpdb->get_row( "SELECT region_name FROM all_regions WHERE region_id = $hotel_region_id" );
						$current_hotel_id = $hotels_data[$i][hotel_id];
						$current_list = $wpdb->get_row( "SELECT list_row_id FROM all_region_list_edit WHERE region_id = $region_id AND list_hotel_id = $current_hotel_id" );
		// Если не нужна выборка или отель еще не в списке
						if (!$region_id || !$current_list) {
							echo '<tr class="hotels search_visible">
								<td class="hotels_title search_visible_data">'.$hotels_data[$i][hotel_title].'</td>
								<td class="hotels_region">'.$hotel_region->region_name.'</td>
								<td class="hotels_stars"><span style="color: brown;">'.$hotels_data[$i][hotel_stars].'&#9733;</span></td>
								<td class="hotels_note">'.$hotels_data[$i][hotel_note].'</td>
								<td class="hotels_link"><a href="http://t.zp.ua/hotel-test/?hotel_id='.$hotels_data[$i][hotel_id].'"  target="_blank" style="color: green;">На сайте</a></td>';
								if ($region_id) {
									echo '<td class="hotels_link"><a href="/hotel-list/?action=select&region_id='.$region_id.'&hotel_id='.$hotels_data[$i][hotel_id].'">Выбрать</a></td>';
								}
								else {
									echo '<td class="hotels_link"><a href="/hotel-edit/?action=edit&hotel_id='.$hotels_data[$i][hotel_id].'"><span style="color: blue;">Редактор</span></a></td>
										  <td class="hotels_link"><a href="/hotel-edit/?action=copy&hotel_id='.$hotels_data[$i][hotel_id].'">Копировать</a></td>
										  <td class="hotels_link"><a href="'.$hotels_data[$i][hotel_gif_url].'" target="_blank" style="color: green;">Preview GIF</a></td>';
								}
							echo '</tr>';
						}
					}
				echo '</tbody>
				  </table>';
		}
	// ВЫВОД ДЛЯ iPhone
		else {
			echo '<table>';
			for ($i = 0; $i < count($hotels_data); $i++) {
				$hotel_region_id = $hotels_data[$i][region_id];
				$hotel_region = object_to_array($wpdb->get_row( "SELECT meta_volue FROM all_meta WHERE meta_id = $hotel_region_id" ));
				$current_hotel_id = $hotels_data[$i][hotel_id];
				$current_list = object_to_array($wpdb->get_row( "SELECT * FROM all_region_list_edit WHERE region_id = $region_id AND list_hotel_id = $current_hotel_id" ));
		// Если режим выбора отеля и текущего отеля нет в текущем листе
				if ($action == 'select' && !$current_list) {
					echo '<tr class="search_visible" style="border-color: #FFFFFF;"><td>
							<a href="/hotel-list/?action=select&region_id='.$region_id.'&hotel_id='.$hotels_data[$i][hotel_id].'">
								<div class="iphone_hotels_select">
									<div class="iphone_hotel_name search_visible_data"><strong>'.$hotels_data[$i][hotel_title].'&nbsp;&nbsp;<span style="color: brown;">'.$hotels_data[$i][hotel_stars].'&#9733;</span></strong></div>
									'.$hotels_data[$i][hotel_note].'
								</div>
							</a>
					</td></tr>';
				}
		// Если режим просмотра таблицы
				if ($action != 'select') {
					echo '<tr class="search_visible" style="border-color: #FFFFFF;"><td>
							<div class="iphone_hotels_noselect">
								<div class="iphone_hotel_name search_visible_data"><strong>'.$hotels_data[$i][hotel_title].'&nbsp;&nbsp;<span style="color: brown;">'.$hotels_data[$i][hotel_stars].'&#9733;</span></strong></div>
								<span style="color: blue">'.$hotel_region[meta_volue].'</span></br>';
								if ($hotels_data[$i][hotel_note]) {
									echo $hotels_data[$i][hotel_note].'</br>';
								}
								echo '</br><big><a href="/hotel-edit/?action=edit&hotel_id='.$hotels_data[$i][hotel_id].'">Редактор</a>&nbsp;&nbsp;&nbsp;&nbsp;
								<span style="color: green"><a href="http://t.zp.ua/hotel-test/?hotel_id='.$hotels_data[$i][hotel_id].'"  target="_blank" style="color: green;">На_сайте</a></span>&nbsp;&nbsp;&nbsp;&nbsp;
								<a href="/hotel-edit/?action=copy&hotel_id='.$hotels_data[$i][hotel_id].'">Копировать</a>&nbsp;&nbsp;&nbsp;&nbsp;
								<a href="'.$hotels_data[$i][hotel_gif_url].'" target="_blank" style="color: green;">Preview GIF</a></big>
							</div>
					</td></tr>';
				}
			}
			echo '</table>';
		}
	}
}
?>