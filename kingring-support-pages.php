<?php
/**
 * Plugin Name: Codex Plugin
 * Description: Standalone layouts for the /support/ and /contactus/ pages without changing the active WordPress theme.
 * Version: 2.4.7
 * Author: Codex
 * Update URI: https://github.com/w3009090/kingring-support-pages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'KINGRING_SUPPORT_PAGES_DIR', plugin_dir_path( __FILE__ ) );
define( 'KINGRING_SUPPORT_PAGES_URL', plugin_dir_url( __FILE__ ) );
define( 'KINGRING_SUPPORT_PAGES_VERSION', '2.4.7' );
define( 'KINGRING_SUPPORT_PAGES_GITHUB_API', 'https://api.github.com/repos/w3009090/kingring-support-pages/releases/latest' );

function kingring_support_pages_defaults() {
	return array(
		'header_support_label'     => 'Support',
		'header_contact_label'     => 'Contact',
		'header_store_label'       => 'Store',
		'store_url'                => 'https://www.amazon.com/kingring',
		'support_eyebrow'          => 'Customer Care',
		'support_title'            => 'How can we help you today?',
		'support_intro'            => 'Honest support, quick replies, and a love for every detail — just like the rings we make.',
		'contact_card_title'       => 'Contact Us',
		'contact_card_text'        => 'Questions, returns, or feedback — write to our team and we’ll reply within a few hours.',
		'contact_card_action'      => 'Open Form',
		'rate_card_title'          => 'Rate Your Order',
		'rate_card_text'           => 'Already received your ring? Share a quick review on Amazon — it means a lot.',
		'rate_card_action'         => 'Rate on Amazon',
		'rate_url'                 => 'https://www.amazon.com/gp/your-account/order-history?opt=ab&search=King+Ring',
		'store_card_title'         => 'Visit the Store',
		'store_card_text'          => 'Browse the full King Ring collection and find your next favourite piece.',
		'store_card_action'        => 'Open Store',
		'support_quote'            => '“Every ring carries a story. We’re here to make sure yours is a good one.”',
		'support_quote_author'     => '— King Ring Team',
		'contact_eyebrow'          => 'Get in Touch',
		'contact_title'            => 'We’d love to hear from you.',
		'contact_intro'            => 'Fill out the form and we’ll reply within a few hours.',
		'name_label'               => 'Name',
		'email_label'              => 'Email',
		'order_number_label'       => 'Order Number (if available)',
		'message_label'            => 'Message',
		'submit_label'             => 'Send Message',
		'form_recipient'           => 'support@kingring.store',
		'success_message'          => 'Thank you. Your message has been sent.',
		'address_heading'          => 'Address',
		'address_text'             => "King Ring TM\n18117 Biscayne Blvd STE 2640\nMiami, FL 33160, United States",
		'email_heading'            => 'Email',
		'public_email'             => 'support@kingring.store',
		'reply_time_heading'       => 'Reply Time',
		'reply_time_text'          => 'Within a few hours, Mon–Fri.',
		'map_url'                  => 'https://maps.google.com/maps?q=18117%20Biscayne%20Blvd&t=m&z=13&output=embed&iwloc=near',
		'footer_copyright'         => '© 2027 King Ring TM · All rights reserved',
		'logo_url'                 => '',
		'hero_image_url'           => '',
		'contact_intro'            => 'Fill out the form and we will reply within 1 business day.',
		'subject_label'            => 'Subject',
		'subject_options'          => "Return or Exchange\nSize Issue\nDamaged or Incorrect Item\nOrder Status\nProduct Question\nOther",
		'attachment_label'         => 'Attach files (optional)',
		'attachment_help'          => 'JPG, PNG, WEBP or PDF. Up to 3 files, 5 MB each.',
		'privacy_label'            => 'I agree to the Privacy Policy.',
		'privacy_url'              => '',
		'success_message'          => 'Thank you. Your request {ticket} has been sent.',
		'auto_reply_subject'       => 'We received your King Ring support request {ticket}',
		'auto_reply_message'       => "Hello {name},\n\nThank you for contacting King Ring Support. We received your request {ticket} about \"{subject}\".\n\nOur team will reply within 1 business day.\n\nKing Ring Support",
		'reply_time_text'          => 'Within 1 business day, Mon-Fri.',
	);
}

function kingring_support_pages_settings() {
	$saved = get_option( 'kingring_support_pages_settings', array() );
	return wp_parse_args( is_array( $saved ) ? $saved : array(), kingring_support_pages_defaults() );
}

function kingring_support_pages_setting( $key ) {
	$settings = kingring_support_pages_settings();
	return isset( $settings[ $key ] ) ? $settings[ $key ] : '';
}

function kingring_support_pages_subjects() {
	$subjects = preg_split( '/\r\n|\r|\n/', kingring_support_pages_setting( 'subject_options' ) );
	$subjects = array_filter( array_map( 'sanitize_text_field', $subjects ) );
	return array_values( $subjects );
}

function kingring_support_pages_generate_ticket() {
	return 'KR-' . gmdate( 'Ymd' ) . '-' . strtoupper( wp_generate_password( 6, false, false ) );
}

function kingring_support_pages_replace_tokens( $text, $tokens ) {
	foreach ( $tokens as $token => $value ) {
		$text = str_replace( '{' . $token . '}', $value, $text );
	}
	return $text;
}

function kingring_support_pages_upload_attachments() {
	$result = array(
		'paths' => array(),
		'error' => '',
	);

	if ( empty( $_FILES['kingring_attachments']['name'] ) || ! is_array( $_FILES['kingring_attachments']['name'] ) ) {
		return $result;
	}

	$files = $_FILES['kingring_attachments'];
	$count = count( array_filter( $files['name'] ) );

	if ( $count > 3 ) {
		$result['error'] = __( 'Please attach no more than 3 files.', 'kingring-support-pages' );
		return $result;
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';

	$allowed_mimes = array(
		'jpg|jpeg|jpe' => 'image/jpeg',
		'png'          => 'image/png',
		'webp'         => 'image/webp',
		'pdf'          => 'application/pdf',
	);

	foreach ( $files['name'] as $index => $filename ) {
		if ( ! $filename ) {
			continue;
		}

		$error_code = isset( $files['error'][ $index ] ) ? (int) $files['error'][ $index ] : UPLOAD_ERR_NO_FILE;
		$file_size  = isset( $files['size'][ $index ] ) ? (int) $files['size'][ $index ] : 0;

		if ( UPLOAD_ERR_OK !== $error_code || $file_size > 5 * MB_IN_BYTES ) {
			$result['error'] = __( 'Each attachment must be a valid file no larger than 5 MB.', 'kingring-support-pages' );
			break;
		}

		$file = array(
			'name'     => sanitize_file_name( $filename ),
			'type'     => isset( $files['type'][ $index ] ) ? $files['type'][ $index ] : '',
			'tmp_name' => isset( $files['tmp_name'][ $index ] ) ? $files['tmp_name'][ $index ] : '',
			'error'    => $error_code,
			'size'     => $file_size,
		);

		$uploaded = wp_handle_upload(
			$file,
			array(
				'test_form' => false,
				'mimes'     => $allowed_mimes,
			)
		);

		if ( isset( $uploaded['error'] ) || empty( $uploaded['file'] ) ) {
			$result['error'] = __( 'One of the attached files has an unsupported format.', 'kingring-support-pages' );
			break;
		}

		$result['paths'][] = $uploaded['file'];
	}

	if ( $result['error'] ) {
		kingring_support_pages_delete_attachments( $result['paths'] );
		$result['paths'] = array();
	}

	return $result;
}

function kingring_support_pages_delete_attachments( $paths ) {
	foreach ( $paths as $path ) {
		if ( is_string( $path ) && file_exists( $path ) ) {
			wp_delete_file( $path );
		}
	}
}

function kingring_support_pages_sanitize_settings( $input ) {
	$defaults = kingring_support_pages_defaults();
	$output   = array();
	$urls     = array( 'store_url', 'rate_url', 'map_url', 'logo_url', 'hero_image_url', 'privacy_url' );
	$emails   = array( 'form_recipient', 'public_email' );
	$areas    = array(
		'support_intro',
		'contact_card_text',
		'rate_card_text',
		'store_card_text',
		'support_quote',
		'contact_intro',
		'address_text',
		'subject_options',
		'auto_reply_message',
	);

	foreach ( $defaults as $key => $default ) {
		$value = isset( $input[ $key ] ) ? wp_unslash( $input[ $key ] ) : $default;

		if ( in_array( $key, $urls, true ) ) {
			$output[ $key ] = esc_url_raw( $value );
		} elseif ( in_array( $key, $emails, true ) ) {
			$output[ $key ] = sanitize_email( $value );
		} elseif ( in_array( $key, $areas, true ) ) {
			$output[ $key ] = sanitize_textarea_field( $value );
		} else {
			$output[ $key ] = sanitize_text_field( $value );
		}

		if ( in_array( $key, array( 'form_recipient', 'public_email', 'store_url', 'rate_url', 'map_url', 'subject_options' ), true ) && ! $output[ $key ] ) {
			$output[ $key ] = $default;
		}
	}

	return $output;
}

function kingring_support_pages_upgrade_settings() {
	$installed_version = get_option( 'kingring_support_pages_installed_version', '0' );

	if ( version_compare( $installed_version, KINGRING_SUPPORT_PAGES_VERSION, '>=' ) ) {
		return;
	}

	$saved = get_option( 'kingring_support_pages_settings', array() );

	if ( is_array( $saved ) ) {
		if ( isset( $saved['success_message'] ) && 'Thank you. Your message has been sent.' === $saved['success_message'] ) {
			$saved['success_message'] = 'Thank you. Your request {ticket} has been sent.';
		}

		if ( isset( $saved['contact_intro'] ) && false !== stripos( $saved['contact_intro'], 'within a few hours' ) ) {
			$saved['contact_intro'] = 'Fill out the form and we will reply within 1 business day.';
		}

		if ( isset( $saved['reply_time_text'] ) && false !== stripos( $saved['reply_time_text'], 'few hours' ) ) {
			$saved['reply_time_text'] = 'Within 1 business day, Mon-Fri.';
		}

		update_option( 'kingring_support_pages_settings', $saved );
	}

	update_option( 'kingring_support_pages_installed_version', KINGRING_SUPPORT_PAGES_VERSION );
}
add_action( 'plugins_loaded', 'kingring_support_pages_upgrade_settings' );

function kingring_support_pages_admin_menu() {
	add_menu_page(
		__( 'King Ring Support', 'kingring-support-pages' ),
		__( 'King Ring Support', 'kingring-support-pages' ),
		'manage_options',
		'kingring-support-pages',
		'kingring_support_pages_settings_page',
		'dashicons-format-chat',
		58
	);
}
add_action( 'admin_menu', 'kingring_support_pages_admin_menu' );

function kingring_support_pages_admin_assets( $hook ) {
	if ( 'toplevel_page_kingring-support-pages' === $hook ) {
		wp_enqueue_media();
	}
}
add_action( 'admin_enqueue_scripts', 'kingring_support_pages_admin_assets' );

function kingring_support_pages_register_settings() {
	register_setting(
		'kingring_support_pages_group',
		'kingring_support_pages_settings',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'kingring_support_pages_sanitize_settings',
			'default'           => kingring_support_pages_defaults(),
		)
	);
}
add_action( 'admin_init', 'kingring_support_pages_register_settings' );

function kingring_support_pages_admin_field( $settings, $key, $label, $type = 'text' ) {
	$name  = 'kingring_support_pages_settings[' . $key . ']';
	$value = isset( $settings[ $key ] ) ? $settings[ $key ] : '';
	?>
	<label class="krsp-admin-field">
		<span><?php echo esc_html( $label ); ?></span>
		<?php if ( 'textarea' === $type ) : ?>
			<textarea name="<?php echo esc_attr( $name ); ?>" rows="3"><?php echo esc_textarea( $value ); ?></textarea>
		<?php elseif ( 'media' === $type ) : ?>
			<div class="krsp-media-field">
				<input name="<?php echo esc_attr( $name ); ?>" type="url" value="<?php echo esc_attr( $value ); ?>">
				<button class="button krsp-media-select" type="button"><?php esc_html_e( 'Choose Image', 'kingring-support-pages' ); ?></button>
			</div>
		<?php else : ?>
			<input name="<?php echo esc_attr( $name ); ?>" type="<?php echo esc_attr( $type ); ?>" value="<?php echo esc_attr( $value ); ?>">
		<?php endif; ?>
	</label>
	<?php
}

function kingring_support_pages_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$settings = kingring_support_pages_settings();
	?>
	<div class="wrap krsp-admin">
		<h1><?php esc_html_e( 'King Ring Support', 'kingring-support-pages' ); ?></h1>
		<p><?php esc_html_e( 'Edit the support and contact pages here. Design and layout remain protected.', 'kingring-support-pages' ); ?></p>
		<form action="options.php" method="post">
			<?php settings_fields( 'kingring_support_pages_group' ); ?>

			<section>
				<h2><?php esc_html_e( 'Header', 'kingring-support-pages' ); ?></h2>
				<div class="krsp-admin-grid">
					<?php kingring_support_pages_admin_field( $settings, 'header_support_label', 'Support menu label' ); ?>
					<?php kingring_support_pages_admin_field( $settings, 'header_contact_label', 'Contact menu label' ); ?>
					<?php kingring_support_pages_admin_field( $settings, 'header_store_label', 'Store button label' ); ?>
					<?php kingring_support_pages_admin_field( $settings, 'store_url', 'Store URL', 'url' ); ?>
					<?php kingring_support_pages_admin_field( $settings, 'logo_url', 'Header logo', 'media' ); ?>
				</div>
			</section>

			<section>
				<h2><?php esc_html_e( 'Support Page', 'kingring-support-pages' ); ?></h2>
				<div class="krsp-admin-grid">
					<?php kingring_support_pages_admin_field( $settings, 'support_eyebrow', 'Small heading' ); ?>
					<?php kingring_support_pages_admin_field( $settings, 'support_title', 'Main heading' ); ?>
					<?php kingring_support_pages_admin_field( $settings, 'support_intro', 'Introduction', 'textarea' ); ?>
					<?php kingring_support_pages_admin_field( $settings, 'hero_image_url', 'Main image', 'media' ); ?>
					<?php kingring_support_pages_admin_field( $settings, 'contact_card_title', 'Contact card title' ); ?>
					<?php kingring_support_pages_admin_field( $settings, 'contact_card_text', 'Contact card text', 'textarea' ); ?>
					<?php kingring_support_pages_admin_field( $settings, 'contact_card_action', 'Contact card button' ); ?>
					<?php kingring_support_pages_admin_field( $settings, 'rate_card_title', 'Rating card title' ); ?>
					<?php kingring_support_pages_admin_field( $settings, 'rate_card_text', 'Rating card text', 'textarea' ); ?>
					<?php kingring_support_pages_admin_field( $settings, 'rate_card_action', 'Rating card button' ); ?>
					<?php kingring_support_pages_admin_field( $settings, 'rate_url', 'Rating URL', 'url' ); ?>
					<?php kingring_support_pages_admin_field( $settings, 'store_card_title', 'Store card title' ); ?>
					<?php kingring_support_pages_admin_field( $settings, 'store_card_text', 'Store card text', 'textarea' ); ?>
					<?php kingring_support_pages_admin_field( $settings, 'store_card_action', 'Store card button' ); ?>
					<?php kingring_support_pages_admin_field( $settings, 'support_quote', 'Quote', 'textarea' ); ?>
					<?php kingring_support_pages_admin_field( $settings, 'support_quote_author', 'Quote author' ); ?>
				</div>
			</section>

			<section>
				<h2><?php esc_html_e( 'Contact Page', 'kingring-support-pages' ); ?></h2>
				<div class="krsp-admin-grid">
					<?php kingring_support_pages_admin_field( $settings, 'contact_eyebrow', 'Small heading' ); ?>
					<?php kingring_support_pages_admin_field( $settings, 'contact_title', 'Main heading' ); ?>
					<?php kingring_support_pages_admin_field( $settings, 'contact_intro', 'Introduction', 'textarea' ); ?>
					<?php kingring_support_pages_admin_field( $settings, 'subject_label', 'Subject field label' ); ?>
					<?php kingring_support_pages_admin_field( $settings, 'subject_options', 'Subject options (one per line)', 'textarea' ); ?>
					<?php kingring_support_pages_admin_field( $settings, 'name_label', 'Name field label' ); ?>
					<?php kingring_support_pages_admin_field( $settings, 'email_label', 'Email field label' ); ?>
					<?php kingring_support_pages_admin_field( $settings, 'order_number_label', 'Order number field label' ); ?>
					<?php kingring_support_pages_admin_field( $settings, 'message_label', 'Message field label' ); ?>
					<?php kingring_support_pages_admin_field( $settings, 'attachment_label', 'Attachment field label' ); ?>
					<?php kingring_support_pages_admin_field( $settings, 'attachment_help', 'Attachment help text' ); ?>
					<?php kingring_support_pages_admin_field( $settings, 'privacy_label', 'Privacy agreement text' ); ?>
					<?php kingring_support_pages_admin_field( $settings, 'privacy_url', 'Privacy Policy URL', 'url' ); ?>
					<?php kingring_support_pages_admin_field( $settings, 'submit_label', 'Submit button label' ); ?>
					<?php kingring_support_pages_admin_field( $settings, 'form_recipient', 'Form recipient email', 'email' ); ?>
					<?php kingring_support_pages_admin_field( $settings, 'success_message', 'Success message' ); ?>
					<?php kingring_support_pages_admin_field( $settings, 'auto_reply_subject', 'Automatic reply subject' ); ?>
					<?php kingring_support_pages_admin_field( $settings, 'auto_reply_message', 'Automatic reply message', 'textarea' ); ?>
					<?php kingring_support_pages_admin_field( $settings, 'address_heading', 'Address heading' ); ?>
					<?php kingring_support_pages_admin_field( $settings, 'address_text', 'Address', 'textarea' ); ?>
					<?php kingring_support_pages_admin_field( $settings, 'email_heading', 'Email heading' ); ?>
					<?php kingring_support_pages_admin_field( $settings, 'public_email', 'Public email', 'email' ); ?>
					<?php kingring_support_pages_admin_field( $settings, 'reply_time_heading', 'Reply time heading' ); ?>
					<?php kingring_support_pages_admin_field( $settings, 'reply_time_text', 'Reply time text' ); ?>
					<?php kingring_support_pages_admin_field( $settings, 'map_url', 'Google Maps embed URL', 'url' ); ?>
				</div>
			</section>

			<section>
				<h2><?php esc_html_e( 'Footer', 'kingring-support-pages' ); ?></h2>
				<div class="krsp-admin-grid">
					<?php kingring_support_pages_admin_field( $settings, 'footer_copyright', 'Copyright text ({year} inserts 2027)' ); ?>
				</div>
			</section>

			<?php submit_button( __( 'Save Changes', 'kingring-support-pages' ) ); ?>
		</form>
	</div>
	<style>
		.krsp-admin{max-width:1180px}.krsp-admin>p{color:#646970}.krsp-admin section{margin:22px 0;padding:22px;background:#fff;border:1px solid #dcdcde;border-radius:4px}.krsp-admin section h2{margin-top:0}.krsp-admin-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px 24px}.krsp-admin-field{display:grid;gap:7px}.krsp-admin-field span{font-weight:600}.krsp-admin-field input,.krsp-admin-field textarea{width:100%;max-width:none}.krsp-admin-field textarea{resize:vertical}.krsp-media-field{display:flex;gap:8px}.krsp-media-field input{flex:1}@media(max-width:782px){.krsp-admin-grid{grid-template-columns:1fr}.krsp-media-field{align-items:stretch;flex-direction:column}}
	</style>
	<script>
		document.addEventListener('click', function (event) {
			var button = event.target.closest('.krsp-media-select');
			if (!button || typeof wp === 'undefined' || !wp.media) {
				return;
			}
			event.preventDefault();
			var input = button.parentElement.querySelector('input');
			var frame = wp.media({ title: 'Choose Image', button: { text: 'Use Image' }, multiple: false });
			frame.on('select', function () {
				var attachment = frame.state().get('selection').first().toJSON();
				input.value = attachment.url;
			});
			frame.open();
		});
	</script>
	<?php
}

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
			KINGRING_SUPPORT_PAGES_VERSION
		);
		wp_enqueue_style(
			'kingring-support-pages-overrides',
			KINGRING_SUPPORT_PAGES_URL . 'assets/card-borders.css',
			array( 'kingring-support-pages' ),
			KINGRING_SUPPORT_PAGES_VERSION
		);
	}
}
add_action( 'wp_enqueue_scripts', 'kingring_support_pages_assets' );

/**
 * Fetch and cache the latest public GitHub release.
 *
 * @return array|false
 */
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

/**
 * Find a downloadable ZIP attached to the release.
 *
 * @param array $release GitHub release data.
 * @return string
 */
function kingring_support_pages_release_package( $release ) {
	if ( empty( $release['assets'] ) || ! is_array( $release['assets'] ) ) {
		return '';
	}

	foreach ( $release['assets'] as $asset ) {
		$name = isset( $asset['name'] ) ? strtolower( $asset['name'] ) : '';
		$url  = isset( $asset['browser_download_url'] ) ? $asset['browser_download_url'] : '';

		if ( $url && 'codexplugin.zip' === $name ) {
			return esc_url_raw( $url );
		}
	}

	foreach ( $release['assets'] as $asset ) {
		$name = isset( $asset['name'] ) ? strtolower( $asset['name'] ) : '';
		$url  = isset( $asset['browser_download_url'] ) ? $asset['browser_download_url'] : '';

		if ( $url && in_array( $name, array( 'codex plugin.zip', 'codex.plugin.zip' ), true ) ) {
			return esc_url_raw( $url );
		}
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

/**
 * Add GitHub releases to the standard WordPress update screen.
 *
 * @param object $transient Update transient.
 * @return object
 */
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

	$update = (object) array(
			'id'           => 'github.com/w3009090/kingring-support-pages',
			'slug'         => 'kingring-support-pages',
			'plugin'       => $plugin_file,
			'new_version'  => $version,
			'url'          => 'https://github.com/w3009090/kingring-support-pages',
			'package'      => $package,
			'tested'       => '',
			'requires_php' => '7.4',
	);
	if ( $package && version_compare( KINGRING_SUPPORT_PAGES_VERSION, $version, '<' ) ) {
		$transient->response[ $plugin_file ] = $update;
	} else {
		$transient->no_update[ $plugin_file ] = $update;
	}

	return $transient;
}
add_filter( 'pre_set_site_transient_update_plugins', 'kingring_support_pages_check_for_update' );

function kingring_support_pages_enable_auto_updates( $update, $item ) {
	if ( isset( $item->plugin ) && plugin_basename( __FILE__ ) === $item->plugin ) {
		return true;
	}
	return $update;
}
add_filter( 'auto_update_plugin', 'kingring_support_pages_enable_auto_updates', 10, 2 );

/**
 * Display GitHub release information in the WordPress plugin details modal.
 *
 * @param false|object|array $result Existing result.
 * @param string             $action API action.
 * @param object             $args   Plugin API arguments.
 * @return false|object|array
 */
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
		'name'          => 'Codex Plugin',
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
	$settings = kingring_support_pages_settings();
	$logo_url = $settings['logo_url'] ? $settings['logo_url'] : kingring_support_pages_image( 'logo-line-white.png' );
	?>
	<a class="kr-screen-reader-text" href="#kr-content"><?php esc_html_e( 'Skip to content', 'kingring-support-pages' ); ?></a>
	<header class="kr-header">
		<div class="kr-header__inner">
			<a class="kr-logo" href="<?php echo esc_url( home_url( '/support/' ) ); ?>" aria-label="<?php esc_attr_e( 'King Ring Support', 'kingring-support-pages' ); ?>">
				<img src="<?php echo esc_url( $logo_url ); ?>" width="550" height="103" alt="<?php esc_attr_e( 'King Ring', 'kingring-support-pages' ); ?>">
			</a>
			<nav class="kr-nav" aria-label="<?php esc_attr_e( 'Support menu', 'kingring-support-pages' ); ?>">
				<a href="<?php echo esc_url( home_url( '/support/' ) ); ?>"><?php echo esc_html( $settings['header_support_label'] ); ?></a>
				<a href="<?php echo esc_url( home_url( '/contactus/' ) ); ?>"><?php echo esc_html( $settings['header_contact_label'] ); ?></a>
				<a class="kr-store-button" href="<?php echo esc_url( $settings['store_url'] ); ?>">
					<span><?php echo esc_html( $settings['header_store_label'] ); ?></span>
					<span class="kr-store-button__arrow" aria-hidden="true">&rarr;</span>
				</a>
			</nav>
		</div>
	</header>
	<?php
}

function kingring_support_pages_footer() {
	$settings  = kingring_support_pages_settings();
	$copyright = str_replace( '{year}', '2027', $settings['footer_copyright'] );
	$email     = $settings['public_email'];
	?>
	<footer class="kr-footer">
		<div class="kr-footer__inner">
			<p class="kr-footer__copyright kr-footer__copyright--editable"><?php echo esc_html( $copyright ); ?></p>
			<a class="kr-footer__email" href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
		</div>
	</footer>
	<?php
}
