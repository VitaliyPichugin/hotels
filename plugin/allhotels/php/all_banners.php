<?php
/*
  Таблицы по направлениям GET region_id
  Author:  alexdzyaba
  Version: 1.0
 */
 
function all_banners () {
// Зарегистрирован ли пользователь
	if ( is_user_logged_in() ) {
		global $wpdb, $is_iphone;
		if ($_POST['banner_save']) {
			$banner_save = $_POST['banner_save'];
	// 3 банера Горящие плюс 4-ый на главную
			if ($banner_save < 5) {
				$wpdb->update( 'all_banners',
					array(
						'banner_price' => $_POST['price'],
						'banner_string_1' => $_POST['string_1'],
						'banner_string_2' => $_POST['string_2'],
						'banner_img' => $_POST['img'],
						'banner_url' => $_POST['url']
					),
					array ('banner_id' => $banner_save)
				);
				echo '<p style="color: blue;">Изменения сохранены</p>';
			}
	// Болгария
			elseif ($banner_save == 5){
				$wpdb->update ( 'all_banners', array('banner_price' => $_POST['price_5']), array ('banner_id' => '5') );
				$wpdb->update ( 'all_banners', array('banner_price' => $_POST['price_6']), array ('banner_id' => '6') );
				$wpdb->update ( 'all_banners', array('banner_price' => $_POST['price_7']), array ('banner_id' => '7') );
				$wpdb->update ( 'all_banners', array('banner_price' => $_POST['price_8']), array ('banner_id' => '8') );
			}
	// Греция
			elseif ($banner_save == 6){
				$wpdb->update ( 'all_banners', array('banner_price' => $_POST['price_9']), array ('banner_id' => '9') );
				$wpdb->update ( 'all_banners', array('banner_price' => $_POST['price_10']), array ('banner_id' => '10') );
				$wpdb->update ( 'all_banners', array('banner_price' => $_POST['price_11']), array ('banner_id' => '11') );
				$wpdb->update ( 'all_banners', array('banner_price' => $_POST['price_12']), array ('banner_id' => '12') );
			}
	// Египет
			elseif ($banner_save == 7){
				$wpdb->update ( 'all_banners', array('banner_price' => $_POST['price_13']), array ('banner_id' => '13') );
				$wpdb->update ( 'all_banners', array('banner_price' => $_POST['price_14']), array ('banner_id' => '14') );
			}
	// Испания
			elseif ($banner_save == 8){
				$wpdb->update ( 'all_banners', array('banner_price' => $_POST['price']), array ('banner_id' => '15') );
			}
	// Черногория
			elseif ($banner_save == 9){
				$wpdb->update ( 'all_banners', array('banner_price' => $_POST['price']), array ('banner_id' => '16') );
			}
		}
		$banners_data = $wpdb->get_results( "SELECT * FROM all_banners" );
		echo '<p><big><strong>Редактирование баннеров</strong></big></p>
		<p style="text-align: left; font-size: 40%; border-bottom: 1px solid #DDD;">&nbsp;</p>
		<form action="" method="post">
			<p style="color: blue;">Горящие туры левый</p>
			<div style="display: inline-block;">1 строка 20 симв.</div>
			<div style="display: inline-block; vertical-align: middle; margin-right: 20px; min-width: 250px;"><input type="text" name="string_1" value="'.$banners_data[0]->banner_string_1.'" maxlength="20"></div>
			<div style="display: inline-block;">2 строка 20 симв.</div>
			<div style="display: inline-block; vertical-align: middle; margin-right: 20px; min-width: 250px;"><input type="text" name="string_2" value="'.$banners_data[0]->banner_string_2.'" maxlength="20"></div>
			<div style="display: inline-block;">Цена 13 симв.</div>
			<div style="display: inline-block; vertical-align: middle;"><input type="text" name="price" value="'.$banners_data[0]->banner_price.'" maxlength="13"></div></br></br>
			<div style="display: inline-block; margin-right: 40px;">';
			echo '<input type="radio" name="img" value="bus" ';
			if ($banners_data[0]->banner_img == 'bus') {
				echo 'checked';
			}
			echo '> Автобус &nbsp;&nbsp;&nbsp;&nbsp';
			echo '<input type="radio" name="img" value="plane" ';
			if ($banners_data[0]->banner_img == 'plane') {
				echo 'checked';
			}
			echo '> Самолет &nbsp;&nbsp;&nbsp;&nbsp';
			echo '<input type="radio" name="img" value="pasport" ';
			if ($banners_data[0]->banner_img == 'pasport') {
				echo 'checked';
			}
			echo '> Паспорт</div>		
			<div style="display: inline-block;">Ссылка</div>
			<div style="display: inline-block; vertical-align: middle; margin-right: 20px;  min-width: 600px;"><input type="text" name="url" value="'.$banners_data[0]->banner_url.'"></div>
			<button type="submit" name="banner_save" value=1 class="btn_mail_green">Сохранить</button></br>
		</form>
		<form action="" method="post">
			<p style="text-align: left; font-size: 40%; border-bottom: 1px solid #DDD;">&nbsp;</p>
			<p style="color: blue;">Горящие туры средний</p>
			<div style="display: inline-block;">1 строка 20 симв.</div>
			<div style="display: inline-block; vertical-align: middle; margin-right: 20px; min-width: 250px;"><input type="text" name="string_1" value="'.$banners_data[1]->banner_string_1.'" maxlength="20"></div>
			<div style="display: inline-block;">2 строка 20 симв.</div>
			<div style="display: inline-block; vertical-align: middle; margin-right: 20px; min-width: 250px;"><input type="text" name="string_2" value="'.$banners_data[1]->banner_string_2.'" maxlength="20"></div>
			<div style="display: inline-block;">Цена 13 симв.</div>
			<div style="display: inline-block; vertical-align: middle;"><input type="text" name="price" value="'.$banners_data[1]->banner_price.'" maxlength="13"></div></br></br>
			<div style="display: inline-block; margin-right: 40px;">';
			echo '<input type="radio" name="img" value="bus" ';
			if ($banners_data[1]->banner_img == 'bus') {
				echo 'checked';
			}
			echo '> Автобус &nbsp;&nbsp;&nbsp;&nbsp';
			echo '<input type="radio" name="img" value="plane" ';
			if ($banners_data[1]->banner_img == 'plane') {
				echo 'checked';
			}
			echo '> Самолет &nbsp;&nbsp;&nbsp;&nbsp';
			echo '<input type="radio" name="img" value="pasport" ';
			if ($banners_data[1]->banner_img == 'pasport') {
				echo 'checked';
			}
			echo '> Паспорт</div>
			<div style="display: inline-block;">Ссылка</div>
			<div style="display: inline-block; vertical-align: middle; margin-right: 20px;  min-width: 600px;"><input type="text" name="url" value="'.$banners_data[1]->banner_url.'"></div>
			<button type="submit" name="banner_save" value=2 class="btn_mail_green">Сохранить</button></br>
		</form>
		<form action="" method="post">
			<p style="text-align: left; font-size: 40%; border-bottom: 1px solid #DDD;">&nbsp;</p>
			<p style="color: blue;">Горящие туры правый</p>
			<div style="display: inline-block;">1 строка 20 симв.</div>
			<div style="display: inline-block; vertical-align: middle; margin-right: 20px; min-width: 250px;"><input type="text" name="string_1" value="'.$banners_data[2]->banner_string_1.'" maxlength="20"></div>
			<div style="display: inline-block;">2 строка 20 симв.</div>
			<div style="display: inline-block; vertical-align: middle; margin-right: 20px; min-width: 250px;"><input type="text" name="string_2" value="'.$banners_data[2]->banner_string_2.'" maxlength="20"></div>
			<div style="display: inline-block;">Цена 13 симв.</div>
			<div style="display: inline-block; vertical-align: middle;"><input type="text" name="price" value="'.$banners_data[2]->banner_price.'" maxlength="13"></div></br></br>
			<div style="display: inline-block; margin-right: 40px;">';
			echo '<input type="radio" name="img" value="bus" ';
			if ($banners_data[2]->banner_img == 'bus') {
				echo 'checked';
			}
			echo '> Автобус &nbsp;&nbsp;&nbsp;&nbsp';
			echo '<input type="radio" name="img" value="plane" ';
			if ($banners_data[2]->banner_img == 'plane') {
				echo 'checked';
			}
			echo '> Самолет &nbsp;&nbsp;&nbsp;&nbsp';
			echo '<input type="radio" name="img" value="pasport" ';
			if ($banners_data[2]->banner_img == 'pasport') {
				echo 'checked';
			}
			echo '> Паспорт</div>
			<div style="display: inline-block;">Ссылка</div>
			<div style="display: inline-block; vertical-align: middle; margin-right: 20px;  min-width: 600px;"><input type="text" name="url" value="'.$banners_data[2]->banner_url.'"></div>
			<button type="submit" name="banner_save" value=3 class="btn_mail_green">Сохранить</button>
		</form>
		<form action="" method="post">
			<p style="text-align: left; font-size: 40%; border-bottom: 1px solid #DDD;">&nbsp;</p>
			<p style="color: blue;">Главная крайний справа (первые три дублируют горящие туры)</p>
			<div style="display: inline-block;">1 строка 17 симв.</div>
			<div style="display: inline-block; vertical-align: middle; margin-right: 20px; min-width: 250px;"><input type="text" name="string_1" value="'.$banners_data[3]->banner_string_1.'" maxlength="17"></div>
			<div style="display: inline-block;">2 строка 17 симв.</div>
			<div style="display: inline-block; vertical-align: middle; margin-right: 20px; min-width: 250px;"><input type="text" name="string_2" value="'.$banners_data[3]->banner_string_2.'" maxlength="17"></div>
			<div style="display: inline-block;">Цена 13 симв.</div>
			<div style="display: inline-block; vertical-align: middle;"><input type="text" name="price" value="'.$banners_data[3]->banner_price.'" maxlength="13"></div></br></br>
			<div style="display: inline-block; margin-right: 40px;">';
			echo '<input type="radio" name="img_4" value="bus" ';
			if ($banners_data[3]->banner_img == 'bus') {
				echo 'checked';
			}
			echo '> Автобус &nbsp;&nbsp;&nbsp;&nbsp';
			echo '<input type="radio" name="img" value="plane" ';
			if ($banners_data[3]->banner_img == 'plane') {
				echo 'checked';
			}
			echo '> Самолет &nbsp;&nbsp;&nbsp;&nbsp';
			echo '<input type="radio" name="img" value="pasport" ';
			if ($banners_data[3]->banner_img == 'pasport') {
				echo 'checked';
			}
			echo '> Паспорт</div>
			<div style="display: inline-block;">Ссылка</div>
			<div style="display: inline-block; vertical-align: middle; margin-right: 20px;  min-width: 600px;"><input type="text" name="url" value="'.$banners_data[3]->banner_url.'"></div>
			<button type="submit" name="banner_save" value=4 class="btn_mail_green">Сохранить</button>
		</form>
		<form action="" method="post">
			<p style="text-align: left; font-size: 40%; border-bottom: 1px solid #DDD;">&nbsp;</p>
			<p style="color: blue;">Болгария Цена 4 баннера по 13 симв.</p>
			<div style="display: inline-block;">Визы</div>
			<div style="display: inline-block; vertical-align: middle; margin-right: 20px; min-width: 150px;"><input type="text" name="price_5" value="'.$banners_data[4]->banner_price.'" maxlength="13"></div>
			<div style="display: inline-block;">Запорожье</div>
			<div style="display: inline-block; vertical-align: middle; margin-right: 20px; min-width: 150px;"><input type="text" name="price_6" value="'.$banners_data[5]->banner_price.'" maxlength="13"></div>
			<div style="display: inline-block;">Киев</div>
			<div style="display: inline-block; vertical-align: middle; margin-right: 20px; min-width: 150px;"><input type="text" name="price_7" value="'.$banners_data[6]->banner_price.'" maxlength="13"></div>
			<div style="display: inline-block;">Львов</div>
			<div style="display: inline-block; vertical-align: middle; margin-right: 20px; min-width: 150px;"><input type="text" name="price_8" value="'.$banners_data[7]->banner_price.'" maxlength="13"></div>
			<button type="submit" name="banner_save" value=5 class="btn_mail_green">Сохранить</button>
		<form action="" method="post">
			<p style="text-align: left; font-size: 40%; border-bottom: 1px solid #DDD;">&nbsp;</p>
			<p style="color: blue;">Греция Цена 4 баннера слева - направо по 10 симв.</p>
			<div style="display: inline-block;">1</div>
			<div style="display: inline-block; vertical-align: middle; margin-right: 20px; min-width: 150px;"><input type="text" name="price_9" value="'.$banners_data[8]->banner_price.'" maxlength="10"></div>
			<div style="display: inline-block;">2</div>
			<div style="display: inline-block; vertical-align: middle; margin-right: 20px; min-width: 150px;"><input type="text" name="price_10" value="'.$banners_data[9]->banner_price.'" maxlength="10"></div>
			<div style="display: inline-block;">3</div>
			<div style="display: inline-block; vertical-align: middle; margin-right: 20px; min-width: 150px;"><input type="text" name="price_11" value="'.$banners_data[10]->banner_price.'" maxlength="10"></div>
			<div style="display: inline-block;">4</div>
			<div style="display: inline-block; vertical-align: middle; margin-right: 20px; min-width: 150px;"><input type="text" name="price_12" value="'.$banners_data[11]->banner_price.'" maxlength="10"></div>
			<button type="submit" name="banner_save" value=6 class="btn_mail_green">Сохранить</button>
		</form>
		<form action="" method="post">
			<p style="text-align: left; font-size: 40%; border-bottom: 1px solid #DDD;">&nbsp;</p>
			<p style="color: blue;">Египет Цена по 13 симв.</p>
			<div style="display: inline-block;">Хургада</div>
			<div style="display: inline-block; vertical-align: middle; margin-right: 20px; min-width: 150px;"><input type="text" name="price_13" value="'.$banners_data[12]->banner_price.'" maxlength="13"></div>
			<div style="display: inline-block;">Шарм-эль-Шейх</div>
			<div style="display: inline-block; vertical-align: middle; margin-right: 20px; min-width: 150px;"><input type="text" name="price_14" value="'.$banners_data[13]->banner_price.'" maxlength="13"></div>
			<button type="submit" name="banner_save" value=7 class="btn_mail_green">Сохранить</button>
		</form>
		<form action="" method="post">
			<p style="text-align: left; font-size: 40%; border-bottom: 1px solid #DDD;">&nbsp;</p>
			<p style="color: blue;">Испания Цена 13 симв.</p>
			<div style="display: inline-block;">Горящие туры</div>
			<div style="display: inline-block; vertical-align: middle; margin-right: 20px; min-width: 150px;"><input type="text" name="price" value="'.$banners_data[14]->banner_price.'" maxlength="13"></div>
			<button type="submit" name="banner_save" value=8 class="btn_mail_green">Сохранить</button>
		</form>
		<form action="" method="post">
			<p style="text-align: left; font-size: 40%; border-bottom: 1px solid #DDD;">&nbsp;</p>
			<p style="color: blue;">Черногория Цена 13 симв.</p>
			<div style="display: inline-block;">Горящие туры</div>
			<div style="display: inline-block; vertical-align: middle; margin-right: 20px; min-width: 150px;"><input type="text" name="price" value="'.$banners_data[15]->banner_price.'" maxlength="13"></div>
			<button type="submit" name="banner_save" value=9 class="btn_mail_green">Сохранить</button>
		</form>';
	}
}
?>

