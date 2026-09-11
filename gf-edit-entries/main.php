<?php
/**
 * Plugin Name: Gravity Forms - Edit entries
 * Description: This plugin lets you edit the entries for the forms created with gravity
 * Author: Bright Plugins
 * Version: 1.1
 * Tested up to: 7.1
 * Author URI: http://brightplugins.com/
 */

if (!defined('ABSPATH')) {
    die();
}


if (class_exists('GFForms')) {
    add_action('gform_entries_first_column_actions', 'bv_gravity_first_column_actions', 10, 4);
    add_action('gform_entries_first_column_actions', 'bv_gravity_first_column_quick_edit', 10, 4);
    add_action('gform_entry_detail', 'bv_gravity_add_to_details', 10, 2);
    add_action('admin_enqueue_scripts', 'bpgf_edit_enqueue_admin_script');
    add_action('admin_footer', 'bpgf_edit_markup');
   

    add_action('wp_ajax_gf_form_edit', 'bp_add_quick_edit_markup');
    add_action('wp_ajax_bpgf_update_entry', 'bpgf_update_entry');

    
    function bpgf_edit_markup(){
        //get the field
         $field = GFFormsModel::get_entry(176);
        $form = GFAPI::get_form(5);

        fw_print($form['fields']);
    }
    /**
     * Enqueue a script in the WordPress admin on page gf_entries.
     *
     * @param int $hook Hook suffix for the current admin page.
     */
    function bpgf_edit_enqueue_admin_script($hook)
    {
        if ('forms_page_gf_entries' != $hook) {
            return;
        }
        wp_enqueue_style('gf-edit-entries', plugin_dir_url(__FILE__) . 'assets/css/admin.css', [], time());
        wp_enqueue_style('fancybox', plugin_dir_url(__FILE__) . 'assets/css/jquery.fancybox.min.css', [], time());
        wp_enqueue_script('fancybox', plugin_dir_url(__FILE__). 'assets/js/jquery.fancybox.min.js', array('jquery'), time(), true);

        wp_enqueue_script('gf-edit-entries', plugin_dir_url(__FILE__) . 'assets/js/gf-edits.js', array('jquery','fancybox'), time());
        $params = array(
            'ajax_url' => admin_url('admin-ajax.php', 'relative'),
            'ajax_nonce' => wp_create_nonce('bp_gf_edit'),
            'ajax_nonce_update' => wp_create_nonce('bp_gf_update'),

        );
        wp_localize_script('gf-edit-entries', 'gf_edit_parms', $params);
    }
  

    /**
     * Add edit button into frst column
     * which redirect into the Form edit page
     *
     * @param [type] $form_id
     * @param [type] $field_id
     * @param [type] $value
     * @param [type] $entry
     * @return void
     */
    function bv_gravity_first_column_actions($form_id, $field_id, $value, $entry)
    {
        $lead_id = $entry['id'];
        $id = $entry['form_id'];
        echo "| <a href='admin.php?page=gf_entries&view=entry&id={$id}&lid={$lead_id}&order=ASC&filter&paged=1&pos=0&field_id&operator&edit=1'>Edit</a> ";
    }

    /**
     * Add Quick Edit button into the first column
     *
     * @param [int] $form_id
     * @param [int] $field_id
     * @param [] $value
     * @param [int] $entry
     * @return void
     */
    function bv_gravity_first_column_quick_edit($form_id, $field_id, $value, $entry)
    {
        $lead_id = $entry['id'];
        $id = $entry['form_id'];
      
        echo "| <a  href='javascript:;' data-lead_id='$lead_id' data-form_id='$id' class='bp_gf_quick_view'>Quick Edit</a>";
    }


    /**
     * Add javscript code into the entry details page
     *
     * @param [type] $form
     * @param [type] $entry
     * @return void
     */
    function bv_gravity_add_to_details($form, $entry)
    {
        if (isset($_REQUEST['edit']) && $_REQUEST['edit'] == 1) {
            echo '<script type="text/javascript">jQuery(document).ready(function(){jQuery("#gform_edit_button").click();});</script>';
            //document.getElementById("gform_edit_button").click();
        }
    }

    /**
    * Ajax function for update entries
    *
    * @return void
    */
    function bp_add_quick_edit_markup()
    {
        if (!DOING_AJAX) {
            wp_die();
        } // Not Ajax

        // Check for nonce security
        $nonce = $_POST['nonce'];

        if (!wp_verify_nonce($nonce, 'bp_gf_edit')) {
            wp_die('oops!');
        }
        $entryId = $_POST['entryId'];
        $formId = $_POST['formId'];
        $ids = $_POST['ids'];
        ?>
        <form class="bp-entry-details" method="post" name="bpgf-update-entries">
            <?php
            // GFAPI::update_entry_field(176, 4, 'niloy');
            foreach ($ids as $key => $id) {

                $field = GFFormsModel::get_field($formId, $id);
             
                $entry = GFAPI::get_entry($entryId); ?>
                <div class="form-field">    
                    <label><?php echo $field['label']; ?></label>
                    <input type="text" name="<?php echo $id; ?>" placeholder="<?php echo $field['label'];?>" value="<?php echo $entry[$id]?> ">
                </div>
                <?php
            } ?>
   
            <button type="submit" class="button button-primary">Update </button>
        </form>
        <?

        // RIP
        wp_die();
    }
    function bpgf_update_entry()
    {
        if (!DOING_AJAX) {
            wp_die();
        } // Not Ajax

        // Check for nonce security
        $nonce = $_POST['nonce'];
        $entryData = $_POST['fdata'];


        if (!wp_verify_nonce($nonce, 'bp_gf_update')) {
            wp_die('oops!');
        }
        
           
            // GFAPI::update_entry_field(176, 4, 'niloy');
        print_r($entryData);

        // RIP
        wp_die();
    }
}




function bv_create_support_notice()
{
    $class = 'notice notice-warning';
    $message ='If you need dedicated/professional assistance with this plugin or just want an expert to get your site built and or to run the faster, you may hire us at';

    printf('<div class="%1$s"><p>%2$s <a href="https://www.brightvessel.com/" target="_blank">Bright Vessel</a>. <small><a href="?bvclose=true">[x]</a></small></p></div>', esc_attr($class), esc_html($message));
}

if (isset($_GET['bvclose']) && $_GET['bvclose'] == 'true') {
    add_option('bvclose', 1);
}
if (intval(get_option('bvclose')) !== 1) {
    add_action('admin_notices', 'bv_create_support_notice');
}
