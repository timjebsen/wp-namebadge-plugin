<?php

/**
 * Plugin Name: OzWear Name Badge Form
 * Description: Name Badge form. Create multiple name badges, details are included in order line items, and accessable by a csv file. Toggle the csv file as an attachement on the order email in settings.
 * Author: Tim Jebsen (timjebsen@outlook.com.au)
 */


// Helper funcs
include 'includes/helper.php';
include 'includes/classes.php';
include 'includes/admin_view.php';
include 'includes/public_form_view.php';
include 'includes/handle_requests.php';
include 'includes/wc_hooks.php';

// Global vars
$custom_data_key = 'name_badge_data';
$admin_edit_form = false;
$admin_edit_form_id;
$admin_edit_form_field_name = '';
$admin_new_field = false;

// Activation - Deactivation Hooks

function activation_hook()
{
    update_option('badge_form_fields', null);
    update_option('badge_form_field_product_id', '');
}

function deactivation_hook()
{
    delete_option('badge_form_field_product_id');
    delete_option('badge_form_fields');
}

register_activation_hook(__FILE__, 'activation_hook');
register_deactivation_hook(__FILE__, 'deactivation_hook');

// Handle Requests
add_action('init', 'handle_requests');


// Register resources
function register_js_resource()
{
    wp_register_script('badge_form_script', plugins_url('resources/badge_script.js', __FILE__), false, NULL, 'all');
}
add_action('init', 'register_js_resource');

function register_style()
{
    wp_register_style('badge_form_style', plugins_url('resources/badge_form_style.css', __FILE__));
}

add_action('init', 'register_style');

// Admin settings view
add_action('admin_menu', 'namebadge_designer_admin_menu');

// Optional shortcode for public form
add_shortcode('badge_form', 'badge_form_func');

// Replace product summary with form
add_action( 'woocommerce_single_product_summary', 'badge_form_remove_product_summary', 1, 1 );

// See wc_hooks.php for wc cart/checkout/item metadata hooks

