<div class="wrap">
	<h1>Datawiza Proxy Auth Plugin</h1>

	<form method="post" action="options.php">
		<?php settings_fields( 'datawiza-sign-in-widget' ); ?>
		<?php do_settings_sections( 'datawiza-sign-in-widget' ); ?>
		<?php submit_button(); ?>
	</form>
</div>
