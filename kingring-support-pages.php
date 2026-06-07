<?php
/**
 * Plugin Name: King Ring Support Pages
 * Description: Standalone layouts for the /support/ and /contactus/ pages without changing the active WordPress theme.
 * Version: 1.9.0
 * Author: Codex
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'KINGRING_SUPPORT_PAGES_DIR', plugin_dir_path( __FILE__ ) );
define( 'KINGRING_SUPPORT_PAGES_URL', plugin_dir_url( __FILE__ ) );

function kingring_support_pages_template( $template ) {
	if ( is_page( 'support' ) ) {
		return KINGRING_SUPPORT_PAGES_DIR . 'templates/support.php';
	}

	if ( is_page( 'contactus' ) ) {
		return KINGRING_SUPPORT_PAGES_DIR . 'templates/contactus.php';
	}

	return $template;
}
add_filter( 'template_include', 'kingring_support_pages_template', 99 );

function kingring_support_pages_assets() {
	if ( is_page( array( 'support', 'contactus' ) ) ) {
		wp_enqueue_style(
			'kingring-support-pages',
			KINGRING_SUPPORT_PAGES_URL . 'assets/style.css',
			array(),
			'1.9.0'
		);
	}
}
add_action( 'wp_enqueue_scripts', 'kingring_support_pages_assets' );

function kingring_support_pages_image( $filename ) {
	return esc_url( KINGRING_SUPPORT_PAGES_URL . 'assets/images/' . ltrim( $filename, '/' ) );
}

function kingring_support_pages_header() {
	?>
	<a class="kr-screen-reader-text" href="#kr-content"><?php esc_html_e( 'Skip to content', 'kingring-support-pages' ); ?></a>
	<header class="kr-header">
		<div class="kr-header__inner">
			<a class="kr-logo" href="<?php echo esc_url( home_url( '/support/' ) ); ?>" aria-label="<?php esc_attr_e( 'King Ring Support', 'kingring-support-pages' ); ?>">
				<img src="<?php echo kingring_support_pages_image( 'logo-line-white.png' ); ?>" width="550" height="103" alt="<?php esc_attr_e( 'King Ring', 'kingring-support-pages' ); ?>">
			</a>
			<nav class="kr-nav" aria-label="<?php esc_attr_e( 'Support menu', 'kingring-support-pages' ); ?>">
				<a href="<?php echo esc_url( home_url( '/support/' ) ); ?>"><?php esc_html_e( 'Support', 'kingring-support-pages' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/contactus/' ) ); ?>"><?php esc_html_e( 'Contact Us', 'kingring-support-pages' ); ?></a>
				<a class="kr-store-button" href="https://www.amazon.com/kingring">
					<span><?php esc_html_e( 'Store', 'kingring-support-pages' ); ?></span>
					<span class="kr-store-button__arrow" aria-hidden="true">&rarr;</span>
				</a>
			</nav>
		</div>
	</header>
	<?php
}

function kingring_support_pages_footer() {
	?>
	<footer class="kr-footer">
		<div class="kr-footer__inner">
			<p>
				<a href="<?php echo esc_url( home_url( '/support/' ) ); ?>"><?php esc_html_e( 'Support Main Page', 'kingring-support-pages' ); ?></a>
				| King Ring TM | All Rights Reserved |
				<a href="mailto:support@kingring.store">support@kingring.store</a>
				| <?php echo esc_html( gmdate( 'Y' ) ); ?>
			</p>
			<a href="<?php echo esc_url( home_url( '/support/' ) ); ?>">
				<img src="<?php echo kingring_support_pages_image( 'logo-footer-dark.png' ); ?>" width="427" height="317" alt="<?php esc_attr_e( 'King Ring logo', 'kingring-support-pages' ); ?>">
			</a>
		</div>
	</footer>
	<?php
}
