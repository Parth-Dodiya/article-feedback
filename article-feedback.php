<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.google.com
 * @since             1.0.0
 * @package           Article_Feedback
 *
 * @wordpress-plugin
 * Plugin Name:       Article Feedback Plugin
 * Plugin URI:        https://www.google.com
 * Description:       This Plugin will collect data for the feedback of article.
 * Version:           1.0.0
 * Author:            Parth Dodiya
 * Author URI:        https://www.google.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       article-feedback
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'ARTICLE_FEEDBACK_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-article-feedback-activator.php
 */
function activate_article_feedback() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-article-feedback-activator.php';
	Article_Feedback_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-article-feedback-deactivator.php
 */
function deactivate_article_feedback() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-article-feedback-deactivator.php';
	Article_Feedback_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_article_feedback' );
register_deactivation_hook( __FILE__, 'deactivate_article_feedback' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-article-feedback.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_article_feedback() {

	$plugin = new Article_Feedback();
	$plugin->run();

}
run_article_feedback();


// Installation plugin
function af_activate() {

    // // Add default options
    // add_option('af_display_at', '["post"]');
    // add_option('af_display_text', 'Was this article helpful?');
    // add_option('af_display_yes_text', 'Yes');
    // add_option('af_display_no_text', 'No');
    // add_option('af_display_thank', 1);
    // add_option('af_display_thank_text', 'Thanks for your feedback!');
    // add_option('af_display_count', 0);
    // add_option('af_display_count_text', 'Users who found it useful:');

    // new code
    $af_options_args = array(
        'af_display_at' => ["post"],
        'af_display_text' => 'Was this article helpful?', 
        'af_display_yes_text' => 'Yes', 
        'af_display_no_text' => 'No', 
        'af_display_thank' => 1, 
        'af_display_thank_text' => 'Thanks for your feedback!',
        'af_display_count' => 0,
        'af_display_count_text' => 'Users who found it useful:'
    );
    
    add_option('af_options_settings', serialize($af_options_args) );


    // Insert DB Tables
    af_init_db();
    
    // echo "<pre>";
    // print_r(serialize($af_options_args));
    // echo "</pre>";
    // exit();
    
}

register_activation_hook( __FILE__, 'af_activate' );


// Initialize DB Tables
function af_init_db() {

    // WP Globals
    global $table_prefix, $wpdb;

    // Customer Table
    $af_table = $table_prefix . 'feedback_tbl';

    // Create Customer Table if not exist
    if( $wpdb->get_var( "show tables like '$af_table'" ) != $af_table ) {

        // Query - Create Table
        $sql = "CREATE TABLE `$af_table` (";
        $sql .= " `id` int(11) NOT NULL auto_increment, ";
        $sql .= " `post_id` varchar(500) NOT NULL, ";
        $sql .= " `ip_address` varchar(500) NOT NULL, ";
        $sql .= " `feedback` varchar(500) NOT NULL, ";
        $sql .= " `network_id` varchar(500), ";
        $sql .= " PRIMARY KEY (`id`) ";
        $sql .= ") ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";

        // Include Upgrade Script
        require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
    
        // Create Table
        // echo "<pre>";
        // print_r(dbDelta( $sql ));
        // echo "</pre>";
        // exit();
        dbDelta( $sql );
    }

}