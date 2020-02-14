<?php

?>
<div id="add-to-cal-form-wrap">
	<form method="post" id="add-to-cal-form">

		<?php wp_nonce_field(); ?>

		<div class="row title">
			<p class="label"><?php _e( 'Event Title', 'add-to-cal' ); ?></p>
			<input type="text" class="regular-text" name="title" placeholder="<?php esc_attr_e( 'Your event title...', 'add-to-cal' ); ?>" value="" required>
			<p class="description"><?php _e( 'The name of your event.', 'add-to-cal' ); ?></p>
		</div>

		<div class="row times">
			<p class="label"><?php _e( 'Event Duration', 'add-to-cal' ); ?></p>
			<input type="date" name="start_date" value="<?php esc_attr_e( date_i18n( 'Y-m-d' ) ); ?>" required><input type="time" name="start_time" value="09:00:00" required>
			<?php _e( 'To', 'add-to-cal' ); ?>
			<input type="date" name="end_date" value="<?php esc_attr_e( date_i18n( 'Y-m-d' ) ); ?>" required><input type="time" name="end_time" value="10:00:00" required>
			<p class="description"><?php _e( 'The start and end time of your event. Timezone conversion will be calculated automatically.', 'add-to-cal' ); ?></p>
		</div>

		<div class="row description">
			<p class="label"><?php _e( 'Event Description', 'add-to-cal' ); ?></p>
			<textarea name="desc" class="regular-text" placeholder="<?php esc_attr_e( 'Your event description...', 'add-to-cal' ); ?>" rows="4"></textarea>
			<p class="description"><?php _e( 'Provide additional context for your event.', 'add-to-cal' ); ?></p>
		</div>

		<div class="row location">
			<p class="label"><?php _e( 'Event Location', 'add-to-cal' ); ?></p>
			<input type="text" class="regular-text" name="location" placeholder="<?php esc_attr_e( 'Your event location...', 'add-to-cal' ); ?>" value="">
			<p class="description"><?php _e( 'The location of your event, for example a Zoom meeting URL.', 'add-to-cal' ); ?></p>
		</div>

		<div class="row text">
			<p class="label"><?php _e( 'Link Text', 'add-to-cal' ); ?></p>
			<input type="text" class="regular-text" name="text" placeholder="<?php esc_attr_e( 'Add To Google Calendar', 'add-to-cal' ); ?>" value="">
			<p class="description"><?php _e( 'The link text for the shortcode. Ex: Add To Calendar', 'add-to-cal' ); ?></p>
		</div>

		<?php submit_button( 'Generate' ); ?>

		<div id="rendered" class="postbox" style="display: none">
			<div class="row">
				<p class="label"><?php _e( 'Raw Link', 'add-to-cal' ); ?></p>
				<textarea class="code regular-text" id="link" onfocus="this.select()" readonly></textarea>
				<p class="description"><?php _e( 'The raw link to use on other sites or in emails.', 'add-to-cal' ); ?></p>
			</div>
            <div class="row">
                <p class="label"><?php _e( 'HTML Link', 'add-to-cal' ); ?></p>
                <textarea class="code regular-text" id="html" onfocus="this.select()" readonly></textarea>
                <p class="description"><?php _e( 'The html code to use on other sites or in emails.', 'add-to-cal' ); ?></p>
            </div>
			<div class="row">
				<p class="label"><?php _e( 'Shortcode', 'add-to-cal' ); ?></p>
				<textarea class="code  regular-text" id="shortcode" onfocus="this.select()" readonly></textarea>
				<p class="description"><?php _e( 'Use this shortcode anywhere on your site.', 'add-to-cal' ); ?></p>
			</div>
		</div>
	</form>
</div>
