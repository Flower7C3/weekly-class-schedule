<?php
/**
 * WCS4 Widgets
 */

/**
 * Adds Foo_Widget widget.
 */
class WCS4_TodayClassesWidget extends WP_Widget
{

    /**
     * Register widget with WordPress.
     */
    public function __construct()
    {
        parent::__construct(
            'wcs4_today_lessons_widget', # Base ID
            _x('WCS Today\'s Classes', 'widget settings', 'wcs4')
        );

        # IMPORTANT
        wcs4_set_global_timezone();
    }

    /**
     * Front-end display of widget.
     *
     * @param array $args Widget arguments.
     * @param array $instance Saved values from database.
     * @see WP_Widget::widget()
     *
     */
    public function widget($args, $instance)
    {
        $output = '';

        $title = apply_filters('widget_title', $instance['title']);

        echo $args['before_widget'];
        if (!empty($title)) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        # Get today's weekday index
        $today = date('w');
        $time = date('H:i:s');
        $classroom_ids = $instance['classroom'] ?: 'all';
        $max_lessons = (int)$instance['max_lessons'];
        $limit = (is_int($max_lessons)) ? $max_lessons : null;
        $no_entries_msg = ($instance['no_entries_text'] !== '') ? $instance['no_entries_text'] : _x(
            'No lessons today',
            'widget settings',
            'wcs4'
        );
        $template = $instance['template'];

        $lessons = WCS_Schedule::get_items($classroom_ids, 'all', 'all', 'all', $today, $time, 1, $limit);

        if (empty($lessons)) {
            $output .= '<div class="wcs4-no-lessons">' . $no_entries_msg . '</div>';
            echo $output;
            return;
        }

        $output .= '<ul class="wcs4-today-lessons-widget-list">';
        foreach ($lessons as $lesson) {
            $output .= '<li>' . WCS_Output::process_template($lesson, $template) . '</li>';
        }

        $output .= '</ul>';
        echo $output;
        echo $args['after_widget'];
    }

    /**
     * Back-end widget form.
     *
     * @param array $instance Previously saved values from database.
     * @see WP_Widget::form()
     *
     */
    public function form($instance)
    {
        $title = (isset($instance['title'])) ? $instance['title'] : _x("Today's Classes", 'widget settings', 'wcs4');
        $max_lessons = (isset($instance['max_lessons'])) ? $instance['max_lessons'] : 5;
        $classroom = (isset($instance['classroom'])) ? $instance['classroom'] : 'all';
        $no_entries_text = (isset($instance['no_entries_text'])) ? $instance['no_entries_text'] : _x(
            'No lessons today',
            'widget settings',
            'wcs4'
        );
        $template = (isset($instance['template'])) ? $instance['template'] : _x(
            '{start time}: {subject link} @{classroom link}',
            'widget template',
            'wcs4'
        );

        /* Print Form */
        ?>
        <p>
            <label for="<?php
            echo $this->get_field_id('title'); ?>"><?php
                _ex('Title', 'widget settings', 'wcs4'); ?>:</label>
            <input class="widefat" id="<?php
            echo $this->get_field_id('title'); ?>" name="<?php
            echo $this->get_field_name('title'); ?>" type="text" value="<?php
            echo esc_attr($title); ?>"/>
        </p>

        <p>
            <label for="<?php
            echo $this->get_field_id('max_lessons'); ?>"><?php
                _ex('Maximum lessons to display', 'widget settings', 'wcs4'); ?>:</label>
            <input class="widefat" id="<?php
            echo $this->get_field_id('max_lessons'); ?>" name="<?php
            echo $this->get_field_name('max_lessons'); ?>" type="text" value="<?php
            echo esc_attr($max_lessons); ?>"/>
            <span class='wcs4-description'><?php
                _ex('Maximum number of lessons to display on list', 'widget settings', 'wcs4'); ?></span>
        </p>

        <p>
            <label for="<?php
            echo $this->get_field_id('classroom'); ?>"><?php
                _ex('Classrooms to display', 'widget settings', 'wcs4'); ?>:</label>
            <?php
            echo WCS_Admin::generate_admin_select_list(
                'classroom',
                $this->get_field_id('classroom'),
                $this->get_field_name('classroom') . '[]',
                $classroom,
                false,
                true,
                'widefat'
            ); ?>
        </p>

        <p>
            <label for="<?php
            echo $this->get_field_id('no_entries_text'); ?>"><?php
                _ex('No entries message', 'widget settings', 'wcs4'); ?>:</label>
            <input class="widefat" id="<?php
            echo $this->get_field_id('no_entries_text'); ?>" name="<?php
            echo $this->get_field_name('no_entries_text'); ?>" type="text" value="<?php
            echo esc_attr($no_entries_text); ?>"/>
        </p>

        <p>
            <label for="<?php
            echo $this->get_field_id('template'); ?>"><?php
                _ex('Template', 'widget settings', 'wcs4'); ?>:</label>
            <textarea class="widefat" id="<?php
            echo $this->get_field_id('template'); ?>" name="<?php
            echo $this->get_field_name('template'); ?>"><?php
                echo esc_attr($template); ?></textarea>
        </p>
        <?php
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     * @see WP_Widget::update()
     *
     */
    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['max_lessons'] = strip_tags($new_instance['max_lessons']);
        $instance['classroom'] = [];
        foreach ($new_instance['classroom'] as $classroom) {
            $instance['classroom'][] = (int)$classroom;
        }
        $instance['no_entries_text'] = strip_tags($new_instance['no_entries_text']);
        $instance['template'] = wcs4_validate_html($new_instance['template']);
        return $instance;
    }

} # class WCS4_TodayClassesWidget

# Register WCS4 widgets
add_action('widgets_init', static function () {
    # Register today's subjects widget
    register_widget('WCS4_TodayClassesWidget');
});
