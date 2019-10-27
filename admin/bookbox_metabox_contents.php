<?php
	$asin_html = '<abbr title="' . esc_attr(__('Amazon Standard Identification Number', 'deadtree')) . '">' . __('ASIN', 'deadtree') . '</abbr>';

	wp_nonce_field('deadtree', 'dt_bookbox_nonce');

	$deadtree = DeadTrees::get_dt();
	$rawdata = $deadtree->get_raw_bookbox_info();
?>
<p><?php _e('Info about this book used to display links to Amazon where your readers can buy the book.', 'deadtree'); ?></p>

<p>
	<label for="dt_bookbox_isbn"><?php _e('ISBN:', 'deadtree'); ?></label>
	<input type="text" name="dt_bookbox_isbn" id="dt_bookbox_isbn" value="<?php echo esc_attr($rawdata['isbn']); ?>" />
	<span class="howto"><abbr title="<?php _e('International Standard Book Number', 'deadtree'); ?>"><?php _e('ISBN', 'deadtree'); ?></abbr> <?php _e('(ISBN-10 or ISBN-13)', 'deadtree'); ?></span>
</p>


<p>
	<label for="dt_bookbox_asin"><?php _e('Amazon US ASIN:', 'deadtree'); ?></label>
	<input type="text" name="dt_bookbox_asin_amazon.com" id="dt_bookbox_asin_amazon.com" value="<?php echo esc_attr($rawdata['asin_amazon.com']); ?>" />
	<span class="howto"><?php 
	echo sprintf( __('The main %s for the book. This is used as a default for all Amazon sites.', 'deadtree'), $asin_html ); ?></span>
</p>

<p>
	<label for="dt_bookbox_asin_ca"><?php _e('Amazon Canada ASIN:', 'deadtree'); ?></label>
	<input type="text" name="dt_bookbox_asin_amazon.ca" id="dt_bookbox_asin_amazon.ca" value="<?php echo esc_attr($rawdata['asin_amazon.ca']); ?>" />
	<span class="howto"><?php
	echo sprintf( __('The %s to use when sending readers Amazon.ca, if you want it to be different from Amazon.com', 'deadtree' ), $asin_html);
	?></span>
</p>

<p>
	<label for="dt_bookbox_asin_uk"><?php _e('Amazon UK ASIN:', 'deadtree'); ?></label>
	<input type="text" name="dt_bookbox_asin_amazon.co.uk" id="dt_bookbox_asin_amazon.co.uk" value="<?php echo esc_attr($rawdata['asin_amazon.co.uk']); ?>" />
	<span class="howto"><?php
	echo sprintf( __('The %s to use when sending readers Amazon.co, if you want it to be different from Amazon.com', 'deadtree'), $asin_html);
	?></span>
</p>


<p>
	<label for="dt_bookbox_comment"><?php _ex('Comment', 'Bookbox Metabox comment field label', 'deadtree'); ?></label>
	<textarea rows="2" cols="33" type="text" name="dt_bookbox_comment" id="dt_bookbox_comment"><?php echo esc_textarea($rawdata['comment']); ?></textarea>
	<span class="howto"><?php _e('Add a small message to the Bookbox if you wish.', 'deadtree'); ?></span>
</p>

<?php if(isset($rawdata['cover_image_attachment_id'])): ?>
<p><?php _e( 'Cover: ', 'deadtree' ); ?><br><?php echo wp_get_attachment_image($rawdata['cover_image_attachment_id'], 'dt_book_cover_thumb'); ?></p>

<?php endif; ?>