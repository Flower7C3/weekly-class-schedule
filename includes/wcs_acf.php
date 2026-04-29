<?php

add_action('acf/include_fields', function () {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group(array(
        'key' => 'group_60fc1b2601d2b',
        'title' => 'Kontakt',
        'fields' => array(
            array(
                'key' => 'field_60fc1b2fd8af2',
                'label' => 'Email',
                'name' => 'email',
                'aria-label' => '',
                'type' => 'email',
                'instructions' => 'Wypełnij to pole, aby móc korzystać z narzędzia <b>Udostępnij</b>',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => 'np. jan@example.com',
                'prepend' => '',
                'append' => '',
            ),
            array(
                'key' => 'field_60fc2346c6843',
                'label' => 'Telefon',
                'name' => 'phone',
                'aria-label' => '',
                'type' => 'text',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => 'np. 123 456 789',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'wcs4_teacher',
                ),
            ),
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'wcs4_student',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'seamless',
        'label_placement' => 'left',
        'instruction_placement' => 'field',
        'hide_on_screen' => array(
            0 => 'discussion',
            1 => 'comments',
            2 => 'revisions',
            3 => 'slug',
            4 => 'author',
            5 => 'send-trackbacks',
        ),
        'active' => true,
        'description' => '',
        'show_in_rest' => 0,
        'display_title' => '',
        'allow_ai_access' => false,
        'ai_description' => '',
    ));
});

