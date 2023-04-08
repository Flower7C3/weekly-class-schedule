<?php
/**
 * @var array $items
 * @var string $orderby
 * @var string $order
 */

?>
<div class="wcs4-day-content-wrapper" data-hash="<?= md5(serialize($items) . $orderby . $order) ?>">
    <?php
    if ($items): ?>
        <?php
        $days = [];
        /** @var WCS_DB_Progress_Item $item */
        foreach ($items as $item) {
            $days[$item->getDate()][] = $item;
        }
        ?>
        <?php
        foreach ($days as $day => $dayData): ?>
            <section id="wcs4-progress-day-<?= $day ?>">
                <h2>
                    <?= $day ?>
                    <span class="spinner"></span>
                </h2>
                <table class="wp-list-table widefat fixed striped wcs4-admin-progress-table">
                    <thead>
                    <tr>
                        <th class="column-primary sortable <?= ($order === 'asc') ? 'asc' : 'desc' ?><?= ('time' === $orderby) ? ' sorted' : '' ?>">
                            <a href="#" data-orderby="time" data-order="<?= ($order === 'desc') ? 'asc' : 'desc' ?>">
                            <span>
                                <?= __('Type', 'wcs4') ?> / <?= __('Start', 'wcs4') ?> – <?= __(
                                    'End',
                                    'wcs4'
                                ) ?></span>
                                <span class="sorting-indicator"></span>
                            </a>
                        </th>
                        <th scope="col">
                            <span><?= __('Subject', 'wcs4') ?></span>
                        </th>
                        <th scope="col">
                            <span><?= __('Teacher', 'wcs4') ?></span>
                        </th>
                        <th scope="col">
                            <span><?= __('Student', 'wcs4') ?></span>
                        </th>
                        <th scope="col">
                            <span><?= __('Improvements', 'wcs4') ?></span>
                        </th>
                        <th scope="col">
                            <span><?= __('Indications', 'wcs4') ?></span>
                        </th>
                        <th scope="col"
                            class="sortable <?= ($order === 'asc') ? 'asc' : 'desc' ?><?= ('updated-at' === $orderby) ? ' sorted' : '' ?>">
                            <a href="#" data-orderby="updated-at"
                               data-order="<?= ($order === 'desc') ? 'asc' : 'desc' ?>">
                                <span><?= __('Date', 'wcs4') ?></span>
                                <span class="sorting-indicator"></span>
                            </a>
                        </th>
                    </tr>
                    </thead>
                    <tbody id="the-list-<?= $day ?>">
                    <?php
                    /** @var WCS_DB_Progress_Item $item */
                    foreach ($dayData as $item): ?>
                        <tr id="progress-<?= $item->getId() ?>" data-id="<?= $item->getId() ?>">
                            <td class="column-primary
                                <?= (current_user_can(WCS4_JOURNAL_MANAGE_CAPABILITY)) ? ' has-row-actions' : '' ?>">
                                <?php
                                if ($item->isTypePartial()): ?>
                                    <?= _x('Partial', 'item type', 'wcs4') ?>
                                <?php
                                elseif ($item->isTypePeriodic()): ?>
                                    <?= _x('Periodic', 'item type', 'wcs4') ?>
                                <?php
                                else: ?>
                                    <?= _x('undefined', 'item type', 'wcs4') ?>
                                <?php
                                endif; ?>
                                <?php
                                if ($item->isTypePeriodic()): ?>
                                    <?= $item->getStartDate() ?>
                                    -
                                    <?= $item->getEndDate() ?>
                                <?php
                                endif; ?>
                                <?php
                                if (current_user_can(WCS4_JOURNAL_MANAGE_CAPABILITY)): ?>
                                    <div class="row-actions">
                                        <?php
                                        if ($item->isTypePeriodic()): ?>
                                            <span class="download hide-if-no-js">
                                            <a href="<?= admin_url(
                                                'admin-ajax.php'
                                            ) ?>?action=wcs_download_progress_html&id=<?= $item->getId() ?>"
                                               target="_blank"
                                               class="wcs4-download-progress-button"
                                            >
                                                <?= __('Download progresses as HTML', 'wcs4') ?>
                                            </a>
                                            |
                                        </span>
                                        <?php
                                        endif; ?>
                                        <span class="edit hide-if-no-js">
                                            <a href="#" class="wcs4-edit-progress-button"
                                               id="wcs4-edit-button-<?= $item->getId() ?>"
                                               data-progress-id="<?= $item->getId() ?>">
                                                <?= __('Edit', 'wcs4') ?>
                                            </a>
                                            |
                                        </span>
                                        <span class="copy hide-if-no-js">
                                            <a href="#" class="wcs4-copy-progress-button"
                                               id="wcs4-copy-button-<?= $item->getId() ?>"
                                               data-progress-id="<?= $item->getId() ?>">
                                                <?= __('Duplicate', 'wcs4') ?>
                                            </a>
                                            |
                                        </span>
                                        <span class="delete hide-if-no-js">
                                            <a href="#" class="wcs4-delete-progress-button"
                                               id=wcs4-delete-<?= $item->getId() ?>"
                                               data-progress-id="<?= $item->getId() ?>"
                                               data-date="<?= $item->getDate() ?>">
                                                <?= __('Delete', 'wcs4') ?>
                                            </a>
                                        </span>
                                    </div>
                                <?php
                                endif; ?>
                                <button type="button" class="toggle-row">
                                    <span class="screen-reader-text"><?= __('Show more details') ?></span>
                                </button>
                            </td>
                            <td data-colname="<?= __('Subject', 'wcs4') ?>">
                                <?php
                                if ($item->isTypePartial()): ?>
                                    <?php
                                    WCS_Output::item_admin_link(
                                        'search_wcs4_progress_subject_id',
                                        $item->getSubject()
                                    ); ?>
                                <?php
                                else: ?>
                                    -
                                <?php
                                endif; ?>
                            </td>
                            <td data-colname="<?= __('Teacher', 'wcs4') ?>">
                                <ul>
                                    <?php
                                    foreach ($item->getTeachers() as $item_teacher): ?>
                                        <li>
                                            <?php
                                            WCS_Output::item_admin_link(
                                                'search_wcs4_progress_teacher_id',
                                                $item_teacher
                                            ); ?>
                                        </li>
                                    <?php
                                    endforeach; ?>
                                </ul>
                            </td>
                            <td data-colname="<?= __('Student', 'wcs4') ?>">
                                <?php
                                WCS_Output::item_admin_link(
                                    'search_wcs4_progress_student_id',
                                    $item->getStudent()
                                ); ?>
                            </td>
                            <td data-colname="<?= __('Improvements', 'wcs4') ?>">
                                <?= $item->getImprovements() ?>
                            </td>
                            <td data-colname="<?= __('Indications', 'wcs4') ?>">
                                <?= $item->getIndications() ?>
                            </td>
                            <td data-colname="<?= __('Updated at', 'wcs4') ?>">
                                <?php
                                if ($item->getUpdatedAt() instanceof DateTimeInterface): ?>
                                    <span title="<?= sprintf(
                                        __('Updated at %s by %s', 'wcs4'),
                                        $item->getUpdatedAt()->format('Y-m-d H:i:s'),
                                        $item->getUpdatedBy()->display_name ?: 'nn'
                                    ) ?>">
                                        <?= $item->getUpdatedAt()->format('Y-m-d H:i:s') ?>
                                        <?= $item->getUpdatedBy()->display_name ?>
                                    </span>
                                <?php
                                else: ?>
                                    <span title="<?= sprintf(
                                        __('Created at %s by %s', 'wcs4'),
                                        $item->getCreatedAt()->format('Y-m-d H:i:s'),
                                        $item->getCreatedBy()->display_name ?: 'nn'
                                    ) ?>">
                                        <?= $item->getCreatedAt()->format('Y-m-d H:i:s') ?>
                                        <?= $item->getCreatedBy()->display_name ?>
                                    </span>
                                <?php
                                endif; ?>
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
        <div class="wcs4-no-progresses">
            <p><?= __('No progresses', 'wcs4') ?></p>
        </div>
    <?php
    endif; ?>
</div>
