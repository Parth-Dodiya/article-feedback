<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.google.com
 * @since      1.0.0
 *
 * @package    Article_Feedback
 * @subpackage Article_Feedback/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Article_Feedback
 * @subpackage Article_Feedback/public
 * @author     Parth Dodiya <parth.dodiya@iflair.com>
 */
class Article_Feedback_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Article_Feedback_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Article_Feedback_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/article-feedback-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Article_Feedback_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Article_Feedback_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		$ajax_url = admin_url( 'admin-ajax.php' );
		wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/article-feedback-public.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'af_ajax_object', array(
			'ajaxurl'	=> admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) )
		));
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/article-feedback-public.js', array( 'jquery' ), $this->version, false );
	}

}

// Adds "was this helpful" after the content
function af_add_after_content($content){

	// WP Globals
    global $table_prefix, $wpdb;

    // header("Content-Type: application/json; charset=UTF-8");
	header('Access-Control-Allow-Origin: *'); 
	$ipaddress = '';
	if (isset($_SERVER['HTTP_CLIENT_IP']))
	$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
	else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
	$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
	else if(isset($_SERVER['HTTP_X_FORWARDED']))
	$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
	else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
	$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
	else if(isset($_SERVER['HTTP_FORWARDED']))
	$ipaddress = $_SERVER['HTTP_FORWARDED'];
	else if(isset($_SERVER['REMOTE_ADDR']))
	$ipaddress = $_SERVER['REMOTE_ADDR'];
	else
	$ipaddress = 'UNKNOWN';

    // Customer Table
    $af_table = $table_prefix . 'feedback_tbl';

	// Read selected post types
	$af_settings = unserialize(get_option("af_options_settings"));	

	$af_displaying_post_types = $af_settings['af_display_at'];
	
	$post_type_c =  get_post_type();	

	if (in_array($post_type_c, $af_displaying_post_types)) {
		// Get post id
		$post_id = get_the_ID();
			
		$network_id = get_current_blog_id();

    	$content .= '<div id="article-feedback-public" data-post-id="'.$post_id.'" data-ip-address="'.$ipaddress.'" data-network-id="'.$network_id.'"><div id="af-title">'.$af_settings["af_display_text"].'</div><div id="article-feedback-yes-no"><span data-value-af="0" id="feedback-button0" value="0" class="article-feedback-button">'.$af_settings["af_display_no_text"].'</span><span data-value-af="1" id="feedback-button1" value="1" class="article-feedback-button">'.$af_settings["af_display_yes_text"].'</span></div>';
    	if ($af_settings['af_display_thank'] == 1) {
    		$content .= '<div id="article-feedback-thank-block" class="display-none"><span>'.$af_settings['af_display_thank_text'].'</span></div>';
    	}
    	if ($af_settings['af_display_count'] == 1) {
    		$content .= '<div id="article-feedback-count-block"><span>'.$af_settings['af_display_count_text'].'<p class="article-feedback-counts"></p></span></div>';
    	}

    	$content .= '</div>';
	}

	return $content;

}
add_action( "the_content", "af_add_after_content", 1);

/**
 * Ajax call for Article-Feedback after submitting response
 */
add_action('wp_ajax_article_feedback_ajax', 'article_feedback_ajax');
add_action('wp_ajax_nopriv_article_feedback_ajax', 'article_feedback_ajax');
function article_feedback_ajax() {
	
	// WP Globals
    global $table_prefix, $wpdb;

    // Customer Table
    $af_table = $table_prefix . 'feedback_tbl';

	$data = array(
	    'post_id' => $_POST['postId'],
	    'ip_address' => $_POST['IP'],
	    'feedback' => $_POST['feedback'],
	    'network_id' => $_POST['network']
	);
	$where = array(
		'network_id' => $_POST['network'],
		'post_id' => $_POST['postId'],
		'ip_address' => $_POST['IP']
	);


	$check_past_entries = $wpdb->get_results("SELECT * FROM ".$af_table." WHERE post_id = '".$_POST['postId']."' AND network_id = '".$_POST['network']."'" );

	echo "<pre>";
	print_r("SELECT * FROM ".$af_table." WHERE post_id = '".$_POST['postId']."' AND network_id = '".$_POST['network']."'");
	echo "</pre>";
	// exit();
	


	if (!empty($check_past_entries)) {
		$wpdb->update( $af_table, $data, $where );
	} else {
		$response['query_response'] = $wpdb->insert( $af_table, $data);
	}

	$feedback_count_query = $wpdb->get_results("SELECT ( SELECT COUNT(*) FROM ".$af_table." WHERE post_id = '".$_POST['postId']."' AND network_id = '".$_POST['network']."' AND feedback = '1' ) AS positive_fb_count, ( SELECT COUNT(*) FROM ".$af_table." WHERE post_id = '".$_POST['postId']."' AND network_id = '".$_POST['network']."' ) AS fb_count" );

	$response['positive_fb_count'] = $feedback_count_query[0]->positive_fb_count;
	$response['fb_count'] = $feedback_count_query[0]->fb_count;

	// echo $response;
	echo json_encode( $response );
	die();
}


/**
 * Ajax call for Article-Feedback on Page load of Single.php
 */
add_action('wp_ajax_article_feedback_check_ajax', 'article_feedback_check_ajax');
add_action('wp_ajax_nopriv_article_feedback_check_ajax', 'article_feedback_check_ajax');
function article_feedback_check_ajax() {	
	
	// WP Globals
    global $table_prefix, $wpdb;

    // Customer Table
    $af_table = $table_prefix . 'feedback_tbl';

	$feedback_check_query = $wpdb->get_results("SELECT * FROM ".$af_table." WHERE ip_address = '".$_POST['IP']."' AND post_id = '".$_POST['postId']."' AND network_id = '".$_POST['network']."'" );
	

	$results['past_feedback'] = $feedback_check_query[0]->feedback;
	$results['post_id'] = $_POST['postId'];

	if (($results['past_feedback'] == '')) {
		$results['past_feedback'] = '0';
	}

	$feedback_count_query = $wpdb->get_results("SELECT ( SELECT COUNT(*) FROM ".$af_table." WHERE post_id = '".$_POST['postId']."' AND network_id = '".$_POST['network']."' AND feedback = '1' ) AS positive_fb_count, ( SELECT COUNT(*) FROM ".$af_table." WHERE post_id = '".$_POST['postId']."' AND network_id = '".$_POST['network']."' ) AS fb_count" );

	$results['positive_fb_count'] = $feedback_count_query[0]->positive_fb_count;
	$results['fb_count'] = $feedback_count_query[0]->fb_count;

	echo json_encode( $results );
	die();
}