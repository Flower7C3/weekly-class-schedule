<?php
/**
 * @var array $taxonomyTypes
 * @var array $wcs4_options
 */

?>
<details>
    <summary><strong><?= __('Journals Settings', 'wcs4') ?></strong></summary>
    <table class="form-table">
        <thead>
        <tr>
            <th style="width:20%"></th>
            <?php
            foreach ($taxonomyTypes as $key => $name):
                if ($key !== 'classroom'):?>
                    <th style="width:18%"><?= $name['post'] ?></th>
                <?php
                endif;
            endforeach; ?>
            <th style="width:18%"><?= __('Common', 'wcs4') ?></th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <th>
                <?= _x('Add New Journal', 'options general settings', 'wcs4') ?>
                <div class="wcs4-description">
                    <?= _x(
                        'Will display form allowing anyone to add new lesson journal.',
                        'options general settings',
                        'wcs4'
                    ) ?>
                </div>
            </th>
            <?php
            foreach ($taxonomyTypes as $key => $name):
                if ($key !== 'classroom'): ?>
                    <td data-key="<?= $key ?>" data-type="wcs4_journal_create">
                        <?= wcs4_bool_checkbox(
                            'wcs4_' . $key . '_journal_create',
                            'wcs4_' . $key . '_journal_create',
                            $wcs4_options[$key . '_journal_create'],
                            __('Yes')
                        )
                        ?>
                    </td>
                <?php
                endif;
            endforeach; ?>
        </tr>
        <tr>
            <th>
                <?= _x('Edit Journal due day', 'options general settings', 'wcs4') ?>
                <div class="wcs4-description">
                    <?= _x(
                        'Will allow to edit journal due month day.',
                        'options general settings',
                        'wcs4'
                    ) ?>
                </div>
            </th>
            <td></td>
            <td data-type="wcs4_journal_edit_masters">
                <?= wcs4_textfield(
                    'wcs4_journal_edit_masters',
                    'wcs4_journal_edit_masters',
                    $wcs4_options['journal_edit_masters'],
                    20
                ) ?>
            </td>
        </tr>
        <tr>
            <th>
                <?= _x('Detect journal collisions', 'options general settings', 'wcs4') ?>
                <div class="wcs4-description">
                    <?= _x(
                        'Enabling this feature will prevent journaling of multiple events, with same teacher or student at the same time.',
                        'options general settings',
                        'wcs4'
                    ) ?>
                </div>
            </th>
            <?php
            foreach ($taxonomyTypes as $key => $name): ?>
                <td data-key="<?= $key ?>" data-type="wcs4_journal_collision">
                    <?php
                    if ($key !== 'subject' && $key !== 'classroom'): ?>
                        <?= wcs4_bool_checkbox(
                            'wcs4_journal_' . $key . '_collision',
                            'wcs4_journal_' . $key . '_collision',
                            $wcs4_options['journal_' . $key . '_collision'],
                            __('Yes')
                        ) ?>
                    <?php
                    endif; ?>
                </td>
            <?php
            endforeach; ?>
        </tr>
        <tr>
            <th>
                <?= _x('Display Journals', 'options general settings', 'wcs4') ?>
                <div class="wcs4-description">
                    <?= _x(
                        'Will display amount of journals for all.',
                        'options general settings',
                        'wcs4'
                    ) ?>
                </div>
            </th>
            <?php
            foreach ($taxonomyTypes as $key => $name):
                if ($key !== 'classroom'): ?>
                    <td data-key="<?= $key ?>" data-type="wcs4_journal_view">
                        <?= wcs4_textfield(
                            'wcs4_' . $key . '_journal_view',
                            'wcs4_' . $key . '_journal_view',
                            $wcs4_options[$key . '_journal_view'],
                            20
                        )
                        ?>
                    </td>
                <?php
                endif;
            endforeach; ?>
        </tr>
        <tr>
            <th>
                <?= _x('Journal shortcode template', 'options general settings', 'wcs4') ?>
            </th>
            <?php
            foreach ($taxonomyTypes as $key => $name):
                if ($key !== 'classroom'): ?>
                    <td data-key="<?= $key ?>" data-type="wcs4_journal_shortcode_template">
                        <?php
                        wp_editor(
                            $wcs4_options[$key . '_journal_shortcode_template'],
                            'wcs4_' . $key . '_journal_shortcode_template',
                            [
                                'wpautop' => true,
                                'media_buttons' => false,
                                'textarea_name' => 'wcs4_' . $key . '_journal_shortcode_template',
                                'textarea_rows' => 6,
                            ]
                        );
                        ?>
                    </td>
                <?php
                endif;
            endforeach; ?>
            <td>
                <?php
                wp_editor(
                    $wcs4_options['journal_shortcode_template'],
                    'wcs4_journal_shortcode_template',
                    [
                        'wpautop' => true,
                        'media_buttons' => false,
                        'textarea_name' => 'wcs4_journal_shortcode_template',
                        'textarea_rows' => 6,
                    ]
                );
                ?>
            </td>
        </tr>
        <tr>
            <th>
                <?= _x('Download Journals as CSV', 'options general settings', 'wcs4') ?>
                <div class="wcs4-description">
                    <?= _x(
                        'Will display extra link to download journal as CSV.',
                        'options general settings',
                        'wcs4'
                    ) ?>
                </div>
            </th>
            <?php
            foreach ($taxonomyTypes as $key => $name):
                if ($key !== 'classroom'): ?>
                    <td data-key="<?= $key ?>" data-type="wcs4_journal_download_csv">
                        <?= wcs4_bool_checkbox(
                            'wcs4_' . $key . '_journal_download_csv',
                            'wcs4_' . $key . '_journal_download_csv',
                            $wcs4_options[$key . '_journal_download_csv'],
                            __('Yes')
                        )
                        ?>
                    </td>
                <?php
                endif;
            endforeach; ?>
        </tr>
        <tr>
            <th>
                <?= _x('Download Journals as HTML', 'options general settings', 'wcs4') ?>
                <div class="wcs4-description">
                    <?= _x(
                        'Will display extra link to download journal as HTML.',
                        'options general settings',
                        'wcs4'
                    ) ?>
                </div>
            </th>
            <?php
            foreach ($taxonomyTypes as $key => $name):
                if ($key !== 'classroom'): ?>
                    <td data-key="<?= $key ?>" data-type="wcs4_journal_download_html">
                        <?= wcs4_bool_checkbox(
                            'wcs4_' . $key . '_journal_download_html',
                            'wcs4_' . $key . '_journal_download_html',
                            $wcs4_options[$key . '_journal_download_html'],
                            __('Yes')
                        )
                        ?>
                    </td>
                <?php
                endif;
            endforeach; ?>
        </tr>
        </tbody>
    </table>
    <hr>
    <h2>
        <?= _x('HTML journal template', 'options general settings', 'wcs4') ?>
    </h2>
    <table class="form-table">
        <tbody>
        <tr>
            <th>
                <?= _x('HTML code', 'options general settings', 'wcs4') ?>
            </th>
            <td data-type="wcs4_journal_html_template_code">
                <?php
                wp_editor(
                    wp_kses_stripslashes($wcs4_options['journal_html_template_code']),
                    'wcs4_journal_html_template_code',
                    [
                        'wpautop' => true,
                        'media_buttons' => false,
                        'textarea_name' => 'wcs4_journal_html_template_code',
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
            <td data-type="wcs4_journal_html_table_columns">
                <textarea name="wcs4_journal_html_table_columns"
                          class="widefat textarea code_editor"
                          style="width:100%" rows="10"><?= $wcs4_options['journal_html_table_columns'] ?></textarea>
            </td>
        </tr>
        <tr>
            <th>
                <?= _x('CSS code', 'options general settings', 'wcs4') ?>
            </th>
            <td>
            <textarea name="wcs4_journal_html_template_style"
                      class="widefat textarea code_editor"
                      style="width:100%" rows="5"><?= wp_unslash(
                    $wcs4_options['journal_html_template_style']
                ) ?></textarea>
            </td>
        </tr>
        </tbody>
    </table>
    <hr>
    <h2>
        <?= _x('CSV journal template', 'options general settings', 'wcs4') ?>
    </h2>
    <table class="form-table">
        <tbody>
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
            <td data-type="wcs4_journal_csv_table_columns">
                <textarea name="wcs4_journal_csv_table_columns"
                          class="widefat textarea code_editor"
                          style="width:100%" rows="5"><?= $wcs4_options['journal_csv_table_columns'] ?></textarea>
            </td>
        </tr>
        </tbody>
    </table>
</details>
