<div class="wrap">
<h2><?php _e('Dead Trees Settings', 'deadtree'); ?></h2>


<form method="post" action="options.php">
<?php settings_fields('deadtree_options'); ?>

<?php do_settings_sections('deadtree'); ?>

<?php submit_button(); ?>

</form>


</div>