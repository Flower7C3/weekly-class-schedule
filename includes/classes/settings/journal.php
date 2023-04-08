<?php
/**
 * @var array $taxonomyTypes
 * @var array $wcs4_options
 */

?>
<details>
    <summary><strong><?php
            echo __('Journals Settings', 'wcs4') ?></strong></summary>
    <table class="form-table">
        <thead>
        <tr>
            <th style="width:20%"></th>
            <?php
            foreach ($taxonomyTypes as $key => $name):
                if ($key !== 'classroom'):?>
                    <th style="width:18%"><?php
                        echo $name['post'] ?></th>
                <?php
                endif;
            endforeach; ?>
            <th style="width:18%"><?php
                echo __('Common', 'wcs4') ?></th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <th>
                <?php
                _ex('Add New Journal', 'options general settings', 'wcs4'); ?>
                <div class="wcs4-description">
                    <?php
                    _ex(
                        'Will display form allowing anyone to add new lesson journal.',
                        'options general settings',
                        'wcs4'
                    ) ?>
                </div>
            </th>
            <?php
            foreach ($taxonomyTypes as $key => $name):
                if ($key !== 'classroom'): ?>
                    <td data-key="<?php
                    echo $key ?>" data-type="wcs4_journal_create">
                        <?php
                        echo wcs4_bool_checkbox(
                            'wcs4_' . $key . '_journal_create',
                            'wcs4_' . $key . '_journal_create',
                            $wcs4_options[$key . '_journal_create'],
                            __('Yes')
                        );
                        ?>
                    </td>
                <?php
                endif;
            endforeach; ?>
        </tr>
        <tr>
            <th>
                <?php
                _ex('Detect journal collisions', 'options general settings', 'wcs4'); ?>
                <div class="wcs4-description">
                    <?php
                    _ex(
                        'Enabling this feature will prevent journaling of multiple events, with same teacher or student at the same time.',
                        'options general settings',
                        'wcs4'
                    ) ?>
                </div>
            </th>
            <?php
            foreach ($taxonomyTypes as $key => $name): ?>
                <td data-key="<?php
                echo $key ?>" data-type="wcs4_journal_collision">
                    <?php
                    if ($key !== 'subject' && $key !== 'classroom'): ?>
                        <?php
                        echo wcs4_bool_checkbox(
                            'wcs4_journal_' . $key . '_collision',
                            'wcs4_journal_' . $key . '_collision',
                            $wcs4_options['journal_' . $key . '_collision'],
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
                <?php
                _ex('Display Journals', 'options general settings', 'wcs4'); ?>
                <div class="wcs4-description">
                    <?php
                    _ex(
                        'Will display amount of journals for all.',
                        'options general settings',
                        'wcs4'
                    ) ?>
                </div>
            </th>
            <?php
            foreach ($taxonomyTypes as $key => $name):
                if ($key !== 'classroom'): ?>
                    <td data-key="<?php
                    echo $key ?>" data-type="wcs4_journal_view">
                        <?php
                        echo wcs4_textfield(
                            'wcs4_' . $key . '_journal_view',
                            'wcs4_' . $key . '_journal_view',
                            $wcs4_options[$key . '_journal_view'],
                            20
                        );
                        ?>
                    </td>
                <?php
                endif;
            endforeach; ?>
        </tr>
        <tr>
            <th>
                <?php
                _ex('Journal shortcode template', 'options general settings', 'wcs4'); ?>
            </th>
            <?php
            foreach ($taxonomyTypes as $key => $name):
                if ($key !== 'classroom'): ?>
                    <td data-key="<?php
                    echo $key ?>" data-type="wcs4_journal_shortcode_template">
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
                <?php
                _ex('Download journals as CSV', 'options general settings', 'wcs4'); ?>
                <div class="wcs4-description">
                    <?php
                    _ex(
                        'Will display extra link to download journal as CSV.',
                        'options general settings',
                        'wcs4'
                    ) ?>
                </div>
            </th>
            <?php
            foreach ($taxonomyTypes as $key => $name):
                if ($key !== 'classroom'): ?>
                    <td data-key="<?php
                    echo $key ?>" data-type="wcs4_journal_download_csv">
                        <?php
                        echo wcs4_bool_checkbox(
                            'wcs4_' . $key . '_journal_download_csv',
                            'wcs4_' . $key . '_journal_download_csv',
                            $wcs4_options[$key . '_journal_download_csv'],
                            __('Yes')
                        );
                        ?>
                    </td>
                <?php
                endif;
            endforeach; ?>
        </tr>
        <tr>
            <th>
                <?php
                _ex('Download journals as HTML', 'options general settings', 'wcs4'); ?>
                <div class="wcs4-description">
                    <?php
                    _ex(
                        'Will display extra link to download journal as HTML.',
                        'options general settings',
                        'wcs4'
                    ) ?>
                </div>
            </th>
            <?php
            foreach ($taxonomyTypes as $key => $name):
                if ($key !== 'classroom'): ?>
                    <td data-key="<?php
                    echo $key ?>" data-type="wcs4_journal_download_html">
                        <?php
                        echo wcs4_bool_checkbox(
                            'wcs4_' . $key . '_journal_download_html',
                            'wcs4_' . $key . '_journal_download_html',
                            $wcs4_options[$key . '_journal_download_html'],
                            __('Yes')
                        );
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
        <?php
        _ex('HTML journal template', 'options general settings', 'wcs4') ?>
    </h2>
    <table class="form-table">
        <tbody>
        <tr>
            <th>
                <?php
                _ex('HTML code', 'options general settings', 'wcs4') ?>
            </th>
            <td data-type="wcs4_journal_html_template_code">
                <?php
                wp_editor(
                    $wcs4_options['journal_html_template_code'],
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
            <td data-type="wcs4_journal_html_table_columns">
                <textarea name="wcs4_journal_html_table_columns"
                          class="widefat textarea code_editor"
                          style="width:100%" rows="10"><?php
                    echo $wcs4_options['journal_html_table_columns']; ?></textarea>
            </td>
        </tr>
        <tr>
            <th>
                <?php
                _ex('CSS code', 'options general settings', 'wcs4') ?>
            </th>
            <td>
            <textarea name="wcs4_journal_html_template_style"
                      class="widefat textarea code_editor"
                      style="width:100%" rows="5"><?php
                echo wp_unslash($wcs4_options['journal_html_template_style']); ?></textarea>
            </td>
        </tr>
        </tbody>
    </table>
    <hr>
    <h2>
        <?php
        _ex('CSV journal template', 'options general settings', 'wcs4') ?>
    </h2>
    <table class="form-table">
        <tbody>
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
            <td data-type="wcs4_journal_csv_table_columns">
                <textarea name="wcs4_journal_csv_table_columns"
                          class="widefat textarea code_editor"
                          style="width:100%" rows="5"><?php
                    echo $wcs4_options['journal_csv_table_columns']; ?></textarea>
            </td>
        </tr>
        </tbody>
    </table>
</details>
