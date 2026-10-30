<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$settings = kingring_support_pages_settings();
$hero_url = $settings['hero_image_url'] ? $settings['hero_image_url'] : kingring_support_pages_image( 'hero.jpg' );
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
	<section class="kr-hero">
		<img src="<?php echo esc_url( $hero_url ); ?>" width="1500" height="879" alt="<?php esc_attr_e( 'King Ring product packaging and rings', 'kingring-support-pages' ); ?>">
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
				<img src="<?php echo kingring_support_pages_image( 'icon-store.jpg' ); ?>" width="500" height="500" alt="">
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
</main>
<?php kingring_support_pages_footer(); ?>
<?php wp_footer(); ?>
</body>
</html>
