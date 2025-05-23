<?php
/**
 * Standard [wcs_schedule] shortcode
 *
 * Default:
 *     [wcs_schedule layout="table" classroom="all" subject="all" teacher="all" student="all" limit="" paged=""]
 * @param $atts
 * @return string
 */

use WCS4\Controller\Journal;
use WCS4\Controller\Progress;
use WCS4\Controller\Schedule;
use WCS4\Controller\Settings;
use WCS4\Controller\WorkPlan;
use WCS4\Helper\Output;
use WCS4\Repository\Journal as JournalRepository;
use WCS4\Repository\Progress as ProgressRepository;
use WCS4\Repository\Schedule as ScheduleRepository;
use WCS4\Repository\WorkPlan as WorkPlanRepository;

add_shortcode('wcs_schedule', static function ($atts) {
    $output = '';
    $buffer = '';
    $layout = '';
    $classroom = '';
    $teacher = '';
    $student = '';
    $subject = '';
    $style = '';
    $limit = null;
    $paged = null;
    $template_table_short = '';
    $template_table_details = '';
    $template_list = '';
    $wcs4_options = Settings::load_settings();

    extract(
        shortcode_atts(array(
            'layout' => 'table',
            'classroom' => 'all',
            'teacher' => 'all',
            'student' => 'all',
            'subject' => 'all',
            'style' => 'normal',
            'limit' => null,
            'paged' => null,
            'template_table_short' => $wcs4_options['schedule_template_table_short'],
            'template_table_details' => $wcs4_options['schedule_template_table_details'],
            'template_list' => $wcs4_options['schedule_template_list'],
        ), $atts),
        EXTR_OVERWRITE
    );
    # Get lessons
    $lessons = ScheduleRepository::get_items(
        $classroom,
        $teacher,
        $student,
        $subject,
        null,
        null,
        'visible',
        null,
        $limit,
        $paged
    );

    # Classroom
    $schedule_key = 'wcs4-key-' . preg_replace(
            '/[^A-Za-z0-9]/',
            '-',
            implode('-', [$classroom, $teacher, $student, $subject, $limit, $paged])
        );
    $schedule_key = strtolower($schedule_key);

    $output = apply_filters('wcs4_pre_render', $output, $style);

    $output .= '<div class="wcs4_schedule_wrapper" id="' . $schedule_key . '">';
    if ('table' === $layout) {
        # Render table layout
        $weekdays = wcs4_get_indexed_weekdays($abbr = true);
        $output .= Schedule::get_html_of_schedule_table_for_shortcode(
            $lessons,
            $weekdays,
            $schedule_key,
            $template_table_short,
            $template_table_details
        );
    } elseif ('list' === $layout) {
        # Render list layout
        $weekdays = wcs4_get_weekdays();
        $output .= Schedule::get_html_of_schedule_list_for_shortcode(
            $lessons,
            $weekdays,
            $schedule_key,
            $template_list
        );
    } else {
        $weekdays = wcs4_get_weekdays();
        $buffer = apply_filters(
            'wcs4_render_layout',
            $buffer,
            $lessons,
            $weekdays,
            $classroom,
            $teacher,
            $student,
            $subject,
            $wcs4_options
        );
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
        $wcs4_options = Settings::load_settings();
        $wcs4_js_data = [];
        $wcs4_js_data['options'] = $wcs4_options;
        Output::load_frontend_scripts($wcs4_js_data);
    });

    return $output;
});

/**
 * Standard [wcs_journal] shortcode
 *
 * Default:
 *     [wcs_journal subject="all" teacher="all" student="all" date_from="" date_upto="" template="" limit="" paged=""]
 * @param $atts
 * @return string
 */
add_shortcode('wcs_journal', static function ($atts) {
    $output = '';
    $buffer = '';
    $subject = '';
    $teacher = '';
    $student = '';
    $date_from = '';
    $date_upto = '';
    $style = '';
    $limit = null;
    $paged = null;
    $template = '';
    $wcs4_options = Settings::load_settings();

    extract(
        shortcode_atts(array(
            'subject' => 'all',
            'teacher' => 'all',
            'student' => 'all',
            'style' => 'normal',
            'date_from' => null,
            'date_upto' => null,
            'limit' => null,
            'paged' => null,
            'template' => $wcs4_options['journal_shortcode_template'],
        ), $atts),
        EXTR_OVERWRITE
    );

    # Get journals
    $journals = JournalRepository::get_items(
        '!' . $teacher,
        '!' . $student,
        '!' . $subject,
        $date_from,
        $date_upto,
        null,
        null,
        null,
        'time',
        'DESC',
        $limit,
        $paged
    );

    # Classroom
    $schedule_key = 'wcs4-key-' . preg_replace(
            '/[^A-Za-z0-9]/',
            '-',
            implode('-', [$teacher, $student, $subject, $date_from, $date_upto, $limit, $paged])
        );
    $schedule_key = strtolower($schedule_key);

    $output = apply_filters('wcs4_pre_render', $output, $style);

    # Render list layout
    $output .= '<div class="wcs4_schedule_wrapper" id="' . $schedule_key . '">';
    if (!empty($limit) && $limit > 0) {
        $output .= '<p><em>' . sprintf(__('Showing latest %d items.', 'wcs4'), $limit) . '</em></p>';
    }
    $output .= Journal::get_html_of_journal_list_for_shortcode(
        $journals,
        $schedule_key,
        $template
    );
    $output .= '</div>';

    $output = apply_filters('wcs4_post_render', $output, $style, $teacher, $student, $subject, $date_from, $date_upto);

    # Only load front end scripts and styles if it's our shortcode
    add_action('wp_footer', static function () {
        $wcs4_options = Settings::load_settings();
        $wcs4_js_data = [];
        $wcs4_js_data['options'] = $wcs4_options;
        Output::load_frontend_scripts($wcs4_js_data);
    });

    return $output;
});

add_shortcode('wcs_journal_create', static function ($atts) {
    $subject = '';
    $teacher = '';
    $student = '';

    extract(
        shortcode_atts(array(
            'subject' => '',
            'teacher' => '',
            'student' => '',
        ), $atts),
        EXTR_OVERWRITE
    );

    $modal = Journal::get_html_of_shortcode_form($subject, $teacher, $student);

    # Only load front end scripts and styles if it's our shortcode
    add_action('wp_footer', static function () use ($modal) {
        $wcs4_options = Settings::load_settings();
        $wcs4_js_data = [];
        $wcs4_js_data['options'] = $wcs4_options;
        Output::load_frontend_scripts($wcs4_js_data);
        echo trim($modal);
    });
    return Journal::get_html_of_shortcode_button($subject, $teacher, $student);
});

add_shortcode('student_progress', static function ($atts) {
    $output = '';
    $buffer = '';
    $subject = '';
    $teacher = '';
    $student = '';
    $date_from = '';
    $date_upto = '';
    $style = '';
    $limit = null;
    $paged = null;
    $template_partial = '';
    $template_periodic = '';
    $wcs4_options = Settings::load_settings();

    extract(
        shortcode_atts(array(
            'subject' => 'all',
            'teacher' => 'all',
            'student' => 'all',
            'style' => 'normal',
            'date_from' => null,
            'date_upto' => null,
            'limit' => null,
            'paged' => null,
            'template_partial' => null,
            'template_periodic' => null,
        ), $atts),
        EXTR_OVERWRITE
    );

    # Get progresses
    $progresses = ProgressRepository::get_items(
        null,
        '!' . $teacher,
        '!' . $student,
        '!' . $subject,
        $date_from,
        $date_upto,
        null,
        null,
        null,
        'time',
        'DESC',
        $limit,
        $paged
    );

    # Classroom
    $schedule_key = 'wcs4-key-' . preg_replace(
            '/[^A-Za-z0-9]/',
            '-',
            implode('-', [$teacher, $student, $subject, $date_from, $date_upto, $limit, $paged])
        );
    $schedule_key = strtolower($schedule_key);

    $output = apply_filters('wcs4_pre_render', $output, $style);

    # Render list layout
    $output .= '<div class="wcs4_schedule_wrapper" id="' . $schedule_key . '">';
    if (!empty($limit) && $limit > 0) {
        $output .= '<p><em>' . sprintf(__('Showing latest %d items.', 'wcs4'), $limit) . '</em></p>';
    }
    $output .= Progress::get_html_of_progress_list_for_shortcode(
        $progresses,
        $schedule_key,
        $template_partial,
        $template_periodic
    );
    $output .= '</div>';

    $output = apply_filters('wcs4_post_render', $output, $style, $teacher, $student, $subject, $date_from, $date_upto);

    # Only load front end scripts and styles if it's our shortcode
    add_action('wp_footer', static function () {
        $wcs4_options = Settings::load_settings();
        $wcs4_js_data = [];
        $wcs4_js_data['options'] = $wcs4_options;
        Output::load_frontend_scripts($wcs4_js_data);
    });

    return $output;
});

add_shortcode('student_progress_create', static function ($atts) {
    $subject = '';
    $teacher = '';
    $student = '';

    extract(
        shortcode_atts(array(
            'subject' => '',
            'teacher' => '',
            'student' => '',
        ), $atts),
        EXTR_OVERWRITE
    );

    $modal = Progress::get_html_of_shortcode_form($subject, $teacher, $student);

    # Only load front end scripts and styles if it's our shortcode
    add_action('wp_footer', static function () use ($modal) {
        $wcs4_options = Settings::load_settings();
        $wcs4_js_data = [];
        $wcs4_js_data['options'] = $wcs4_options;
        Output::load_frontend_scripts($wcs4_js_data);
        echo trim($modal);
    });

    return Progress::get_html_of_shortcode_button($subject, $teacher, $student);
});

add_shortcode('wcs_student_work_plan', static function ($atts) {
    $output = '';
    $buffer = '';
    $subject = '';
    $teacher = '';
    $student = '';
    $date_from = '';
    $date_upto = '';
    $style = '';
    $limit = null;
    $paged = null;
    $template_partial = '';
    $template_periodic = '';
    $wcs4_options = Settings::load_settings();

    extract(
        shortcode_atts(array(
            'subject' => 'all',
            'teacher' => 'all',
            'student' => 'all',
            'style' => 'normal',
            'date_from' => null,
            'date_upto' => null,
            'limit' => null,
            'paged' => null,
            'template_partial' => $wcs4_options['work_plan_shortcode_template_partial_type'],
            'template_periodic' => $wcs4_options['work_plan_shortcode_template_periodic_type'],
        ), $atts),
        EXTR_OVERWRITE
    );

    # Get work_plans
    $work_plans = WorkPlanRepository::get_items(
        null,
        '!' . $teacher,
        '!' . $student,
        '!' . $subject,
        $date_from,
        $date_upto,
        null,
        null,
        null,
        'time',
        'DESC',
        $limit,
        $paged
    );

    # Classroom
    $schedule_key = 'wcs4-key-' . preg_replace(
            '/[^A-Za-z0-9]/',
            '-',
            implode('-', [$teacher, $student, $subject, $date_from, $date_upto, $limit, $paged])
        );
    $schedule_key = strtolower($schedule_key);

    $output = apply_filters('wcs4_pre_render', $output, $style);

    # Render list layout
    $output .= '<div class="wcs4_schedule_wrapper" id="' . $schedule_key . '">';
    if (!empty($limit) && $limit > 0) {
        $output .= '<p><em>' . sprintf(__('Showing latest %d items.', 'wcs4'), $limit) . '</em></p>';
    }
    $output .= WorkPlan::get_html_of_work_plan_list_for_shortcode(
        $work_plans,
        $schedule_key,
        $template_partial,
        $template_periodic
    );
    $output .= '</div>';

    $output = apply_filters('wcs4_post_render', $output, $style, $teacher, $student, $subject, $date_from, $date_upto);

    # Only load front end scripts and styles if it's our shortcode
    add_action('wp_footer', static function () {
        $wcs4_options = Settings::load_settings();
        $wcs4_js_data = [];
        $wcs4_js_data['options'] = $wcs4_options;
        Output::load_frontend_scripts($wcs4_js_data);
    });

    return $output;
});

add_shortcode('wcs_student_work_plan_create', static function ($atts) {
    $subject = '';
    $teacher = '';
    $student = '';

    extract(
        shortcode_atts(array(
            'subject' => '',
            'teacher' => '',
            'student' => '',
        ), $atts),
        EXTR_OVERWRITE
    );

    $modal = WorkPlan::get_html_of_shortcode_form($subject, $teacher, $student);

    # Only load front end scripts and styles if it's our shortcode
    add_action('wp_footer', static function () use ($modal) {
        $wcs4_options = Settings::load_settings();
        $wcs4_js_data = [];
        $wcs4_js_data['options'] = $wcs4_options;
        Output::load_frontend_scripts($wcs4_js_data);
        echo $modal;
    });
    return WorkPlan::get_html_of_shortcode_button($subject, $teacher, $student);
});


add_shortcode('wp_query', static function (array $options = []) {
    $shortcode_atts = shortcode_atts([
        'post_type' => 'page',
        'order_direction' => 'ASC',
        'order_field' => 'title',
        'category' => '',
        'taxonomy' => '',
        'taxonomy_field' => '',
        'taxonomy_terms' => '',
        'post_status' => 'publish',
        'has_password' => null,
        'posts_per_page' => '10',
        'paged' => '1',
        'display_link' => 'yes',
        'display_excerpt' => 'no',
        'display_taxonomy' => '',
        'display_taxonomy_pattern' => ' – %s',
        'display_empty_message' => 'yes',
        'layout' => 'ul',
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
    if ($shortcode_atts['order_direction']) {
        $args['order_direction'] = $shortcode_atts['order_direction'];
    }
    if ($shortcode_atts['order_field']) {
        $args['order_field'] = $shortcode_atts['order_field'];
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
        switch ($shortcode_atts['layout']) {
            default:
            case 'ul';
                $response[] = '<ul>';
                break;
            case 'ol';
                $response[] = '<ol>';
                break;
            case 'details';
                break;
        }
        while ($the_query->have_posts()) {
            $the_query->the_post();
            $post = get_post();
            $row_value = [];
            $row_value['title'] = $post->post_title;
            if (in_array($shortcode_atts['display_link'], [true, 'true', 1, '1', 'yes'], true)) {
                if (('publish' === get_post_status($post) && !post_password_required($post)) || is_user_logged_in()) {
                    $row_value['title'] = sprintf('<a href="%s">%s</a>', get_permalink($post), $post->post_title);
                }
            }
            if (!empty($display_taxonomies)) {
                foreach ($display_taxonomies as $display_taxonomy) {
                    $terms = wp_get_post_terms(get_the_ID(), $display_taxonomy);
                    if (!empty($terms)) {
                        $termNames = [];
                        foreach ($terms as $term) {
                            if (isset($shortcode_atts['taxonomy_field'], $shortcode_atts['taxonomy_terms']) && in_array(
                                    $term->{$shortcode_atts['taxonomy_field']},
                                    $shortcode_atts['taxonomy_terms'],
                                    true
                                )) {
                                continue;
                            }
                            $termNames[] = $term->name;
                        }
                        if (!empty($termNames)) {
                            sort($termNames);
                            $row_value['taxonomy'] = sprintf(
                                $shortcode_atts['display_taxonomy_pattern'],
                                implode(', ', $termNames)
                            );
                        }
                    }
                }
            }
            $excerpt = null;
            if (in_array($shortcode_atts['display_excerpt'], [true, 'true', 1, '1', 'yes'], true)) {
                $content_arr = get_extended($post->post_content);
                $excerpt = apply_filters('the_content', apply_filters('the_content', $content_arr['main']));
            }
            switch ($shortcode_atts['layout']) {
                default:
                case 'ul';
                case 'ol';
                    if (isset($excerpt)) {
                        $row_value[''] = '<br>';
                        $row_value['excerpt'] = $excerpt;
                    }
                    $response[] = sprintf('<li>%s</li>', implode('', $row_value));
                    break;
                case 'details';
                    $response[] = '<details>';
                    $response[] = sprintf('<summary>%s</summary>', implode('', $row_value));
                    if (isset($excerpt)) {
                        $response[] = sprintf('<div>%s</div>', $excerpt);
                    }
                    $response[] = '</details>';
                    break;
            }
        }
        wp_reset_postdata();
        switch ($shortcode_atts['layout']) {
            default:
            case 'ul';
                $response[] = '</ul>';
                break;
            case 'ol';
                $response[] = '</ol>';
                break;
            case 'details';
                break;
        }
    } else {
        if (in_array($shortcode_atts['display_empty_message'], [true, 'true', 1, '1', 'yes'], true)) {
            $response[] = '<p>' . $shortcode_atts['empty_message'] . '</p>';
        }
    }
    return implode('', $response);
});
