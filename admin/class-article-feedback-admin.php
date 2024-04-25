<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.google.com
 * @since      1.0.0
 *
 * @package    Article_Feedback
 * @subpackage Article_Feedback/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Article_Feedback
 * @subpackage Article_Feedback/admin
 * @author     Parth Dodiya <parth.dodiya@iflair.com>
 */
class Article_Feedback_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/article-feedback-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/article-feedback-admin.js', array( 'jquery' ), $this->version, false );

	}

}

// Register Settings page
function af_register_settings_page(){
  add_options_page('Article Feedback Settings', 'AF Setings', 'manage_options', 'af-settings', 'af_settings_page');
}

add_action('admin_menu', 'af_register_settings_page');

// Settings page
function af_settings_page() {

	// If isset
	if(isset($_POST['af_settings_nonce'])){

		// Check Nonce
		if(wp_verify_nonce($_POST['af_settings_nonce'], "af_settings_nonce")) {

			$af_display_thank = empty($_POST['af_display_thank'])?'0':'1';
			$af_display_count = empty($_POST['af_display_count'])?'0':'1';

			// echo "<pre>";
			// print_r($_POST);
			// echo "</pre>";

			// echo "<pre>";
			// print_r($af_display_count);
			// echo "</pre>";
			// exit();
			

			// new code
			$af_options_args = array(
		        'af_display_at' => $_POST['af_display_at'],
		        'af_display_text' => $_POST['af_display_text'],
		        'af_display_yes_text' => $_POST['af_display_yes_text'],
		        'af_display_no_text' => $_POST['af_display_no_text'],
		        'af_display_thank' => $af_display_thank,
		        'af_display_thank_text' => $_POST['af_display_thank_text'],
		        'af_display_count' => $af_display_count,
		        'af_display_count_text' => $_POST['af_display_count_text']
		    );

		    // Update options
		    $af_settings_save = update_option('af_options_settings', serialize($af_options_args));

		    if ($af_settings_save == 1) {
		    	// Settings saved
				echo '<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible"><p><strong>Settings saved.</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';

		    } else {
		    	// Settings not saved
				echo '<div id="setting-error-settings_updated" class="error settings-error notice-error is-dismissible"><p><strong>Settings not saved.</strong> Please Try again.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
		    }
		}
	}
	
	?>
	<div class="wrap">
		<h2>Article Feedback Plugin Settings</h2>
		
		<form method="post" action="options-general.php?page=af-settings">

			<?php $af_settings = unserialize(get_option("af_options_settings")); ?>

			<input type="hidden" value="<?php echo wp_create_nonce("af_settings_nonce"); ?>" name="af_settings_nonce" />
			<table class="form-table">
				<tr>
					<th scope="row"><label for="af_display_at">Display At</label></th>
					<td>
						<?php
						// Post Types
						$post_types = get_post_types(array('public' => true), 'names');
						
						// Read selected post types
						$selected_type_array = $af_settings['af_display_at'];

						foreach ($post_types as $post_type) {

							// Skip Attachment
							if($post_type == 'attachment'){
								continue;
							}

							// Get value
							$checkbox = '';
							if(!empty($selected_type_array)){
								if(in_array($post_type, $selected_type_array)){
									$checkbox = ' checked';
								}
							}

							// print inputs
							echo '<label for="'.$post_type.'" style="margin-right:18px;"><input'.$checkbox.' name="af_display_at[]" type="checkbox" id="'.$post_type.'" value="'.$post_type.'">'.$post_type.'</label>';
						}
						?>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="af_display_text">Question</label>
					</th>
					<td>
						<input type="text" placeholder="Was this article helpful?" class="regular-text" id="af_display_text" name="af_display_text" value="<?php echo $af_settings['af_display_text']; ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="af_display_yes_text">Positive Answer</label>
					</th>
					<td>
						<input type="text" placeholder="Yes" class="regular-text" id="af_display_yes_text" name="af_display_yes_text" value="<?php echo $af_settings['af_display_yes_text']; ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="af_display_no_text">Negative Answer</label>
					</th>
					<td>
						<input type="text" placeholder="No" class="regular-text" id="af_display_no_text" name="af_display_no_text" value="<?php echo $af_settings['af_display_no_text']; ?>" />
					</td>
				</tr>
				<?php 
					$af_display_thank_checked = '';
					if (!empty($af_settings['af_display_thank'])) {
						$af_display_thank_checked = 'checked';
					} 
				?>
				<tr>
					<th scope="row">
						<label for="af_display_thank">Display Thank You Message</label>
					</th>
					<td>
						<label for="af_display_thank_yes" style="margin-right:18px;"><input type="checkbox" class="regular-text" id="af_display_thank" name="af_display_thank" value="1" <?php echo $af_display_thank_checked; ?>/>Yes</label>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="af_display_thank_text">Thank You Message</label>
					</th>
					<td>
						<input type="text" placeholder="Thanks for your feedback!" class="regular-text" id="af_display_thank_text" name="af_display_thank_text" value="<?php echo $af_settings['af_display_thank_text']; ?>" />
					</td>
				</tr>
				<?php 
					$af_display_count_checked = '';
					if (!empty($af_settings['af_display_count'])) {
						$af_display_count_checked = 'checked';
					} 
				?>
				<tr>
					<th scope="row">
						<label for="af_display_count">Display Count Message</label>
					</th>
					<td>
						<label for="af_display_count_yes" style="margin-right:18px;"><input type="checkbox" class="regular-text" id="af_display_count" name="af_display_count" value="1" <?php echo $af_display_count_checked; ?>/>Yes</label>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="af_display_count_text">Count Message</label>
					</th>
					<td>
						<input type="text" placeholder="Users who found it useful:" class="regular-text" id="af_display_count_text" name="af_display_count_text" value="<?php echo $af_settings['af_display_count_text']; ?>" />
					</td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
<?php
}

function af_post_type_dynamic_actions () {

	// Read selected post types
	$af_settings = unserialize(get_option("af_options_settings"));	
	$af_post_types = $af_settings['af_display_at'];

	foreach ($af_post_types as $af_post_type) {

		add_filter('manage_'.$af_post_type.'_posts_columns','filter_cpt_columns', 10, 1);
		add_action( 'manage_'.$af_post_type.'_posts_custom_column','action_custom_columns_content', 10, 2 );

	 } 
}
add_action( 'admin_init', 'af_post_type_dynamic_actions' );


function filter_cpt_columns( $columns ) {

    // this will add the column to the end of the array
    $columns['feedback'] = 'Feedback';

    // as with all filters, we need to return the passed content/variable
    return $columns;
}

function action_custom_columns_content ( $columns, $post_id ) {

	// WP Globals
    global $table_prefix, $wpdb;

    // Customer Table
    $af_table = $table_prefix . 'feedback_tbl';

    // Network Site ID
    $network_id = get_current_blog_id();

    //run a if statement to display selection menu
    if ($columns == 'feedback') {

    	$feedback_count_query = $wpdb->get_results("SELECT ( SELECT COUNT(*) FROM ".$af_table." WHERE post_id = '".$post_id."' AND network_id = '".$network_id."' AND feedback = '1' ) AS positive_fb_count, ( SELECT COUNT(*) FROM ".$af_table." WHERE post_id = '".$post_id."' AND network_id = '".$network_id."' ) AS fb_count" );

    	echo $feedback_count_query[0]->positive_fb_count ."/". $feedback_count_query[0]->fb_count;
    }
}