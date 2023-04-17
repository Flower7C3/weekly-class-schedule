<?php
/**
 * @var array $taxonomyTypes
 * @var array $wcs4_options
 */

?>
<details>
    <summary><strong><?= _x('Taxonomy Type Settings', 'options general settings', 'wcs4') ?></strong></summary>
    <table class="form-table">
        <thead>
        <tr>
            <th></th>
            <?php
            foreach ($taxonomyTypes as $name): ?>
                <th><?= $name['tax'] ?></th>
            <?php
            endforeach; ?>
        </tr>
        </thead>
        <tbody>
        <tr>
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
            <?php
            foreach ($taxonomyTypes as $key => $name): ?>
                <td data-key="<?= $key ?>" data-type="wcs4_taxonomy_slug">
                    <?= wcs4_textfield(
                        'wcs4_' . $key . '_taxonomy_slug',
                        'wcs4_' . $key . '_taxonomy_slug',
                        $wcs4_options[$key . '_taxonomy_slug'],
                        20
                    ) ?>
                </td>
            <?php
            endforeach; ?>
        </tr>
        <tr>
            <th>
                <?= _x('Is taxonomy hierarchical?', 'options general settings', 'wcs4') ?>
            </th>
            <?php
            foreach ($taxonomyTypes as $key => $name): ?>
                <td data-key="<?= $key ?>" data-type="wcs4_taxonomy_hierarchical">
                    <?= wcs4_bool_checkbox(
                        'wcs4_' . $key . '_taxonomy_hierarchical',
                        'wcs4_' . $key . '_taxonomy_hierarchical',
                        $wcs4_options[$key . '_taxonomy_hierarchical'],
                        __('Yes')
                    ) ?>
                </td>
            <?php
            endforeach; ?>
        </tr>
        </tbody>
    </table>
</details>
