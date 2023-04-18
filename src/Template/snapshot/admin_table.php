<?php
/**
 * @var array $items
 * @var string $order_field
 * @var string $order_direction
 */

use WCS4\Entity\Snapshot_Item;
use WCS4\Helper\Output;

?>
<div class="wcs4-day-content-wrapper"
     data-hash="<?= md5(serialize($items) . time() . $order_field . $order_direction) ?>">
    <?php
    if ($items): ?>
        <?php
        $groups = [];
        /** @var Snapshot_Item $item */
        foreach ($items as $item) {
            switch ($order_field) {
                case 'location':
                    $key = $item->getTitle();
                    break;
                default:
                case 'updated-at':
                    $key = $item->getCreatedAt()->format('Y-m-d');
                    break;
            }
            $groups[$key][] = $item;
        }
        if ($order_direction === 'desc') {
            krsort($groups);
        } else {
            ksort($groups);
        }

        ?>
        <?php
        foreach ($groups as $groupName => $groupData): ?>
            <section id="wcs4-snapshot-day-<?= $groupName ?>">
                <h2>
                    <?= $groupName ?>
                    <span class="spinner"></span>
                </h2>
                <table class="wp-list-table widefat fixed striped wcs4-admin-progress-table">
                    <thead>
                    <tr>
                        <?php
                        admin_th(
                            __('Location', 'wcs4'),
                            'location',
                            $order_direction,
                            $order_field
                        );
                        admin_th(
                            __('Query', 'wcs4')
                        );
                        admin_th(
                            __('Version', 'wcs4')
                        );
                        admin_th(
                            __('Updated at', 'wcs4'),
                            'updated-at',
                            $order_direction,
                            $order_field
                        ); ?>
                    </tr>
                    </thead>
                    <tbody id="the-list-<?= $groupName ?>">
                    <?php
                    /** @var Snapshot_Item $item */
                    foreach ($groupData as $item): ?>
                        <tr id="progress-<?= $item->getId() ?>"
                            data-type="snapshot"
                            data-id="<?= $item->getId() ?>">
                            <td class="column-primary has-row-actions">
                                <?php
                                switch ($item->getAction()) {
                                    default:
                                        $label = $item->getAction();
                                        break;
                                    case 'wcs_download_schedules_html':
                                        $label = __('Schedules', 'wcs4');
                                        break;
                                    case 'wcs_download_journals_html':
                                        $label = __('Journals', 'wcs4');
                                        break;
                                    case 'wcs_download_work_plans_html':
                                        $label = __('Work Plans', 'wcs4');
                                        break;
                                    case 'wcs_download_progresses_html':
                                        $label = __('Progresses', 'wcs4');
                                        break;
                                }
                                Output::admin_search_link(
                                    'search_wcs4_snapshot_location',
                                    $item->getAction(),
                                    $label
                                ); ?>
                                <?= $item->getTitle() ?>
                                <div class="row-actions">
                                    <span class="download">
                                        <a href="<?= admin_url(
                                            'admin-ajax.php'
                                        ) ?>?action=wcs_view_snapshot&nonce=<?=wp_create_nonce('snapshot')?>&id=<?= $item->getId() ?>"
                                           target="_blank"
                                           class="wcs4-view-button"
                                        >
                                            <?= __('Preview') ?>
                                        </a>
                                        |
                                    </span>
                                    <span class="download">
                                        <a href="<?= $item->getUrl() ?>"
                                           target="_blank"
                                           class="wcs4-view-button"
                                        >
                                            <?= __('Reload') ?>
                                        </a>
                                        |
                                    </span>
                                    <?php
                                    if (current_user_can(WCS4_SNAPSHOT_MANAGE_CAPABILITY)): ?>
                                        <span class="delete hide-if-no-js">
                                            <a href="#" class="wcs4-delete-button">
                                                <?= __('Delete', 'wcs4') ?>
                                            </a>
                                        </span>
                                    <?php
                                    endif; ?>
                                </div>
                                <button type="button" class="toggle-row">
                                    <span class="screen-reader-text"><?= __('Show more details') ?></span>
                                </button>
                            </td>
                            <td>
                                <small><?= $item->getQueryString() ?></small>
                            </td>
                            <td>
                                <?= $item->getVersion() ?>
                            </td>
                            <td data-colname="<?= __('Updated at', 'wcs4') ?>">
                                <?php
                                include __DIR__ . '/../_common/updated_at.php' ?>
                            </td>
                        </tr>
                    <?php
                    endforeach; ?>
                    </tbody>
                </table>
            </section>
        <?php
        endforeach; ?>
    <?php
    else: ?>
        <div class="wcs4-no-snapshots">
            <p><?= __('No snapshots', 'wcs4') ?></p>
        </div>
    <?php
    endif; ?>
</div>
