<?php


/**
 * Standard [wcs] shortcode
 *
 * Default:
 *     [wcs layout="table" classroom="all" class="all" teacher="all" student="all"]
 * @param $atts
 * @return string
 */
add_shortcode('wcs', static function ($atts) {
    $output = '';
    $buffer = '';
    $layout = '';
    $classroom = '';
    $teacher = '';
    $student = '';
    $subject = '';
    $style = '';
    $template_table_short = '';
    $template_table_details = '';
    $template_list = '';
    $wcs4_options = wcs4_load_settings();

    extract(shortcode_atts(array(
        'layout' => 'table',
        'classroom' => 'all',
        'teacher' => 'all',
        'student' => 'all',
        'subject' => 'all',
        'style' => 'normal',
        'template_table_short' => $wcs4_options['template_table_short'],
        'template_table_details' => $wcs4_options['template_table_details'],
        'template_list' => $wcs4_options['template_list'],
    ), $atts), EXTR_OVERWRITE);

    # Get lesssons
    $lessons = wcs4_get_lessons($classroom, $teacher, $student, $subject);

    # Classroom
    $schedule_key = 'wcs4-key-' . preg_replace('/[^A-Za-z0-9]/', '-', implode('-', [$classroom, $teacher, $student, $subject]));
    $schedule_key = strtolower($schedule_key);

    $output = apply_filters('wcs4_pre_render', $output, $style);
    $output .= '<div class="wcs4-schedule-wrapper" id="' . $schedule_key . '">';

    if ($layout === 'table') {
        # Render table layout
        $weekdays = wcs4_get_indexed_weekdays($abbr = TRUE);
        $output .= wcs4_render_table_schedule($lessons, $weekdays, $schedule_key, $template_table_short, $template_table_details);
    } else if ($layout === 'list') {
        # Render list layout
        $weekdays = wcs4_get_weekdays();
        $output .= wcs4_render_list_schedule($lessons, $weekdays, $schedule_key, $template_list);
    } else {
        $weekdays = wcs4_get_weekdays();
        $buffer = apply_filters('wcs4_render_layout', $buffer, $lessons, $weekdays, $classroom, $teacher, $student, $subject, $wcs4_options);
        if (empty($buffer)) {
            $output .= __('Unsupported layout', 'wcs4');
        } else {
            $output .= $buffer;
        }
    }

    $output .= '</div>';
    $output = apply_filters('wcs4_post_render', $output, $style, $weekdays, $classroom, $teacher, $student, $subject);

    # Only load front end scripts and styles if it's our shortcode
    add_action('wp_footer', static function () {
        $wcs4_options = wcs4_load_settings();
        $wcs4_js_data = [];
        $wcs4_js_data['options'] = $wcs4_options;
        wcs4_load_frontend_scripts($wcs4_js_data);
    });

    return $output;
});

add_shortcode('wcs_new_report', static function () {
    ob_start();
    wcs4_report_manage_form();
    $result = ob_get_clean();

    # Only load front end scripts and styles if it's our shortcode
    add_action('wp_footer', static function () {
        $wcs4_options = wcs4_load_settings();
        $wcs4_js_data = [];
        $wcs4_js_data['options'] = $wcs4_options;
        wcs4_load_frontend_scripts($wcs4_js_data);
    });
    return trim($result);
});


add_shortcode('wp_query', static function (array $options = []) {
    $shortcode_atts = shortcode_atts([
        'post_type' => 'page',
        'order' => 'ASC',
        'orderby' => 'title',
        'category' => '',
        'taxonomy' => '',
        'taxonomy_field' => '',
        'taxonomy_terms' => '',
        'post_status' => 'publish',
        'has_password' => null,
        'posts_per_page' => '10',
        'paged' => '1',
        'display_link' => 'yes',
        'display_taxonomy' => '',
        'display_taxonomy_pattern' => ' – %s',
        'display_empty_message' => 'yes',
        'empty_message' => __('No posts found.'),
    ], $options);

    $shortcode_atts['taxonomy_terms'] = explode(',', $shortcode_atts['taxonomy_terms']);
    $shortcode_atts['post_type'] = explode(',', $shortcode_atts['post_type']);

    $args = [];
    if ($shortcode_atts['post_type']) {
        $args['post_type'] = $shortcode_atts['post_type'];
    }
    if ($shortcode_atts['post_status']) {
        $args['post_status'] = $shortcode_atts['post_status'];
    }
    if ($shortcode_atts['has_password']) {
        $args['has_password'] = $shortcode_atts['has_password'];
    }
    if ($shortcode_atts['order']) {
        $args['order'] = $shortcode_atts['order'];
    }
    if ($shortcode_atts['orderby']) {
        $args['orderby'] = $shortcode_atts['orderby'];
    }
    if ($shortcode_atts['posts_per_page']) {
        $args['posts_per_page'] = $shortcode_atts['posts_per_page'];
    }
    if ($shortcode_atts['paged']) {
        $args['paged'] = $shortcode_atts['paged'];
    }
    if ($shortcode_atts['category']) {
        $args['category'] = $shortcode_atts['category'];
    }
    if ($shortcode_atts['taxonomy']) {
        $args['tax_query'] = [
            [
                'taxonomy' => $shortcode_atts['taxonomy'],
                'field' => $shortcode_atts['taxonomy_field'],
                'terms' => $shortcode_atts['taxonomy_terms'],
            ],
        ];
    }
    $display_taxonomies = explode(',', $shortcode_atts['display_taxonomy']);

    $the_query = new WP_Query($args);
    $response = [];
    if ($the_query->have_posts()) {
        $response[] = '<ul>';
        while ($the_query->have_posts()) {
            $the_query->the_post();
            $link = null;
            if (in_array($shortcode_atts['display_link'], [true, 'true', 1, '1', 'yes'], true)) {
                if (('publish' === get_post_status() && !post_password_required()) || is_user_logged_in()) {
                    $link = get_permalink();
                }
            }
            global $post;
            $postName = $post->post_title;
            if (!empty($display_taxonomies)) {
                foreach ($display_taxonomies as $display_taxonomy) {
                    $terms = wp_get_post_terms(get_the_ID(), $display_taxonomy);
                    if (!empty($terms)) {
                        $termNames = [];
                        foreach ($terms as $term) {
                            if (isset($shortcode_atts['taxonomy_field'], $shortcode_atts['taxonomy_terms']) && in_array($term->{$shortcode_atts['taxonomy_field']}, $shortcode_atts['taxonomy_terms'], true)) {
                                continue;
                            }
                            $termNames[] = $term->name;
                        }
                        if (!empty($termNames)) {
                            sort($termNames);
                            $postName .= sprintf($shortcode_atts['display_taxonomy_pattern'], implode(', ', $termNames));
                        }
                    }
                }
            }
            if (null === $link) {
                $response[] = sprintf('<li>%s</li>', $postName);
            } else {
                $response[] = sprintf('<li><a href="%s">%s</a></li>', $link, $postName);
            }
        }
        wp_reset_postdata();
        $response[] = '</ul>';
    } else if (in_array($shortcode_atts['display_empty_message'], [true, 'true', 1, '1', 'yes'], true)) {
        $response[] = '<p>' . $shortcode_atts['empty_message'] . '</p>';
    }
    return implode('', $response);
});
