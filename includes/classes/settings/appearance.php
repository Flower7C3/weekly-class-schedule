<?php
/**
 * @var array $wcs4_options
 */

?>
<details>
    <summary><strong><?php
            _ex('Appearance Settings', 'options appearance settings', 'wcs4') ?></strong></summary>
    <table class="form-table">
        <tbody>
        <tr>
            <th>
                <?php
                _ex('Open template links in new tabs', 'options general settings', 'wcs4') ?>
                <div class="wcs4-description">
                    <?php
                    _ex(
                        'Enabling this will open the template links (e.g. [subject link]) in a new tab.',
                        'options general settings',
                        'wcs4'
                    ) ?>
                </div>
            </th>
            <td><?php
                echo wcs4_bool_checkbox(
                    'wcs4_open_template_links_in_new_tab',
                    'wcs4_open_template_links_in_new_tab',
                    $wcs4_options['open_template_links_in_new_tab'],
                    __('Yes')
                ); ?></td>
        </tr>
        <tr>
            <th>
                <?php
                _ex('Lesson base', 'options appearance settings', 'wcs4') ?>
                <div class="wcs4-description">
                    <?php
                    _ex(
                        'The default background color for lessons in the schedule.',
                        'options appearance settings',
                        'wcs4'
                    ) ?>
                </div>
            </th>
            <td><?php
                echo wcs4_colorpicker('wcs4_color_base', $wcs4_options['color_base']) ?></td>
            <th>
                <?php
                _ex('Lesson details box', 'options appearance settings', 'wcs4') ?>
                <div class="wcs4-description">
                    <?php
                    _ex(
                        'Background color of the lesson details box which appears when hovering over a lesson.',
                        'options appearance settings',
                        'wcs4'
                    ) ?>
                </div>
            </th>
            <td>
                <?php
                echo wcs4_colorpicker(
                    'wcs4_color_details_box',
                    $wcs4_options['color_details_box']
                ) ?>
            </td>
            <th>
                <?php
                _ex('Text', 'options appearance settings', 'wcs4') ?>
                <div class="wcs4-description">
                    <?php
                    _ex(
                        'Text color of schedule entries/lessons.',
                        'options appearance settings',
                        'wcs4'
                    ) ?>
                </div>
            </th>
            <td>
                <?php
                echo wcs4_colorpicker('wcs4_color_text', $wcs4_options['color_text']) ?>
            </td>
        </tr>
        <tr>
            <th>
                <?php
                _ex('Border', 'options appearance settings', 'wcs4') ?>
                <div class="wcs4-description">
                    <?php
                    _ex(
                        'This color is used for all borders in the schedule output.',
                        'options appearance settings',
                        'wcs4'
                    ) ?>
                </div>
            </th>
            <td>
                <?php
                echo wcs4_colorpicker('wcs4_color_border', $wcs4_options['color_border']) ?>
            </td>
            <th>
                <?php
                _ex('Schedule headings color', 'options appearance settings', 'wcs4') ?>
                <div class="wcs4-description">
                    <?php
                    _ex(
                        'Text color of the schedule headings (weekdays, hours).',
                        'options appearance settings',
                        'wcs4'
                    ) ?>
                </div>
            </th>
            <td>
                <?php
                echo wcs4_colorpicker(
                    'wcs4_color_headings_text',
                    $wcs4_options['color_headings_text']
                ) ?>
            </td>
            <th>
                <?php
                _ex('Schedule headings background', 'options appearance settings', 'wcs4') ?>
                <div class="wcs4-description">
                    <?php
                    _ex(
                        'Background color of the schedule headings (weekdays, hours).',
                        'options appearance settings',
                        'wcs4'
                    ) ?>
                </div>
            </th>
            <td><?php
                echo wcs4_colorpicker(
                    'wcs4_color_headings_background',
                    $wcs4_options['color_headings_background']
                ) ?></td>
        </tr>
        <tr>
            <th>
                <?php
                _ex('Background', 'options appearance settings', 'wcs4') ?>
                <div class="wcs4-description">
                    <?php
                    _ex(
                        'Background color for the entire schedule.',
                        'options appearance settings',
                        'wcs4'
                    ) ?>
                </div>
            </th>
            <td><?php
                echo wcs4_colorpicker(
                    'wcs4_color_background',
                    $wcs4_options['color_background']
                ) ?></td>
            <th>
                <?php
                _ex('qTip background', 'options appearance settings', 'wcs4') ?>
                <div class="wcs4-description">
                    <?php
                    _ex(
                        'Background color of the qTip pop-up box.',
                        'options appearance settings',
                        'wcs4'
                    ) ?>
                </div>
            </th>
            <td><?php
                echo wcs4_colorpicker(
                    'wcs4_color_qtip_background',
                    $wcs4_options['color_qtip_background']
                ) ?></td>
            <th>
                <?php
                _ex('Links', 'options appearance settings', 'wcs4') ?>
                <div class="wcs4-description">
                    <?php
                    _ex(
                        'The color of the links which appear in the lesson details box.',
                        'options appearance settings',
                        'wcs4'
                    ) ?>
                </div>
            </th>
            <td><?php
                echo wcs4_colorpicker('wcs4_color_links', $wcs4_options['color_links']) ?></td>
        </tr>
        </tbody>
    </table>
</details>