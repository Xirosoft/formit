<?php 
namespace Xirosoft\Formit\API;
use WP_REST_Controller;
use WP_REST_Server;
// use WP_REST_Request;

if ( ! class_exists( 'Formit_FormitForm' ) ) {

    class Formit_FormitForm extends WP_REST_Controller{
        
        function __construct() {
            // Set the namespace for the REST API endpoints.
            $this->namespace = 'formit/v1';
        
            // Define the base URL endpoint for your forms.
            $this->rest_base = 'forms';
        }
        
        /**
        * Register Routes function
        *
        * This method registers a REST API route for your plugin.
        *
        * @return void
        */
        public function formit_register_routes() {
            // Register a REST route for reading data (GET request) under the specified namespace and base.
            register_rest_route(
                $this->namespace,
                '/' . $this->rest_base,
                [
                    [
                        'methods' => WP_REST_Server::READABLE,
                        // Callback function to handle GET requests for retrieving items.
                        'callback' => [$this, 'formit_get_items'],
                        // Callback function to check permissions for GET requests.
                        'permission_callback' => [$this, 'formit_formit_get_items_permissions_check'],
                        // Define the arguments (parameters) that can be passed to the GET request.
                        'args' => $this->formit_get_collection_params(),
                    ],
                    // Callback function to retrieve the schema for the items.
                    'schema' => [$this, 'formit_get_item_schema'],
                ]
            );
        }
        

        /**
        * Permission Control Function
        *
        * This function checks if the current user has the 'manage_options' capability,
        * which typically indicates an administrator role. If the user has this capability,
        * they are granted permission to access the data.
        *
        * @param [type] $request
        * @return boolean
        */
        public function formit_formit_get_items_permissions_check($request){
            if(current_user_can('manage_options')){
                return true; // Allow access if the user has the 'manage_options' capability.
            }
            return true; // Deny access for users without the required capability.
        }

        /**
        * Get Items Function
        *
        * This function handles the GET request to retrieve items (forms) based on the provided parameters.
        *
        * @param [type] $request
        */
        public function formit_get_items($request){
            $args   = [];
            $params = $this->formit_get_collection_params();

            // Iterate through the request parameters and build an array of valid arguments.
            foreach ($params as $key => $value) {
                if (isset($request[$key])) {
                    $args[$key] = $request[$key];
                }
            }

            // Rename 'per_page' to 'number' and calculate the 'offset' for pagination.
            $args['number'] = $args['per_page'];
            $args['offset'] = $args['number'] * ( $args['page'] - 1 );

            // Remove 'per_page' and 'page' from the arguments.
            unset( $args['per_page'] );
            unset( $args['page'] );

            $data   = [];
            $forms  = fromit_get_forms( $args ); // Get forms based on the arguments.

            // Prepare the retrieved forms for the response.
            foreach ($forms as $form) {
                $response = $this->formit_prepare_item_for_response( $form, $request );
                $data[] = $this->prepare_response_for_collection($response);
            }

            $total = formit_forms_count(); // Get the total number of forms.
            $max_pages = ceil($total / $args['number']); // Calculate the maximum number of pages.

            // Create a REST API response with the data and set headers for total count and total pages.
            $response = rest_ensure_response($data);
            $response->header('X-WP-Total', (int) $total);
            $response->header('X-WP-TotalPages', (int) $max_pages);

            return $response;
        }


        /**
        * Prepares the item for the REST response.
        *
        * This function formats the data item to be returned as a REST response.
        *
        * @since 4.7.0
        *
        * @param mixed           $item    WordPress representation of the item.
        * @param WP_REST_Request $request Request object.
        * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
        */
        public function formit_prepare_item_for_response($item, $request) {
            $data = [];
            $fields = $this->get_fields_for_response($request);

            // Check if specific fields are requested and include them in the response.
            if (in_array('id', $fields, true)) {
                $data['id'] = (int) $item->id;
            }
            if (in_array('post_id', $fields, true)) {
                $data['post_id'] =  $item->post_id;
            }
            if (in_array('form_title', $fields, true)) {
                $data['form_title'] =  $item->form_title;
            }
            if (in_array('form_html', $fields, true)) {
                $data['form_html'] =  $item->form_html;
            }
            if (in_array('date', $fields, true)) {
                // Format the date in RFC3339 format.
                $data['date'] =  mysql_to_rfc3339($item->created_at);
            }

            // Determine the context (view, edit) and filter the data accordingly.
            $context = !empty($request['context']) ? $request['context'] : 'view';
            $data = $this->filter_response_by_context($data, $context);

            // Create a REST response object and add links.
            $response = rest_ensure_response($data);
            $response->add_links($this->formit_prepare_links($item));

            return $response;
        }

        /**
        * Prepare Links Function
        *
        * This function generates and returns the links for the REST response.
        *
        * @param [type] $item
        */
        public function formit_prepare_links($item) {
            $base = wp_sprintf('%s/%s', $this->namespace, $this->rest_base);

            // Define 'self' and 'collection' links for the item.
            $links = [
                'self' => [
                    'href' => rest_url(trailingslashit($base) . $item->id),
                ],
                'collection' => [
                    'href' => rest_url($base),
                ]
            ];
            return  $links;
        }

        /**
        * Get Item Schema Function
        *
        * This function defines the schema for the items to be used in the REST API.
        */
        public function formit_get_item_schema() {
            if ($this->schema) {
                return $this->add_additional_fields_schema($this->schema);
            }

            $schema = [
                '$schema' => 'http://json-schema.org/draft-04/schema#',
                'title' => 'forms',
                'type' => 'object',
                'properties' => [
                    'id' => [
                        'description' => __('Unique Identifier for the Objects.', 'formit'),
                        'type' => 'integer',
                        'context' => ['view', 'edit'],
                        'readonly' => true,
                    ],
                    'post_id' => [
                        'description' => __('Form Identifier for the Objects.', 'formit'),
                        'type' => 'integer',
                        'context' => ['view', 'edit'],
                        'readonly' => true,
                    ],
                    'form_title' => [
                        'description' => __('Form Title', 'formit'),
                        'type' => 'string',
                        'context' => ['view', 'edit'],
                        'required' => true,
                        'arg_options' => [
                            'sanitize_callback' => 'sanitize_text_fields',
                        ],
                    ],
                    'form_html' => [
                        'description' => __('Form HTML dom', 'formit'),
                        'type' => 'string',
                        'context' => ['view', 'edit'],
                        'readonly' => true,
                        'required' => true,
                        'arg_options' => [
                            'sanitize_callback' => 'sanitize_text_fields',
                        ],
                    ],
                    'date' => [
                        'description' => __('Publish date', 'formit'),
                        'type' => 'string',
                        'format' => 'date-time',
                        'context' => ['view'],
                        'readonly' => true,
                    ],
                ]
            ];

            $this->schema = $schema;
            return $this->add_additional_fields_schema($this->schema);
        }

        /**
        * Get Collection Params Function
        *
        * This function retrieves collection parameters and removes the 'search' parameter.
        */
        public function formit_get_collection_params() {
            $params = parent::get_collection_params();
            unset($params['search']);
            return $params;
        }

    }
}