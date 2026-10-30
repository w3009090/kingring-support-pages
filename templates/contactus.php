<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$settings       = kingring_support_pages_settings();
$subjects       = kingring_support_pages_subjects();
$privacy_url    = $settings['privacy_url'] ? $settings['privacy_url'] : get_privacy_policy_url();
$privacy_url    = $privacy_url ? $privacy_url : home_url( '/privacy-policy/' );
$status         = '';
$error          = '';
$name           = '';
$email          = '';
$subject        = '';
$order_number   = '';
$message        = '';
$privacy_agreed = false;

if ( 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $_POST['kingring_contact_nonce'] ) ) {
	$nonce = sanitize_text_field( wp_unslash( $_POST['kingring_contact_nonce'] ) );

	if ( ! wp_verify_nonce( $nonce, 'kingring_contact_form' ) ) {
		$error = __( 'Please refresh the page and try again.', 'kingring-support-pages' );
	} else {
		$name           = isset( $_POST['kingring_name'] ) ? sanitize_text_field( wp_unslash( $_POST['kingring_name'] ) ) : '';
		$email          = isset( $_POST['kingring_email'] ) ? sanitize_email( wp_unslash( $_POST['kingring_email'] ) ) : '';
		$subject        = isset( $_POST['kingring_subject'] ) ? sanitize_text_field( wp_unslash( $_POST['kingring_subject'] ) ) : '';
		$order_number   = isset( $_POST['kingring_order_number'] ) ? sanitize_text_field( wp_unslash( $_POST['kingring_order_number'] ) ) : '';
		$message        = isset( $_POST['kingring_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['kingring_message'] ) ) : '';
		$privacy_agreed = ! empty( $_POST['kingring_privacy'] );

		if ( ! in_array( $subject, $subjects, true ) ) {
			$subject = '';
		}

		if ( ! $name || ! is_email( $email ) || ! $subject || ! $message || ! $privacy_agreed ) {
			$error = __( 'Please complete all required fields and accept the Privacy Policy.', 'kingring-support-pages' );
		} else {
			$attachments = kingring_support_pages_upload_attachments();

			if ( $attachments['error'] ) {
				$error = $attachments['error'];
			} else {
				$ticket    = kingring_support_pages_generate_ticket();
				$mail_body = sprintf(
					"Ticket: %s\nSubject: %s\nName: %s\nEmail: %s\nOrder Number: %s\n\nMessage:\n%s",
					$ticket,
					$subject,
					$name,
					$email,
					$order_number ? $order_number : 'Not provided',
					$message
				);

				$sent = wp_mail(
					$settings['form_recipient'],
					sprintf( '[%s] %s - %s', $ticket, $subject, $name ),
					$mail_body,
					array( 'Reply-To: ' . $name . ' <' . $email . '>' ),
					$attachments['paths']
				);

				if ( $sent ) {
					$tokens = array(
						'name'    => $name,
						'ticket'  => $ticket,
						'subject' => $subject,
					);

					wp_mail(
						$email,
						kingring_support_pages_replace_tokens( $settings['auto_reply_subject'], $tokens ),
						kingring_support_pages_replace_tokens( $settings['auto_reply_message'], $tokens ),
						array( 'Reply-To: King Ring Support <' . $settings['public_email'] . '>' )
					);

					$status = kingring_support_pages_replace_tokens( $settings['success_message'], $tokens );
					$name = $email = $subject = $order_number = $message = '';
					$privacy_agreed = false;
				} else {
					$error = sprintf(
						__( 'The message could not be sent. Please email %s.', 'kingring-support-pages' ),
						$settings['public_email']
					);
				}

				kingring_support_pages_delete_attachments( $attachments['paths'] );
			}
		}
	}
}
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
<main id="kr-content" class="kr-contact-page">
	<header class="kr-contact-hero">
		<p class="kr-eyebrow"><?php echo esc_html( $settings['contact_eyebrow'] ); ?></p>
		<h1><?php echo esc_html( $settings['contact_title'] ); ?></h1>
		<p><?php echo esc_html( $settings['contact_intro'] ); ?></p>
	</header>

	<div class="kr-contact-layout">
	<section class="kr-contact-form-panel" aria-label="<?php esc_attr_e( 'Contact form', 'kingring-support-pages' ); ?>">
		<?php if ( $status ) : ?><p class="kr-notice kr-success"><?php echo esc_html( $status ); ?></p><?php endif; ?>
		<?php if ( $error ) : ?><p class="kr-notice kr-error"><?php echo esc_html( $error ); ?></p><?php endif; ?>
		<form class="kr-form" method="post" enctype="multipart/form-data">
			<?php wp_nonce_field( 'kingring_contact_form', 'kingring_contact_nonce' ); ?>
			<label class="kr-select-field">
				<span><?php echo esc_html( $settings['subject_label'] ); ?></span>
				<select name="kingring_subject" class="<?php echo $subject ? 'has-value' : ''; ?>" required>
					<option value="" disabled <?php selected( $subject, '' ); ?>><?php echo esc_html( $settings['subject_label'] ); ?></option>
					<?php foreach ( $subjects as $subject_option ) : ?>
						<option value="<?php echo esc_attr( $subject_option ); ?>" <?php selected( $subject, $subject_option ); ?>><?php echo esc_html( $subject_option ); ?></option>
					<?php endforeach; ?>
				</select>
			</label>
			<label>
				<span><?php echo esc_html( $settings['name_label'] ); ?></span>
				<input name="kingring_name" type="text" value="<?php echo esc_attr( $name ); ?>" autocomplete="name" placeholder=" " required>
			</label>
			<label>
				<span><?php echo esc_html( $settings['email_label'] ); ?></span>
				<input name="kingring_email" type="email" value="<?php echo esc_attr( $email ); ?>" autocomplete="email" placeholder=" " required>
			</label>
			<label>
				<span><?php echo esc_html( $settings['order_number_label'] ); ?></span>
				<input name="kingring_order_number" type="text" value="<?php echo esc_attr( $order_number ); ?>" autocomplete="off" placeholder=" ">
			</label>
			<label>
				<span><?php echo esc_html( $settings['message_label'] ); ?></span>
				<textarea name="kingring_message" rows="7" placeholder=" " required><?php echo esc_textarea( $message ); ?></textarea>
			</label>
			<div class="kr-file-field">
				<label for="kingring-attachments"><?php echo esc_html( $settings['attachment_label'] ); ?></label>
				<input id="kingring-attachments" name="kingring_attachments[]" type="file" accept=".jpg,.jpeg,.png,.webp,.pdf" multiple>
				<p><?php echo esc_html( $settings['attachment_help'] ); ?></p>
			</div>
			<label class="kr-consent">
				<input name="kingring_privacy" type="checkbox" value="1" <?php checked( $privacy_agreed ); ?> required>
				<span>
					<a href="<?php echo esc_url( $privacy_url ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $settings['privacy_label'] ); ?></a>
				</span>
			</label>
			<button type="submit"><?php echo esc_html( $settings['submit_label'] ); ?></button>
		</form>
	</section>

	<aside class="kr-contact-details" aria-label="<?php esc_attr_e( 'Contact details', 'kingring-support-pages' ); ?>">
		<div class="kr-contact-detail">
			<span class="kr-contact-detail__icon" aria-hidden="true">
				<svg viewBox="0 0 24 24"><path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
			</span>
			<div>
				<h2><?php echo esc_html( $settings['address_heading'] ); ?></h2>
				<p><?php echo nl2br( esc_html( $settings['address_text'] ) ); ?></p>
			</div>
		</div>

		<div class="kr-contact-detail">
			<span class="kr-contact-detail__icon" aria-hidden="true">
				<svg viewBox="0 0 24 24"><rect width="18" height="14" x="3" y="5" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
			</span>
			<div>
				<h2><?php echo esc_html( $settings['email_heading'] ); ?></h2>
				<p><a href="mailto:<?php echo esc_attr( $settings['public_email'] ); ?>"><?php echo esc_html( $settings['public_email'] ); ?></a></p>
			</div>
		</div>

		<div class="kr-contact-detail">
			<span class="kr-contact-detail__icon" aria-hidden="true">
				<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
			</span>
			<div>
				<h2><?php echo esc_html( $settings['reply_time_heading'] ); ?></h2>
				<p><?php echo esc_html( $settings['reply_time_text'] ); ?></p>
			</div>
		</div>
	</aside>
	</div>

	<section class="kr-contact-map" aria-label="<?php esc_attr_e( 'Map', 'kingring-support-pages' ); ?>">
		<iframe loading="lazy" src="<?php echo esc_url( $settings['map_url'] ); ?>" title="<?php esc_attr_e( 'King Ring location', 'kingring-support-pages' ); ?>"></iframe>
	</section>
</main>
<?php kingring_support_pages_footer(); ?>
<?php wp_footer(); ?>
</body>
</html>
