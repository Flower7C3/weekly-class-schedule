<?php
/**
 * @var array $search
 */

use WCS4\Helper\Admin;

?>
<form id="<?= $search['id'] ?>" class="results-filter" method="get" action="<?= admin_url('admin.php') ?>">
    <input id="search_wcs4_page" type="hidden" name="page" value="<?= $_GET['page'] ?>"/>
    <div class="search-box">
        <?php
        foreach ($search['fields'] as $id => $field): ?>
            <div class="alignleft">
                <label for="<?= $id ?>">
                    <?= $field['label'] ?>
                </label>
                <?php
                switch ($field['type'] ?? null) {
                    case 'generate_admin_select_list':
                        $name = $field['name'];
                        echo Admin::generate_admin_select_list(
                            $name,
                            $id,
                            $name,
                            array_key_exists($name, $_GET) ? (int)$_GET[$name] : ''
                        );
                        break;
                    default:
                        echo $field['input'];
                } ?>
            </div>
        <?php
        endforeach; ?>
        <div class="alignleft buttons">
            <button type="submit" class="button button-primary">
                <span class="dashicons dashicons-filter"></span>
                <?= $search['submit'] ?>
            </button>
            <button type="reset" class="button button-secondary">
                <span class="dashicons dashicons-no"></span>
                <?= __('Reset form', 'wcs4') ?>
            </button>
        </div>
        <div class="wp-clearfix"></div>
        <?php
        if (!empty($search['buttons'])): ?>
            <div class="alignleft buttons">
                <?php
                foreach ($search['buttons'] as $button): ?>
                    <button type="<?= $button['type'] ?? 'submit' ?>"
                            class="button button-secondary"
                        <?php
                        if (empty($button['data-action'])): ?>
                            name="action"
                            value="<?= $button['action'] ?>"
                            formaction="<?= admin_url('admin-ajax.php') ?>"
                            formtarget="<?= $button['formtarget']??'' ?>"
                        <?php
                        else: ?>
                            data-action="<?= $button['data-action'] ?>"
                        <?php
                        endif; ?>
                    >
                        <span class="<?= $button['icon'] ?>"></span>
                        <?= $button['label'] ?>
                    </button>
                <?php
                endforeach; ?>
            </div>
            <div class="wp-clearfix"></div>
        <?php
        endif; ?>
    </div>
</form>
