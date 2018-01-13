<?php
/*
  Создание или редактирование данных отеля GET action edit - редактирование, copy - копирование в новый и редактирование. Новый - параметр не передается
  Author:  alexdzyaba
  Version: 1.0
 */
// wp_enqueue_script('script1', home_url().'/wp-content/plugins/allhotels/js/search_hotels.js');
function hotel_edit () {


// Зарегистрирован ли пользователь. Проверяем, чтобы он имел права Редактор или выше (Админ, Суперадмин)
    if ( is_user_logged_in() ) {
        if( current_user_can('edit_pages') ) {
            // Подключаем PHP файлы
            require_once dirname(__FILE__) . '/GifCreator/GifCreator.php';
            global $wpdb, $is_iphone;
            $action = $_GET['action'];
            if ($action == copy) {
                echo '<p><big><strong>Копирование информации об отеле</strong></big></p>';
            }
			elseif ($action == edit) {
                echo '<p><big><strong>Редактирование информации об отеле</strong></big></p>';
            }
            else {
                echo '<p><big><strong>Добавление в базу инфориации о новом отеле</strong></big></p>';
            }
            // Если введены данные
            if ($_POST['hotel_save']) {
                // Формируем из данных редактора Краткое описание, Изображение для превью, Полное описание и Изображения для слайда
                if (!$_POST['hotel_editor']) {
                    echo '<p style="color: red;">Не введено описание отеля!</p>';
                }
                else {
                    // При передаче в POST возникает экранирование убираем
                    $print_editor = str_replace('\"', '"', $_POST['print_editor']);
                    $print_editor = str_replace("\'", "'", $print_editor);
                    $hotel_editor = str_replace('\"', '"', $_POST['hotel_editor']);
                    $hotel_editor = str_replace("\'", "'", $hotel_editor);
                    $hotel_editor_array = explode("<img", $hotel_editor);
                    if (count($hotel_editor_array) < 3) {
                        echo '<p style="color: red;">Неверный формат редактируемого поля</p>';
                        $edit_error = 1;
                    }
                    else {
                        $edit_error = 0;
                        // Изображения для превью и GIF
                        if (count($hotel_editor_array) > 10) {
                            $max_count = 10;
                        }
                        else {
                            $max_count = count($hotel_editor_array);
                        }
                        for ($i = 1; $i < $max_count; $i++) {
                            $preview_image = stristr( $hotel_editor_array[$i], "http://");
                            $preview_image_array = explode('"', $preview_image);
                            $hotel_preview_image[$i-1] = rtrim($preview_image_array[0], "\\");
                            // Периодичность смены кадров
                            $durations[$i-1] = 200;
                        }
                        // В качестве названия берем имя первой картинки, меняем расширение на gif
                        $pos = strripos($hotel_preview_image[0], "/") + 1;
                        $gif_url = substr($hotel_preview_image[0], $pos);
                        $pos = strripos($gif_url, ".");
                        $hotel_gif_url = substr($gif_url, 0, $pos);
                        $hotel_gif_url .= '.gif';
                        // Полное описание
                        $description = $hotel_editor_array[count($hotel_editor_array)-1];
                        $pos = stripos($description, ">");
                        $hotel_description = trim(substr($description, $pos+1));
                        // Превью картинок для сохранения
                        $hotel_editor_array[count($hotel_editor_array)-1] = substr($hotel_editor_array[count($hotel_editor_array)-1], 0, $pos+1);
                        $hotel_slide_images = implode("<img", $hotel_editor_array);
                    }
                }
                // Если это новый отель или скопированный
                if (!$action || $action == copy || $_POST['hotel_save'] == 2) {
                    // Проверяем название на наличие и оригинальность
                    $hotel_title = $_POST['hotel_title'];
                    $hotel_data = object_to_array($wpdb->get_row( "SELECT * FROM all_hotels WHERE hotel_title = '$hotel_title'" ));
                    if (!$_POST['hotel_region_id']) {
                        echo '<p style="color: red;">Не выбрано направление!</p>';
                        $edit_error = 1;
                    }
                    else {
                        if ($hotel_data) {
                            echo '<p style="color: red;">Отель с названием <strong>'.$hotel_title.'</strong> уже есть в базе!</p>';
                            $edit_error = 1;
                        }
                        // Записываем новую строчку в базах отелей отелей для новых или скопированных
                        else {
                            if (!$edit_error) {
                               $wpdb->insert('all_hotels',
                                    array(
                                        'region_id' => $_POST['hotel_region_id'],
                                        'hotel_title' => $hotel_title ,
                                        'hotel_annotate' => $_POST['hotel_annotate'],
                                        'hotel_stars' => $_POST['hotel_stars'],
                                        'hotel_main_image' => $hotel_preview_image[0],
                                        'hotel_slide_images' => $hotel_slide_images ,
                                        'hotel_description' => $hotel_description,
                                        'hotel_note' => $_POST['hotel_note'],
                                        'hotel_print_images' => $print_editor,
										'ittour_id' => $_POST['id_hotel_it_tour']
                                    )
                                );
                                // Если информация не сохранилась

                                $last_hotel = object_to_array($wpdb->get_row( "SELECT * FROM all_hotels ORDER BY hotel_id DESC LIMIT 1" ));
                                if ($last_hotel[hotel_title] != $hotel_title) {
                                    echo '<p style="color: red;"><strong><big>Информация почему-то не сохранилась!</big></strong></p>';
                                    $edit_error = 1;
                                    $hotel_save_problem = 1;
                                }
                                // Если информация сохранилась
                                else {
                                    echo '<p style="color: blue;">Информация о новом отеле сохранена</p>';
                                    // Создаем GIF
                                    $gc = new GifCreator();
                                    $gc->create($hotel_preview_image, $durations, 0);
                                    $gifBinary = $gc->getGif();
                                    $upload_gif = wp_upload_bits( $hotel_gif_url, null, $gifBinary );
                                    // Отдельно записываем GIF, чтобы ошибка при создании не помешела записать данные
                                     $wpdb->update( 'all_hotels',
                                        array(
                                            'hotel_gif_url' => $upload_gif['url']
                                        ),
                                        array ('hotel_id' => $last_hotel[hotel_id])
                                    );
                                }
                            }
                        }
                    }
                }
                // Редактируем данные отеля в базах
				elseif ($action == edit) {
                    if (!$edit_error) {
                        $hotel_id = $_GET['hotel_id'];
                        $hotel_old_title = object_to_array($wpdb->get_row( "SELECT hotel_title FROM all_hotels WHERE hotel_id = $hotel_id" ));
                        // Если при редактировании название отеля не изменялось
                        if (($hotel_old_title[hotel_title] == $_POST['hotel_title']) || $_POST['hotel_save'] == 3) {
                             $wpdb->update( 'all_hotels',
                                array(
                                    'region_id' => $_POST['hotel_region_id'],
                                    'hotel_title' => $_POST['hotel_title'],
                                    'hotel_annotate' => $_POST['hotel_annotate'],
                                    'hotel_stars' => $_POST['hotel_stars'],
                                    'hotel_main_image' => $hotel_preview_image[0],
                                    'hotel_slide_images' => $hotel_slide_images ,
                                    'hotel_description' => $hotel_description,
                                    'hotel_note' => $_POST['hotel_note'],
                                    'hotel_print_images' => $print_editor,
									'ittour_id' => $_POST['id_hotel_it_tour']
                                ),
                                array ('hotel_id' => $hotel_id)
                            );
                            echo '<p style="color: blue;">Изменения сохранены</p>';
                            // Создаем GIF
                            $gc = new GifCreator();
                            $gc->create($hotel_preview_image, $durations, 0);
                            $gifBinary = $gc->getGif();
                            $upload_gif = wp_upload_bits( $hotel_gif_url, null, $gifBinary );
                            // Отдельно записываем GIF, чтобы ошибка при создании не помешела записать данные
                             $wpdb->update( 'all_hotels',
                                array(
                                    'hotel_gif_url' => $upload_gif['url']
                                ),
                                array ('hotel_id' => $hotel_id)
                            );
                        }
                        else {
                            echo '<p style="color: red;"><big><strong>Вы изменили название отеля</strong></big></p>';
                            $edit_error = 1;
                            $hotel_title_question = 1;
                        }
                    }
                }
            }
            // Редактирование или копирование и редактирование
            if ($action && !$hotel_title_question && !$hotel_save_problem) {
                $hotel_id = $_GET['hotel_id'];
                $hotel_data = object_to_array($wpdb->get_row( "SELECT * FROM all_hotels WHERE hotel_id = $hotel_id" ));
                $hotel_title = $hotel_data[hotel_title];
                $hotel_ittour_id = $hotel_data[ittour_id];
                $hotel_region_id = $hotel_data[region_id];
                $hotel_editor = $hotel_data[hotel_slide_images].$hotel_data[hotel_description];
                $print_editor = $hotel_data[hotel_print_images];
                if ($action == 'edit') {
                    $hotel_annotate = $hotel_data[hotel_annotate];
                    $hotel_stars = $hotel_data[hotel_stars];
                    $hotel_note = $hotel_data[hotel_note];
                }
                else {
                    $hotel_annotate = '';
                    $hotel_stars = 0;
                    $hotel_note = '';
                }
            }
            // Создание новой записи
            else {
                if (!$edit_error) {
                    $hotel_title = '';
                    $hotel_ittour_id = '';
                    $hotel_region_id = 0;
                    $hotel_stars = 0;
                    $hotel_annotate = '';
                    $hotel_note = '';
                    $hotel_editor = '';
                    $print_editor = '';

                }
                else {
                    $hotel_title = $_POST['hotel_title'];

                    $hotel_ittour_id = $_POST['id_hotel_it_tour'];
                    $hotel_region_id = $_POST['region_id'];
                    $hotel_stars = $_POST['hotel_stars'];
                    $hotel_annotate = $_POST['hotel_annotate'];
                    $hotel_note = $_POST['hotel_note'];
                    $hotel_editor = str_replace('\"', '"', $_POST['hotel_editor']);
                    $print_editor = str_replace('\"', '"', $_POST['print_editor']);
                }
            }
            $regions_input = '';
            if($_POST['param']) {
                $param = json_decode($_POST['param']);
                $row = get_text($param->id);
                echo json_encode($row);
                exit();
            }
            echo '<form action="" method="post">';
            $all_regions = $wpdb->get_results( "SELECT * FROM all_regions WHERE region_double = 0 ORDER BY region_main ASC;" );
            echo '<p><strong>Страны:</strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            for ($i = 0; $i < count($all_regions); $i++) {
                if ($all_regions[$i]->region_main) {
                    echo '<input type="radio" name="hotel_region_id" value="'.$all_regions[$i]->region_id.'"';
                    if ($all_regions[$i]->region_id == $hotel_region_id) {
                        echo ' checked';
                    }
                    echo '>'.$all_regions[$i]->region_name.'&nbsp;&nbsp;&nbsp;&nbsp;';
                }
                else {
                    if ($all_regions[$i]->country_id) {
                        $regions_input .= '<input type="radio" name="hotel_region_id" value="'.$all_regions[$i]->region_id.'"';
                        if ($all_regions[$i]->region_id == $hotel_region_id) {
                            $regions_input .= ' checked';
                        }
                        $regions_input .= '>'.$all_regions[$i]->region_name.'&nbsp;&nbsp;&nbsp;&nbsp;';
                    }
                }
            }
            echo '</p>
					  <p><strong>Регионы:</strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$regions_input.'</p>
					  <p>Название отеля<input type="text" name="hotel_title" id="tags" required value="'.$hotel_title.'"></p>
					   <p>ID отеля от IT-Tour<input type="text" name="id_hotel_it_tour" value="'.$hotel_ittour_id.'" id="tags_id" readonly  ></p>
					  <p>Количество звезд &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					  ';
            for ($i = 0; $i < 6; $i++) {
                if ($i == $hotel_stars)
                    echo '<input type="radio" name="hotel_stars" value="'.$i.'" checked>'.$i.'&nbsp;&nbsp;&nbsp;&nbsp;';
                else
                    echo '<input type="radio" name="hotel_stars" value="'.$i.'">'.$i.'&nbsp;&nbsp;&nbsp;&nbsp;';
            }
            echo '</p>
					  <p>Краткое описание<input type="text" name="hotel_annotate" value="'.$hotel_annotate.'" placeholder="Максимум 42 символа" maxlength="42"></p>';
            echo 'Картинки (первая для аннотации) - Полное описание</br>';
            wp_editor($hotel_editor, 'editor', array(
                'wpautop'       => 1,
                'media_buttons' => 1,
                'textarea_name' => 'hotel_editor',
                'textarea_rows' => 20,
                'tabindex'      => null,
                'editor_css'    => '',
                'editor_class'  => '',
                'teeny'         => 0,
                'dfw'           => 0,
                'tinymce'       => 1,
                'quicktags'     => 1,
                'drag_drop_upload' => false
            ) );
            echo '</br>Картинки Для печати 4 шт.</br>';
            wp_editor($print_editor, 'print', array(
                'wpautop'       => 1,
                'media_buttons' => 1,
                'textarea_name' => 'print_editor',
                'textarea_rows' => 10,
                'tabindex'      => null,
                'editor_css'    => '',
                'editor_class'  => '',
                'teeny'         => 0,
                'dfw'           => 0,
                'tinymce'       => 1,
                'quicktags'     => 1,
                'drag_drop_upload' => false
            ) );
            echo '<p>&nbsp;</p><p>Примечание<input type="text" name="hotel_note" value="'.$hotel_note.'"></p>';
            if (!$hotel_title_question) {
                echo '<button type="submit" name="hotel_save" value=1 id="btn_h" class="btn_mail_green">Сохранить</button>';
            }
            else {
                echo '<button type="submit" name="hotel_save" value=2 class="btn_mail_green">Сохранить как новый отель</button>';
                echo '<button type="submit" name="hotel_save" value=3 class="btn_mail_brown">Подтвердить изменения</button>';
            }
            echo '</form>';
        }
    }
}
?>
