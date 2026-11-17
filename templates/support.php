<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$store_theme = kingring_support_pages_uses_store_theme();
$settings = kingring_support_pages_settings();
$hero_url = $settings['hero_image_url']
	? $settings['hero_image_url']
	: kingring_support_pages_image( 'hero.jpg' );
$store_card_image = kingring_support_pages_image( 'icon-store.jpg' );

if ( $store_theme && function_exists( 'wc_get_product' ) ) {
	$store_product_post = get_page_by_path( 'k13', OBJECT, 'product' );
	$store_product = $store_product_post ? wc_get_product( $store_product_post->ID ) : false;
	if ( $store_product && $store_product->get_image_id() ) {
		$store_product_image = wp_get_attachment_image_url( $store_product->get_image_id(), 'woocommerce_thumbnail' );
		if ( $store_product_image ) {
			$store_card_image = $store_product_image;
		}
	}
}

if ( $store_theme ) {
	get_header();
	?>
	<div id="kr-support-content" class="krsp-store-page krsp-store-support">
	<?php
} else {
	?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'kr-page' ); ?>>
<?php wp_body_open(); ?>
<?php kingring_support_pages_header(); ?>
<main id="kr-content">
	<?php
}
?>
	<section class="kr-hero">
		<img src="<?php echo esc_url( $hero_url ); ?>" width="1500" height="879" alt="<?php esc_attr_e( 'Friends wearing King Ring jewelry', 'kingring-support-pages' ); ?>">
	</section>
	<section class="kr-support">
		<p class="kr-eyebrow"><?php echo esc_html( $settings['support_eyebrow'] ); ?></p>
		<h1><?php echo esc_html( $settings['support_title'] ); ?></h1>
		<p class="kr-support-intro"><?php echo esc_html( $settings['support_intro'] ); ?></p>

		<div class="kr-grid">
			<a class="kr-card" href="<?php echo esc_url( home_url( '/contactus/' ) ); ?>">
				<img src="<?php echo kingring_support_pages_image( 'icon-chat.jpg' ); ?>" width="500" height="500" alt="">
				<strong><?php echo esc_html( $settings['contact_card_title'] ); ?></strong>
				<span class="kr-card__description"><?php echo esc_html( $settings['contact_card_text'] ); ?></span>
				<span class="kr-card__action"><?php echo esc_html( $settings['contact_card_action'] ); ?> <span aria-hidden="true">&nearr;</span></span>
			</a>
			<a class="kr-card" href="<?php echo esc_url( $settings['rate_url'] ); ?>">
				<img src="<?php echo kingring_support_pages_image( 'icon-order.jpg' ); ?>" width="500" height="500" alt="">
				<strong><?php echo esc_html( $settings['rate_card_title'] ); ?></strong>
				<span class="kr-card__description"><?php echo esc_html( $settings['rate_card_text'] ); ?></span>
				<span class="kr-card__action"><?php echo esc_html( $settings['rate_card_action'] ); ?> <span aria-hidden="true">&nearr;</span></span>
			</a>
			<a class="kr-card" href="<?php echo esc_url( $settings['store_url'] ); ?>">
				<img src="<?php echo esc_url( $store_card_image ); ?>" width="500" height="500" alt="">
				<strong><?php echo esc_html( $settings['store_card_title'] ); ?></strong>
				<span class="kr-card__description"><?php echo esc_html( $settings['store_card_text'] ); ?></span>
				<span class="kr-card__action"><?php echo esc_html( $settings['store_card_action'] ); ?> <span aria-hidden="true">&nearr;</span></span>
			</a>
		</div>

		<blockquote class="kr-support-quote">
			<p><?php echo esc_html( $settings['support_quote'] ); ?></p>
			<cite><?php echo esc_html( $settings['support_quote_author'] ); ?></cite>
		</blockquote>

	</section>
<?php if ( $store_theme ) : ?>
	</div>
	<?php get_footer(); ?>
<?php else : ?>
	</main>
	<?php kingring_support_pages_footer(); ?>
	<?php wp_footer(); ?>
	</body>
	</html>
<?php endif; ?>
