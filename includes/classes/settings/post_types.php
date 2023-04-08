<?php
/**
 * @var array $taxonomyTypes
 * @var array $wcs4_options
 */
?>
<details>
    <summary><strong><?php
            _ex('Post Type Settings', 'options general settings', 'wcs4') ?></strong></summary>
    <table class="form-table">
        <thead>
        <tr>
            <th style="width:20%"></th>
            <?php
            foreach ($taxonomyTypes as $name): ?>
                <th><?php
                    echo $name['post'] ?></th>
            <?php
            endforeach; ?>
        </tr>
        </thead>
        <tbody>
        <tr>
            <th>
                <?php
                _ex('Custom archive URL', 'options general settings', 'wcs4') ?>
                <div class="wcs4-description">
                    <?php
                    _ex(
                        'Empty value will disable custom archive URL.',
                        'options general settings',
                        'wcs4'
                    ) ?>
                </div>
            </th>
            <?php
            foreach ($taxonomyTypes as $key => $name): ?>
                <td data-key="<?php
                echo $key ?>" data-type="wcs4_archive_slug">
                    <?php
                    echo wcs4_textfield(
                        'wcs4_' . $key . '_archive_slug',
                        'wcs4_' . $key . '_archive_slug',
                        $wcs4_options[$key . '_archive_slug'],
                        20
                    ); ?>
                </td>
            <?php
            endforeach; ?>
        </tr>
        <tr>
            <th>
                <?php
                _ex('Custom item URL', 'options general settings', 'wcs4') ?>
                <div class="wcs4-description">
                    <?php
                    _ex(
                        'Empty value will disable custom item URL.',
                        'options general settings',
                        'wcs4'
                    ) ?>
                </div>
            </th>
            <?php
            foreach ($taxonomyTypes as $key => $name): ?>
                <td data-key="<?php
                echo $key ?>" data-type="wcs4_post_slug">
                    <?php
                    echo wcs4_textfield(
                        'wcs4_' . $key . '_post_slug',
                        'wcs4_' . $key . '_post_slug',
                        $wcs4_options[$key . '_post_slug'],
                        20
                    ); ?>
                </td>
            <?php
            endforeach; ?>
        </tr>
        <tr>
            <th>
                <?php
                _ex('Hashed item slug', 'options general settings', 'wcs4'); ?>
                <div class="wcs4-description">
                    <?php
                    _ex(
                        'Hashing slug will protect real page address.',
                        'options general settings',
                        'wcs4'
                    ) ?>
                </div>
            </th>
            <?php
            foreach ($taxonomyTypes as $key => $name): ?>
                <td data-key="<?php
                echo $key ?>" data-type="wcs4_hashed_slug">
                    <?php
                    echo wcs4_bool_checkbox(
                        'wcs4_' . $key . '_hashed_slug',
                        'wcs4_' . $key . '_hashed_slug',
                        $wcs4_options[$key . '_hashed_slug'],
                        __('Yes')
                    ); ?>
                </td>
            <?php
            endforeach; ?>
        </tr>
        <tr>
            <th>
                <?php
                _ex('Satisfy access for any item', 'options general settings', 'wcs4'); ?>
                <div class="wcs4-description">
                    <?php
                    _ex(
                        'Will grant access to any password protected resource if entered from type.',
                        'options general settings',
                        'wcs4'
                    ) ?>
                </div>
            </th>
            <?php
            foreach ($taxonomyTypes as $key => $name): ?>
                <td data-key="<?php
                echo $key ?>" data-type="wcs4_post_pass_satisfy_any">
                    <?php
                    echo wcs4_bool_checkbox(
                        'wcs4_' . $key . '_post_pass_satisfy_any',
                        'wcs4_' . $key . '_post_pass_satisfy_any',
                        $wcs4_options[$key . '_post_pass_satisfy_any'],
                        __('Yes')
                    ); ?>
                </td>
            <?php
            endforeach; ?>
        </tr>
        </tbody>
    </table>
</details>
