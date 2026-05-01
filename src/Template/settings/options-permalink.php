<?php
/**
 * Shown on Settings → Permalinks.
 *
 * @var array $taxonomyTypes
 * @var array $wcs4_options
 */

?>
<div id="wcs4-url-settings">
<input type="hidden" name="wcs4_permalink_section" value="1"/>

<h2 class="title">
    <?= _x('Weekly Class Schedule URL Settings', 'options', 'wcs4') ?>
</h2>

<table class="settings form-table">
    <thead>
    <tr>
        <th>
            <?= _x('Taxonomies', 'options', 'wcs4') ?>
        </th>
        <th>
            <?= _x('Custom taxonomy URL', 'options general settings', 'wcs4') ?>
            <div class="wcs4-description">
                <?= _x(
                    'Empty value will disable custom taxonomy URL.',
                    'options general settings',
                    'wcs4'
                ) ?>
            </div>
        </th>
        <th>
            <?= _x('Is taxonomy hierarchical?', 'options general settings', 'wcs4') ?>
        </th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($taxonomyTypes as $key => $name): ?>
        <tr>
            <th><?= $name['tax'] ?></th>
            <td data-key="<?= $key ?>" data-type="wcs4_taxonomy_slug">
                <?= wcs4_textfield(
                    'wcs4_' . $key . '_taxonomy_slug',
                    'wcs4_' . $key . '_taxonomy_slug',
                    $wcs4_options[$key . '_taxonomy_slug'],
                    20
                ) ?>
            </td>
            <td data-key="<?= $key ?>" data-type="wcs4_taxonomy_hierarchical">
                <?= wcs4_bool_checkbox(
                    'wcs4_' . $key . '_taxonomy_hierarchical',
                    'wcs4_' . $key . '_taxonomy_hierarchical',
                    $wcs4_options[$key . '_taxonomy_hierarchical'],
                    __('Yes')
                ) ?>
            </td>
        </tr>
    <?php
    endforeach; ?>
    </tbody>
    <thead>
    <tr>
        <th>
            <?= _x('Archives', 'options', 'wcs4') ?>
        </th>
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
    </thead>
    <tbody>
    <?php
    foreach ($taxonomyTypes as $key => $name): ?>
        <tr>
            <th><?= $name['post'] ?></th>
            <td data-key="<?= $key ?>" data-type="wcs4_archive_slug">
                <?= wcs4_textfield(
                    'wcs4_' . $key . '_archive_slug',
                    'wcs4_' . $key . '_archive_slug',
                    $wcs4_options[$key . '_archive_slug'],
                    20
                ) ?>
            </td>
        </tr>
    <?php
    endforeach; ?>
    </tbody>
    <thead>
    <tr>
        <th>
            <?= _x('Post types', 'options', 'wcs4') ?>
        </th>
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
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($taxonomyTypes as $key => $name): ?>
        <tr>
            <th><?= $name['post'] ?></th>
            <td data-key="<?= $key ?>" data-type="wcs4_post_slug">
                <?= wcs4_textfield(
                    'wcs4_' . $key . '_post_slug',
                    'wcs4_' . $key . '_post_slug',
                    $wcs4_options[$key . '_post_slug'],
                    20
                ) ?>
            </td>
            <td data-key="<?= $key ?>" data-type="wcs4_hashed_slug">
                <?= wcs4_bool_checkbox(
                    'wcs4_' . $key . '_hashed_slug',
                    'wcs4_' . $key . '_hashed_slug',
                    $wcs4_options[$key . '_hashed_slug'],
                    __('Yes')
                ) ?>
            </td>
            <td data-key="<?= $key ?>" data-type="wcs4_post_pass_satisfy_any">
                <?= wcs4_bool_checkbox(
                    'wcs4_' . $key . '_post_pass_satisfy_any',
                    'wcs4_' . $key . '_post_pass_satisfy_any',
                    $wcs4_options[$key . '_post_pass_satisfy_any'],
                    __('Yes')
                ) ?>
            </td>
        </tr>
    <?php
    endforeach; ?>
    </tbody>
</table>
</div>
