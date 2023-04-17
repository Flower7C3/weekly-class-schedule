<?php
/**
 * @var array $items
 * @var string $order_field
 * @var string $order_direction
 */

use WCS4\Entity\Snapshot_Item;
use WCS4\Entity\Progress_Item;

?>
<div class="wcs4-day-content-wrapper" data-hash="<?= md5(serialize($items) . $order_field . $order_direction) ?>">
    <?php
    if ($items): ?>
        <?php
        $groups = [];
        /** @var Progress_Item $item */
        foreach ($items as $item) {
            $key = $item->getCreatedAt()->format('Y-m-d');
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
                            __('Title', 'wcs4'),
                            'title',
                            $order_direction,
                            $order_field
                        );
                        admin_th(
                            __('Location', 'wcs4')
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
                                <?= $item->getTitle() ?>
                                <div class="row-actions">
                                    <span class="download hide-if-no-js">
                                        <a href="<?= admin_url(
                                            'admin-ajax.php'
                                        ) ?>?action=wcs_view_snapshot&id=<?= $item->getId() ?>"
                                           target="_blank"
                                           class="wcs4-view-button"
                                        >
                                            <?= __('Cached', 'wcs4') ?>
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
                            <td class="has-row-actions">
                                <?= $item->getPage() ?>/<?= $item->getAction() ?>
                                <br>
                                <small><?= $item->getParamsRaw() ?></small>
                                <div class="row-actions">
                                    <div class="download hide-if-no-js">
                                        <a href="<?= $item->getUrl() ?>"
                                           target="_blank"
                                           class="wcs4-view-button"
                                        >
                                            <?= __('Current', 'wcs4') ?>
                                        </a>
                                    </div>
                                </div>
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
