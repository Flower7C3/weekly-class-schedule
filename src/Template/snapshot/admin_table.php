<?php
/**
 * @var array $items
 * @var string $orderField
 * @var string $orderDirection
 */

use WCS4\Entity\Item;
use WCS4\Entity\Snapshot_Item;
use WCS4\Helper\Output;

?>
<div class="wcs4-day-content-wrapper"
     data-hash="<?= md5(serialize($items) . time() . $orderField . $orderDirection) ?>">
    <?php
    if ($items): ?>
        <?php
        $groups = [];
        /** @var Snapshot_Item $item */
        foreach ($items as $item) {
            switch ($orderField) {
                case 'operation':
                    $key = $item->getOperationLabel();
                    break;
                default:
                case 'updated-at':
                    $key = $item->getCreatedAt()->format('Y-m-d');
                    break;
            }
            $groups[$key][] = $item;
        }
        if ($orderDirection === 'desc') {
            krsort($groups);
        } else {
            ksort($groups);
        }
        foreach ($groups as $key => $group) {
            if ($orderDirection === 'desc') {
                krsort($groups[$key]);
            } else {
                ksort($groups[$key]);
            }
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
                        <th class="manage-column column-cb check-column"></th>
                        <?php
                        admin_th(
                            __('Action', 'wcs4'),
                            'action',
                            $orderDirection,
                            $orderField,
                        );
                        admin_th(
                            __('Title', 'wcs4'),
                            'title',
                            $orderDirection,
                            $orderField,
                        );
                        admin_th(__('Subject', 'wcs4'));
                        admin_th(__('Teacher', 'wcs4'));
                        admin_th(__('Student', 'wcs4'));
                        admin_th(
                            __('Location', 'wcs4'),
                        );
                        admin_th(
                            name: __('Version', 'wcs4'),
                            className: 'check-column',
                        );
                        admin_th(
                            __('Updated at', 'wcs4'),
                            'updated-at',
                            $orderDirection,
                            $orderField,
                        ); ?>
                    </tr>
                    </thead>
                    <tbody id="the-list-<?= $groupName ?>">
                    <?php
                    /** @var Snapshot_Item $item */
                    foreach ($groupData as $item): ?>
                        <tr data-scope="snapshot"
                            data-id="<?= $item->getId() ?>">
                            <th scope="row" class="check-column"><?= $item->getActionIcon() ?></th>
                            <td class="column-primary has-row-actions">
                                <?php
                                Output::admin_search_link(
                                    'search_wcs4_snapshot_log_action',
                                    $item->getAction(),
                                    $item->getActionLabel()
                                ); ?>
                                <div class="row-actions">
                                    <span class="download">
                                        <a href="<?= admin_url(
                                            'admin-ajax.php?action=wcs_view_snapshot'
                                            . '&nonce=' . wp_create_nonce('snapshot')
                                            . '&id=' . $item->getId()
                                        ) ?>"
                                           target="_blank"
                                           class="wcs4-view-button"
                                        >
                                            <?= __('Preview', 'wcs4') ?>
                                        </a>
                                        |
                                    </span>
                                    <span class="download">
                                        <a href="<?= $item->getUrl() ?>"
                                           target="_blank"
                                           class="wcs4-view-button"
                                        >
                                            <?= __('Reload', 'wcs4') ?>
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
                                <?= $item->getTitle() ?>
                            </td>
                            <td data-colname="<?= __('Subject', 'wcs4') ?>">
                                <?php
                                Output::snapshot_item_admin_link(
                                    'search_wcs4_snapshot_subject_id',
                                    $item->getQueryEntityId('subject')
                                ); ?>
                            </td>
                            <td data-colname="<?= __('Teacher', 'wcs4') ?>">
                                <?php
                                Output::snapshot_item_admin_link(
                                    'search_wcs4_snapshot_teacher_id',
                                    $item->getQueryEntityId('teacher'),
                                ); ?>
                            </td>
                            <td data-colname="<?= __('Student', 'wcs4') ?>">
                                <?php
                                Output::snapshot_item_admin_link(
                                    'search_wcs4_snapshot_student_id',
                                    $item->getQueryEntityId('student')
                                ); ?>
                            </td>
                            <td>
                                <small><?php
                                    Output::admin_search_link(
                                        'search_wcs4_snapshot_log_location',
                                        $item->getQueryHash(),
                                        $item->getQueryString()
                                    ) ?></small>
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
