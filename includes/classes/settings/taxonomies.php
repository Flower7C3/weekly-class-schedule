<?php
/**
 * @var array $taxonomyTypes
 * @var array $wcs4_options
 */

?>
<details>
    <summary><strong><?php
            _ex('Taxonomy Type Settings', 'options general settings', 'wcs4') ?></strong></summary>
    <table class="form-table">
        <thead>
        <tr>
            <th></th>
            <?php
            foreach ($taxonomyTypes as $name): ?>
                <th><?php
                    echo $name['tax'] ?></th>
            <?php
            endforeach; ?>
        </tr>
        </thead>
        <tbody>
        <tr>
            <th>
                <?php
                _ex('Custom taxonomy URL', 'options general settings', 'wcs4') ?>
                <div class="wcs4-description">
                    <?php
                    _ex(
                        'Empty value will disable custom taxonomy URL.',
                        'options general settings',
                        'wcs4'
                    ) ?>
                </div>
            </th>
            <?php
            foreach ($taxonomyTypes as $key => $name): ?>
                <td data-key="<?php
                echo $key ?>" data-type="wcs4_taxonomy_slug">
                    <?php
                    echo wcs4_textfield(
                        'wcs4_' . $key . '_taxonomy_slug',
                        'wcs4_' . $key . '_taxonomy_slug',
                        $wcs4_options[$key . '_taxonomy_slug'],
                        20
                    ); ?>
                </td>
            <?php
            endforeach; ?>
        </tr>
        <tr>
            <th>
                <?php
                _ex('Is taxonomy hierarchical?', 'options general settings', 'wcs4') ?>
            </th>
            <?php
            foreach ($taxonomyTypes as $key => $name): ?>
                <td data-key="<?php
                echo $key ?>" data-type="wcs4_taxonomy_hierarchical">
                    <?php
                    echo wcs4_bool_checkbox(
                        'wcs4_' . $key . '_taxonomy_hierarchical',
                        'wcs4_' . $key . '_taxonomy_hierarchical',
                        $wcs4_options[$key . '_taxonomy_hierarchical'],
                        __('Yes')
                    ); ?>
                </td>
            <?php
            endforeach; ?>
        </tr>
        </tbody>
    </table>
</details>
