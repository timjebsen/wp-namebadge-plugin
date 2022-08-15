<?php
//  Admin Menu

function namebadge_designer_admin_menu()
{
    add_menu_page('Ozwear Name Badge Editor', 'Ozwear Name Badges', 'manage_options', 'ozwear-namebadge-admin-menu', 'namebadge_editor_page', '', 200);
}

function namebadge_editor_page()
{
    global $admin_new_field;

    $badge_product_id = get_option('badge_form_field_product_id');


    if (!$badge_product_id != '') {
    ?>
        <div class="notice notice-warning is-dismissible">
            <p><?php _e('Warning! No product has been assigned as a badge. Please go to a product and under "Product Data" -> "General" select the checkbox.', 'sample-text-domain'); ?></p>
        </div>
    <?php
    }

    // Badge fields to be shown on front end. Array of objects
    $badge_fields_objs = get_option('badge_form_fields');
    $badge_fields_array = array();

    // Build kv array of fields
    if ($badge_fields_objs) {
        foreach ($badge_fields_objs as $obj) {
            array_push($badge_fields_array, get_object_vars($obj));
        }
    }

    // Display table of form fields
    ?>
    <div class="wrap">
        <h2>Ozwear Name Badge</h2>
        <h3>Fields</h3>
        <?php

        $lt = new table();
        $lt->fields = $badge_fields_array;
        $lt->prepare_items();
        $lt->display();

        if (!$admin_new_field) {
        ?>
            <a href="?page=<?php echo $_REQUEST['page'] ?>&action=new_field" class="button" style="margin-top: 10px">New Field</a>
        <?php
        } else {
        ?>
            <form method="POST">
                <input type="hidden" name="action" value="new_field">
                <div class="ozwear-field-form" style="display: flex; flex-direction: row; margin-top:10px">
                    <div class="ozwear-new-field-field" style="display: flex; flex-direction:column; ">
                        <label for="label">Label</label>
                        <input name="label" type="text">
                    </div>
                    <div class="ozwear-new-field-field" style="display: flex; flex-direction:column; ">
                        <label for="type">Type</label>
                        <select name="type">
                            <option value="text">Text</option>
                            <option value="radio">Radio</option>
                            <option value="select">Select</option>
                        </select>
                    </div>
                    <div class="ozwear-new-field-field" style="display: flex; flex-direction:column; ">
                        <label for="description">Description</label>
                        <input name="description" type="text">
                    </div>
                    <div class="ozwear-new-field-field" style="display: flex; flex-direction:column; ">
                        <label for="options">Options (semicolon seperated values ('value 1;value 2;value 3'))</label>
                        <input name="options" type="text">
                    </div>
                    <input type="submit" class="button" value="Submit">
                </div>

            </form>
        <?php
        }

        // Display Table of CSV files
        ?>
    </div>

    <div class="wrap">
        <h3>Download CSV</h3>
        <?php

        $csv_table = new csv_table();
        $csv_table->data = get_csv_table_items();
        $csv_table->prepare_items();
        $csv_table->display();

        ?>
    </div>
<?php

}

?>