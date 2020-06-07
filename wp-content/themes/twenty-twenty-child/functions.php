<?php
/* enqueue scripts and style from parent theme */
function twentytwenty_styles()
{
    wp_enqueue_style('parent', get_template_directory_uri() . '/style.css');
}

add_action('wp_enqueue_scripts', 'twentytwenty_styles');

add_action('graphql_register_types', function () {

    register_graphql_mutation('createNewsletterEntry', [
        'inputFields' => [
            'name' => [
                'type' => 'String',
                'description' => 'Name',
            ],
            'email' => [
                'type' => 'String',
                'description' => 'Email',
            ],
        ],
        'outputFields' => [
            'success' => [
                'type' => 'Boolean',
                'description' => 'Whether or not data was stored successfully',
                'resolve' => function ($payload, $args, $context, $info) {
                    return isset($payload['success']) ? $payload['success'] : null;
                }
            ],
            'data' => [
                'type' => 'String',
                'description' => 'Payload of submitted fields',
                'resolve' => function ($payload, $args, $context, $info) {
                    return isset($payload['data']) ? $payload['data'] : null;
                }
            ]
        ],
        'mutateAndGetPayload' => function ($input, $context, $info) {

            if (!class_exists('ACF')) return [
                'success' => false,
                'data' => 'ACF is not installed'
            ];

            $sanitized_data = [];
            $errors = [];
            $acceptable_fields = [
                'name' => 'field_5edc920824336',
                'email' => 'field_5edc921424337',
            ];

            foreach ($acceptable_fields as $field_key => $acf_key) {
                if (!empty($input[$field_key])) {
                    $sanitized_data[$field_key] = sanitize_text_field($input[$field_key]);
                } else {
                    $errors[] = $field_key . ' was not filled out.';
                }
            }

            if (!empty($errors)) return [
                'success' => false,
                'data' => $errors
            ];

            $newsletter = wp_insert_post([
                'post_type' => 'newsletter',
                'post_title' => $sanitized_data['name'],
                'post_status' => 'publish',
            ], true);

            if (is_wp_error($newsletter)) return [
                'success' => false,
                'data' => $newsletter->get_error_message()
            ];

            foreach ($acceptable_fields as $field_key => $acf_key) {
                update_field($acf_key, $sanitized_data[$field_key], $newsletter);
            }

            return [
                'success' => true,
                'data' => json_encode($sanitized_data)
            ];

        }
    ]);


    register_graphql_mutation('createContactEntry', [
        'inputFields' => [
            'first_name' => [
                'type' => 'String',
                'description' => 'First Name',
            ],
            'last_name' => [
                'type' => 'String',
                'description' => 'Last Name',
            ],
            'email' => [
                'type' => 'String',
                'description' => 'Email',
            ],
            'message' => [
                'type' => 'String',
                'description' => 'Message',
            ],
        ],
        'outputFields' => [
            'success' => [
                'type' => 'Boolean',
                'description' => 'Whether or not data was stored successfully',
                'resolve' => function ($payload, $args, $context, $info) {
                    return isset($payload['success']) ? $payload['success'] : null;
                }
            ],
            'data' => [
                'type' => 'String',
                'description' => 'Payload of submitted fields',
                'resolve' => function ($payload, $args, $context, $info) {
                    return isset($payload['data']) ? $payload['data'] : null;
                }
            ]
        ],
        'mutateAndGetPayload' => function ($input, $context, $info) {

            if (!class_exists('ACF')) return [
                'success' => false,
                'data' => 'ACF is not installed'
            ];

            $sanitized_data = [];
            $errors = [];
            $acceptable_fields = [
                'first_name' => 'field_5edc924cad86e',
                'last_name' => 'field_5edc925ead86f',
                'email' => 'field_5edc9263ad870',
                'message' => 'field_5edc9269ad871',
            ];

            foreach ($acceptable_fields as $field_key => $acf_key) {
                if (!empty($input[$field_key])) {
                    $sanitized_data[$field_key] = sanitize_text_field($input[$field_key]);
                } else {
                    $errors[] = $field_key . ' was not filled out.';
                }
            }

            if (!empty($errors)) return [
                'success' => false,
                'data' => $errors
            ];

            $contact = wp_insert_post([
                'post_type' => 'contact',
                'post_title' => $sanitized_data['first_name'] . ' ' . $sanitized_data['last_name'],
                'post_status' => 'publish',
            ], true);

            if (is_wp_error($contact)) return [
                'success' => false,
                'data' => $contact->get_error_message()
            ];

            foreach ($acceptable_fields as $field_key => $acf_key) {
                update_field($acf_key, $sanitized_data[$field_key], $contact);
            }

            return [
                'success' => true,
                'data' => json_encode($sanitized_data)
            ];

        }
    ]);
});