<?php
/**
 * The template for displaying pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages and that
 * other "pages" on your WordPress site will use a different template.
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since Pochta 1.0
   Template Name: Delete
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?> <!--class="no-js no-svg">-->
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel='stylesheet' id='twentyseventeen-style-css'  href='http://all.t.zp.ua/wp-content/themes/alltzpua/style.css?ver=4.7.4' type='text/css' media='all' />
<link rel='stylesheet' href='http://all.t.zp.ua/wp-content/plugins/allhotels/css/allhotels.css' />

</head>

<body <?php body_class(); ?>>
	<div id="page" class="site">
		<?php
		while ( have_posts() ) : the_post();

			get_template_part( 'template-parts/page/content', 'page' );

		endwhile; // End of the loop.
		?>
	</div><!-- #page -->
</body>
</html>
