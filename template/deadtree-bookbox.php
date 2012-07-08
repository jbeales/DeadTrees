<div class="dt_bookbox">
<div class="dt_bookbox-image"><?php echo dt_get_bookbox_image(); ?></div>
<h6><?php _e('Want to read it yourself?', 'deadtree'); ?></h6>
<?php
$comment = dt_get_bookbox_comment();
if(!empty($comment)) {
	echo '<p class="dt_bookbox_extra_comment">' . esc_html($comment) . '</p>';
}
?>


<p class="dt_booklinks"><a href="<?php echo dt_get_amazon_url('amazon.com'); ?>" rel="nofollow">Amazon.com (USA)</a> <a href="<?php echo dt_get_amazon_url('amazon.ca'); ?>" rel="nofollow">Amazon.ca (Canada)</a> <a href="<?php echo dt_get_amazon_url('amazon.co.uk'); ?>" rel="nofollow">Amazon.co.uk (UK)</a></p>
</div>