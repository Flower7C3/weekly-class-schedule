<?php
/**
 * @var array $taxonomyTypes
 * @var array $wcs4_options
 */

?>
<details>
    <summary><strong><?= _x('Post Type Settings', 'options general settings', 'wcs4') ?></strong></summary>
    <table class="form-table">
        <thead>
        <tr>
            <th style="width:20%"></th>
            <?php
            foreach ($taxonomyTypes as $name): ?>
                <th><?= $name['post'] ?></th>
            <?php
            endforeach; ?>
        </tr>
        </thead>
        <tbody>
        <tr>
            <th>
                <?= _x('Custom archive URL', 'options general settings', 'wcs4') ?>
                <div class="wcs4-description">
                    <?= _x(
                        'Empty value will disable custom archive URL.',
                        'options general settings',
                        'wcs4'
                    ) ?>
                </div>
            </th>
            <?php
            foreach ($taxonomyTypes as $key => $name): ?>
                <td data-key="<?= $key ?>" data-type="wcs4_archive_slug">
                    <?= wcs4_textfield(
                        'wcs4_' . $key . '_archive_slug',
                        'wcs4_' . $key . '_archive_slug',
                        $wcs4_options[$key . '_archive_slug'],
                        20
                    ) ?>
                </td>
            <?php
            endforeach; ?>
        </tr>
        <tr>
            <th>
                <?= _x('Custom item URL', 'options general settings', 'wcs4') ?>
                <div class="wcs4-description">
                    <?= _x(
                        'Empty value will disable custom item URL.',
                        'options general settings',
                        'wcs4'
                    ) ?>
                </div>
            </th>
            <?php
            foreach ($taxonomyTypes as $key => $name): ?>
                <td data-key="<?= $key ?>" data-type="wcs4_post_slug">
                    <?= wcs4_textfield(
                        'wcs4_' . $key . '_post_slug',
                        'wcs4_' . $key . '_post_slug',
                        $wcs4_options[$key . '_post_slug'],
                        20
                    ) ?>
                </td>
            <?php
            endforeach; ?>
        </tr>
        <tr>
            <th>
                <?= _x('Hashed item slug', 'options general settings', 'wcs4') ?>
                <div class="wcs4-description">
                    <?= _x(
                        'Hashing slug will protect real page address.',
                        'options general settings',
                        'wcs4'
                    ) ?>
                </div>
            </th>
            <?php
            foreach ($taxonomyTypes as $key => $name): ?>
                <td data-key="<?= $key ?>" data-type="wcs4_hashed_slug">
                    <?= wcs4_bool_checkbox(
                        'wcs4_' . $key . '_hashed_slug',
                        'wcs4_' . $key . '_hashed_slug',
                        $wcs4_options[$key . '_hashed_slug'],
                        __('Yes')
                    ) ?>
                </td>
            <?php
            endforeach; ?>
        </tr>
        <tr>
            <th>
                <?= _x('Satisfy access for any item', 'options general settings', 'wcs4') ?>
                <div class="wcs4-description">
                    <?= _x(
                        'Will grant access to any password protected resource if entered from type.',
                        'options general settings',
                        'wcs4'
                    ) ?>
                </div>
            </th>
            <?php
            foreach ($taxonomyTypes as $key => $name): ?>
                <td data-key="<?= $key ?>" data-type="wcs4_post_pass_satisfy_any">
                    <?= wcs4_bool_checkbox(
                        'wcs4_' . $key . '_post_pass_satisfy_any',
                        'wcs4_' . $key . '_post_pass_satisfy_any',
                        $wcs4_options[$key . '_post_pass_satisfy_any'],
                        __('Yes')
                    ) ?>
                </td>
            <?php
            endforeach; ?>
        </tr>
        </tbody>
    </table>
</details>
