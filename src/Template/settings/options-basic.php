<?php
/**
 * @var array $wcs4_options
 */

?>
<div class="wrap">
    <h1 class="wp-heading-inline">
        <?= _x('Weekly Class Schedule Basic Settings', 'options', 'wcs4') ?>
    </h1>
    <form action="" method="post" name="wcs4_basic_settings">
        <fieldset class="wcs4-settings-fieldset">
            <legend>
                <strong><?= _x('Journals', 'options general settings', 'wcs4') ?></strong>
            </legend>
            <table class="form-table">
                <thead>
                <tr>
                    <th style="width:20%"></th>
                    <th style="width:40%"><?= _x('For teachers', 'options general settings', 'wcs4') ?></th>
                    <th style="width:40%"><?= _x('For students', 'options general settings', 'wcs4') ?></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <th><?= _x('Header HTML code', 'options general settings', 'wcs4') ?></th>
                    <td>
                        <?php
                        wp_editor(
                            wp_unslash($wcs4_options['journal_teachers_html_header_code'] ?? ''),
                            'wcs4_journal_teachers_html_header_code',
                            [
                                'wpautop' => true,
                                'media_buttons' => true,
                                'textarea_name' => 'wcs4_journal_teachers_html_header_code',
                                'textarea_rows' => 14,
                            ]
                        );
                        ?>
                    </td>
                    <td>
                        <?php
                        wp_editor(
                            wp_unslash($wcs4_options['journal_students_html_header_code'] ?? ''),
                            'wcs4_journal_students_html_header_code',
                            [
                                'wpautop' => true,
                                'media_buttons' => true,
                                'textarea_name' => 'wcs4_journal_students_html_header_code',
                                'textarea_rows' => 14,
                            ]
                        );
                        ?>
                    </td>
                </tr>
                </tbody>
            </table>
        </fieldset>

        <fieldset class="wcs4-settings-fieldset">
            <legend>
                <strong><?= _x('Work Plans', 'options general settings', 'wcs4') ?></strong>
            </legend>
            <table class="form-table">
                <thead>
                <tr>
                    <th style="width:20%"></th>
                    <th style="width:40%"><?= _x('For multiple items page.', 'options general settings', 'wcs4') ?></th>
                    <th style="width:40%"><?= _x('For single item page.', 'options general settings', 'wcs4') ?></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <th><?= _x('Header HTML code', 'options general settings', 'wcs4') ?></th>
                    <td>
                        <?php
                        wp_editor(
                            wp_unslash($wcs4_options['work_plan_html_header_code_partial_type'] ?? ''),
                            'wcs4_work_plan_html_header_code_partial_type',
                            [
                                'wpautop' => true,
                                'media_buttons' => true,
                                'textarea_name' => 'wcs4_work_plan_html_header_code_partial_type',
                                'textarea_rows' => 14,
                            ]
                        );
                        ?>
                    </td>
                    <td>
                        <?php
                        wp_editor(
                            wp_unslash($wcs4_options['work_plan_html_header_code_periodic_type'] ?? ''),
                            'wcs4_work_plan_html_header_code_periodic_type',
                            [
                                'wpautop' => true,
                                'media_buttons' => true,
                                'textarea_name' => 'wcs4_work_plan_html_header_code_periodic_type',
                                'textarea_rows' => 14,
                            ]
                        );
                        ?>
                    </td>
                </tr>
                </tbody>
            </table>
        </fieldset>

        <fieldset class="wcs4-settings-fieldset">
            <legend>
                <strong><?= _x('Progresses', 'options general settings', 'wcs4') ?></strong>
            </legend>
            <table class="form-table">
                <thead>
                <tr>
                    <th style="width:20%"></th>
                    <th style="width:40%"><?= _x('For multiple items page.', 'options general settings', 'wcs4') ?></th>
                    <th style="width:40%"><?= _x('For single item page.', 'options general settings', 'wcs4') ?></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <th><?= _x('Header HTML code', 'options general settings', 'wcs4') ?></th>
                    <td>
                        <?php
                        wp_editor(
                            wp_unslash($wcs4_options['progress_html_header_code_partial_type'] ?? ''),
                            'wcs4_progress_html_header_code_partial_type',
                            [
                                'wpautop' => true,
                                'media_buttons' => true,
                                'textarea_name' => 'wcs4_progress_html_header_code_partial_type',
                                'textarea_rows' => 14,
                            ]
                        );
                        ?>
                    </td>
                    <td>
                        <?php
                        wp_editor(
                            wp_unslash($wcs4_options['progress_html_header_code_periodic_type'] ?? ''),
                            'wcs4_progress_html_header_code_periodic_type',
                            [
                                'wpautop' => true,
                                'media_buttons' => true,
                                'textarea_name' => 'wcs4_progress_html_header_code_periodic_type',
                                'textarea_rows' => 14,
                            ]
                        );
                        ?>
                    </td>
                </tr>
                </tbody>
            </table>
        </fieldset>

        <?php
        submit_button(_x('Save Settings', 'options', 'wcs4'));
        wp_nonce_field('wcs4_save_options', 'wcs4_options_nonce');
        ?>
    </form>
</div>

