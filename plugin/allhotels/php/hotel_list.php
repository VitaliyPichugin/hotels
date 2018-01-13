<?php
/*
  Таблицы по направлениям GET region_id
  Author:  alexdzyaba
  Version: 1.0
 */
 
function hotel_list () {
// Зарегистрирован ли пользователь
	if ( is_user_logged_in() ) {
		global $wpdb, $is_iphone;
		if ($_GET['region_id']) {
			$region_id = $_GET['region_id'];
			if ($_GET['action']) {
				$current_action = $_GET['action'];
			}
	// Если не указаны никакие параметры (первый вход), создаем полную копию в базе редактирования
			if (!$current_action && !$_GET['list_id']) {
				$wpdb->delete( all_region_list_edit, array( 'region_id' => $region_id) );
				$current_region_lists = $wpdb->get_results( "SELECT * FROM all_region_list WHERE region_id = '$region_id'");
				for ($i = 0; $i < count($current_region_lists); $i++) {
					$wpdb->insert('all_region_list_edit',
						array(
							'row_type' => $current_region_lists[$i]->row_type,
							'region_id' => $current_region_lists[$i]->region_id,
							'list_hotel_id' => $current_region_lists[$i]->list_hotel_id,
							'list_number' => $current_region_lists[$i]->list_number,
							'from_city_id' => $current_region_lists[$i]->from_city_id,
							'list_days_long' => $current_region_lists[$i]->list_days_long,
							'list_start_date' => $current_region_lists[$i]->list_start_date,
							'meal_id' => $current_region_lists[$i]->meal_id,
							'price' => $current_region_lists[$i]->price,
							'currency_id' => $current_region_lists[$i]->currency_id,
							'list_row_title' => $current_region_lists[$i]->list_row_title
						)
					);
				}
			}
	// Текстовое название региона
			if ($_GET['hotel_id']) {
				$hotel_id = $_GET['hotel_id'];
			}
			$current_region = $wpdb->get_row( "SELECT region_name FROM all_regions WHERE region_id = $region_id" );
			echo '<big>Выборка отелей <span style="color: blue;">'.$current_region->region_name.'</span></strong></big>';
	// Если нажата кнопка Сохранить регион
			if ($_POST['region_save']) {
		// Удаляем из базы листов все листы с row_type не 'hotel' (не отели) и с HOTEL_ID, которых нет в базе редактирования листов
				$region_lists = $wpdb->get_results( "SELECT * FROM all_region_list WHERE region_id = '$region_id'");
				for ($i = 0; $i < count($region_lists); $i++) {
					$old_hotel_id = $region_lists[$i]->list_hotel_id;
					$old_list_id = $region_lists[$i]->list_row_id;
					$current_region_list_edit = $wpdb->get_row( "SELECT * FROM all_region_list_edit WHERE region_id = '$region_id' AND list_hotel_id = '$old_hotel_id'");
					if ($region_lists[$i]->row_type != 'hotel' || !$current_region_list_edit) {
						$wpdb->delete( all_region_list, array( 'list_row_id' => $old_list_id) );
					}
				}
		// Добавляем в базу листов из базы редактирования листов все листы с row_type не 'hotel' (не отели) и те, котрыхх в базе редактирования нет, те, которые есть, редактируем
				$region_lists_edit = $wpdb->get_results( "SELECT * FROM all_region_list_edit WHERE region_id = '$region_id'");
				for ($i = 0; $i < count($region_lists_edit); $i++) {
					$new_hotel_id = $region_lists_edit[$i]->list_hotel_id;
					$current_region_list = $wpdb->get_row( "SELECT list_row_id FROM all_region_list WHERE region_id = '$region_id' AND list_hotel_id = '$new_hotel_id'");
			// Это не отель или этого отеля в базе нет, добавляем
					if ($region_lists_edit[$i]->row_type != 'hotel' || !$current_region_list) {
						$wpdb->insert('all_region_list',
							array(
								'row_type' => $region_lists_edit[$i]->row_type,
								'region_id' => $region_lists_edit[$i]->region_id,
								'list_hotel_id' => $region_lists_edit[$i]->list_hotel_id,
								'list_number' => $region_lists_edit[$i]->list_number,
								'from_city_id' => $region_lists_edit[$i]->from_city_id,
								'list_days_long' => $region_lists_edit[$i]->list_days_long,
								'list_start_date' => $region_lists_edit[$i]->list_start_date,
								'meal_id' => $region_lists_edit[$i]->meal_id,
								'price' => $region_lists_edit[$i]->price,
								'currency_id' => $region_lists_edit[$i]->currency_id,
								'list_row_title' => $region_lists_edit[$i]->list_row_title
							)
						);
					}
			// Это отель и он в базе есть, редактируем
					else {
						$wpdb->update( 'all_region_list',
							array(
								'list_number' => $region_lists_edit[$i]->list_number,
								'from_city_id' => $region_lists_edit[$i]->from_city_id,
								'list_days_long' => $region_lists_edit[$i]->list_days_long,
								'list_start_date' => $region_lists_edit[$i]->list_start_date,
								'meal_id' => $region_lists_edit[$i]->meal_id,
								'price' => $region_lists_edit[$i]->price,
								'currency_id' => $region_lists_edit[$i]->currency_id,
								'list_row_title' => $region_lists_edit[$i]->list_row_title
							),
							array ('region_id' => $region_id, 'list_hotel_id' => $new_hotel_id)
						);
					}
				}
				$end_region = 1;
				$current_action = "";
			}
	// Если нажата Переместить вверх
			if ($current_action == 'up' && $_GET['list_number']) {
				$list_number = $_GET['list_number'];
				$wpdb->query("UPDATE all_region_list_edit SET list_number = 99999 WHERE region_id = $region_id AND list_number = $list_number - 1");
				$wpdb->query("UPDATE all_region_list_edit SET list_number = list_number-1 WHERE region_id = $region_id AND list_number = $list_number");
				$wpdb->query("UPDATE all_region_list_edit SET list_number = '$list_number' WHERE region_id = $region_id AND list_number = 99999");
			}
	// Если нажата Переместить вниз
			if ($current_action == 'down' && $_GET['list_number']) {
				$list_number = $_GET['list_number'];
				$wpdb->query("UPDATE all_region_list_edit SET list_number = 99999 WHERE region_id = $region_id AND list_number = $list_number + 1");
				$wpdb->query("UPDATE all_region_list_edit SET list_number = list_number+1 WHERE region_id = $region_id AND list_number = $list_number");
				$wpdb->query("UPDATE all_region_list_edit SET list_number = '$list_number' WHERE region_id = $region_id AND list_number = 99999");
			}
	// Если нажата Удалить
			if ($current_action == 'delete' && $_GET['list_number']) {
				$list_number = $_GET['list_number'];
				$current_list = $wpdb->get_results( "SELECT * FROM all_region_list_edit WHERE region_id = '$region_id' ORDER BY list_number ASC;" );
				$wpdb->delete( all_region_list_edit, array( 'region_id' => $region_id, 'list_number' => $list_number ) );
				for ($i = $list_number - 1; $i < count($current_list); $i++) {
					$next_hotel = $current_list[$i]->list_row_id;
					$wpdb->query("UPDATE all_region_list_edit SET list_number = list_number-1 WHERE list_row_id  = $next_hotel");
				}
			}
	// Если нажата Редактировать
			if ($current_action == 'edit' && $_GET['list_id']) {
				$list_id = $_GET['list_id'];
				$current_list = $wpdb->get_row( "SELECT * FROM all_region_list_edit WHERE list_row_id = $list_id" );
				$hotel_id = $current_list->list_hotel_id;
				$from_id = $current_list->from_city_id;
				$meal_id = $current_list->meal_id;
				$hotel_position = $current_list->list_number;
				$days_long = $current_list->list_days_long;
				$row_title = $current_list->list_row_title;
				$row_title = str_replace(array("\r\n","\r","\n","\\r","\\n","\\r\\n"), "<br/>", $row_title);
				$row_title = str_replace("<br/><br/>", "<br/>", $row_title);
				$row_title = str_replace("<br/><br/>", "<br/>", $row_title);
				$row_title = str_replace("<br/>&nbsp;<br/>", "<br/>", $row_title);
				$hotel_price = $current_list->price;
				$currency_id = $current_list->currency_id;
		// Если это отель
				if ($current_list->row_type == 'hotel') {
					$start_date_array = explode('-', $current_list->list_start_date);
					$start_date_value = $start_date_array[1].'/'.$start_date_array[2].'/'.$start_date_array[0];
					$action_edit = 1;
				}
				else {
			// Если подзаголовок
					if ($current_list->row_type == 'head') {
						$action_heading_edit = 1;
					}
			// Если якорь		
					else {
						$action_anchor_edit = 1;
					}
				}
			}
	// Если нажата кнопка Сохранить  лист, подзаголовок или якорь
			if ($_POST['list_save'] || $_POST['heading_save'] || $_POST['anchor_save']) {
				$from_id = 1;
				$meal_id = 0;
				$days_long = 1;
				$hotel_price = 1;
				$currency_id = 0;
				$start_date = '2001-01-01';
		// Если Сохранить подзаголовок
				if ($_POST['heading_save']) {
					$row_title = str_replace('\"', '"', $_POST['heading']);
					$row_title = str_replace("\'", "'", $row_title);
					$row_title = str_replace(array("\r\n","\r","\n","\\r","\\n","\\r\\n"),"<br/>",$row_title);
					$row_title = str_replace("<br/><br/>", "<br/>", $row_title);
					$row_title = str_replace("<br/><br/>", "<br/>", $row_title);
					$row_title = str_replace("<br/>&nbsp;<br/>", "<br/>", $row_title);
					$hotel_position = $_POST['hotel_position'];
					$row_type = 'head';
					$hotel_id = 0;
				}
		// Если Сохранить якорь
				elseif ($_POST['anchor_save']) {
					$row_title = $_POST['anchor'];
					$hotel_position = $_POST['hotel_position'];
					$row_type = 'anchor';
					$hotel_id = 0;
				}
		// Если сохранить лист
				else {
					$from_id = (int)$_POST['from_id'];
					$meal_id = (int)$_POST['meal_id'];
					$row_title = $_POST['row_title'];
					$hotel_position = (int)$_POST['hotel_position'];
					$row_type = 'hotel';
					$days_long = (int)$_POST['days_long'];
					$hotel_price = (int)$_POST['hotel_price'];
					$currency_id = (int)$_POST['currency_id'];
					if ($_POST['start_date']) {
						$start_date_array = explode('/', $_POST['start_date']);
						$start_date = $start_date_array[2].'-'.$start_date_array[0].'-'.$start_date_array[1];
					}
				}
		// Если все поля введены
				if ($from_id && $row_title && $start_date && $days_long && $hotel_price) {
			// Если режим ввода нового отеля, заголовка или якоря
					if ($current_action == 'select' || $current_action == 'add_heading' || $current_action == 'add_anchor') {
				// Считываем лист по текущему региону
						$current_list = $wpdb->get_results( "SELECT * FROM all_region_list_edit WHERE region_id = '$region_id' ORDER BY list_number ASC;" );
				// Если введенный номер позиции больше последнего, то добавляется последняя строка
						if (count($current_list) < $hotel_position) {
							$hotel_position = count($current_list) + 1;
						}
				// Если добавляется не последняя строка перенумеровываем все листы, имеющие больший или тот же номер
						else {
							for ($i = $hotel_position - 1; $i < count($current_list); $i++) {
								$next_hotel = $current_list[$i]->list_row_id;
								$wpdb->query("UPDATE all_region_list_edit SET list_number = list_number+1 WHERE list_row_id = $next_hotel");
							}
						}
				// Сохраняем новый лист
						$wpdb->insert('all_region_list_edit',
							array(
								'row_type' => $row_type,
								'region_id' => $region_id,
								'list_hotel_id' => $hotel_id,
								'list_number' => $hotel_position,
								'from_city_id' => $from_id,
								'list_days_long' => $days_long,
								'list_start_date' => $start_date,
								'meal_id' => $meal_id,
								'price' => $hotel_price,
								'currency_id' => $currency_id,
								'list_row_title' => $row_title
							)
						);
						$current_hotel = $wpdb->get_row( "SELECT hotel_title FROM all_hotels WHERE hotel_id = $hotel_id" );
						echo '<p><span style="color: blue;">'.$current_hotel->hotel_title.'</span> добавлен в список</p>';
						$end_select = 1;
					}
			// Если режим редактирования
					if ($current_action == 'edit') {
				// Сравниваем новый и старый номера позиций
						$old_number = $wpdb->get_row( "SELECT list_number FROM all_region_list_edit WHERE list_row_id = $list_id" );
						$old_position = $old_number->list_number;
						$hotel_position = $_POST['hotel_position'];
				// Считываем лист по текущему региону
						$current_list = $wpdb->get_results( "SELECT * FROM all_region_list_edit WHERE region_id = '$region_id' ORDER BY list_number ASC;" );
					// Если введенный номер позиции больше последнего, то заменяется последняя строка
						if (count($current_list) < $hotel_position) {
							$hotel_position = count($current_list);
						}
					// если позиция изменилась в меньшую сторону
						if ($old_position > $hotel_position) {
							for ($i = $hotel_position - 1; $i < $old_position - 1; $i++) {
								$next_hotel = $current_list[$i]->list_row_id;
								$wpdb->query("UPDATE all_region_list_edit SET list_number = list_number+1 WHERE list_row_id = $next_hotel");
							}
						}
					// если позиция изменилась в большую сторону
						if ($old_position < $hotel_position) {
							for ($i = $old_position - 1; $i < $hotel_position; $i++) {
								$next_hotel = $current_list[$i]->list_row_id;
								$wpdb->query("UPDATE all_region_list_edit SET list_number = list_number-1 WHERE list_row_id = $next_hotel");
							}
						}
					// Записываем новые данные
						$wpdb->update( 'all_region_list_edit',
							array(
								'list_number' => $hotel_position,
								'from_city_id' => $from_id,
								'list_days_long' => $days_long,
								'list_start_date' => $start_date,
								'meal_id' => $meal_id,
								'price' => $hotel_price,
								'currency_id' => $currency_id,
								'list_row_title' => $row_title
							),
							array('list_row_id' => $list_id)
						);
					// Выводим сообщение
						$current_hotel = $wpdb->get_row( "SELECT hotel_title FROM all_hotels WHERE hotel_id = $hotel_id" );
						echo '<p>Данные <span style="color: blue;">'.$current_hotel->hotel_title.'</span> изменены</p>';
						$end_edit = 1;
					}
				}
		// Если не все поля введены
				else {
					echo '<p style="color: red;">Не все поля введены!</p>';
		// Изначальный формат даты для value и ставим признак Ошибка записи
					$start_date_value =$_POST['start_date'];
					$save_error = 1;
				}
			}
		// Если нажата кнопка Удалить отмеченные
			if ($_POST['delete_checked']) {
				$current_list = $wpdb->get_results( "SELECT list_number FROM all_region_list_edit WHERE region_id = '$region_id' ORDER BY list_number ASC;" );
			// Удаляем все отмеченные строчки
				for ($list_number = 1; $list_number <= count($current_list); $list_number++) {
					if ($_POST['delete'.$list_number]) {
						$wpdb->delete( all_region_list_edit, array( 'region_id' => $region_id, 'list_number' => $list_number ) );
					}
				}
			// Восстанавливаем нумерацию
				$current_delete_list = $wpdb->get_results( "SELECT list_row_id FROM all_region_list_edit WHERE region_id = '$region_id' ORDER BY list_number ASC;" );
				for ($i = 0; $i < count($current_delete_list); $i++) {
					$curremt_num = $i+1;
					$curremt_list_id = $current_delete_list[$i]->list_row_id;
					$wpdb->query("UPDATE all_region_list_edit SET list_number = '$curremt_num' WHERE list_row_id  = $curremt_list_id");
				}
				$delete_checked_save = 1;
			}
		// Если вносятся изменения и внесение не завершено, выводим кнопки
			if ( $current_action && ($current_action != 'add_heading' || $_POST['heading_save']) && ($current_action != 'edit' || $end_edit) && ($current_action != 'select' ||  $end_select) && ($current_action != 'add_anchor' || $_POST['anchor_save']) || $delete_checked_save) {
				$edit_list = 1;
				echo '</br><div style="display: inline-block; margin-right: 20px;">
						<form action="" method="post">
							<button type="submit" name="region_save" value=1 class="btn_mail_green">Сохранить изменения
						</form>
					  </div>
					  <div style="display: inline-block;">
						<form action="" method="get">
							<button type="submit" name="region_id" value="'.$region_id.'" class="btn_mail_brown">Не сохранять выйти
						</form>
					  </div>';
			}
			else {
				$edit_list = 0;
			}
// Вывод интерфейса и таблицы
			echo '<form action="" method="post">';
	// ВЫВОД ДЛЯ ДЕСКТОПОВ
			if(!$is_iphone) {
		// Если был выбран отель на странице /hotels/, и при этом не был сохранен или нажата Редактировать, открываем интерфейс добавления отеля в таблицу
				if ( (($current_action == 'select' && $_GET['hotel_id'] && !$_POST['list_save']) || $save_error || ($action_edit && !$end_edit)) && !$end_region) {
					$new_hotel = $wpdb->get_row( "SELECT hotel_title, hotel_stars FROM all_hotels WHERE hotel_id = $hotel_id" );
					echo '<div style="display: inline-block; margin-right: 40px;">Выбран отель <span style="color: brown;">'.$new_hotel->hotel_title.'&nbsp;&nbsp;'.$new_hotel->hotel_stars.'&#9733;</span></div>';
					$from_cities = $wpdb->get_results( "SELECT * FROM all_from_city ORDER BY from_city_name ASC;" );
					echo '<div style="display: inline-block;">';
			// Откуда
					for ($i = 0; $i < count($from_cities); $i++) {
						if ($from_cities[$i]->from_city_id == $from_id) {
							echo '<input type="radio" name="from_id" value="'.$from_cities[$i]->from_city_id.'" checked>&nbsp;'.$from_cities[$i]->from_city_name.'&nbsp;&nbsp;&nbsp;&nbsp;';
						}
						else {
							echo '<input type="radio" name="from_id" value="'.$from_cities[$i]->from_city_id.'">&nbsp;'.$from_cities[$i]->from_city_name.'&nbsp;&nbsp;&nbsp;&nbsp;';
						}
					}
			// Заголовок
					if ($row_title) {
						$title_text = $row_title;
					}
					else {
						$title_text = $new_hotel->hotel_title;
						$title_text = substr($title_text, 0, 26);
					}
					echo '</div></br></br>
						  <div style="display: inline-block; margin-right: 40px;"><input type="text" name="row_title" value="'.$title_text.'" style="width: 700px;" maxlength="26" required></div>';
			// Позиция
					if (!$hotel_position) {
						$hotel_position = 1;
					}
					echo '<div style="display: inline-block;"><input type="number" name="hotel_position" min="1" value="'.$hotel_position.'" style="width: 110px;" required></div></br></br>';
			// Питание
					$all_meals = $wpdb->get_results( "SELECT * FROM all_meals ORDER BY meal_id" );
					if (!$meal_id) {
						$meal_id = $all_meals[0]->meal_id;
					}
					for ($i = 0; $i < count($all_meals); $i++) {
						if ($all_meals[$i]->meal_id == $meal_id ) {
							echo '<input type="radio" name="meal_id" value="'.$all_meals[$i]->meal_id.'" checked>'.$all_meals[$i]->meal_name.'&nbsp;&nbsp;&nbsp;&nbsp;';
						}
						else {
							echo '<input type="radio" name="meal_id" value="'.$all_meals[$i]->meal_id.'">'.$all_meals[$i]->meal_name.'&nbsp;&nbsp;&nbsp;&nbsp;';
						}	
					}
			// Дата, длительность, цена, кнопка Сохранить
					$all_currency = $wpdb->get_results( "SELECT currency_symbol FROM all_currency" );
					echo '</br></br><div style="display: inline-block; margin-right: 40px;"><input type="text" id="datepicker" name="start_date" placeholder="Дата" value="'.$start_date_value.'" style="width: 110px;" required></div>
						  <div style="display: inline-block; margin-right: 40px;"><input type="number" name="days_long" min="1" max="20" placeholder="Дней" value="'.$days_long.'" style="width: 100px;" required></div>
						  <div style="display: inline-block; margin-right: 40px;"><input type="text" name="hotel_price" placeholder="Стоимость" value="'.$hotel_price.'" style="width: 120px;" required></div>
						  <div style="display: inline-block; margin-right: 40px;">
							<input type="radio" name="currency_id" value=1 checked>'.$all_currency[0]->currency_symbol;
							for ($i = 1; $i < count($all_currency); $i++) {
								echo '&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="currency_id" value="'.$all_currency[$i]->currency_id.'">'.$all_currency[$i]->currency_symbol;
							}
					echo '</div>
						  <div style="display: inline-block; vertical-align: top;"><button type="submit" name="list_save" value=1 class="btn_mail_green">Сохранить</button></div>';
				}
		// Если вводится подзаголовок, якорь или ничего
				else {
					if ( (($current_action == 'add_anchor' && !$_POST['anchor_save'])  || ($action_anchor_edit && !$end_edit)) && !$end_region ) {
						echo '<p><span style="color: blue;">Редактирование якоря</span></p>';
			// Ввод якоря
						echo '<div style="display: inline-block;">Маленькие английские буквы без пробелов </div>
							  <div style="display: inline-block; min-width: 250px;"><input type="text" name="anchor" value="'.$row_title.'" required></div>';
			// Позиция
						if (!$hotel_position) {
							$hotel_position = 1;
						}
						echo '<div style="display: inline-block; margin: 0 20px;"><input type="number" name="hotel_position" min="1" value="'.$hotel_position.'" style="width: 110px;"></div>';
						echo '<div style="display: inline-block; vertical-align: top;"><button type="submit" name="anchor_save" value=1 class="btn_mail_green">Сохранить</button></div>';
					}
					elseif ( (($current_action == 'add_heading' && !$_POST['heading_save']) || $save_error || ($action_heading_edit && !$end_edit)) && !$end_region ) {
						echo '<p><span style="color: blue;">Редактирование заголовка</span></p>';
			// Ввод подзаголовка
						wp_editor($row_title, 'editor', array(
							'wpautop'       => 1,
							'media_buttons' => 1,
							'textarea_name' => 'heading',
							'textarea_rows' => 3,
							'tabindex'      => null,
							'editor_css'    => '',
							'editor_class'  => '',
							'teeny'         => 0,
							'dfw'           => 0,
							'tinymce'       => 1,
							'quicktags'     => 1,
							'drag_drop_upload' => false
						) );
			// Позиция
						if (!$hotel_position) {
							$hotel_position = 1;
						}
						echo '<div style="display: inline-block;"><input type="number" name="hotel_position" min="1" value="'.$hotel_position.'" style="width: 110px;"></div>';
						echo '<div style="display: inline-block; vertical-align: top; margin-left: 20px;"><button type="submit" name="heading_save" value=1 class="btn_mail_green">Сохранить</button></div>';
					}
		// Если новый отель не выбирался, ошибки при вводе не было, не редактируется лист, не вводится и не радактируется подзаголовок, выводим ссылки Добавить отель, Добавить подзаголовок, Добавить якорь и На сайте
					else {
						echo '<a href="/hotels/?region_id='.$region_id.'&select_region_id='.$region_id.'" style="color: blue;">Добавить отель</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							  <a href="/hotel-list/?action=add_heading&region_id='.$region_id.'">Добавить подзаголовок</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							  <a href="/hotel-list/?action=add_anchor&region_id='.$region_id.'">Добавить якорь</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							  <a href="http://t.zp.ua/hotel-test/?region_id='.$region_id.'&edit_list='.$edit_list.'" target="_blank" style="color: green;">На сайте</a>';
					}
				}
		// Кнопка Удадить отмеченные
				echo '<p style="text-align: right;"><button type="submit" name="delete_checked" value=1 class="btn_mail_text" style="color: red;">Удалить отмеченные</button></p>';
		// Считываем лист по текущему региону  и выводим список в таблицу
				$current_list = $wpdb->get_results( "SELECT * FROM all_region_list_edit WHERE region_id = '$region_id' ORDER BY list_number ASC;" );
				echo '<table class="list">
					<tbody>';
					for ($i = 0; $i < count($current_list); $i++) {
						$current_hotel_id = $current_list[$i]->list_hotel_id;
			// Если текущий лист - отель
						if ($current_list[$i]->row_type == 'hotel') {
							$current_hotel = $wpdb->get_row( "SELECT * FROM all_hotels WHERE hotel_id = $current_hotel_id" );
							echo '<tr class="list">
								<td class="list_checkbox"><input type="checkbox" name="delete'.$current_list[$i]->list_number.'" value=1></td>
								<td class="list_number">'.$current_list[$i]->list_number.'</td>
								<td class="list_title">'.$current_hotel->hotel_title.'</td>
								<td class="list_stars"><span style="color: brown;">'.$current_hotel->hotel_stars.'&#9733;</span></td>';
				// Дата цвет в зависимости от даты Красный - просрочено Зеленый - не просрочено и не сегодня и Цена
								$current_date_array = explode('-', current_time('mysql', false));
								$current_date = $current_date_array[0] * 10000 + $current_date_array[1] * 100 + $current_date_array[2];
								$list_date_array = explode('-', $current_list[$i]->list_start_date);
								$list_date = $list_date_array[0] * 10000 + $list_date_array[1] * 100 + $list_date_array[2];
								if ($list_date > $current_date) {
									echo '<td class="list_date" style="color: green;">';
								}
								elseif ($list_date < $current_date) {
									echo '<td class="list_date" style="color: red;">';
								}
								else {
									echo '<td class="list_date">';
								}
								echo $current_list[$i]->list_start_date.'</td>
									<td class="list_days">'.$current_list[$i]->list_days_long.' дней</td>
									<td class="list_price">'.$current_list[$i]->price.' ';
										$currency_id = $current_list[$i]->currency_id;
										$currency = $wpdb->get_row( "SELECT currency_symbol FROM all_currency WHERE currency_id = $currency_id" );
										echo $currency->currency_symbol.'</td>
									<td class="list_note">'.$current_hotel->hotel_note.'</td>';
				// Откуда
								$from_city_id = $current_list[$i]->from_city_id;
								$from_city = $wpdb->get_row( "SELECT from_city_name FROM all_from_city WHERE from_city_id = $from_city_id" );
								echo '<td class="list_from">'.$from_city_id->from_city_name.'</td>';
						}
			// Если текущий лист - не отель
						else {
							echo '<tr class="list">
									<td class="list_checkbox"><input type="checkbox" name="delete'.$current_list[$i]->list_number.'" value=1></td>
									<td class="list_number">'.$current_list[$i]->list_number.'</td>';
				// Это подзаголовок
							if ($current_list[$i]->row_type == 'head') {
								echo '<td colspan="7">&nbsp;&nbsp;&nbsp;'.$current_list[$i]->list_row_title.' </td>';
							}
				// Это якорь
							else {
								echo '<td colspan="7" style="color: green; text-align: right;"><em>'.$current_list[$i]->list_row_title.' </em></td>';
							}
									
						}
						echo '<td class="list_link"><a style="color: red;" href="/hotel-list/?region_id='.$region_id.'&action=delete&list_number='.$current_list[$i]->list_number.'">Удалить</a></td>';
			// Вверх Вниз
							if ($i) {
								echo '<td class="list_link"><a href="/hotel-list/?region_id='.$region_id.'&action=up&list_number='.$current_list[$i]->list_number.'">&uarr; Вверх &uarr;</a></td>';
							}
							else {
								echo '<td class="list_link"></td>';
							}
							if ($i < count($current_list) - 1) {
								echo '<td class="list_link"><a href="/hotel-list/?region_id='.$region_id.'&action=down&list_number='.$current_list[$i]->list_number.'">&darr; Вниз &darr;</a></td>';
							}
							else {
								echo '<td class="list_link"></td>';
							}
						echo '<td class="list_link"><a style="color: blue" href="/hotel-list/?region_id='.$region_id.'&action=edit&list_id='.$current_list[$i]->list_row_id.'">Редактop</a></td>
							  </tr>';
					}
				echo '</tbody>
				</table>';
			}
	// ВЫВОД ДЛЯ iPhone
			else {
		// Если был выбран отель на странице /hotels/, и при этом не был сохранен или нажата Редактировать, открываем интерфейс добавления отеля в таблицу
				if (($current_action == 'select' && $_GET['hotel_id'] && !$_POST['list_save']) || $save_error || ($action_edit && !$end_edit) ) {
					$new_hotel = $wpdb->get_row( "SELECT hotel_title, hotel_stars FROM all_hotels WHERE hotel_id = $hotel_id" );
					echo 'Выбран отель <span style="color: brown;">'.$new_hotel->hotel_title.'&nbsp;&nbsp;'.$new_hotel->hotel_stars.'&#9733;</span></br>';
					$from_cities = $wpdb->get_results( "SELECT * FROM all_from_city ORDER BY from_city_name ASC;" );
			// Откуда
					for ($i = 0; $i < count($from_cities); $i++) {
						if ($from_cities[$i]->from_city_id == $from_id) {
							echo '<input type="radio" name="from_id" value="'.$from_cities[$i]->from_city_id.'" checked>&nbsp;'.$from_cities[$i]->from_city_name.'</br>';
						}
						else {
							echo '<input type="radio" name="from_id" value="'.$from_cities[$i]->from_city_id.'">&nbsp;'.$from_cities[$i]->from_city_name.'</br>';
						}
					}
			// Заголовок
					echo '</br><input type="text" name="row_title" placeholder="Заголовок 26 символов" value="'.$row_title.'" maxlength="26"></br>';
			// Позиция
					if (!$hotel_position) {
						$hotel_position = 1;
					}
					echo '<input type="number" name="hotel_position" min="1" value="'.$hotel_position.'"></br>';
			// Питание
					$all_meals = $wpdb->get_results( "SELECT * all_meals" );
					if (!$meal_id) {
						$meal_id = $all_meals[0]->meal_id;
					}
					for ($i = 0; $i < count($all_meals); $i++) {
						if ($all_meals[$i]->meal_id == $meal_id ) {
							echo '<input type="radio" name="resort_id" value="'.$all_meals[$i]->meal_id.'" checked>&nbsp;'.$$all_meals[$i]->meal_name.'&nbsp;&nbsp;&nbsp;&nbsp;';
						}
						else {
							echo '<input type="radio" name="resort_id" value="'.$all_meals[$i]->meal_id.'">&nbsp;'.$all_meals[$i]->meal_name.'&nbsp;&nbsp;&nbsp;&nbsp;';
						}	
					}
			// Дата, длительность, цена, кнопка Сохранить
					$all_currency = $wpdb->get_results( "SELECT currency_symbol FROM all_currency" );
					echo '<input type="text" id="datepicker" name="start_date" placeholder="Дата" value="'.$start_date_value.'"></br>
						  <input type="number" name="days_long" min="1" max="20" placeholder="Дней" value="'.$days_long.'"></br>
						  <input type="text" name="hotel_price" placeholder="Стоимость" value="'.$hotel_price.'"></br>
						  <input type="radio" name="currency_id" value=1 checked>'.$all_currency[0]->currency_symbol;
							for ($i = 1; $i < count($all_currency); $i++) {
								echo '&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="currency_id" value="'.$all_currency[$i]->currency_id.'">'.$all_currency[$i]->currency_symbol;
							}
					echo '<button type="submit" name="list_save" value=1 class="btn_mail_green">Сохранить</button>';
				}
		// Если вводится подзаголовок, якорь или ничего
				else {
					if ( (($current_action == 'add_anchor' && !$_POST['anchor_save'])  || ($action_anchor_edit && !$end_edit)) && !$end_region ) {
			// Ввод якоря
						echo '<input type="text" name="anchor" value="">';
			// Позиция
						if (!$hotel_position) {
							$hotel_position = 1;
						}
						echo '<input type="number" name="hotel_position" min="1" value="'.$hotel_position.'"></br>';
						echo '<button type="submit" name="anchor_save" value=1 class="btn_mail_green">Сохранить</button>';
					}
					elseif (($current_action == 'add_heading' && !$_POST['heading_save']) || $save_error || ($action_heading_edit && !$end_edit) ) {
			// Ввод подзаголовка
						wp_editor($row_title, 'editor', array(
							'wpautop'       => 1,
							'media_buttons' => 1,
							'textarea_name' => 'heading',
							'textarea_rows' => 1,
							'tabindex'      => null,
							'editor_css'    => '',
							'editor_class'  => '',
							'teeny'         => 0,
							'dfw'           => 0,
							'tinymce'       => 1,
							'quicktags'     => 1,
							'drag_drop_upload' => false
						) );
			// Позиция
						if (!$hotel_position) {
							$hotel_position = 1;
						}
						echo '<input type="number" name="hotel_position" min="1" value="'.$hotel_position.'"></br>';
						echo '<button type="submit" name="heading_save" value=1 class="btn_mail_green">Сохранить</button>';
					}
				// Если новый отель не выбирался, ошибки при вводе не было, не редактируется лист, не вводится и не радактируется подзаголовок, выводим ссылки Добавить отель, Длбавить подзаголовок и На сайте
					else {
						echo '<a href="/hotels/?region_id='.$region_id.'&select_region_id='.$region_id.'" style="color: blue;">Добавить отель</a>&nbsp;&nbsp;
							  <a href="/hotel-list/?action=add_heading&region_id='.$region_id.'">Добавить подзаголовок</a>&nbsp;&nbsp;
							  <a href="/hotel-list/?action=add_anchor&region_id='.$region_id.'">Добавить якорь</a>&nbsp;&nbsp;&nbsp;
							  <a href="http://t.zp.ua/hotel-test/?region_id='.$region_id.'&edit_list='.$edit_list.'" target="_blank" style="color: green;">На сайте</a></br>';
					}
				}
		// Считываем лист по текущему региону  и выводим список в таблицу
				$current_list = $wpdb->get_results( "SELECT * FROM all_region_list_edit WHERE region_id = '$region_id' ORDER BY list_number ASC;" );
				for ($i = 0; $i < count($current_list); $i++) {
					$current_hotel_id = $current_list[$i]->list_hotel_id;
			// Если это вывод отеля
					if ($current_list[$i]->row_type == 'hotel') {
						echo '<div class="iphone_list_hotel">';
						$current_hotel = $wpdb->get_row( "SELECT * FROM all_hotels WHERE hotel_id = $current_hotel_id" );
						echo '<strong>'.$current_list[$i]->list_number.'</strong>&nbsp;&nbsp;
							<span style="color: blue">'.$current_hotel->hotel_title.'</span>&nbsp;&nbsp;
							<span style="color: brown;">'.$current_hotel->hotel_stars.'&#9733;</span></br>';
				// Дата цвет в зависимости от даты Красный - просрочено Зеленый - не просрочено и не сегодня и Цена
							$current_date_array = explode('-', current_time('mysql', false));
							$current_date = $current_date_array[0] * 10000 + $current_date_array[1] * 100 + $current_date_array[2];
							$list_date_array = explode('-', $current_list[$i]->list_start_date);
							$list_date = $list_date_array[0] * 10000 + $list_date_array[1] * 100 + $list_date_array[2];
							if ($list_date > $current_date) {
								echo '<span style="color: green;">';
							}
							elseif ($list_date < $current_date) {
								echo '<span style="color: red;">';
							}
							else {
								echo '<span style="color: black;">';
							}
							echo $current_list[$i]->list_start_date.'</span>&nbsp;&nbsp;
								'.$current_list[$i]->list_days_long.' дней&nbsp;&nbsp;
							<span style="color: blue;">'.$current_list[$i]->price.' ';
							$currency_id = $current_list[$i]->currency_id;
							$currency = $wpdb->get_row( "SELECT currency_symbol FROM all_currency WHERE currency_id = $currency_id" );
							echo $currency->currency_symbol.'</span></br>';
							if ($current_hotel->hotel_note) {
								echo $current_hotel->hotel_note.'</br>';
							}
				// Откуда
							$from_city_id = $current_list[$i]->from_city_id;
							$from_city = $wpdb->get_row( "SELECT from_city_name FROM all_from_city WHERE from_city_id = $from_city_id" );
							echo '<span style="color: blue;">'.$from_city_id->from_city_name.'</span>
						</div>';
					}
			// Если текущий лист - не отель
					else {
				// Это подзаголовок
						if ($current_list[$i]->row_type == 'head') {
							echo '<div class="iphone_list_header">';
						}
				// Это якорь
						else {
							echo '<div class="iphone_list_anchor">';
						}
						echo '<strong>'.$current_list[$i]->list_number.'</strong>&nbsp;&nbsp;&nbsp;'.$current_list[$i]->list_row_title.'
						</div>';	
					}
			// Вверх Вниз
					echo '<div class="iphone_list_link"><big>';
						if ($i) {
							echo '<a href="/hotel-list/?region_id='.$region_id.'&action=up&list_number='.$current_list[$i]->list_number.'">&uarr;Вверх&uarr;</a>&nbsp;&nbsp;&nbsp;';
						}
						if ($i < count($current_list) - 1) {
							echo '<td class="list_link"><a href="/hotel-list/?region_id='.$region_id.'&action=down&list_number='.$current_list[$i]->list_number.'">&darr;Вниз&darr;</a>&nbsp;&nbsp;&nbsp';
						}
						echo '<a style="color: red;" href="/hotel-list/?region_id='.$region_id.'&action=delete&list_number='.$current_list[$i]->list_number.'">Удалить</a>&nbsp;&nbsp;
							  <a style="color: blue" href="/hotel-list/?region_id='.$region_id.'&action=edit&list_id='.$current_list[$i]->list_row_id.'">Редактop</a>
					</big> </div>';
				}
			}
			echo '</form>';
		}

	}
}
?>

