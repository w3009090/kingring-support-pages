<?php
/**
 * Plugin Name: King Ring Support Pages
 * Description: Standalone layouts for the /support/ and /contactus/ pages without changing the active WordPress theme.
 * Version: 2.0.0
 * Author: Codex
 * Update URI: https://github.com/w3009090/kingring-support-pages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'KINGRING_SUPPORT_PAGES_DIR', plugin_dir_path( __FILE__ ) );
define( 'KINGRING_SUPPORT_PAGES_URL', plugin_dir_url( __FILE__ ) );
define( 'KINGRING_SUPPORT_PAGES_VERSION', '2.0.0' );
define( 'KINGRING_SUPPORT_PAGES_GITHUB_API', 'https://api.github.com/repos/w3009090/kingring-support-pages/releases/latest' );

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
		wp_enqueue_style( 'kingring-support-pages', KINGRING_SUPPORT_PAGES_URL . 'assets/style.css', array(), KINGRING_SUPPORT_PAGES_VERSION );
	}
}
add_action( 'wp_enqueue_scripts', 'kingring_support_pages_assets' );

function kingring_support_pages_latest_release() {
	$cache_key = 'kingring_support_pages_github_release';
	$release   = get_site_transient( $cache_key );
	if ( false !== $release ) {
		return is_array( $release ) ? $release : false;
	}
	$response = wp_remote_get(
		KINGRING_SUPPORT_PAGES_GITHUB_API,
		array(
			'timeout' => 12,
			'headers' => array(
				'Accept'     => 'application/vnd.github+json',
				'User-Agent' => 'King-Ring-Support-Pages/' . KINGRING_SUPPORT_PAGES_VERSION,
			),
		)
	);
	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
		set_site_transient( $cache_key, 'error', HOUR_IN_SECONDS );
		return false;
	}
	$release = json_decode( wp_remote_retrieve_body( $response ), true );
	if ( ! is_array( $release ) || empty( $release['tag_name'] ) ) {
		set_site_transient( $cache_key, 'error', HOUR_IN_SECONDS );
		return false;
	}
	set_site_transient( $cache_key, $release, 6 * HOUR_IN_SECONDS );
	return $release;
}

function kingring_support_pages_release_package( $release ) {
	if ( empty( $release['assets'] ) || ! is_array( $release['assets'] ) ) {
		return '';
	}
	foreach ( $release['assets'] as $asset ) {
		$name = isset( $asset['name'] ) ? strtolower( $asset['name'] ) : '';
		$url  = isset( $asset['browser_download_url'] ) ? $asset['browser_download_url'] : '';
		if ( $url && 'kingring-support-pages.zip' === $name ) {
			return esc_url_raw( $url );
		}
	}
	foreach ( $release['assets'] as $asset ) {
		$name = isset( $asset['name'] ) ? strtolower( $asset['name'] ) : '';
		$url  = isset( $asset['browser_download_url'] ) ? $asset['browser_download_url'] : '';
		if ( $url && '.zip' === substr( $name, -4 ) ) {
			return esc_url_raw( $url );
		}
	}
	return '';
}

function kingring_support_pages_check_for_update( $transient ) {
	if ( empty( $transient->checked ) ) {
		return $transient;
	}
	$plugin_file = plugin_basename( __FILE__ );
	$release     = kingring_support_pages_latest_release();
	if ( ! $release ) {
		return $transient;
	}
	$version = ltrim( sanitize_text_field( $release['tag_name'] ), 'vV' );
	$package = kingring_support_pages_release_package( $release );
	if ( $package && version_compare( KINGRING_SUPPORT_PAGES_VERSION, $version, '<' ) ) {
		$transient->response[ $plugin_file ] = (object) array(
			'id'           => 'github.com/w3009090/kingring-support-pages',
			'slug'         => 'kingring-support-pages',
			'plugin'       => $plugin_file,
			'new_version'  => $version,
			'url'          => 'https://github.com/w3009090/kingring-support-pages',
			'package'      => $package,
			'tested'       => '',
			'requires_php' => '7.4',
		);
	}
	return $transient;
}
add_filter( 'pre_set_site_transient_update_plugins', 'kingring_support_pages_check_for_update' );

function kingring_support_pages_plugin_information( $result, $action, $args ) {
	if ( 'plugin_information' !== $action || empty( $args->slug ) || 'kingring-support-pages' !== $args->slug ) {
		return $result;
	}
	$release = kingring_support_pages_latest_release();
	if ( ! $release ) {
		return $result;
	}
	$version = ltrim( sanitize_text_field( $release['tag_name'] ), 'vV' );
	$package = kingring_support_pages_release_package( $release );
	$notes   = isset( $release['body'] ) ? wp_kses_post( nl2br( $release['body'] ) ) : '';
	return (object) array(
		'name'          => 'King Ring Support Pages',
		'slug'          => 'kingring-support-pages',
		'version'       => $version,
		'author'        => '<a href="https://github.com/w3009090">w3009090</a>',
		'homepage'      => 'https://github.com/w3009090/kingring-support-pages',
		'download_link' => $package,
		'requires_php'  => '7.4',
		'sections'      => array(
			'description' => 'Standalone layouts for the King Ring support and contact pages.',
			'changelog'   => $notes ? $notes : 'See the latest GitHub release for changes.',
		),
	);
}
add_filter( 'plugins_api', 'kingring_support_pages_plugin_information', 20, 3 );

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
				<a class="kr-store-button" href="https://www.amazon.com/kingring"><span><?php esc_html_e( 'Store', 'kingring-support-pages' ); ?></span><span class="kr-store-button__arrow" aria-hidden="true">&rarr;</span></a>
			</nav>
		</div>
	</header>
	<?php
}

function kingring_support_pages_footer() {
	?>
	<footer class="kr-footer">
		<div class="kr-footer__inner">
			<p><a href="<?php echo esc_url( home_url( '/support/' ) ); ?>"><?php esc_html_e( 'Support Main Page', 'kingring-support-pages' ); ?></a> | King Ring TM | All Rights Reserved | <a href="mailto:support@kingring.store">support@kingring.store</a> | <?php echo esc_html( gmdate( 'Y' ) ); ?></p>
			<a href="<?php echo esc_url( home_url( '/support/' ) ); ?>"><img src="<?php echo kingring_support_pages_image( 'logo-footer-dark.png' ); ?>" width="427" height="317" alt="<?php esc_attr_e( 'King Ring logo', 'kingring-support-pages' ); ?>"></a>
		</div>
	</footer>
	<?php
}
