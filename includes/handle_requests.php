<?php

function handle_requests()
{
    function badge_product_checkbox()
    {
        // check if product id already set as badge product
        $cb_value = 'no';
        $value = 'yes';

        if (get_option('badge_form_field_product_id') == $_GET['post']) {
            $cb_value = 'yes';
            $value = 'yes';
        }

        $args = array(
            'name' => 'badge_product_id',
            'id' => 'badge_product_id',
            'label' => __('Use this product for the badge plugin', 'badge_product'),
            'class' => 'product-cb',
            'cbvalue' => $cb_value,
            'value' => $value,
            'desc_tip' => __('This product will be used for the badge creator.', 'ctwc'),
        );
        woocommerce_wp_checkbox($args);
    }
    add_action('woocommerce_product_options_general_product_data', 'badge_product_checkbox');

    function badge_product_save_selection($post_id)
    {
        if (isset($_POST['badge_product_id'])) {
            update_option('badge_form_field_product_id',  $post_id);
        }
    }
    add_action('woocommerce_process_product_meta', 'badge_product_save_selection');

    // GET Actions
    if (isset($_GET['action'])) {
        if ($_GET['action'] == 'edit') {
            global $admin_edit_form;
            global $admin_edit_form_id;
            global $admin_edit_form_field_name;

            $admin_edit_form = true;
            $admin_edit_form_id = $_GET['id'];
            $admin_edit_form_field_name = $_GET['field'];
        } else if ($_GET['action'] == 'delete') {
            bd_action_delete_field($_GET);
        } else if ($_GET['action'] == 'new_field') {
            global $admin_new_field;
            $admin_new_field = true;
        }
    }

    // POST actions
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'update') {
            global $admin_edit_form;
            $admin_edit_form = false;
            bd_action_update_field($_POST);
        } else if ($_POST['action'] == 'new_field') {
            bd_add_new_field($_POST);
        }
    }
}

?>