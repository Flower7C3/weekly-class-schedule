<?php
/**
 * @var array $wcs4_options
 */

?>
<details>
    <summary><strong><?= _x('Appearance Settings', 'options appearance settings', 'wcs4') ?></strong></summary>
    <table class="form-table">
        <tbody>
        <tr>
            <th>
                <?= _x('Open template links in new tabs', 'options general settings', 'wcs4') ?>
                <div class="wcs4-description">
                    <?= _x(
                        'Enabling this will open the template links (e.g. [subject link]) in a new tab.',
                        'options general settings',
                        'wcs4'
                    ) ?>
                </div>
            </th>
            <td><?= wcs4_bool_checkbox(
                    'wcs4_open_template_links_in_new_tab',
                    'wcs4_open_template_links_in_new_tab',
                    $wcs4_options['open_template_links_in_new_tab'],
                    __('Yes')
                ) ?></td>
        </tr>
        <tr>
            <th>
                <?= _x('Lesson base', 'options appearance settings', 'wcs4') ?>
                <div class="wcs4-description">
                    <?= _x(
                        'The default background color for lessons in the schedule.',
                        'options appearance settings',
                        'wcs4'
                    ) ?>
                </div>
            </th>
            <td><?= wcs4_colorpicker('wcs4_color_base', $wcs4_options['color_base']) ?></td>
            <th>
                <?= _x('Lesson details box', 'options appearance settings', 'wcs4') ?>
                <div class="wcs4-description">
                    <?= _x(
                        'Background color of the lesson details box which appears when hovering over a lesson.',
                        'options appearance settings',
                        'wcs4'
                    ) ?>
                </div>
            </th>
            <td>
                <?= wcs4_colorpicker(
                    'wcs4_color_details_box',
                    $wcs4_options['color_details_box']
                ) ?>
            </td>
            <th>
                <?= _x('Text', 'options appearance settings', 'wcs4') ?>
                <div class="wcs4-description">
                    <?= _x(
                        'Text color of schedule entries/lessons.',
                        'options appearance settings',
                        'wcs4'
                    ) ?>
                </div>
            </th>
            <td>
                <?= wcs4_colorpicker('wcs4_color_text', $wcs4_options['color_text']) ?>
            </td>
        </tr>
        <tr>
            <th>
                <?= _x('Border', 'options appearance settings', 'wcs4') ?>
                <div class="wcs4-description">
                    <?= _x(
                        'This color is used for all borders in the schedule output.',
                        'options appearance settings',
                        'wcs4'
                    ) ?>
                </div>
            </th>
            <td>
                <?= wcs4_colorpicker('wcs4_color_border', $wcs4_options['color_border']) ?>
            </td>
            <th>
                <?= _x('Schedule headings color', 'options appearance settings', 'wcs4') ?>
                <div class="wcs4-description">
                    <?= _x(
                        'Text color of the schedule headings (weekdays, hours).',
                        'options appearance settings',
                        'wcs4'
                    ) ?>
                </div>
            </th>
            <td>
                <?= wcs4_colorpicker(
                    'wcs4_color_headings_text',
                    $wcs4_options['color_headings_text']
                ) ?>
            </td>
            <th>
                <?= _x('Schedule headings background', 'options appearance settings', 'wcs4') ?>
                <div class="wcs4-description">
                    <?= _x(
                        'Background color of the schedule headings (weekdays, hours).',
                        'options appearance settings',
                        'wcs4'
                    ) ?>
                </div>
            </th>
            <td><?= wcs4_colorpicker(
                    'wcs4_color_headings_background',
                    $wcs4_options['color_headings_background']
                ) ?></td>
        </tr>
        <tr>
            <th>
                <?= _x('Background', 'options appearance settings', 'wcs4') ?>
                <div class="wcs4-description">
                    <?= _x(
                        'Background color for the entire schedule.',
                        'options appearance settings',
                        'wcs4'
                    ) ?>
                </div>
            </th>
            <td><?= wcs4_colorpicker(
                    'wcs4_color_background',
                    $wcs4_options['color_background']
                ) ?></td>
            <th>
                <?= _x('qTip background', 'options appearance settings', 'wcs4') ?>
                <div class="wcs4-description">
                    <?= _x(
                        'Background color of the qTip pop-up box.',
                        'options appearance settings',
                        'wcs4'
                    ) ?>
                </div>
            </th>
            <td><?= wcs4_colorpicker(
                    'wcs4_color_qtip_background',
                    $wcs4_options['color_qtip_background']
                ) ?></td>
            <th>
                <?= _x('Links', 'options appearance settings', 'wcs4') ?>
                <div class="wcs4-description">
                    <?= _x(
                        'The color of the links which appear in the lesson details box.',
                        'options appearance settings',
                        'wcs4'
                    ) ?>
                </div>
            </th>
            <td><?= wcs4_colorpicker('wcs4_color_links', $wcs4_options['color_links']) ?></td>
        </tr>
        </tbody>
    </table>
</details>