<?php 
namespace Xirosoft\Formit\Admin\Exports;
use Xirosoft\Formit\Query;

class ExportCsv
{
    function __construct(){
        // CSV export hook
        add_action('wp_ajax_export_csv', [$this, 'export_csv']);
        add_action('wp_ajax_nopriv_export_csv', [$this, 'export_csv']);
    }    

    
    /**
     * Hook the export_csv function to a custom admin-ajax endpoint
     *
     * @return void
     */
    function export_csv() {
        $query = new Query;
        // Ensure the user has permission to export data
        if (!current_user_can('manage_options')) {
            wp_die('You do not have permission to export data.');
        }
        // Get the full URL of the site
        $site_url = site_url();

        // Use parse_url to extract the host (domain name) from the URL
        $host = parse_url($site_url, PHP_URL_HOST);

        $min = 1;  // Minimum value
        $max = 1000;  // Maximum value

        $randomNumber = rand($min, $max);

        // Define CSV file name and headers
        $filename = $host.'formit-submission-data-'.$randomNumber.'.csv';
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
        // Replace this with your actual database query
        $submissions = $query->get_submission_data(); // Replace with your function to fetch data
      
        if ($submissions) {
            foreach ($submissions as $submission) {
                // Sanitize and format the data as needed
                $user_locationJson = $submission['user_location'];
                $mailBodyJson = $submission['mail_body'];
                $userAgentJson = $submission['user_agent'];

                $mailBodyJsoeDecode = json_decode($mailBodyJson, true);
                $userlocationJsoeDecode = json_decode($user_locationJson, true);
                $userAgentJsonJsoeDecode = json_decode($userAgentJson, true);    
                
                $data = array(
                    sanitize_text_field($submission['form_title']),
                    sanitize_text_field($query->get_email_from_mail_body($mailBodyJsoeDecode)),
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
        fclose($output);
    
        // Prevent WordPress from rendering anything else
        exit;
    }

}
