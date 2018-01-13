<?php
/*
  Редактирование страницы  Мета
  Author:  alexdzyaba
  Version: 1.0
 */
 
function all_meta () {
// Зарегистрирован ли пользователь
	if ( is_user_logged_in() ) {
		if( current_user_can('edit_pages') ) {
			global $wpdb, $is_iphone;
/*			
	// Замена адреса сайта в wp_options wp_posts wp_postmeta all_hotels all_region_list all_region_list_edit
			$results_list = $wpdb->get_results( "SELECT * FROM all_region_list " );
			for ($i = 0; $i < count($results_list); $i++) {
				if (strpos($results_list[$i]->list_row_title, 'all.t.zp.ua')) {
					$new_row = str_replace('all.t.zp.ua', 'hotels.t.zp.ua', $results_list[$i]->list_row_title);
					$wpdb->update ( 'all_region_list', array('list_row_title' => $new_row), array ('list_row_id' => $results_list[$i]->list_row_id) );
				}
			}
	
			$results_list = $wpdb->get_results( "SELECT * FROM all_region_list_edit " );
			for ($i = 0; $i < count($results_list); $i++) {
				if (strpos($results_list[$i]->list_row_title, 'all.t.zp.ua')) {
					$new_row = str_replace('all.t.zp.ua', 'hotels.t.zp.ua', $results_list[$i]->list_row_title);
					$wpdb->update ( 'all_region_list_edit', array('list_row_title' => $new_row), array ('list_row_id' => $results_list[$i]->list_row_id) );
				}
			}
			
			$results_list = $wpdb->get_results( "SELECT * FROM all_hotels " );
			for ($i = 0; $i < count($results_list); $i++) {
				if (strpos($results_list[$i]->hotel_main_image, 'all.t.zp.ua')) {
					$new_row = str_replace('all.t.zp.ua', 'hotels.t.zp.ua', $results_list[$i]->hotel_main_image);
					$wpdb->update ( 'all_hotels', array('hotel_main_image' => $new_row), array ('hotel_id' => $results_list[$i]->hotel_id) );
				}
			}
			for ($i = 0; $i < count($results_list); $i++) {
				if (strpos($results_list[$i]->hotel_slide_images, 'all.t.zp.ua')) {
					$new_row = str_replace('all.t.zp.ua', 'hotels.t.zp.ua', $results_list[$i]->hotel_slide_images);
					$wpdb->update ( 'all_hotels', array('hotel_slide_images' => $new_row), array ('hotel_id' => $results_list[$i]->hotel_id) );
				}
			}
			for ($i = 0; $i < count($results_list); $i++) {
				if (strpos($results_list[$i]->hotel_gif_url, 'all.t.zp.ua')) {
					$new_row = str_replace('all.t.zp.ua', 'hotels.t.zp.ua', $results_list[$i]->hotel_gif_url);
					$wpdb->update ( 'all_hotels', array('hotel_gif_url' => $new_row), array ('hotel_id' => $results_list[$i]->hotel_id) );
				}
			}
			for ($i = 0; $i < count($results_list); $i++) {
				if (strpos($results_list[$i]->hotel_print_images, 'all.t.zp.ua')) {
					$new_row = str_replace('all.t.zp.ua', 'hotels.t.zp.ua', $results_list[$i]->hotel_print_images);
					$wpdb->update ( 'all_hotels', array('hotel_print_images' => $new_row), array ('hotel_id' => $results_list[$i]->hotel_id) );
				}
			}
			
			$results_list = $wpdb->get_results( "SELECT * FROM wp_postmeta " );
			for ($i = 0; $i < count($results_list); $i++) {
				if (strpos($results_list[$i]->meta_value, 'all.t.zp.ua')) {
					$new_row = str_replace('all.t.zp.ua', 'hotels.t.zp.ua', $results_list[$i]->meta_value);
					$wpdb->update ( 'wp_postmeta', array('meta_value' => $new_row), array ('meta_id' => $results_list[$i]->meta_id) );
				}
			}
			
			$results_list = $wpdb->get_results( "SELECT * FROM wp_posts " );
			for ($i = 0; $i < count($results_list); $i++) {
				if (strpos($results_list[$i]->guid, 'all.t.zp.ua')) {
					$new_row = str_replace('all.t.zp.ua', 'hotels.t.zp.ua', $results_list[$i]->guid);
					$wpdb->update ( 'wp_posts', array('guid' => $new_row), array ('ID' => $results_list[$i]->ID) );
				}
			}
			$results_list = $wpdb->get_results( "SELECT * FROM wp_posts " );
			for ($i = 0; $i < count($results_list); $i++) {
				if (strpos($results_list[$i]->post_content, 'all.t.zp.ua')) {
					$new_row = str_replace('all.t.zp.ua', 'hotels.t.zp.ua', $results_list[$i]->post_content);
					$wpdb->update ( 'wp_posts', array('post_content' => $new_row), array ('ID' => $results_list[$i]->ID) );
				}
			}
			
			$results_list = $wpdb->get_results( "SELECT * FROM wp_options " );
			for ($i = 0; $i < count($results_list); $i++) {
				if (strpos($results_list[$i]->option_value, 'all.t.zp.ua')) {
					$new_row = str_replace('all.t.zp.ua', 'hotels.t.zp.ua', $results_list[$i]->option_value);
					$wpdb->update ( 'wp_options', array('option_value' => $new_row), array ('ID' => $results_list[$i]->ID) );
				}
			}
*/

	// Если изменен список Email
			if ($_POST['emails_save']) {
				$updated = $wpdb->update ( 'all_meta', array('meta_volue' => $_POST['request_email']), array ('meta_type' => 'request_email') );
				echo '<p style="color: blue;">Список Email для запросов изменен</p>';
			}
	// Если введено название новой страны, региона или копии
			if ($_POST['new_region']) {
		// Новая страна
				if ($_GET['action'] == add_country) {
					$wpdb->insert('all_countries',
						array(
							'country_name' => $_POST['new_region']
						)
					);
					$last_counyty = $wpdb->get_row( "SELECT country_id FROM all_countries ORDER BY country_id DESC LIMIT 1" );
					$wpdb->insert('all_regions',
						array(
							'region_name' => $_POST['new_region'],
							'region_double' => FALSE,
							'country_id' => $last_counyty->country_id,
							'region_main' => TRUE
						)
					);
				}
		// Новый регион
				if ($_GET['action'] == add_slave) {
					$wpdb->insert('all_regions',
						array(
							'region_name' => $_POST['new_region'],
							'region_double' => FALSE,
							'country_id' => $_GET['country_id'],
							'region_main' => FALSE
						)
					);
				}
		// Новая копия
				if ($_GET['action'] == add_double) {
					$wpdb->insert('all_regions',
						array(
							'region_name' => $_POST['new_region'],
							'region_double' => TRUE,
							'country_id' => $_GET['country_id'],
							'region_main' => FALSE
						)
					);
				}
			}
	// Если введен новый город выезда
			if ($_POST['new_meta_from']) {
				$wpdb->insert('all_from_city',
					array(
						'from_city_name' => $_POST['new_meta_from']
					)
				);
				echo '<p><span style="color: blue;">Информация о новом городе выезда добавлена</span></p>';
				$action_end = 1;
			}
			if (!$action_end) {
	// Если нажата кнопка Добавить страну. регион или копию, интерфейс
				if ($_GET['action'] == add_country || $_GET['action'] == add_slave || $_GET['action'] == add_double) {
					echo '<div style="display: inline-block;">
								<form action="" method="post">
								<div style="display: inline-block; min-width: 400px;">
									<input type="text" name="new_region" placeholder="';
									if ($_GET['action'] == add_country ) {
										echo 'Новая страна">';
									}
									elseif ($_GET['action'] == add_slave) {
										echo 'Новый регион">';
									}
									else {
										echo 'Новая копия">';
									}
								echo '</div>
								<div style="display: inline-block;">
									<button type="submit" class="btn_mail_green">Сохранить</button></td>
								</div>
							</form>
						</div>
						<div style="display: inline-block;">
							<form action="" method="get">
									<button type="submit" class="btn_mail_brown">Не сохранять</button>
							</form>
						</div></br></br>';
				}
	// Если нажата кнопка Добавить город выезда
				if ($_GET['action'] == add_from) {
					echo '<div style="display: inline-block;">
							<form action="" method="post">
								<div style="display: inline-block; min-width: 400px;">
									<input type="text" name="new_meta_from" placeholder="Новый город выезда">
								</div>
								<div style="display: inline-block;">
									<button type="submit" class="btn_mail_green">Сохранить</button>
								</div>
							</form>
						</div>
						<div style="display: inline-block;">
							<form action="" method="get">
									<button type="submit" class="btn_mail_brown">Не сохранять</button>
							</form>
						</div></br></br>';
				}
			}
			echo '<span style="color: red;"><strong>Добавляя, проверяйте внимательно. Редактировать нельзя!</strong></span></br></br>';
	// Таблица стран и регионов
			$countries = $wpdb->get_results( "SELECT * FROM all_countries ORDER BY country_name ASC" );
			echo '<div style="display: inline-block; min-width: 80%;"><table class="regions">';
				for ($i = 0; $i < count($countries); $i++) {
					echo '<tr class="regions">
							<td class="regions_country">';
								$country_name = $countries[$i]->country_name;
								$country_id = $countries[$i]->country_id;
								$region_main = $wpdb->get_row( "SELECT region_id FROM all_regions WHERE country_id = $country_id AND region_main = TRUE" );
								echo '<a href="/hotel-list/?region_id='.$region_main->region_id.'" style="color: black;">'.$country_name.'</a>
							</td>
							<td>';
								$double_regions = $wpdb->get_results( "SELECT * FROM all_regions WHERE country_id = $country_id AND region_double = TRUE ORDER BY region_main ASC" );
								for ($k = 0; $k < count($double_regions); $k++) {
									echo '<a href="/hotel-list/?region_id='.$double_regions[$k]->region_id.'" style="color: green;">'.$double_regions[$k]->region_name.'</a>&nbsp;&nbsp;&nbsp;&nbsp;';
								}
								$slave_regions = $wpdb->get_results( "SELECT * FROM all_regions WHERE country_id = $country_id AND region_double = FALSE AND region_main = FALSE ORDER BY region_name ASC" );
								for ($k = 0; $k < count($slave_regions); $k++) {
									echo '<a href="/hotel-list/?region_id='.$slave_regions[$k]->region_id.'" style="color: blue;">'.$slave_regions[$k]->region_name.'</a>&nbsp;&nbsp;&nbsp;&nbsp;';
								}
							echo '</td>
							<td class="regions_link"><a href="/?action=add_slave&country_id='.$country_id.'" style="color: blue;">добавить регион</a></td>
							<td class="regions_link"><a href="/?action=add_double&country_id='.$country_id.'" style="color: green;">добавить копию</a></td>
						</tr>';
				}
			echo '</table>
				  <a href="/?action=add_country" style="color: red;">Добавить страну</a></br>
				  </div>';
	// Откуда Список и Форма
			$from_cities = $wpdb->get_results( "SELECT from_city_name FROM all_from_city ORDER BY from_city_name ASC;" );
			echo '<div style="display: inline-block; vertical-align: top; margin-left: 30px; border-left: 1px solid #777777; padding-left: 30px;">';
			for ($i = 0; $i < count($from_cities); $i++) {
				echo $from_cities[$i]->from_city_name.'</br>';
			}
			echo '</br><a href="/?action=add_from" style="color: red;">Добавить</a>
				  </div>';
	// Список Email для получения запросов
			$request_email = $wpdb->get_row( "SELECT meta_volue FROM all_meta WHERE meta_type = 'request_email'" );
			echo '</br></br><p style="text-align: left; font-size: 40%; border-bottom: 1px solid #DDD;">&nbsp;</p>
				  <p style="color: blue;"><strong>Список Email для получения запросов</strong></p>
				  <form action="" method="post">
					<input type="text" name="request_email" value="'.$request_email->meta_volue.'"></br>
					<button type="submit" name="emails_save" value=1 class="btn_mail_green">Сохранить</button></td>
				  </form>';
		}
	}
}
?>

