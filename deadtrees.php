<?php


/**
 * @package Dead Trees: A Wordpress plugin to help avid readers share the books that they're enjoying
 * @author John Beales (http://johnbeales.com)
 * @version 1.0.3
 * 
 */

/**
 * Plugin Name: DeadTrees
 * Plugin URI: http://johnbeales.com
 * Description: A Wordpress plugin to help avid readers share books that they enjoy.
 * Version: 1.0.4
 * Author: John Beales
 * Author URI: http://johnbeales.com
 */



class DeadTrees {

	protected $basedir;

	public function get_basedir() {
		return $this->basedir;
	}

	static public function get_dt() {
		static $dt = false;
		if(false === $dt) {
			$dt = new DeadTrees();
		}
		return $dt;
	}

	protected $allowable_display_locations;

	protected $default_affiliate_ids = array();

	const debug = false;


	protected function _maybe_log_item($item) {
		if(self::debug) {

			$debugfile = $this->basedir . '/debug.txt';

			ob_start();
			var_dump($item);
			echo "\n\r\n\r";
			debug_print_backtrace();
			echo "\n\r===================\n\r";

			$debugstr = ob_get_clean();
			file_put_contents($debugfile, $debugstr, FILE_APPEND);
		}
	}


	function __construct() {

		$this->basedir = dirname(__FILE__);

		$this->allowable_display_locations  = array( 
			'none' => __('Book section only', 'deadtree'),
			'tag' => __('Book section and tag archives', 'deadtree'),
			'tag|home' => __('Book section, tag and date archive pages', 'deadtree')
		);

		if(get_option('dt_default_to_dev_affiliate', true)) {
			$this->default_affiliate_ids = array(
				'amazon.com' => 'deadtrees-20',
				'amazon.ca' => 'deadtrees-ca-20',
				'amazon.co.uk' => 'deadtrees-21'
			);
		} 


		$this->setup_hooks();
	}



	protected function _get_amazon_creds() {
		static $creds = false;
		if(!$creds) {
			$creds = get_option('dt_amazon_api_creds', array());
		}

		return $creds;
		
	}

	protected function _get_aws_key_id() {
		$creds = $this->_get_amazon_creds();
		if(is_array($creds) && isset($creds['key_id'])) {
			return $creds['key_id'];
		}
		return false;
	}

	protected function _get_aws_secret_key() {
		$creds = $this->_get_amazon_creds();
		if(is_array($creds) && isset($creds['secret_key'])) {
			return $creds['secret_key'];
		}
		return false;
	}


	protected function _get_image_url($asin) {

		//  gmdate('Y-m-d\TH:i:s\Z', time());


		$request_params = array(
			'Service' => 'AWSECommerceService',
			'AWSAccessKeyId' =>  $this->_get_aws_key_id(),
			'Operation' => 'ItemLookup',
			'ItemId' => $asin,
			'ResponseGroup' => 'Images',
			'Version' => '2011-08-01',
			'Timestamp' => gmdate('Y-m-d\TH:i:s\Z', time()),
			'AssociateTag' => 'johnbeales-20'
		);

		uksort( $request_params, 'strnatcmp');

		if(defined('PHP_QUERY_RFC3986')) {
			$stringtosign = http_build_query($request_params, '', '&', PHP_QUERY_RFC3986);
		} else {
			$stringtosign = http_build_query($request_params, '', '&');
		}

		

		$url = 'https://webservices.amazon.com/onca/xml?' . $stringtosign;

		$prefix = "GET\n";
		$prefix .= "webservices.amazon.com\n";
		$prefix .= "/onca/xml\n";

		$stringtosign = $prefix . $stringtosign;

		$sig = hash_hmac('sha256', $stringtosign, $this->_get_aws_secret_key(), true);
		$sig = base64_encode($sig);

		$url .= '&Signature='.urlencode($sig);

		$result = wp_remote_request($url);


		if(200 == $result['response']['code']) {

			$result_obj = new SimpleXMLElement($result['body']);

			if($result_obj->Items && $result_obj->Items->Item->LargeImage) {
				$image = $result_obj->Items->Item->LargeImage;
				return $image->URL;
			} else {
				$this->_maybe_log_item($result_obj);
			}

		} else {
			$this->_maybe_log_item($result);
		}
		return false;
		
	}


/**
 * Settings!!! ===============================================
 */	

	public function add_settings_page() {
		add_submenu_page('options-general.php', __('Dead Trees', 'deadtree'), __('Dead Trees', 'deadtree'), 'manage_options', 'dt-settings', array(&$this, 'show_settings_page'));
	}

	public function show_settings_page() {
		require_once($this->basedir .'/admin/options.php');
	}


	public function populate_amazon_affiliate_id_field($args) {
		$ids = get_option('dt_amazon_affiliate_ids');
		$site = $args['site'];
		$val = '';
		if(!empty($ids[$site])) {
			$val = $ids[$site];
		}

		echo '<input type="text" name="dt_amazon_affiliate_ids[' . esc_attr($site) .']" value="' . esc_attr($val) .'" />';
	}

	public function validate_amazon_affiliate_ids($input) {
		$ids = array();

		if(!empty($input['amazon.com'])) {
			$ids['amazon.com'] = trim($input['amazon.com']);
		}

		if(!empty($input['amazon.ca'])) {
			$ids['amazon.ca'] = trim($input['amazon.ca']);
		}

		if(!empty($input['amazon.co.uk'])) {
			$ids['amazon.co.uk'] = trim($input['amazon.co.uk']);
		}

		return $ids;

	}

	public function amazon_affiliate_ids_section_text() {
		echo '<p>' . __('These are the affiliate IDs that will be used to generate affiliate links to Amazon.', 'deadtree') . '</p>';
	}


	public function cover_size_settings_section_text() {
		echo '<p>' . __('Set the default size to display book covers at, (covers will not be cropped in this size).', 'deadtree') . '</p>'; 
	}


	public function populate_cover_size_settings_field($dimension) {
		
		$dimensions = get_option('dt_default_cover_size');

		$size = 75;
		if(is_array($dimensions) && !empty($dimensions[$dimension])) {
			$size = $dimensions[$dimension];
		}
		echo '<input type="text" name="dt_default_cover_size[' . esc_attr($dimension) .']" value="' . esc_attr($size) .'" />';

	}

	public function validate_default_cover_size($input) {
		
		$dimensions = array();

		$input['width'] = trim($input['width']);
		if(is_numeric($input['width'])) {
			$dimensions['width'] = $input['width'];
		}

		$input['height'] = trim($input['height']);
		if(is_numeric($input['height'])) {
			$dimensions['height'] = $input['height'];
		}

		return $dimensions;
	}

	public function validate_amazon_api_creds($input) {
		$creds = array();

		$input['key_id'] = trim($input['key_id']);
		if(!empty($input['key_id'])) {
			$creds['key_id'] = $input['key_id'];
		}

		$input['secret_key'] = trim($input['secret_key']);
		if(!empty($input['secret_key'])) {
			$creds['secret_key'] = $input['secret_key'];
		}

		return $creds;

	}

	public function amazon_api_creds_section_text() {
		echo '<p>' . __('Enter your credentials for the Amazon.com Product Advertising API here.  Note that these credentials should be for Amazon.com, even if you are planning on sending your affiliate traffic to another Amazon site.', 'deadtree') . '</p>';
	}

	public function populate_amazon_api_creds_field($field) {
		$creds = get_option('dt_amazon_api_creds', array());

		$credential = '';
		if(isset($creds[$field])) {
			$credential = $creds[$field];
		}

		echo '<input type="text" name="dt_amazon_api_creds[' . esc_attr($field) .']" value="' . esc_attr($credential) .'" />';

	}

	public function validate_default_to_dev_affiliate($input) {
		if('yes' == $input) {
			return true;
		}
		return false;
	}

	public function default_to_dev_affiliate_setting_explanation() {

	}

	public function populate_default_to_dev_affiliate() {
		$opt = get_option('dt_default_to_dev_affiliate', true);

		$checker = '';
		if($opt) {
			$checker = ' checked="checked"';
		}

		echo '<input type="checkbox" name="dt_default_to_dev_affiliate" value="yes"' . $checker .' />';
	}

	public function validate_setting_include_books($input) {
		
		if(in_array($input, array_keys($this->allowable_display_locations))) {
			return $input;
		}

	}

	public function setting_include_books_section_text() {
		echo '<p>';
		_e('You can choose to display books only at their main URL, (/books/), or to have them added to tag archive pages and your homepage & archives alongside normal posts.', 'deadtree');
		echo '</p>';
	}

	public function populate_include_books_field() {

		
		$opt = get_option('dt_include_books');

		echo '<select name="dt_include_books" id="dt_include_books">';
		foreach($this->allowable_display_locations as $key => $description) {
			if($opt == $key) {
				$selector = ' selected="selected"';
			} else {
				$selector = '';
			}

			echo '<option value="' . esc_attr($key) . '"' . $selector . '>' . esc_html($description) . '</option>';
		}

		echo '</select>';
	}

	public function validate_setting_checkbox($input) {
		if('yes' == $input) {
			return true;
		}

		return false;
	}


	public function bookbox_settings_summary() {
		echo '<p>';
		_e('We can automatically include Amazon links to the books you read without you having to change your templates. To do this, and have them look best, check both boxes.');
		echo '</p><p>';
		_e('If you want to include only the actual links, or only the styles for the links, select only the first box, or the second box, respectively.', 'deadtree');
		echo '</p>';
	}

	public function populate_send_bookbox_css() {
		$opt = get_option('dt_send_bookbox_css');
		$checker = '';
		if($opt) {
			$checker = ' checked="checked"';
		}

		echo '<input type="checkbox" name="dt_send_bookbox_css" value="yes"' . $checker .' />';

	}

	public function populate_setting_auto_add_bookbox() {
		$opt = get_option('dt_auto_add_bookbox', true);
		$checker = '';
		if($opt) {
			$checker = ' checked="checked"';
		}
		echo '<input type="checkbox" name="dt_auto_add_bookbox" value="yes"' . $checker . ' />';
	}


	public function setup_settings() {

		register_setting('deadtree_options', 'dt_amazon_affiliate_ids', array(&$this, 'validate_amazon_affiliate_ids'));

		add_settings_section('dt_affiliate_ids', __('Amazon Affiliate IDs', 'deadtree'), array(&$this, 'amazon_affiliate_ids_section_text'), 'deadtree');
		add_settings_field('dt_affiliate_id_amazon.com', __('Amazon.com Affiliate ID:', 'deadtree'), array(&$this, 'populate_amazon_affiliate_id_field'), 'deadtree', 'dt_affiliate_ids', array('site' => 'amazon.com'));
		add_settings_field('dt_affiliate_id_amazon.ca', __('Amazon.ca Affiliate ID:', 'deadtree'), array(&$this, 'populate_amazon_affiliate_id_field'), 'deadtree', 'dt_affiliate_ids', array('site' => 'amazon.ca'));
		add_settings_field('dt_affiliate_id_amazon.co.uk', __('Amazon.co.uk Affiliate ID:', 'deadtree'), array(&$this, 'populate_amazon_affiliate_id_field'), 'deadtree', 'dt_affiliate_ids', array('site' => 'amazon.co.uk'));
	

		register_setting('deadtree_options', 'dt_default_cover_size', array(&$this, 'validate_default_cover_size'));

		add_settings_section('dt_default_cover_sizes', __('Default Book Cover Image Size', 'deadtree'), array(&$this, 'cover_size_settings_section_text'), 'deadtree');
		add_settings_field('dt_cover_width', __('Width:', 'deadtree'), array(&$this, 'populate_cover_size_settings_field'), 'deadtree', 'dt_default_cover_sizes', 'width');
		add_settings_field('dt_cover_height', __('Height:', 'deadtree'), array(&$this, 'populate_cover_size_settings_field'), 'deadtree', 'dt_default_cover_sizes', 'height');

		register_setting('deadtree_options', 'dt_amazon_api_creds', array(&$this, 'validate_amazon_api_creds'));
		add_settings_section('dt_amazon_api_creds', __('Amazon API Credentials', 'deadtree'), array(&$this, 'amazon_api_creds_section_text'), 'deadtree');
		add_settings_field('dt_aws_key_id', __('Amazon API Key ID', 'deadtree'), array(&$this, 'populate_amazon_api_creds_field'), 'deadtree', 'dt_amazon_api_creds', 'key_id');
		add_settings_field('dt_aws_secret_key', __('Amazon Secret Key', 'deadtree'), array(&$this, 'populate_amazon_api_creds_field'), 'deadtree', 'dt_amazon_api_creds', 'secret_key');

		register_setting('deadtree_options', 'dt_include_books', array(&$this, 'validate_setting_include_books'));
		add_settings_section('dt_include_books', __('Where should we show books on your site?', 'deadtree'), array(&$this, 'setting_include_books_section_text'), 'deadtree');
		add_settings_field('dt_include_books_location', __('Display books on:', 'deadtree'), array(&$this, 'populate_include_books_field'), 'deadtree', 'dt_include_books');

		register_setting('deadtree_options', 'dt_default_to_dev_affiliate', array(&$this, 'validate_default_to_dev_affiliate'));
		add_settings_section('dt_default_to_dev_affiliate', __('Default to the Developer\'s Amazon Affiliate ID', 'deadtree'), array(&$this, 'default_to_dev_affiliate_setting_explanation'), 'deadtree');
		add_settings_field('dt_default_to_dev_affiliate', __('Check to use the developer\'s Amazon Affiliate ID if no other ID is available', 'deadtree'), array(&$this, 'populate_default_to_dev_affiliate'), 'deadtree', 'dt_default_to_dev_affiliate');

		
		add_settings_section('dt_bookbox_settings', __('Bookbox Settings', 'deadtree'), array(&$this, 'bookbox_settings_summary'), 'deadtree');
		register_setting('deadtree_options', 'dt_auto_add_bookbox', array(&$this, 'validate_setting_checkbox'));
		add_settings_field('dt_auto_add_bookbox', __('Automatically show affiliate links for books', 'deadtree'), array(&$this, 'populate_setting_auto_add_bookbox'), 'deadtree', 'dt_bookbox_settings');
		register_setting('deadtree_options', 'dt_send_bookbox_css', array(&$this, 'validate_setting_checkbox'));
		add_settings_field('dt_send_bookbox_css', __('Check to include default Bookbox CSS', 'deadtree'), array(&$this, 'populate_send_bookbox_css'), 'deadtree', 'dt_bookbox_settings');

		

	}


/**
 *  End Settings  ===================================================
 */


	protected function setup_taxonomies() {

		// Add new taxonomy, NOT hierarchical (like tags)
		$labels = array(
			'name' => _x( 'Book Authors', 'taxonomy general name', 'deadtree' ),
			'singular_name' => _x( 'Writer', 'taxonomy singular name', 'deadtree' ),
			'search_items' =>  __( 'Search Writers', 'deadtree' ),
			'popular_items' => __( 'Frequently Read Writers', 'deadtree' ),
			'all_items' => __( 'All Writers', 'deadtree' ),
			'parent_item' => null,
			'parent_item_colon' => null,
			'edit_item' => __( 'Edit Writer', 'deadtree' ), 
			'update_item' => __( 'Update Writer', 'deadtree' ),
			'add_new_item' => __( 'Add New Writer', 'deadtree' ),
			'new_item_name' => __( 'New Writer Name', 'deadtree' ),
			'separate_items_with_commas' => __( 'Separate writers with commas', 'deadtree' ),
			'add_or_remove_items' => __( 'Add or remove writers', 'deadtree' ),
			'choose_from_most_used' => __( 'Choose from the most used writers', 'deadtree' ),
			'menu_name' => __( 'Writers', 'deadtree' ),
		); 

		register_taxonomy('dt_writer','dt_book',array(
			'hierarchical' => false,
			'labels' => $labels,
			'show_ui' => true,
			'update_count_callback' => '_update_post_term_count',
			'query_var' => 'writer',
			'rewrite' => array( 'slug' => 'writer' ),
		));

	}


	public function setup_cpt() {


		$this->setup_taxonomies();

		$labels = array(
			'name' => __('Books', 'deadtree'),
			'singular_name' => __('Book', 'deadtree'),
			'add_new' => __('Add New', 'deadtree'),
			'all_items' => __('All Books', 'deadtree'),
			'add_new_item' => __('Add New Book', 'deadtree'),
			'edit_item' => __('Edit Book', 'deadtree'),
			'new_item' => __('New Book', 'deadtree'),
			'view_item' => __('View Book', 'deadtree'),
			'search_items' => __('Search Books', 'deadtree'),
			'not_found' => __('No Books Found', 'deadtree'),
			'not_found_in_trash' => __('No Books Found in the Trash', 'deadtree')
		);

		$args = array(
			'labels' => $labels,
			'description' => __('Add books that you have read to your blog, and only write about them if you want to.', 'deadtree'),
			'public' => true,
			'exclude_from_search' => false,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_in_nav_menus' => true,
			'menu_position' => 5,
			'heirarchical' => false,
			'supports' => array(
				'title',
				'author',
				'editor',
				'thumbnail',
				'excerpt',
				'trackbacks',
				'comments',
				'revisions'
			),
			'register_meta_box_cb' => false,
			'taxonomies' => array(
				'post_tag',
				'dt_writer'
			),
			'has_archive' => true,
			'rewrite' => array(
				'slug' => _x('books', 'URL slug for the book post type', 'deadtree') 
			),
			'query_var' => _x('books', 'Query Var, (like URL slug), for the book post type', 'deadtree'),
			'can_export' => true,
		);	

		register_post_type('dt_book', $args);

	}

	public function add_cover_image_size() {
		$thumb_size = get_option('dt_default_cover_size');
		add_image_size('dt_book_cover_thumb', $thumb_size['width'], $thumb_size['height'], false);
	}


	public function add_book_metaboxes() {
		add_meta_box('dt_bookbox_info', __('Bookbox Info', 'deadtree'), array(&$this, 'bookbox_metabox_contents'), 'dt_book', 'side');
	}

	public function enqueue_metabox_styles() {
		$current_screen = get_current_screen();
		if('dt_book' == $current_screen->post_type) {
			wp_enqueue_style('dt_bookbox_metabox', plugins_url('/dead-trees/admin/metaboxes.css'));
		}
	}

	public function bookbox_metabox_contents($post) {
		$post_id = $post->ID;
		require_once($this->basedir . '/admin/bookbox_metabox_contents.php');
	}


	protected function _get_cover_attachment_id($post_id = 0) {
		if(!$post_id) {
			$post_id = get_the_ID();
		}

		$attachment_id = 0;

		if($post_id) {
			$maybe_attachment_id = get_post_meta($post_id, '_dt_cover_post_id', true);
			if(!empty($maybe_attachment_id)) {
				$attachment_id = $maybe_attachment_id;
			}
		}

		return $attachment_id;
	}

	protected function _set_cover_attachment_id($attachment_id, $post_id = 0) {
		if(!$post_id) {
			$post_id = get_the_ID();
		}


		if($post_id) {
			update_post_meta($post_id, '_dt_cover_post_id', $attachment_id);
		}
	}

	protected function _delete_current_cover($post_id = 0) {
		if(!$post_id) {
			$post_id = get_the_ID();
		}


		$attachment_id = $this->_get_cover_attachment_id($post_id);
		if($attachment_id) {
			wp_delete_attachment($attachment_id, true);
		}

		delete_post_meta($post_id, '_dt_cover_post_id');
	}

	public function save_bookbox_metabox($post_id) {
		


		// check for autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
    		return;
      	}

      	if(wp_is_post_revision($post_id)) {
      		return;
      	}

      	// see if we're on the right post type
      	$current_screen = get_current_screen();
		if('dt_book' != $current_screen->post_type) {
			return;
		}


      	// check nonce
      	if ( !wp_verify_nonce( $_POST['dt_bookbox_nonce'], 'deadtree' ) ) {
    		return;
  		}

  		// make sure the current user can edit this post
  		if(current_user_can( 'edit_post', $post_id) ) {
  			$asin_amazon_com = trim($_POST['dt_bookbox_asin_amazon_com']);
  			$asin_amazon_ca = trim($_POST['dt_bookbox_asin_amazon_ca']);
  			$asin_amazon_co_uk = trim($_POST['dt_bookbox_asin_amazon_co_uk']);

  			$comment = trim($_POST['dt_bookbox_comment']);


  			$asin_changed = false;
  			$old_asin_amazon_com = dt_get_asin_com();
  			if($old_asin_amazon_com != $asin_amazon_com) {
  				$asin_changed = true;
  			}


  			update_post_meta($post_id, '_dt_asin_amazon.com', $asin_amazon_com);
  			update_post_meta($post_id, '_dt_asin_amazon.ca', $asin_amazon_ca);
  			update_post_meta($post_id, '_dt_asin_amazon.co.uk', $asin_amazon_co_uk);
  			update_post_meta($post_id, '_dt_bookbox_comment', $comment);


  			$cover_id = $this->_get_cover_attachment_id($post_id);


  			// delete the old cover;
  			if($asin_changed) {
  				$this->_delete_current_cover($post_id);
  			}

  			if(($asin_changed || empty($cover_id) || false === wp_get_attachment_url($cover_id))  && !empty($asin_amazon_com)) {
  				$coverurl = $this->_get_image_url($asin_amazon_com);


  				if($coverurl) {



  					$cover = wp_remote_get($coverurl);
  					if(!is_wp_error( $cover )) {
	  					$filename = wp_upload_dir();

	  					// we're assuming that this is a jpeg for now. Hopefully it is.
	  					$filename = $filename['path'] . '/' . sprintf(__('book-%d-cover.jpg', 'deadtree'), $post_id);

	  					$file_result = file_put_contents($filename, $cover['body']);

	  					$wp_filetype = wp_check_filetype($filename, null );
	  					$attachment = array(
							'post_mime_type' => $wp_filetype['type'],
							'post_title' => sprintf(__('%s Cover', 'deadtree'), get_the_title($post_id)),
							'post_content' => '',
							'post_status' => 'inherit'
						);

						$cover_post_id = wp_insert_attachment($attachment, $filename, $post_id);
						$attach_data = wp_generate_attachment_metadata($cover_post_id, $filename);
  						wp_update_attachment_metadata($cover_post_id,  $attach_data);

						if($cover_post_id) {
							$this->_set_cover_attachment_id($cover_post_id, $post_id);
						}

					}

  				}


  			}

  		}


	}


	public function get_raw_bookbox_info($post_id = NULL) {

		// set up a cache to avoid unneeded DB lookups.
		static $datacache = array();


		if(is_null($post_id)) {
			$post_id = get_the_ID();
		}

		$cachekey = 'P' . $post_id;

		// check for data in the cache, and return it if it's there.
		if(isset($datacache[$cachekey])) {
			return $datacache[$cachekey];
		} else {

			$asin = get_post_meta($post_id, '_dt_asin_amazon.com', true);
			$asin_ca = get_post_meta($post_id, '_dt_asin_amazon.ca', true);
			$asin_uk = get_post_meta($post_id, '_dt_asin_amazon.co.uk', true);

			$comment = get_post_meta($post_id, '_dt_bookbox_comment', true);

			$retdata = array(
				'asin_amazon.com' => $asin,
				'asin_amazon.ca' => $asin_ca,
				'asin_amazon.co.uk' => $asin_uk,
				'comment' => $comment
			);

			$cover_id = $this->_get_cover_attachment_id($post_id);
			if(!empty($cover_id)) {
				$retdata['cover_image_attachment_id'] = $cover_id;
			}

			// save the data to the cache
			$datacache[$cachekey] = $retdata;

			return $retdata;
		}

	}


	public function get_amazon_affiliate_id($domain) {
		$id = '';

		$ids = get_option('dt_amazon_affiliate_ids');

		if(isset($ids[$domain])) {
			$id = $ids[$domain];
		}
		
		if(empty($id) && get_option('dt_default_to_dev_affiliate', true)) {
			if(isset($this->default_affiliate_ids[$domain])) {
				$id = $this->default_affiliate_ids[$domain];
			}
		}

		return $id;

	}




	public function get_bookbox_info($post_id = NULL ) {

		// set up a cache to avoid unneeded DB lookups.
		static $datacache = array();


		if(empty($post_id)) {
			$post_id = get_the_ID();
		}

		$cachekey = 'P' . $post_id;

		
		// check for data in the cache, and return it if it's there.
		if(isset($datacache[$cachekey])) {
			return $datacache[$cachekey];
		} else {

			$rawdata = $this->get_raw_bookbox_info($post_id);

			$asin_amazon_com = $rawdata['asin_amazon.com'];

			$asin_amazon_ca = $rawdata['asin_amazon.ca'];
			if(empty($asin_amazon_ca)) {
				$asin_amazon_ca = $asin_amazon_com;
			}

			$asin_amazon_co_uk = $rawdata['asin_amazon.co.uk'];
			if(empty($asin_amazon_co_uk)) {
				$asin_amazon_co_uk = $asin_amazon_com;
			}

			$comment = $rawdata['comment'];

			$retdata = array(
				'asin_amazon.com' => $asin_amazon_com,
				'asin_amazon.ca' => $asin_amazon_ca,
				'asin_amazon.co.uk' => $asin_amazon_co_uk,
				'comment' => $comment
			);

			if(isset($rawdata['cover_image_attachment_id'])) {
				$retdata['cover_image_attachment_id'] = $rawdata['cover_image_attachment_id'];
			}

			// save the data to the cache
			$datacache[$cachekey] = $retdata;

			return $retdata;
		}
	}


	/**
	 * Template Functions ======================================================
	 */
	




	/**
	 * End Template Funcitons =================================================
	 */


	// called in the_content filter
	public function auto_add_bookbox($content) {
		if('dt_book' == get_post_type(get_the_ID())) {
			$bookbox = dt_get_bookbox();
			$content .= $bookbox;
		}

		return $content;
	}


	public function show_books_in_normal_pages( $query ) {

/*
		$this->allowable_display_locations  = array( 
			'none' => __('Book section only', 'deadtree'),
			'tag' => __('Book section and tag archives', 'deadtree'),
			'tag|home' => __('Book section, tag and date archive pages', 'deadtree')
		);

 */			

		$include_locations = get_option('dt_include_books');

		if('none' !== $include_locations) {

			$include = false;

			if('tag' == $include_locations) {
				$include = is_tag();
			} elseif('tag|home' == $include_locations) {
				$include = (is_tag() || is_home());
			}


			if ( ( $include || is_feed()) && empty( $query->query_vars['suppress_filters'] ) ) {
				
				$query->set( 'post_type', array('post', 'dt_book') );
			}


		}

		return $query;


	}

	public function enqueue_bookbox_styles() {

		wp_enqueue_style('dt_bookbox_style', plugins_url('/dead-trees/style/deadtree-bookbox.css'));
		
	}


	public function setup_hooks() {
		//add_action('init', array(&$this, 'setup_cpt'));
		$this->setup_cpt();

		//add_action('init', array(&$this, 'add_cover_image_size'));
		$this->add_cover_image_size();

		add_filter( 'pre_get_posts', array(&$this, 'show_books_in_normal_pages') );

		if(get_option('dt_send_bookbox_css', true)) {
			add_action('wp_print_styles', array(&$this, 'enqueue_bookbox_styles'));
		}

		if(get_option('dt_auto_add_bookbox', true)) {
			add_filter('the_content', array(&$this, 'auto_add_bookbox'));
		}

		if(is_admin()) {
			add_action('admin_menu', array(&$this, 'add_settings_page'));
			add_action('admin_init', array(&$this, 'setup_settings'));
			add_action('add_meta_boxes', array(&$this, 'add_book_metaboxes'));	// requires WP3.0
			add_action('admin_print_styles-post.php', array(&$this, 'enqueue_metabox_styles'));
			add_action( 'save_post', array(&$this, 'save_bookbox_metabox') );
		}
	}


	public static function activate_action() {
		
		// set the default cover size.
		add_option('dt_default_cover_size', array('width' => 75, 'height' => 75));

		// default to using the developer's affiliate ID if nothing else is available.
		add_option('dt_default_to_dev_affiliate', true);

		// set the default CSS setting
		add_option('dt_send_bookbox_css', true);

		// set the default setting for adding the bookbox.
		add_option('dt_auto_add_bookbox', true);

		flush_rewrite_rules();

	}

	public static function deactivate_action() {
		flush_rewrite_rules();
	}
}




//add_action('plugins_loaded', array('DeadTrees', 'get_dt'));
add_action('init', array('DeadTrees', 'get_dt'));

register_activation_hook(__FILE__, array('DeadTrees', 'activate_action'));
register_deactivation_hook(__FILE__, array('DeadTrees', 'deactivate_action'));


require_once( dirname(__FILE__) . '/template_tags.php');


?>