<div class="sv_setting_subpage">
	<h2><?php _e('General', 'sv_tracking_manager'); ?></h2>
	<h3 class="divider"><?php _e( 'Display styles', 'sv_tracking_manager' ); ?></h3>
	<div class="sv_setting_flex">
	<?php
		echo $module->get_setting('local_cache')->form();
		echo $module->get_setting('bypass_usercentrics')->form();
	?>
	</div>
</div>