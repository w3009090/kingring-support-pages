<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="<?php echo esc_url( KINGRING_SUPPORT_PAGES_URL . 'assets/card-borders.css?ver=' . KINGRING_SUPPORT_PAGES_VERSION ); ?>">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'kr-page' ); ?>>
<?php wp_body_open(); ?>
<?php kingring_support_pages_header(); ?>
<main id="kr-content">
	<section class="kr-hero">
		<img src="<?php echo kingring_support_pages_image( 'hero.jpg' ); ?>" width="1500" height="879" alt="<?php esc_attr_e( 'King Ring product packaging and rings', 'kingring-support-pages' ); ?>">
	</section>
	<section class="kr-support">
		<h1><?php esc_html_e( 'King Ring Support', 'kingring-support-pages' ); ?></h1>
		<div class="kr-grid">
			<a class="kr-card" href="<?php echo esc_url( home_url( '/contactus/' ) ); ?>">
				<img src="<?php echo kingring_support_pages_image( 'icon-chat.jpg' ); ?>" width="500" height="500" alt="">
				<strong><?php esc_html_e( 'Contact Us', 'kingring-support-pages' ); ?></strong>
			</a>
			<a class="kr-card" href="https://www.amazon.com/gp/your-account/order-history?opt=ab&amp;search=King+Ring">
				<img src="<?php echo kingring_support_pages_image( 'icon-order.jpg' ); ?>" width="500" height="500" alt="">
				<strong><?php esc_html_e( 'Rate ordered item in Amazon', 'kingring-support-pages' ); ?></strong>
			</a>
			<a class="kr-card" href="https://www.amazon.com/kingring">
				<img src="<?php echo kingring_support_pages_image( 'icon-store.jpg' ); ?>" width="500" height="500" alt="">
				<strong><?php esc_html_e( 'Back to our store', 'kingring-support-pages' ); ?></strong>
			</a>
		</div>
	</section>
</main>
<?php kingring_support_pages_footer(); ?>
<?php wp_footer(); ?>
</body>
</html>
