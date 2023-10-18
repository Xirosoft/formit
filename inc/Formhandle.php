<?php 
// Enqueue assets
namespace Xirosoft\Formit;
use Xirosoft\Formit\Utils\VisitorInfo;

class Formhandle{
    private $wpdb;
    private $table_name;
    private $data_format;

    // Constructor to initialize the $wpdb object
    public function __construct($ip = null){
        global $wpdb;
        $this->wpdb = $wpdb;
        new API\TrafficLocation();
        $this->table_name = $wpdb->prefix . 'formit_submissions'; // Replace 'ms_form_data' with your custom table name
        $this->data_format = array(
            '%s', // 'mail_body' is a string
            '%s', // 'form_title' is a string
            '%d', // 'form_id' is a string
            '%s', // 'delivery_status' is an integer
            '%s', // 'is_auto_reply' is a string
            '%d', // 'ip_address' is a string
            '%s', // 'user_agent' is a string
            '%s', // 'refer_page' is a string (datetime)
            '%s', // 'refer_page' is a string (datetime)
            '%s', // 'gcaptcha' is a string (datetime)
            '%s', // 'updated_at' is a string (datetime)
            '%s', // 'updated_at' is a string (datetime)
        );

        add_action('wp_ajax_msfrom_submit_ajax_function', [$this, 'msfrom_submit_ajax_function']);
        add_action('wp_ajax_nopriv_msfrom_submit_ajax_function', [$this, 'msfrom_submit_ajax_function']); // For non-authenticated users

    }

    public function insert_data($data) {
        
        global $wpdb;
        $defaults = array(
            'mail_body' => '',
            'form_title' => '',
            'form_id' => '',
            'delivery_status' => '',
            'is_auto_reply' => '', // Include this if needed
            'ip_address' => '',
            'user_agent' => '',
            'refer_page' => '',
            'user_location' => '',
            'gcaptcha' => '',
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql'),
        );
        // Merge provided data with defaults
        $data = wp_parse_args($data, $defaults);

        // Ensure data format matches the expected format
        $wpdb->insert($this->table_name, $data, $this->data_format);
        return $wpdb->insert_id; // Returns the ID of the inserted row
    }
       

    function replaceFieldNames($inputString, $dataTable) {
         // Regular expression pattern to match placeholders with escaped square brackets
        $pattern = '/\[(.*?)\]/';

        // Replace placeholders with their corresponding values
        $replacedString = preg_replace_callback($pattern, function ($matches) use ($dataTable) {
            $placeholder = str_replace(['[', ']'], ['\[', '\]'], $matches[1]); // Escape square brackets
            foreach ($dataTable as $item) {
                if (isset($item["label"]) && strcasecmp($item["label"], $placeholder) === 0) {
                    return $item["value"];
                }
            }

            return $matches[0]; // Return the original text if not found in the data table
        }, $inputString);

        return $replacedString;
    }
    
    function msfrom_submit_ajax_function() {
        $visitorInfo = new VisitorInfo;

        // Grub and formation our formData
        if(!isset($_POST['data'])) {
            wp_send_json_error(array('message' => 'Form data not set yet.'));
        }

        global $wpdb;
        $query      = new Query();
        $formData   = sanitize_text_field($_POST['data']);
        $form_id    = sanitize_text_field($_POST['data'][0]['value']); // Form ID get
        $post_id    = sanitize_text_field($_POST['data'][1]['value']); //post id
        $form_name  = sanitize_text_field($_POST['data'][2]['value']); // form name
        $table_name = $wpdb->prefix . 'formit_forms'; // Replace 'ms_form_data' with your custom table name
        $query      = $wpdb->prepare("SELECT * FROM {$table_name} WHERE id = %s", $form_id);
        $get_mail_settings_data =  $wpdb->get_results($query);
        $data =  json_decode($get_mail_settings_data[0]->form_status_messages);
     
        $jsonData = json_encode($formData); // Encode JSON data
        $trafficLocation = new API\TrafficLocation();
        $trafficLocationJson = json_encode($trafficLocation->getLocation()); // Encode JSON data

        // Define data to insert or update
        $data_to_insert = array(
            'mail_body' => $jsonData,
            'form_title' => $form_name,
            'form_id' => $form_id,
            'delivery_status' => true,
            'is_auto_reply' => true, // Include this if needed
            'ip_address' => $visitorInfo->getPublicIp(),
            'user_agent' => $visitorInfo->getUserAgentInfo(),
            'refer_page' => $visitorInfo->getReferringPage(),
            'user_location' => $trafficLocationJson,
            'gcaptcha' => 'Will Update',
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql'),
        );
        
        $result = $this->insert_data($data_to_insert);

        global $wpdb;
        $query          = new Query;
        $table_name     = $wpdb->prefix . 'formit_forms'; // Replace 'your_custom_table' with your actual table name
        $getFormData    = $query->get_single_row($table_name, $post_id);
        $jsdecode       = json_decode($getFormData['form_configs']);
        $mailTemplate   = $jsdecode[0]->formit_mail_body; // mail Template get from database
        $jsonDataTable  = json_decode($jsonData, true);
        $ReadyMailTemplate = $this->replaceFieldNames($mailTemplate, $jsonDataTable);
        

        // Generate Mail body 
        $mail_html = '<table>';
        foreach($formData as $key => $input) {
            if($formData[$key - 1]['name'] == $input['name']) {
                $mail_html .= '<tr><td>'. esc__html( $input['label'], 'formit' )  .'</td><td>'. esc__html( $input['value']. ' added', 'formit' ) .'</td></tr>';
            } else {
                $mail_html .= '<tr><td>'. esc__html( $input['label'], 'formit' ) .'</td><td>'. esc__html( $input['value'], 'formit' ) .'</td></tr>';
            } 
        }
        $mail_html .= '</table>';
        
        
        // Additional headers
        $mail_from      = $data->formit_sender_mail || get_option('admin_email')  ; // array('Someone Person', someone@mail.com)
        $mail_to        = $data->formit_mail_to || get_option('admin_email') ; // 'admin@mail.com'
        $mail_cc        = $data->formit_mail_to || null; // array('one@mail.com', 'two@mail.com)
        $mail_bcc       = $data->formit_mail_to || null; // array('one@mail.com', 'two@mail.com)
        $mail_headers   = $data->formit_mail_additional_headers || array('Content-Type: text/html; charset=UTF-8');
        // $mail_subject   = $data->msfrom_mail_subject || 'mail_subject';
        $mail_subject   = $data->msfrom_mail_subject; 
        // wp_send_json_success($data);
        
        $mail_message = $this->mail_template($ReadyMailTemplate, '');;
    
        // Send the email 
        // Supported Params: $subject, $body, $from, $to, $cc, $bcc, $headers
        $mail_sent =  $this->mail_sender($subject=$mail_subject, $body=$mail_message ); 
        
        
        // Send a JSON response
        if($mail_sent) {
            // Show redirect or success popup
            // Data doesn't exist, perform an insert
            // I think this condition is not required for us. please see the line:181
            if(!$result){
                $result = $this->insert_data($data_to_insert);
                if($result) {
                    wp_send_json_success(array(
                        'message'=> __('Mail sent successfully done!'),
                        'config' => $get_mail_settings_data[0]
                    ));
                }
            }
            
            wp_send_json_success(array(
                'message'=> __('Mail sent successfully done!'),
                'config' => $get_mail_settings_data[0]
            ));
        } else {
            wp_send_json_error(array('message' => 'Mail does not sent!'));
        }   
        wp_die();
    }
    
    
    function mail_sender($subject=null, $body=null, $from=null, $to=null, $cc=null, $bcc=null, $headers=null) {
        // set default data in function param
        empty($subject) 
            ? $subject = __('Formit Form Submission') 
            : $subject;
            
        empty($body) 
            ? $body = __('Formit Form Submission body is empty') 
            : $body;
    
        empty($to) 
            ? $to = get_option('admin_email') 
            : $to;
    
        empty($headers) 
            ? $headers = array('Content-Type: text/html; charset=UTF-8') 
            : $headers;
    
        // INSERT From emails into headers array
        // ecpectation data: array('Sender Name', 'sender@mail.com') or 'sender@mail.com'
        if(!empty($from)) {
            if(is_arry($from)) {
                $headers[] = 'From: ' .$from[0]. ' <' .$from[1]. '>';
            } else {
                $headers[] = 'From: ' .get_bloginfo('name'). ' <' .$from[1]. '>';
            }
        } else {
            $headers[] = 'From: Formit <formit@xirosoft.com>';
        }
    
        // INSERT Cc emails into headers array
        // ecpectation data: array('info@mail.com', 'user@mail.com')
        if(!empty($cc) && is_array($cc)) {
            foreach($cc as $email) {
                $headers[] = 'Cc: '.$email;
            }
        }
    
        // INSERT Bcc emails into headers array
        // ecpectation data: array('info@mail.com', 'user@mail.com')
        if(!empty($bcc) && is_array($bcc)) {
            foreach($bcc as $email) {
                $headers[] = 'Bcc: '.$email;
            }
        }
    
        // Send mail and return responce
        return $mail_send = wp_mail($to, $subject, $body, $headers);
    }
    
    function mail_template($body=null, $name=null) {
        $basic_style = '<style>
            @import url("https://fonts.googleapis.com/css2?family=Inter:wght@300;500&display=swap") 
            html, body{font-family: "Inter", sans-serif;background-color:#f8f8f8}
            .main{background-color:#fff;width:clamp(500px,70vw,600px);margin:6px auto 25px;}
            .main>table {width:100%}
            table{border:1px solid #fff;border-collapse: collapse}
            tr>td:first-child {font-weight: 600}
            tr:nth-of-type(odd) {background-color: #e6e6e6}
            td,tr{border:1px solid #fff; padding: 4px; font-family: "Inter", sans-serif}
            .empty-message{background-color: #e5a8a8;padding:4px;font-family:"Inter",sans-serif}
            .mail-title {background-color: #f1e0ff; color: #353535;text-align:center;padding:8px;margin-bottom:0;font-family: "Inter", sans-serif}
            .copyright-message{background-color: #780dce; color: #fff;text-align:center;padding:12px;margin-top:16px;font-family: "Inter", sans-serif}
            .copyright-message a{color:#fff;font-weight:600;text-decoration:none}
        </style>';
        $flat_style = '';
    
        $empty_message = $basic_style.'<table class="main"><tr><td><p class="empty-message">The mail is Empty! Please configure on Settings Tab</p></td></tr></table>';
        $template_header = '<h2 class="mail-title">Formit Form</h2>';
        $template_footer = '<div class="copyright-message">Powered By: <a href="https://xirosoft.com" target="_blank" title="xirosoft">xirosoft.com</a></div>';
    
        $html = $basic_style.'<div style="background-color:#a5a5a5;padding: 6px"><div class="main">';
    
        // return template with styles 
        if(!$body) {
            $html = $empty_message;
        } else {
            $html .= $template_header;
            switch ($name){
                case 'FLAT':
                    $html .= $flat_style;
                    $html .= $body;
                break;
                default:
                    $html .= $body;
            }
            $html .= $template_footer.'</div></div>';
        }
        return $html;
    }
}