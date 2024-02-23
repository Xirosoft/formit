<?php 
namespace Xirosoft\Formit\Admin\Exports;
use Xirosoft\Formit\Formit_Query;

if ( ! class_exists( 'Formit_ExportCsv' ) ) {
    class Formit_ExportCsv
    {
        function __construct(){
            // CSV export hook
            add_action('wp_ajax_formit_export_csv', [$this, 'formit_export_csv']);
            add_action('wp_ajax_nopriv_formit_export_csv', [$this, 'formit_export_csv']);
        }    

        
        /**
        * Hook the formit_export_csv function to a custom admin-ajax endpoint
        *
        * @return object
        */

        
        function formit_export_csv() {

            if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'] ) ) , 'formit-nonce' ) ){
                wp_send_json_error(array(
                    'status' => '400',
                    'message' => 'Nonce verification failed'
                ));
            }

            $query = new Formit_Query;
            // Ensure the user has permission to export data
            if (!current_user_can('manage_options')) {
                wp_die('You do not have permission to export data.');
            }
            // Define CSV file name and headers
            $filename = 'form_submission_data.csv';
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
        
            // Output CSV headers
            $output = fopen('php://output', 'w');
            $headers = array(
                'Name',
                'Email',
                'Location',
                'Date and Time',
            );
            fputcsv($output, $headers);

            // Fetch and format data from the 'wp_formit_submissions' table
            $submissions = $query->formit_get_submission_data(); // Replace with your function to fetch data'
        
            if (is_array($submissions)) {
                foreach ($submissions as $submission) {
                    // Sanitize and format the data as needed
                    $user_locationJson          = $submission['user_location'];
                    $mailBodyJson               = $submission['mail_body'];
                    $userAgentJson              = $submission['user_agent'];
                    $mailBodyJsoeDecode         = json_decode($mailBodyJson, true);
                    $userlocationJsoeDecode     = json_decode($user_locationJson, true);
                    $userAgentJsonJsoeDecode    = json_decode($userAgentJson, true);    
                    
                    $data = array(
                        sanitize_text_field($submission['form_title']),
                        sanitize_text_field($query->formit_get_email_from_mail_body($mailBodyJsoeDecode)),
                        sanitize_text_field($userlocationJsoeDecode['country']),
                        sanitize_text_field($submission['created_at']),
                    );
                    // Add data to the CSV
                    fputcsv($output, $data);
                }
            } else {
                wp_die('No data to export.');
            }
        
            // Close the file pointer
            // fclose($output);
            // Prevent WordPress from rendering anything else
            exit;
        }

    }
}