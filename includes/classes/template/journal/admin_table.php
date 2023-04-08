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
        /** @var WCS_DB_Journal_Item $item */
        foreach ($items as $item) {
            $days[$item->getDate()][] = $item;
        }
        ?>
        <?php
        foreach ($days as $day => $dayData): ?>
            <section id="wcs4-journal-day-<?= $day ?>">
                <h2>
                    <?= $day ?>
                    <span class="spinner"></span>
                </h2>
                <table class="wp-list-table widefat fixed striped wcs4-admin-journal-table">
                    <thead>
                    <tr>
                        <th class="column-primary sortable <?= ($order === 'asc') ? 'asc' : 'desc' ?><?= ('time' === $orderby) ? ' sorted' : '' ?>">
                            <a href="#" data-orderby="time" data-order="<?= ($order === 'desc') ? 'asc' : 'desc' ?>">
                                <span><?= __('Start', 'wcs4') ?> – <?= __('End', 'wcs4') ?></span>
                                <span class="sorting-indicator"></span>
                            </a>
                        </th>
                        <th scope="col">
                            <?= __('Subject', 'wcs4') ?>
                        </th>
                        <th scope="col">
                            <?= __('Teacher', 'wcs4') ?>
                        </th>
                        <th scope="col">
                            <?= __('Student', 'wcs4') ?>
                        </th>
                        <th scope="col">
                            <?= __('Topic', 'wcs4') ?>
                        </th>
                        <th scope="col"
                            class="sortable <?= ($order === 'asc') ? 'asc' : 'desc' ?><?= ('updated-at' === $orderby) ? ' sorted' : '' ?>">
                            <a href="#" data-orderby="updated-at"
                               data-order="<?= ($order === 'desc') ? 'asc' : 'desc' ?>">
                                <?= __('Date', 'wcs4') ?>
                                <span class="sorting-indicator"></span>
                            </a>
                        </th>
                    </tr>
                    </thead>
                    <tbody id="the-list-<?= $day ?>">
                    <?php
                    /** @var WCS_DB_Journal_Item $item */
                    foreach ($dayData as $item): ?>
                        <tr id="journal-<?= $item->getId() ?>"
                            data-id="<?= $item->getId() ?>">
                            <td class="column-primary
                                <?= (current_user_can(WCS4_JOURNAL_MANAGE_CAPABILITY)) ? ' has-row-actions' : '' ?>">
                                <?= $item->getStartTime() ?> – <?= $item->getEndTime() ?>
                                <?php
                                if (current_user_can(WCS4_JOURNAL_MANAGE_CAPABILITY)): ?>
                                    <div class="row-actions">
                                                    <span class="edit hide-if-no-js">
                                                        <a href="#" class="wcs4-edit-journal-button"
                                                           id="wcs4-edit-button-<?= $item->getId() ?>"
                                                           data-journal-id="<?= $item->getId() ?>">
                                                            <?= __('Edit', 'wcs4') ?>
                                                        </a>
                                                        |
                                                    </span>
                                        <span class="copy hide-if-no-js">
                                                        <a href="#" class="wcs4-copy-journal-button"
                                                           id="wcs4-copy-button-<?= $item->getId() ?>"
                                                           data-journal-id="<?= $item->getId() ?>">
                                                            <?= __('Duplicate', 'wcs4') ?>
                                                        </a>
                                                        |
                                                    </span>
                                        <span class="delete hide-if-no-js">
                                                        <a href="#" class="wcs4-delete-journal-button"
                                                           id=wcs4-delete-<?= $item->getId() ?>"
                                                           data-journal-id="<?= $item->getId() ?>"
                                                           data-date="<?= $item->getDate() ?>">
                                                            <?= __('Delete', 'wcs4') ?>
                                                        </a>
                                                    </span>
                                    </div>
                                <?php
                                endif; ?>
                                <button type="button" class="toggle-row"><span class="screen-reader-text"><?php
                                        _e('Show more details'); ?></span></button>
                            </td>
                            <td data-colname="<?= __('Subject', 'wcs4') ?>">
                                <?php
                                WCS_Output::item_admin_link(
                                    'search_wcs4_journal_subject_id',
                                    $item->getSubject()
                                ); ?>
                            </td>
                            <td data-colname="<?= __('Teacher', 'wcs4') ?>">
                                <ul>
                                    <?php
                                    foreach ($item->getTeachers() as $item_teacher): ?>
                                        <li>
                                            <?php
                                            WCS_Output::item_admin_link(
                                                'search_wcs4_journal_teacher_id',
                                                $item_teacher
                                            ); ?>
                                        </li>
                                    <?php
                                    endforeach; ?>
                                </ul>
                            </td>
                            <td data-colname="<?= __('Student', 'wcs4') ?>">
                                <ul>
                                    <?php
                                    foreach ($item->getStudents() as $item_student): ?>
                                        <li>
                                            <?php
                                            WCS_Output::item_admin_link(
                                                'search_wcs4_journal_student_id',
                                                $item_student
                                            ); ?>
                                        </li>
                                    <?php
                                    endforeach; ?>
                                </ul>
                            </td>
                            <td data-colname="<?= __('Topic', 'wcs4') ?>">
                                <?= $item->getTopic() ?>
                            </td>
                            <td data-colname="<?= __('Updated at', 'wcs4') ?>">
                                <?php
                                if ($item->getUpdatedAt()): ?>
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
        <div class="wcs4-no-journals">
            <p><?= __('No journals', 'wcs4') ?></p>
        </div>
    <?php
    endif; ?>
</div>
