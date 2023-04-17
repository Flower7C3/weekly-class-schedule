<?php
/**
 * @var array $items
 * @var string $order_field
 * @var string $order_direction
 */

?>
<div class="wcs4-day-content-wrapper" data-hash="<?= md5(serialize($items) . $order_field . $order_direction) ?>">
    <?php
    if ($items): ?>
        <?php
        $groups = [];
        /** @var WCS_DB_Progress_Item $item */
        foreach ($items as $item) {
            switch ($order_field) {
                case 'time':
                    $key = $item->getStartOrType();
                    break;
                case 'subject':
                    $key = $item->getSubject()->getNameFirstLetter();
                    break;
                case 'teacher':
                    $key = $item->getTeacher()->getNameFirstLetter();
                    break;
                case 'student':
                    $key = $item->getStudent()->getNameFirstLetter();
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
            <section id="wcs4-progress-day-<?= $groupName ?>">
                <h2>
                    <?= $groupName ?>
                    <span class="spinner"></span>
                </h2>
                <table class="wp-list-table widefat fixed striped wcs4-admin-progress-table">
                    <thead>
                    <tr>
                        <?php
                        admin_th(
                            __('Type', 'wcs4') . ' / ' . __('Start', 'wcs4') . ' - ' . __('End', 'wcs4'),
                            'time',
                            $order_direction,
                            $order_field
                        );
                        admin_th(
                            __('Subject', 'wcs4'),
                            'subject',
                            $order_direction,
                            $order_field
                        );
                        admin_th(
                            __('Teacher', 'wcs4'),
                            'teacher',
                            $order_direction,
                            $order_field
                        );
                        admin_th(
                            __('Student', 'wcs4'),
                            'student',
                            $order_direction,
                            $order_field
                        );
                        admin_th(__('Indications', 'wcs4'));
                        admin_th(__('Improvements', 'wcs4'));
                        admin_th(
                            __('Date', 'wcs4'),
                            'updated-at',
                            $order_direction,
                            $order_field
                        ); ?>
                    </tr>
                    </thead>
                    <tbody id="the-list-<?= $groupName ?>">
                    <?php
                    /** @var WCS_DB_Progress_Item $item */
                    foreach ($groupData as $item): ?>
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
                                    <ul>
                                        <?php
                                        foreach ($item->getSubjects() as $item_subject): ?>
                                            <li>
                                                <?php
                                                WCS_Output::item_admin_link(
                                                    'search_wcs4_progress_subject_id',
                                                    $item_subject
                                                ); ?>
                                            </li>
                                        <?php
                                        endforeach; ?>
                                    </ul>
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
                            <td data-colname="<?= __('Indications', 'wcs4') ?>">
                                <?= $item->getIndications() ?>
                            </td>
                            <td data-colname="<?= __('Improvements', 'wcs4') ?>">
                                <?= $item->getImprovements() ?>
                            </td>
                            <td data-colname="<?= __('Updated at', 'wcs4') ?>">
                                <?php include __DIR__.'/../_common/updated_at.php' ?>
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
