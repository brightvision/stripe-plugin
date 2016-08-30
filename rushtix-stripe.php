<?php 
/*
Plugin Name: RushTix Stripe
Description: All stripe payments used in rushtix including popups design, css and js. It also includes the new login and Sign-up popups and functioanlity.
Version:     0.1
Author:      Haris Amjed
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: rushtix
*/

//plugin security
defined( 'ABSPATH' ) or die( 'No script kiddies please!');

//Code is splitted up into different files to organize it
require('inc/functions.php');
require('inc/html.php');
require('inc/admin-page.php');

// This function enqueue/add all the css and js for popups
function rushtix_popups_css_js(){
	global $current_user;
	wp_enqueue_script('stripe-lib', 'https://js.stripe.com/v2/', array(), '1', true);
	wp_enqueue_style('mpf-css', plugins_url( '/assets/magnific-popup.css', __FILE__ ) );
	wp_enqueue_script('mpf-js', plugins_url( '/assets/magnific-popup.js', __FILE__ ), array(), '1', true);

	wp_enqueue_style('stripe-css', plugins_url( '/assets/rushtix-stripe.css', __FILE__ ) );

	wp_register_script('stripe-js', plugins_url( '/assets/rushtix-stripe.js', __FILE__ ), array('jquery','mpf-js'), '1', true);

if(function_exists("rt_group_name")) $rt_group_name=rt_group_name();
	else $rt_group_name="Guest Pass";
	wp_localize_script( 'stripe-js', 'params', 
		array('ajax_url'=>admin_url("admin-ajax.php"),
			'publishable_key'=>rt_get_stripe_pub_key(),
			'rt_group_name'=>$rt_group_name,
			'logged_in'=>is_user_logged_in()?'yes':'no',
			'home_url'=>home_url(),
			'current_user_name'=>$current_user->display_name
			)
		);

	wp_enqueue_script('stripe-js');
}

add_action('wp_enqueue_scripts', 'rushtix_popups_css_js',15);//With 15 being loaded after the default priority of 10. Theme use 10

/**
 * events manager pro is a pre-requirements
 */
function emp_stripe_prereq() {
    ?> <div class="error"><p><?php _e('Please ensure you have <a href="http://eventsmanagerpro.com/">Events Manager Pro</a> installed, as this is a requirement for the PayPal Advanced add-on.','events-manager-paypal-advanced'); ?></p>
       </div>
    <?php
}

add_action( 'plugins_loaded', 'emp_stripe_register', 1000);
/**
 * initialise plugin once other plugins are loaded 
 */
function emp_stripe_register() {
	//check that EM Pro is installed
	if( ! defined( 'EMP_VERSION' ) ) {
		add_action( 'admin_notices', 'emp_stripe_prereq' );
		return false; //don't load plugin further
	}
	
	if (class_exists('EM_Gateways')) {
		require_once( plugin_dir_path( __FILE__ ) . 'inc/ticket-payment-gateway.php' );
		EM_Gateways::register_gateway('emp_stripe', 'EM_Gateway_Stripe');
	}
	
}