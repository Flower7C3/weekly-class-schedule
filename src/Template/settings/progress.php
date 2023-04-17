<?php
/**
 * @var array $taxonomyTypes
 * @var array $wcs4_options
 */

?>
<details>
    <summary><strong><?= __('Progresses Settings', 'wcs4') ?></strong></summary>
    <table class="form-table">
        <thead>
        <tr>
            <th style="width:20%"></th>
            <th style="width:40%">
                <?= _x('For all', 'options general settings', 'wcs4') ?>
            </th>
            <th style="width:40%">
                <?= _x('For masters', 'options general settings', 'wcs4') ?>
            </th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <th>
                <?= _x('Add New Progress', 'options general settings', 'wcs4') ?>
                <div class="wcs4-description">
                    <?= _x(
                        'Will display form to add new lesson progress.',
                        'options general settings',
                        'wcs4'
                    ) ?>
                </div>
            </th>
            <td data-type="wcs4_progress_create">
                <?= wcs4_bool_checkbox(
                    'wcs4_progress_create',
                    'wcs4_progress_create',
                    $wcs4_options['progress_create'],
                    __('Yes')
                )
                ?>
            </td>
            <td data-type="wcs4_progress_create_masters">
                <?= wcs4_bool_checkbox(
                    'wcs4_progress_create_masters',
                    'wcs4_progress_create_masters',
                    $wcs4_options['progress_create_masters'],
                    __('Yes')
                )
                ?>
            </td>
        </tr>
        <tr>
            <th>
                <?= _x('Display Progresse', 'options general settings', 'wcs4') ?>
                <div class="wcs4-description">
                    <?= _x(
                        'Will display amount of progresses.',
                        'options general settings',
                        'wcs4'
                    ) ?>
                </div>
            </th>
            <td data-type="wcs4_progress_view">
                <?= wcs4_textfield(
                    'wcs4_progress_view',
                    'wcs4_progress_view',
                    $wcs4_options['progress_view'],
                    20
                )
                ?>
            </td>
            <td data-type="wcs4_progress_view_masters">
                <?= wcs4_textfield(
                    'wcs4_progress_view_masters',
                    'wcs4_progress_view_masters',
                    $wcs4_options['progress_view_masters'],
                    20
                )
                ?>
            </td>
        </tr>
        </tbody>
        <thead>
        <tr>
            <th style="width:20%"></th>
            <th style="width:40%">
                <?= _x(
                    'For multiple items page.',
                    'options general settings',
                    'wcs4'
                ) ?>
            </th>
            <th style="width:40%">
                <?= _x(
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
                <?= _x('Progress shortcode template', 'options general settings', 'wcs4') ?>
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
        <?= _x('HTML progress template', 'options general settings', 'wcs4') ?>
    </h2>
    <table class="form-table">
        <thead>
        <tr>
            <th style="width:20%"></th>
            <th style="width:40%">
                <?= _x(
                    'For multiple items page.',
                    'options general settings',
                    'wcs4'
                ) ?>
            </th>
            <th style="width:40%">
                <?= _x(
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
                <?= _x('HTML code', 'options general settings', 'wcs4') ?>
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
                <?= _x(
                    'Table columns',
                    'options general settings',
                    'wcs4'
                ) ?>
                <div class="wcs4-description">
                    <?= _x(
                        'Columns separated by new line. Values in line: <code>key, title, value</code>.',
                        'options general settings',
                        'wcs4'
                    ) ?>
                </div>
            </th>
            <td data-type="wcs4_progress_html_table_columns">
                <textarea name="wcs4_progress_html_table_columns"
                          class="widefat textarea code_editor"
                          style="width:100%" rows="5"><?= $wcs4_options['progress_html_table_columns'] ?></textarea>
            </td>
            <td></td>
        </tr>
        <tr>
            <th>
                <?= _x('CSS code', 'options general settings', 'wcs4') ?>
            </th>
            <td colspan="2">
                <textarea name="wcs4_progress_html_template_style"
                          class="widefat textarea code_editor"
                          style="width:100%" rows="5"><?= wp_unslash(
                        $wcs4_options['progress_html_template_style']
                    ) ?></textarea>
            </td>
        </tr>
        </tbody>
    </table>
    <hr>
    <h2>
        <?= _x('CSV progress template', 'options general settings', 'wcs4') ?>
    </h2>
    <table class="form-table">
        <tbody>
        <tr>
            <th style="width:20%">
                <?= _x(
                    'Table columns',
                    'options general settings',
                    'wcs4'
                ) ?>
                <div class="wcs4-description">
                    <?= _x(
                        'Columns separated by new line. Values in line: <code>key, title, value</code>.',
                        'options general settings',
                        'wcs4'
                    ) ?>
                </div>
            </th>
            <td data-type="wcs4_progress_csv_table_columns">
                <textarea name="wcs4_progress_csv_table_columns"
                          class="widefat textarea code_editor"
                          style="width:100%" rows="5"><?= $wcs4_options['progress_csv_table_columns'] ?></textarea>
            </td>
        </tr>
        </tbody>
    </table>
</details>
