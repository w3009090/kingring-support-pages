<?php
// Lightweight WordPress-boundary tests; no database, uploads or emails are used.
// Run: php tests/page-routing.php
// Preview: php -S 127.0.0.1:8097 -t . tests/page-routing.php
if ( PHP_SAPI === 'cli-server' && str_starts_with( $_SERVER['REQUEST_URI'], '/assets/' ) ) {
	return false;
}

define( 'ABSPATH', dirname( __DIR__ ) . '/' );
define( 'OBJECT', 'OBJECT' );
$route = '';
$styles = array();
$response_status = 200;
$no_cache = false;
$saved_settings = array();
$active_stylesheet = 'legacy-theme';
$_SERVER['REQUEST_METHOD'] = 'GET';

function add_action( ...$args ) {}
function add_filter( ...$args ) {}
function plugin_basename( $file ) { return basename( dirname( $file ) ) . '/' . basename( $file ); }
function plugin_dir_path( $file ) { return dirname( $file ) . '/'; }
function plugin_dir_url( $file ) { return '/'; }
function get_option( $key, $default = false ) { return $GLOBALS['saved_settings']; }
function get_stylesheet() { return $GLOBALS['active_stylesheet']; }
function get_template() { return $GLOBALS['active_stylesheet']; }
function get_theme_file_uri( $path ) { return '/wp-content/themes/kingring-store' . $path; }
function get_page_by_path( $path, $output, $post_type ) { return 'k13' === $path ? (object) array( 'ID' => 13 ) : null; }
function wc_get_product( $id ) {
	return 13 === $id ? new class() {
		public function get_image_id() { return 1313; }
	} : false;
}
function wp_get_attachment_image_url( $id, $size ) { return 1313 === $id ? '/wp-content/uploads/k13-ring.jpg' : false; }
function wp_parse_args( $values, $defaults ) { return array_merge( $defaults, $values ); }
function is_404() { return $GLOBALS['route'] === '404'; }
function is_page( $slugs ) { return in_array( $GLOBALS['route'], (array) $slugs, true ); }
function wp_enqueue_style( $handle, $url, $dependencies, $version ) {
	$GLOBALS['styles'][ $handle ] = array( $url, $dependencies, $version );
}
function status_header( $code ) {
	$GLOBALS['response_status'] = $code;
	http_response_code( $code );
}
function nocache_headers() { $GLOBALS['no_cache'] = true; }
function home_url( $path ) { return $path; }
function language_attributes() { echo 'lang="en-US"'; }
function bloginfo( $key ) { echo 'UTF-8'; }
function body_class( $classes ) { echo 'class="' . esc_attr( $classes ) . '"'; }
function esc_html( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function esc_attr( $value ) { return esc_html( $value ); }
function esc_url( $value ) { return esc_html( $value ); }
function esc_textarea( $value ) { return esc_html( $value ); }
function __( $value, $domain = '' ) { return $value; }
function esc_html_e( $value, $domain = '' ) { echo esc_html( $value ); }
function esc_attr_e( $value, $domain = '' ) { echo esc_attr( $value ); }
function wp_body_open() {}
function wp_footer() {}
function get_header() { echo '<header class="theme-header">Store header</header><main id="content">'; }
function get_footer() { echo '</main><footer class="theme-footer">Store footer</footer>'; }
function wp_head() {
	echo '<title>King Ring preview</title>';
	foreach ( $GLOBALS['styles'] as $style ) {
		echo '<link rel="stylesheet" href="' . esc_url( $style[0] ) . '">';
	}
}
function get_privacy_policy_url() { return '/privacy-policy/'; }
function sanitize_text_field( $value ) { return strip_tags( trim( $value ) ); }
function wp_nonce_field( $action, $name ) { echo '<input type="hidden" name="' . esc_attr( $name ) . '" value="test-only">'; }
function selected( $a, $b ) { if ( $a === $b ) { echo 'selected'; } }
function checked( $value ) { if ( $value ) { echo 'checked'; } }

require ABSPATH . 'kingring-support-pages.php';

function expect( $condition, $message ) {
	if ( ! $condition ) {
		throw new RuntimeException( $message );
	}
}

function render_page( $slug ) {
	$GLOBALS['route'] = $slug;
	$GLOBALS['styles'] = array();
	$GLOBALS['response_status'] = 200;
	$GLOBALS['no_cache'] = false;
	kingring_support_pages_assets();
	ob_start();
	include kingring_support_pages_template( 'theme.php' );
	return ob_get_clean();
}

if ( PHP_SAPI === 'cli-server' ) {
	$path = trim( parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ), '/' );
	echo render_page( in_array( $path, array( 'support', 'contactus' ), true ) ? $path : '404' );
	return;
}

foreach ( array( 'support', 'contactus', '404' ) as $slug ) {
	$route = $slug;
	expect( kingring_support_pages_template( 'theme.php' ) === ABSPATH . 'templates/' . $slug . '.php', $slug . ' template' );
	$html = render_page( $slug );
	expect( count( $styles ) === 2, $slug . ' stylesheet count' );
	expect( $styles['kingring-support-pages-overrides'][1] === array( 'kingring-support-pages' ), 'CSS load order' );
	expect( $styles['kingring-support-pages-overrides'][2] === KINGRING_SUPPORT_PAGES_VERSION, 'CSS cache version' );
	expect( substr_count( $html, '<header class="kr-header">' ) === 1, $slug . ' shared header' );
	expect( substr_count( $html, '<footer class="kr-footer">' ) === 1, $slug . ' shared footer' );
	expect( str_contains( $html, '2027 King Ring' ), $slug . ' footer year' );
	expect( str_contains( $html, 'href="/support/"' ) && str_contains( $html, 'href="/contactus/"' ), $slug . ' navigation' );
	expect( $response_status === ( $slug === '404' ? 404 : 200 ), $slug . ' HTTP status' );
	if ( $slug === '404' ) {
		expect( $no_cache, 'Do not cache missing pages' );
		expect( str_contains( $html, 'Page not found' ) && str_contains( $html, 'Back to Support' ), '404 recovery content' );
		expect( ! str_contains( $html, 'kr-hero' ) && ! str_contains( $html, '<form' ), '404 has no support hero or contact form' );
	}
	if ( $slug === 'contactus' ) {
		foreach ( array( 'kingring_subject', 'kingring_name', 'kingring_email', 'kingring_order_number', 'kingring_message', 'kingring_attachments[]', 'kingring_privacy' ) as $field ) {
			expect( str_contains( $html, 'name="' . $field . '"' ), 'Contact field ' . $field );
		}
	}
}

$active_stylesheet = 'kingring-store';
foreach ( array( 'support', 'contactus' ) as $slug ) {
	$html = render_page( $slug );
	expect( count( $styles ) === 1, $slug . ' integrated stylesheet count' );
	expect( isset( $styles['kingring-support-pages-store'] ), $slug . ' integrated stylesheet' );
	expect( $styles['kingring-support-pages-store'][1] === array( 'kingring-store' ), $slug . ' store stylesheet dependency' );
	expect( substr_count( $html, 'class="theme-header"' ) === 1, $slug . ' store header once' );
	expect( substr_count( $html, 'class="theme-footer"' ) === 1, $slug . ' store footer once' );
	expect( ! str_contains( $html, '<header class="kr-header">' ), $slug . ' no standalone header' );
	if ( 'support' === $slug ) {
		expect( str_contains( $html, '/assets/images/hero.jpg' ), 'Support preserves original hero' );
		expect( str_contains( $html, '/assets/images/icon-store.jpg' ), 'Support preserves original store icon' );
		expect( ! str_contains( $html, 'krsp-support-hero__copy' ), 'Support does not use split hero copy' );
		expect( str_contains( $html, 'kr-support-quote' ), 'Support preserves brand quote' );
		expect( strpos( $html, 'class="kr-hero"' ) < strrpos( $html, '<h1>' ), 'Support title follows hero' );
	}
	if ( 'contactus' === $slug ) {
		expect( str_contains( $html, 'I agree to the Privacy Policy.' ), 'Contact keeps privacy consent text' );
	}
}

$route = '404';
$styles = array();
expect( kingring_support_pages_template( 'theme.php' ) === 'theme.php', 'Store theme owns 404' );
kingring_support_pages_assets();
expect( $styles === array(), 'Store 404 has no support assets' );

$active_stylesheet = 'legacy-theme';

foreach ( array( 'privacy-policy', 'home', 'post', 'search' ) as $slug ) {
	$route = $slug;
	$styles = array();
	expect( kingring_support_pages_template( 'theme.php' ) === 'theme.php', 'Unrelated theme template: ' . $slug );
	kingring_support_pages_assets();
	expect( $styles === array(), 'Unrelated page assets: ' . $slug );
}

$saved_settings = array(
	'logo_url' => 'https://example.com/custom-logo.png',
	'header_support_label' => 'Help',
	'public_email' => 'custom@example.com',
	'footer_copyright' => 'Copyright {year}',
);
$html = render_page( '404' );
foreach ( array( 'https://example.com/custom-logo.png', '>Help<', 'custom@example.com', 'Copyright 2027' ) as $value ) {
	expect( str_contains( $html, $value ), '404 preserves saved setting: ' . $value );
}

echo "PASS: Support, Contact, 404 rendering, routing, assets, status and saved settings.\n";
