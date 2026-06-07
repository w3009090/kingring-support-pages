<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$status = '';
$error  = '';
$name   = '';
$email  = '';
$message = '';

if ( 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $_POST['kingring_contact_nonce'] ) ) {
	$nonce = sanitize_text_field( wp_unslash( $_POST['kingring_contact_nonce'] ) );
	if ( ! wp_verify_nonce( $nonce, 'kingring_contact_form' ) ) {
		$error = __( 'Please refresh the page and try again.', 'kingring-support-pages' );
	} else {
		$name    = isset( $_POST['kingring_name'] ) ? sanitize_text_field( wp_unslash( $_POST['kingring_name'] ) ) : '';
		$email   = isset( $_POST['kingring_email'] ) ? sanitize_email( wp_unslash( $_POST['kingring_email'] ) ) : '';
		$message = isset( $_POST['kingring_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['kingring_message'] ) ) : '';

		if ( ! $name || ! is_email( $email ) || ! $message ) {
			$error = __( 'Please complete all fields correctly.', 'kingring-support-pages' );
		} else {
			$sent = wp_mail(
				'support@kingring.store',
				sprintf( 'King Ring support message from %s', $name ),
				sprintf( "Name: %s\nEmail: %s\n\nMessage:\n%s", $name, $email, $message ),
				array( 'Reply-To: ' . $name . ' <' . $email . '>' )
			);
			if ( $sent ) {
				$status = __( 'Thank you. Your message has been sent.', 'kingring-support-pages' );
				$name = $email = $message = '';
			} else {
				$error = __( 'The message could not be sent. Please email support@kingring.store.', 'kingring-support-pages' );
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
<main id="kr-content" class="kr-contact">
	<section>
		<h1><?php esc_html_e( 'Contact Us', 'kingring-support-pages' ); ?></h1>
		<p class="kr-intro"><?php esc_html_e( 'Please fill out the form, press "Send" and we will answer in few hours.', 'kingring-support-pages' ); ?></p>
		<?php if ( $status ) : ?><p class="kr-notice kr-success"><?php echo esc_html( $status ); ?></p><?php endif; ?>
		<?php if ( $error ) : ?><p class="kr-notice kr-error"><?php echo esc_html( $error ); ?></p><?php endif; ?>
		<form class="kr-form" method="post">
			<?php wp_nonce_field( 'kingring_contact_form', 'kingring_contact_nonce' ); ?>
			<label>Name<input name="kingring_name" type="text" value="<?php echo esc_attr( $name ); ?>" required></label>
			<label>Email<input name="kingring_email" type="email" value="<?php echo esc_attr( $email ); ?>" required></label>
			<label>Message<textarea name="kingring_message" rows="6" required><?php echo esc_textarea( $message ); ?></textarea></label>
			<button type="submit"><?php esc_html_e( 'Send', 'kingring-support-pages' ); ?></button>
		</form>
	</section>
	<aside>
		<p>King Ring TM, 18117 Biscayne Blvd STE 2640 Miami FL 33160 United States</p>
		<iframe loading="lazy" src="https://maps.google.com/maps?q=18117%20Biscayne%20Blvd&amp;t=m&amp;z=10&amp;output=embed&amp;iwloc=near" title="King Ring location"></iframe>
	</aside>
</main>
<?php kingring_support_pages_footer(); ?>
<?php wp_footer(); ?>
</body>
</html>
