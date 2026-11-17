<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

status_header( 404 );
nocache_headers();
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'kr-page kr-not-found' ); ?>>
<?php wp_body_open(); ?>
<?php kingring_support_pages_header(); ?>
<main id="kr-content" class="kr-not-found__main">
	<section class="kr-not-found__content" aria-labelledby="kr-not-found-title">
		<p class="kr-not-found__code">404</p>
		<h1 id="kr-not-found-title"><?php esc_html_e( 'Page not found', 'kingring-support-pages' ); ?></h1>
		<p class="kr-not-found__intro"><?php esc_html_e( 'This page may have moved, or the address may be incorrect.', 'kingring-support-pages' ); ?></p>
		<div class="kr-not-found__actions">
			<a class="kr-not-found__button" href="<?php echo esc_url( home_url( '/support/' ) ); ?>"><?php esc_html_e( 'Back to Support', 'kingring-support-pages' ); ?></a>
			<a class="kr-not-found__contact" href="<?php echo esc_url( home_url( '/contactus/' ) ); ?>"><?php esc_html_e( 'Contact Us', 'kingring-support-pages' ); ?></a>
		</div>
	</section>
</main>
<?php kingring_support_pages_footer(); ?>
<?php wp_footer(); ?>
</body>
</html>
