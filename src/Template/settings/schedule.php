<?php
/**
 * @var array $taxonomyTypes
 * @var array $wcs4_options
 */

use WCS4\Helper\Admin;

?>
<details>
    <summary><strong><?= __('Schedule Settings', 'wcs4') ?></strong></summary>
    <table class="form-table">
        <thead>
        <tr>
            <th style="width:20%"></th>
            <?php
            foreach ($taxonomyTypes as $name): ?>
                <th style="width:15%"><?= $name['post'] ?></th>
            <?php
            endforeach; ?>
            <th style="width:15%"><?= __('Common', 'wcs4') ?></th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <th>
                <?= _x('Detect lesson collisions', 'options general settings', 'wcs4'); ?>
                <div class="wcs4-description">
                    <?= _x(
                        'Enabling this feature will prevent scheduling of multiple subjects at the same classroom, with same teacher or student at the same time.',
                        'options general settings',
                        'wcs4'
                    ) ?>
                </div>
            </th>
            <?php
            foreach ($taxonomyTypes as $key => $name): ?>
                <td data-key="<?= $key ?>" data-type="wcs4_schedule_collision">
                    <?php
                    if ($key !== 'subject'): ?>
                        <?= wcs4_bool_checkbox(
                            'wcs4_schedule_' . $key . '_collision',
                            'wcs4_schedule_' . $key . '_collision',
                            $wcs4_options['schedule_' . $key . '_collision'],
                            __('Yes')
                        ); ?>
                    <?php
                    endif; ?>
                </td>
            <?php
            endforeach; ?>
        </tr>
        <tr>
            <th>
                <?= _x('Single page schedule layout', 'options general settings', 'wcs4'); ?>
                <div class="wcs4-description">
                    <?= _x(
                        'How schedule should be generated on single page.',
                        'options general settings',
                        'wcs4'
                    ) ?>
                </div>
            </th>
            <?php
            foreach ($taxonomyTypes as $key => $name): ?>
                <td data-key="<?= $key ?>" data-type="wcs4_schedule_layout">
                    <?= Admin::generate_layout_select_list(
                        'wcs4_' . $key . '_schedule_layout',
                        $wcs4_options[$key . '_schedule_layout']
                    ) ?>
                </td>
            <?php
            endforeach; ?>
        </tr>
        <tr>
            <th>
                <?= _x('Class Table Short Template', 'options general settings', 'wcs4'); ?>
            </th>
            <?php
            foreach ($taxonomyTypes as $key => $name): ?>
                <td data-key="<?= $key ?>"
                    data-type="wcs4_schedule_template_table_short"
                    style="opacity: <?= ('table' === $wcs4_options[$key . '_schedule_layout']) ?
                        '1'
                        : '0.6'
                    ?>"
                >
                    <?php
                    wp_editor(
                        $wcs4_options[$key . '_schedule_template_table_short'],
                        'wcs4_' . $key . '_schedule_template_table_short',
                        [
                            'wpautop' => true,
                            'media_buttons' => false,
                            'textarea_name' => 'wcs4_' . $key . '_schedule_template_table_short',
                            'textarea_rows' => 6,
                        ]
                    );
                    ?>
                </td>
            <?php
            endforeach; ?>
            <td>
                <?php
                wp_editor(
                    $wcs4_options['schedule_template_table_short'],
                    'wcs4_schedule_template_table_short',
                    [
                        'wpautop' => true,
                        'media_buttons' => false,
                        'textarea_name' => 'wcs4_schedule_template_table_short',
                        'textarea_rows' => 6,
                    ]
                );
                ?>
            </td>
        </tr>
        <tr>
            <th>
                <?= _x('Class Table Hover Template', 'options general settings', 'wcs4'); ?>
            </th>
            <?php
            foreach ($taxonomyTypes as $key => $name): ?>
                <td data-key="<?= $key ?>"
                    data-type="wcs4_schedule_template_table_details"
                    style="opacity: <?= ('table' === $wcs4_options[$key . '_schedule_layout']) ?
                        '1'
                        : '0.6'
                    ?>"
                >
                    <?php
                    wp_editor(
                        $wcs4_options[$key . '_schedule_template_table_details'],
                        'wcs4_' . $key . '_schedule_template_table_details',
                        [
                            'wpautop' => true,
                            'media_buttons' => false,
                            'textarea_name' => 'wcs4_' . $key . '_schedule_template_table_details',
                            'textarea_rows' => 6,
                        ]
                    );
                    ?>
                </td>
            <?php
            endforeach; ?>
            <td>
                <?php
                wp_editor(
                    $wcs4_options['schedule_template_table_details'],
                    'wcs4_schedule_template_table_details',
                    [
                        'wpautop' => true,
                        'media_buttons' => false,
                        'textarea_name' => 'wcs4_schedule_template_table_details',
                        'textarea_rows' => 6,
                    ]
                );
                ?>
            </td>
        </tr>
        <tr>
            <th>
                <?= _x('Class List Template', 'options general settings', 'wcs4'); ?>
            </th>
            <?php
            foreach ($taxonomyTypes as $key => $name): ?>
                <td data-key="<?= $key ?>"
                    data-type="wcs4_schedule_template_list"
                    style="opacity: <?= ('list' === $wcs4_options[$key . '_schedule_layout']) ?
                        '1' :
                        '0.6'
                    ?>"
                >
                    <?php
                    wp_editor(
                        $wcs4_options[$key . '_schedule_template_list'],
                        'wcs4_' . $key . '_schedule_template_list',
                        [
                            'wpautop' => true,
                            'media_buttons' => false,
                            'textarea_name' => 'wcs4_' . $key . '_schedule_template_list',
                            'textarea_rows' => 6,
                        ]
                    );
                    ?>
                </td>
            <?php
            endforeach; ?>
            <td>
                <?php
                wp_editor(
                    $wcs4_options['schedule_template_list'],
                    'wcs4_schedule_template_list',
                    [
                        'wpautop' => true,
                        'media_buttons' => false,
                        'textarea_name' => 'wcs4_schedule_template_list',
                        'textarea_rows' => 6,
                    ]
                );
                ?>
            </td>
        </tr>
        <tr>
            <th>
                <?= _x('Download iCalendar', 'options general settings', 'wcs4'); ?>
                <div class="wcs4-description">
                    <?= _x(
                        'Will display extra link to download schedule as iCalendar.',
                        'options general settings',
                        'wcs4'
                    ) ?>
                </div>
            </th>
            <?php
            foreach ($taxonomyTypes as $key => $name): ?>
                <td data-key="<?= $key ?>" data-type="wcs4_schedule_download_ical">
                    <?= wcs4_bool_checkbox(
                        'wcs4_' . $key . '_schedule_download_ical',
                        'wcs4_' . $key . '_schedule_download_ical',
                        $wcs4_options[$key . '_schedule_download_ical'],
                        __('Yes')
                    ); ?>
                </td>
            <?php
            endforeach; ?>
        </tr>
        </tbody>
    </table>
</details>

<script>
    jQuery(function () {
        jQuery(document).on('change.wcs4_schedule_layout', '[data-type="wcs4_schedule_layout"] select', function () {
            var key = jQuery(this).closest('[data-key]').attr('data-key');
            var val = jQuery(this).val();
            var $wcs4_schedule_template_table_short = jQuery('[data-type="wcs4_schedule_template_table_short"][data-key="' + key + '"]');
            var $wcs4_schedule_template_table_details = jQuery('[data-type="wcs4_schedule_template_table_details"][data-key="' + key + '"]');
            var $wcs4_schedule_template_list = jQuery('[data-type="wcs4_schedule_template_list"][data-key="' + key + '"]')
            $wcs4_schedule_template_table_short.css('opacity', '0.6');
            $wcs4_schedule_template_table_details.css('opacity', '0.6');
            $wcs4_schedule_template_list.css('opacity', '0.6');
            if ('list' === val) {
                $wcs4_schedule_template_list.css('opacity', '1');
            }
            if ('table' === val) {
                $wcs4_schedule_template_table_short.css('opacity', '1');
                $wcs4_schedule_template_table_details.css('opacity', '1');
            }
        });
    });
</script>
