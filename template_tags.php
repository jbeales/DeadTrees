<?php


function dt_get_bookbox_item($item, $post_id = 0) {
	$deadtree = DeadTrees::get_dt();

	$postmeta = $deadtree->get_bookbox_info($post_id);

	if(isset($postmeta[$item])) {
		return $postmeta[$item];
	} else {
		return;
	}

}


function dt_get_isbn($post_id = 0) {
	return dt_get_bookbox_item('isbn', $post_id);
}

function dt_get_asin_com($post_id = 0) {
	return dt_get_bookbox_item('asin_amazon.com', $post_id);
}

function dt_get_asin_ca($post_id = 0) {
	return dt_get_bookbox_item('asin_amazon.ca', $post_id);
}

function dt_get_asin_uk($post_id = 0) {
	return dt_get_bookbox_item('asin_amazon.co.uk', $post_id);
}

function dt_get_bookbox_comment($post_id = 0) {
	return dt_get_bookbox_item('comment', $post_id);
}

function dt_get_bookbox_image($post_id = 0) {
	$attachment_id = dt_get_bookbox_item('cover_image_attachment_id', $post_id);

	if(!empty($attachment_id)) {
		return wp_get_attachment_image($attachment_id, 'dt_book_cover_thumb');
	}
}


// requires the loop
function dt_bookbox() {
	if('dt_book' == get_post_type(get_the_ID())) {
		$file = locate_template(array('deadtree-bookbox.php'), true, false);
		if(empty($file)) {
			$deadtree = DeadTrees::get_dt();
			require($deadtree->get_basedir() . '/template/deadtree-bookbox.php');
		}
	}
}

// requires loop, will be used in filters
// dt_bookbox() is more efficient, since it doesn't use output buffering.
function dt_get_bookbox() {
	ob_start();
	dt_bookbox();
	$bookbox = ob_get_clean();
	return $bookbox;
}

function dt_get_amazon_url($domain = 'amazon.com', $post_id = 0) {

	$url = '';

	if(empty($post_id)) {
		$post_id = get_the_ID();
	}

	if(!empty($post_id)) {
		
		$domain = strtolower($domain);

		$asin = '';

		switch($domain) {
			case 'amazon.ca':
			$asin = dt_get_asin_ca($post_id);
			break;

			case 'amazon.co.uk':
			$asin = dt_get_asin_uk($post_id);
			break;
		}

		// amazon.com is handled here
		if(empty($asin)) {
			$asin = dt_get_asin_com($post_id);
		}


		if(!empty($asin)) {

			$deadtree = DeadTrees::get_dt();

			$affiliate_id = $deadtree->get_amazon_affiliate_id($domain);
			if(!empty($affiliate_id)) {
				$affiliate_url = '/?tag=' . $affiliate_id;
			} else {
				$affiliate_url = '';
			}

			$url .= 'http://www.' . $domain . '/dp/' . $asin . $affiliate_url;
		}
	}

	return $url;

}


?>