<?php 
namespace Xirosoft\Formit\Admin;
use Xirosoft\Formit\Formit_Query;
use Xirosoft\Formit\Admin\Exports\Formit_ExportCsv;

if ( ! class_exists( 'Formit_FormSubmission' ) ) {
    class Formit_FormSubmission{
        /**
        * All action hook define construction function
        */
        function __construct(){
            
            // For Submission page
            add_action('admin_menu', [$this, 'formit_add_custom_submenu_page']);

            // Get Sumission Data
            add_action('wp_ajax_formit_get_submission_details', [$this, 'formit_get_submission_details']);
            add_action('wp_ajax_nopriv_formit_get_submission_details', [$this, 'formit_get_submission_details']);

            // Seleted Item for this hook
            add_action('wp_ajax_formit_bulk_delete_submissions', [$this, 'formit_bulk_delete_submissions']);
            add_action('wp_ajax_nopriv_formit_bulk_delete_submissions', [$this, 'formit_bulk_delete_submissions']);

            // Hook the formit_delete_single_submission function to the WordPress AJAX action
            add_action('wp_ajax_formit_delete_single_submission', [$this, 'formit_delete_single_submission']);
            add_action('wp_ajax_nopriv_formit_delete_single_submission', [$this, 'formit_delete_single_submission']);

            // Pageination hook
            add_action('wp_ajax_update_items_per_page', [$this,  'update_items_per_page']);
            add_action('wp_ajax_nopriv_update_items_per_page', [$this,  'update_items_per_page']);

            new Formit_ExportCsv();

        }
        /**
        * @formit_add_custom_submenu_page function for new page adeded
        * Ref to 'admin_menu' hook
        * @return void
        */
        function formit_add_custom_submenu_page() {
            add_submenu_page(
                'edit.php?post_type=formit', 
            __('Form Submission', 'formit'),
            __('Form Submission', 'formit'),
                'manage_options',
                'submission',
                [$this, 'formit_render_submission_page']
            );
        }
        

        function formit_render_submission_page() {  
            // Check if the user has the required permissions
            if (!current_user_can('manage_options')) {
                return;
            }
            
            global $wpdb;
            
            // Define the table name
            $table_name = $wpdb->prefix . 'formit_submissions'; // Assuming your table prefix is 'wp_'
            
            // Default pagination parameters
            if (isset($_COOKIE['itemsPerPage'])) {
                $default_per_page = sanitize_text_field($_COOKIE['itemsPerPage']);
            }else{
                $default_per_page = 10;
            }
            
            wp_nonce_field( 'formit_nonce_action', 'formit_nonce_field' );

            // Verify nonce before processing form data
            if ( ! isset( $_POST['formit_nonce_field'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['formit_nonce_field'] ) ) , 'formit_nonce_action' ) ){
                $per_page       = isset($_POST['items_per_page']) ? absint($_POST['items_per_page']) : $default_per_page;
                $current_page   = isset($_GET['paged']) ? absint($_GET['paged']) : 1; // Use 'paged' query parameter for page number
                // Add further processing logic if needed
            } else {
                // Nonce verification failed, handle the error or display a message
                // For example:
                echo esc_html__('Nonce verification failed. Please try again.', 'formit');
            }
            
            
            // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
            $total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
            
            // Calculate the total number of pages based on the selected per-page limit
            $total_pages = ceil($total_items / $per_page);
            
            // Ensure the current page is within valid bounds
            $current_page = max(1, min($current_page, $total_pages));
            
            // Calculate the offset for the SQL query
            $offset = ($current_page - 1) * $per_page;
            
            // Handle form submission to change per-page limit
            if (isset($_POST['items_per_page'])) {
                $per_page = absint($_POST['items_per_page']);
                $current_page = 1; // Reset to the first page when changing per-page limit
                $offset = 0; // Reset the offset
            }
            // Modify the query to retrieve a specific range of rows
            $order_direction = 'DESC'; // or 'ASC'
            $query = "SELECT * FROM %1s ORDER BY created_at %2s LIMIT %d OFFSET %d";
            // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
            $submissions = $wpdb->get_results($wpdb->prepare($query, $table_name, $order_direction, $per_page, $offset), ARRAY_A);
        
            ?>
            <!-- // Display filter options -->
            <div class="wrap"  id="submission-table">
                <?php if($total_items > 0) : ?>
                    <form method="post" class="smform-submisson-filter-form">
                        <div class="data_filter">
                            <div class="bulk-actions">
                                <select id="bulk-action">
                                    <option value=""><?php echo esc_html__('Bulk Actions', 'formit'); ?></option>
                                    <option value="delete"><?php echo esc_html__('Delete', 'formit'); ?></option>
                                </select>
                                <input type="submit" id="do-action" value="Apply" class="button button-primary">
                            </div>
                            <input type="text" id="filter-user-type" placeholder="Type for serach">
                            <select id="filter-form-title">
                                <option value=""><?php echo esc_html__('Select Form Title', 'formit'); ?></option>
                                <?php $this->formit_populate_form_title_dropdown(); ?>
                            </select>
                            <input type="text" id="filter-location" placeholder="Location">
                            <input type="date" id="filter-start-date" placeholder="Start Date">
                            <input type="date" id="filter-end-date" placeholder="End Date">
                            <input type="submit" value="Apply Filters" class="button button-primary">
                        </div>
                        <div class="total__mail">
                            <?php 
                                printf(
                                    esc_html__( '%d', 'formit' ),
                                    esc_html($total_items)
                                );
                            ?>
                        </div>
                        <div class="export-meta">
                            <button class="disabled" type="button" title="Upcoming Export Excel" id="export_excel">
                                <img src="<?php echo esc_url( FORMIT_ASSETS_URL. "img/icons/excel.webp"); ?>" alt="excel" />
                            </span>
                            <button class="disabled" type="button" title="Upcoming Export PDF" id="export_pdf">
                                <img src="<?php echo esc_url( FORMIT_ASSETS_URL. "img/icons/pdf.webp"); ?>" alt="pdf" />
                            </span>
                            <button type="button" title="Export CSV" id="formit_export_csv">
                                <img src="<?php echo esc_url( FORMIT_ASSETS_URL. "img/icons/csv.webp"); ?>" alt="csv" />
                            </span>
                        </div>
                    </form>
                
                <!-- // Display the table -->
                <div class="table-responsive">
                    <table class="widefat formit_data_table">
                        <!-- // Table headers -->
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="select-all"></th>
                                <th class="sortable" data-column="form_name"><?php echo esc_html__('Form Name', 'formit'); ?> </th>
                                <th class="sortable" data-column="email"><?php echo esc_html__('Email', 'formit'); ?> </th>
                                <th class="sortable" data-column="date"><?php echo esc_html__('Date and Time', 'formit'); ?> </th>
                                <th class="sortable" data-column="location"><?php echo esc_html__('Location', 'formit'); ?> </th>
                                <th><?php echo esc_html__('Action', 'formit'); ?></th>
                            </tr>
                        </thead>
        
                        <tbody>
                            <?php 
                                foreach ($submissions as $submission) : 
                                    $user_locationJson = $submission['user_location'];
                                    $mailBodyJson = $submission['mail_body'];
                                    $mailBodyJsoeDecode = json_decode($mailBodyJson, true);
                                    $userlocationJsoeDecode = json_decode($user_locationJson, true);
                                    $query = new Formit_Query;
                                ?>
                                <tr>
                                    <td><input type="checkbox" data-submission-id="<?php echo esc_attr($submission['id']); ?>"></td>
                                    <td><?php echo esc_html($submission['form_title']); ?></td>
                                    <td><?php echo esc_html($query->formit_get_email_from_mail_body($mailBodyJsoeDecode)); ?></td>
                                    <td class="date-and-time"><?php echo esc_html($submission['created_at']); ?></td>
                                    <td><?php echo esc_html($userlocationJsoeDecode['country']); ?></td>
                                    <td>
                                        <div class="action-meta-btn-group">
                                            <a href="#" data-submission-id="<?php echo esc_attr($submission['id']); ?>" class="view-details"><i class="formbuilder-icon-show"></i></a>
                                            <a href="#" data-submission-id="<?php echo esc_attr($submission['id']); ?>" class="edit-single disabled"><i class="formbuilder-icon-pencil"></i></a>
                                            <a href="#" data-submission-id="<?php echo esc_attr($submission['id']); ?>" class="delete-single"><i class="formbuilder-icon-trash"></i></a> 
                                        </div>
                                    </td> 
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            
                <div class="data-table-footer">
                    <form id="items-per-page-form" method="post">
                        <label for="items-per-page"><?php esc_attr_e('Items Per Page:', 'formit'); ?></label>
                        <input type="number" id="items-per-page" name="items_per_page" value="<?php
                            printf(
                                esc_attr__( '%d', 'formit' ),
                                esc_attr($per_page)
                            );
                        ?>">
                        <input type="submit" value="Apply" class="button button-primary">
                    </form>
                    <div class="pagination">
                        <!-- // Display the number pagination controls -->
                        <?php
                            echo '<ul class="pagination">';
                            if ($current_page > 1) {
                                echo '<li><a href="' . admin_url('edit.php?post_type=formit&page=submission&paged=' . ($current_page - 1) . '&items_per_page=' . $per_page) . '" class="prev">Previous</a></li>';
                            }
                            for ($i = 1; $i <= $total_pages; $i++) {
                                $active_class = ($i === $current_page) ? 'active' : '';
                                echo '<li class="' . $active_class . '"><a href="' . admin_url('edit.php?post_type=formit&page=submission&paged=' . $i . '&items_per_page=' . $per_page) . '">' . $i . '</a></li>';
                            }
                            if ($current_page < $total_pages) {
                                echo '<li><a href="' . admin_url('edit.php?post_type=formit&page=submission&paged=' . ($current_page + 1) . '&items_per_page=' . $per_page) . '" class="next">Next</a></li>';
                            }
                            echo '</ul>';
                            ?>
                    </div>
                </div>
            </div>

            <?php else: ?>
                <div class="not-found-submmisions">
                        <div class="not-found-data">
                            <svg width="450" height="308" viewBox="0 0 450 308" fill="none" xmlns="http://www.w3.org/2000/svg"><g filter="url(#filter0_d)"><rect x="118" y="28" width="312" height="205" rx="10.1" fill="#fff"></rect></g><rect x="174" y="84" width="202" height="15" rx="2" fill="#8F99A6" fill-opacity=".2"></rect><rect x="174" y="69" width="179.1" height="9.4" rx="4.7" fill="#9EA9B8" fill-opacity=".7"></rect><rect x="174" y="132.2" width="202" height="15" rx="2" fill="#8F99A6" fill-opacity=".2"></rect><rect x="174" y="117" width="148" height="10" rx="5" fill="#9EA9B8" fill-opacity=".7"></rect><rect x="174" y="183.2" width="202" height="15" rx="2" fill="#8F99A6" fill-opacity=".2"></rect><rect x="174" y="168.2" width="179.1" height="9.4" rx="4.7" fill="#9EA9B8" fill-opacity=".7"></rect><ellipse cx="137" cy="42.2" rx="4" ry="3.8" fill="#F54242"></ellipse><ellipse cx="151" cy="42.2" rx="4" ry="3.8" fill="#F8E434"></ellipse><ellipse cx="165" cy="42.2" rx="4" ry="3.8" fill="#ADD779"></ellipse><g filter="url(#filter1_d)"><rect x="25" y="62" width="312" height="205" rx="10.1" fill="#fff"></rect></g><rect x="81" y="118" width="202" height="15" rx="2" fill="#8F99A6" fill-opacity=".2"></rect><rect x="81" y="103" width="179.1" height="9.4" rx="4.7" fill="#9EA9B8" fill-opacity=".7"></rect><rect x="81" y="166.2" width="202" height="15" rx="2" fill="#8F99A6" fill-opacity=".2"></rect><rect x="81" y="151" width="148" height="10" rx="5" fill="#9EA9B8" fill-opacity=".7"></rect><rect x="81" y="217.2" width="202" height="15" rx="2" fill="#8F99A6" fill-opacity=".2"></rect><rect x="81" y="202.2" width="179.1" height="9.4" rx="4.7" fill="#9EA9B8" fill-opacity=".7"></rect><ellipse cx="44" cy="76.2" rx="4" ry="3.8" fill="#F54242"></ellipse><ellipse cx="58" cy="76.2" rx="4" ry="3.8" fill="#F8E434"></ellipse><ellipse cx="72" cy="76.2" rx="4" ry="3.8" fill="#ADD779"></ellipse><defs><filter id="filter0_d" x="93.6" y=".5" width="360.9" height="253.9" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"><feFlood flood-opacity="0" result="BackgroundImageFix"></feFlood><feColorMatrix in="SourceAlpha" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"></feColorMatrix><feOffset dy="-3.1"></feOffset><feGaussianBlur stdDeviation="12.2"></feGaussianBlur><feColorMatrix values="0 0 0 0 0.164706 0 0 0 0 0.223529 0 0 0 0 0.294118 0 0 0 0.21 0"></feColorMatrix><feBlend in2="BackgroundImageFix" result="effect1_dropShadow"></feBlend><feBlend in="SourceGraphic" in2="effect1_dropShadow" result="shape"></feBlend></filter><filter id="filter1_d" x=".6" y="34.5" width="360.9" height="253.9" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"><feFlood flood-opacity="0" result="BackgroundImageFix"></feFlood><feColorMatrix in="SourceAlpha" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"></feColorMatrix><feOffset dy="-3.1"></feOffset><feGaussianBlur stdDeviation="12.2"></feGaussianBlur><feColorMatrix values="0 0 0 0 0.164706 0 0 0 0 0.223529 0 0 0 0 0.294118 0 0 0 0.21 0"></feColorMatrix><feBlend in2="BackgroundImageFix" result="effect1_dropShadow"></feBlend><feBlend in="SourceGraphic" in2="effect1_dropShadow" result="shape"></feBlend></filter></defs></svg>
                            <h2><?php echo esc_html__('No Submission found','formit'); ?></h2>
                        <p>
                            <?php
                            $route = new Formit_Route;
                            printf(
                                /* translators: %1$s: Start link HTML, %2$s: End link HTML, %3$s: Line break HTML */
                                esc_html__( 'See the %1$sform documentation%2$s for instructions on publishing your form', 'formit' ),
                                '<a href="'. esc_url($route->formit_page_url('docs')) .'" target="_blank">',
                                '</a>'
                            );
                            ?>
                        </p>
                        </div>
                    </div>
                <?php endif; ?>
            
            <?php 
        }



        /**
        * Hook the formit_get_submission_details function to the WordPress AJAX action
        *
        * @return void
        */
        function formit_get_submission_details() {
            if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'] ) ) , 'formit-nonce' ) ){
                wp_send_json_error(array(
                    'status' => '400',
                    'message' => 'Nonce verification failed'
                ));
            }

            // Get the submission ID from the AJAX request
            $submission_id = absint($_POST['submission_id']);
        
            // Fetch submission details from the database based on $submission_id
            $submission_details = $this->formit_get_submission_details_by_id($submission_id);
        
            // You can format the details as HTML or JSON, depending on your needs
            wp_send_json($submission_details);
            wp_die(); // Always include this to terminate the script properly
        }
        

        function formit_get_submission_details_by_id($submission_id) {
            global $wpdb;
            // Get the submission ID from the AJAX request
            $table_name = $wpdb->prefix . 'formit_submissions';
        
            $query = "SELECT * FROM %1s WHERE id = %d";
            // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
            $result = $wpdb->get_row($wpdb->prepare($query, $table_name, $submission_id));
        
            return $result;
        }
        
        function formit_get_distinct_form_titles() {
            global $wpdb;
            $table_name = $wpdb->prefix . 'formit_submissions';

            
            $results = $wpdb->get_results($wpdb->prepare("SELECT DISTINCT form_title FROM %s", $table_name), ARRAY_A);
        
            return $results;
        }
        
        
        /**
        * Populate the Form Title dropdown
        *
        * @return void
        */
        function formit_populate_form_title_dropdown() {
            $form_titles = $this->formit_get_distinct_form_titles();     
            foreach ($form_titles as $title) {
                echo '<option value="' . esc_attr($title['form_title']) . '">' . esc_html($title['form_title']) . '</option>';
            }
        }

        /**
        * Bulk Delete function
        *
        * @return void
        */
        function formit_bulk_delete_submissions() {
            global $wpdb;
        
            if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'] ) ) , 'formit-nonce' ) ){
                wp_send_json_error(array(
                    'status' => '400',
                    'message' => 'Nonce verification failed'
                ));
            }
            // Get the submission IDs to delete from the AJAX request
            $submission_ids = isset( $_POST['submission_ids'] ) ? sanitize_text_field(wp_unslash( $_POST['submission_ids'] )) : array();
            // Verify user capabilities (you can adjust this)
            if (!current_user_can('manage_options')) {
                wp_send_json_error(array('message' => 'Permission denied.'));
            }
            // Delete the submissions from the database
            $table_name = $wpdb->prefix . 'formit_submissions';
            foreach ($submission_ids as $submission_id) {
                $result = $wpdb->delete($table_name, array('id' => $submission_id), array('%d'));
            }
            if ($result === false) {
                wp_send_json_error(array('message' => 'Error deleting submissions.'));
            }
            // Return success response
            wp_send_json_success(array('message' => 'Submissions deleted successfully.'));
        }
        

        function formit_delete_single_submission() {
            global $wpdb;

            if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'] ) ) , 'formit-nonce' ) ){
                wp_send_json_error(array(
                    'status' => '400',
                    'message' => 'Nonce verification failed'
                ));
            }        

            // Get the submission ID to delete from the AJAX request
            $submission_id = absint($_POST['submission_id']); 
            // Verify user capabilities (you can adjust this)
            if (!current_user_can('manage_options')) {
                wp_send_json_error(array('message' => 'Permission denied.'));
            }
            // Delete the submission from the database
            $table_name = $wpdb->prefix . 'formit_submissions';
            $result = $wpdb->delete($table_name, array('id' => $submission_id), array('%d'));
            if ($result === false) {
                wp_send_json_error(array('message' => 'Error deleting the submission.'));
            }
        
            // Return success response
            wp_send_json_success(array('message' => 'Submission deleted successfully.'));
        }
    }
}