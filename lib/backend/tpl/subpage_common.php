<div class="sv_setting_subpage">
	<h2><?php _e('Common', 'sv_plugin_boilerplate'); ?></h2>
    <h3><?php _e('My Setting', 'sv_plugin_boilerplate'); ?></h3>
    <div class="sv_setting_flex">
		<?php
		echo $module->get_setting( 'my_setting' )->form();
		?>
    </div>
</div>