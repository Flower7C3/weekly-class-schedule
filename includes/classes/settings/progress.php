<?php
/**
 * @var array $taxonomyTypes
 * @var array $wcs4_options
 */

?>
<details>
    <summary><strong><?php
            echo __('Progresses Settings', 'wcs4') ?></strong></summary>
    <table class="form-table">
        <thead>
        <tr>
            <th style="width:20%"></th>
            <th style="width:40%">
                <?php
                _ex('For all', 'options general settings', 'wcs4'); ?>
            </th>
            <th style="width:40%">
                <?php
                _ex('For masters', 'options general settings', 'wcs4'); ?>
            </th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <th>
                <?php
                _ex('Add New Progress', 'options general settings', 'wcs4'); ?>
                <div class="wcs4-description">
                    <?php
                    _ex(
                        'Will display form to add new lesson progress.',
                        'options general settings',
                        'wcs4'
                    ) ?>
                </div>
            </th>
            <td data-type="wcs4_progress_create">
                <?php
                echo wcs4_bool_checkbox(
                    'wcs4_progress_create',
                    'wcs4_progress_create',
                    $wcs4_options['progress_create'],
                    __('Yes')
                );
                ?>
            </td>
            <td data-type="wcs4_progress_create_masters">
                <?php
                echo wcs4_bool_checkbox(
                    'wcs4_progress_create_masters',
                    'wcs4_progress_create_masters',
                    $wcs4_options['progress_create_masters'],
                    __('Yes')
                );
                ?>
            </td>
        </tr>
        <tr>
            <th>
                <?php
                _ex('Display Progresse', 'options general settings', 'wcs4'); ?>
                <div class="wcs4-description">
                    <?php
                    _ex(
                        'Will display amount of progresses.',
                        'options general settings',
                        'wcs4'
                    ) ?>
                </div>
            </th>
            <td data-type="wcs4_progress_view">
                <?php
                echo wcs4_textfield(
                    'wcs4_progress_view',
                    'wcs4_progress_view',
                    $wcs4_options['progress_view'],
                    20
                );
                ?>
            </td>
            <td data-type="wcs4_progress_view_masters">
                <?php
                echo wcs4_textfield(
                    'wcs4_progress_view_masters',
                    'wcs4_progress_view_masters',
                    $wcs4_options['progress_view_masters'],
                    20
                );
                ?>
            </td>
        </tr>
        </tbody>
        <thead>
        <tr>
            <th style="width:20%"></th>
            <th style="width:40%">
                <?php
                _ex(
                    'For multiple items page.',
                    'options general settings',
                    'wcs4'
                ) ?>
            </th>
            <th style="width:40%">
                <?php
                _ex(
                    'For single item page.',
                    'options general settings',
                    'wcs4'
                ) ?>
            </th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <th>
                <?php
                _ex('Progress shortcode template', 'options general settings', 'wcs4'); ?>
            </th>
            <td>
                <?php
                wp_editor(
                    $wcs4_options['progress_shortcode_template_partial_type'],
                    'wcs4_progress_shortcode_template_partial_type',
                    [
                        'wpautop' => true,
                        'media_buttons' => false,
                        'textarea_name' => 'wcs4_progress_shortcode_template_partial_type',
                        'textarea_rows' => 6,
                    ]
                );
                ?>
            </td>
            <td>
                <?php
                wp_editor(
                    $wcs4_options['progress_shortcode_template_periodic_type'],
                    'wcs4_progress_shortcode_template_periodic_type',
                    [
                        'wpautop' => true,
                        'media_buttons' => false,
                        'textarea_name' => 'wcs4_progress_shortcode_template_periodic_type',
                        'textarea_rows' => 6,
                    ]
                );
                ?>
            </td>
        </tr>
        </tbody>
    </table>
    <hr>
    <h2>
        <?php
        _ex('HTML progress template', 'options general settings', 'wcs4') ?>
    </h2>
    <table class="form-table">
        <thead>
        <tr>
            <th style="width:20%"></th>
            <th style="width:40%">
                <?php
                _ex(
                    'For multiple items page.',
                    'options general settings',
                    'wcs4'
                ) ?>
            </th>
            <th style="width:40%">
                <?php
                _ex(
                    'For single item page.',
                    'options general settings',
                    'wcs4'
                ) ?>
            </th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <th>
                <?php
                _ex('HTML code', 'options general settings', 'wcs4') ?>
            </th>
            <td>
                <?php
                wp_editor(
                    wp_unslash($wcs4_options['progress_html_template_code_partial_type']),
                    'wcs4_progress_html_template_code_partial_type',
                    [
                        'wpautop' => true,
                        'media_buttons' => true,
                        'textarea_name' => 'wcs4_progress_html_template_code_partial_type',
                        'textarea_rows' => 14,
                    ]
                );
                ?>
            </td>
            <td>
                <?php
                wp_editor(
                    wp_unslash($wcs4_options['progress_html_template_code_periodic_type']),
                    'wcs4_progress_html_template_code_periodic_type',
                    [
                        'wpautop' => true,
                        'media_buttons' => true,
                        'textarea_name' => 'wcs4_progress_html_template_code_periodic_type',
                        'textarea_rows' => 14,
                    ]
                );
                ?>
            </td>
        </tr>
        <tr>
            <th>
                <?php
                _ex(
                    'Table columns',
                    'options general settings',
                    'wcs4'
                ) ?>
                <div class="wcs4-description">
                    <?php
                    _ex(
                        'Columns separated by new line. Values in line: <code>key, title, value</code>.',
                        'options general settings',
                        'wcs4'
                    ) ?>
                </div>
            </th>
            <td data-type="wcs4_progress_html_table_columns">
                <textarea name="wcs4_progress_html_table_columns"
                          class="widefat textarea code_editor"
                          style="width:100%" rows="5"><?php
                    echo $wcs4_options['progress_html_table_columns']; ?></textarea>
            </td>
            <td></td>
        </tr>
        <tr>
            <th>
                <?php
                _ex('CSS code', 'options general settings', 'wcs4') ?>
            </th>
            <td colspan="2">
                <textarea name="wcs4_progress_html_template_style"
                          class="widefat textarea code_editor"
                          style="width:100%" rows="5"><?php
                    echo wp_unslash($wcs4_options['progress_html_template_style']); ?></textarea>
            </td>
        </tr>
        </tbody>
    </table>
    <hr>
    <h2>
        <?php
        _ex('CSV progress template', 'options general settings', 'wcs4') ?>
    </h2>
    <table class="form-table">
        <tbody>
        <tr>
            <th style="width:20%">
                <?php
                _ex(
                    'Table columns',
                    'options general settings',
                    'wcs4'
                ) ?>
                <div class="wcs4-description">
                    <?php
                    _ex(
                        'Columns separated by new line. Values in line: <code>key, title, value</code>.',
                        'options general settings',
                        'wcs4'
                    ) ?>
                </div>
            </th>
            <td data-type="wcs4_progress_csv_table_columns">
                <textarea name="wcs4_progress_csv_table_columns"
                          class="widefat textarea code_editor"
                          style="width:100%" rows="5"><?php
                    echo $wcs4_options['progress_csv_table_columns']; ?></textarea>
            </td>
        </tr>
        </tbody>
    </table>
</details>
