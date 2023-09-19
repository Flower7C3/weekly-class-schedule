<?php
/**
 * @var array $items
 * @var string $orderField
 * @var string $orderDirection
 */

use WCS4\Entity\Progress_Item;
use WCS4\Helper\Output;

?>
<div class="wcs4-day-content-wrapper" data-hash="<?= md5(serialize($items) . $orderField . $orderDirection) ?>">
    <?php
    if ($items): ?>
        <?php
        $groups = [];
        /** @var Progress_Item $item */
        foreach ($items as $item) {
            switch ($orderField) {
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
        if ($orderDirection === 'desc') {
            krsort($groups);
        } else {
            ksort($groups);
        }

        ?>
        <?php
        foreach ($groups as $groupName => $groupData): ?>
            <section id="wcs4_progress_day-<?= $groupName ?>">
                <h2>
                    <?= $groupName ?>
                    <span class="spinner"></span>
                </h2>
                <table class="wp-list-table widefat fixed striped wcs4-admin-progress-table">
                    <thead>
                    <tr>
                        <th title="<?= __('Type', 'wcs4') ?>" class="manage-column column-cb check-column"></th>
                        <?php
                        admin_th(
                            __('Start', 'wcs4') . ' - ' . __('End', 'wcs4'),
                            'time',
                            $orderDirection,
                            $orderField,
                        );
                        admin_th(
                            __('Subject', 'wcs4'),
                            'subject',
                            $orderDirection,
                            $orderField,
                        );
                        admin_th(
                            __('Teacher', 'wcs4'),
                            'teacher',
                            $orderDirection,
                            $orderField,
                        );
                        admin_th(
                            __('Student', 'wcs4'),
                            'student',
                            $orderDirection,
                            $orderField,
                        );
                        admin_th(
                            __('Improvements', 'wcs4'),
                        );
                        admin_th(
                            __('Indications', 'wcs4'),
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
                    /** @var Progress_Item $item */
                    foreach ($groupData as $item): ?>
                        <tr data-scope="progress"
                            data-id="<?= $item->getId() ?>">
                            <th scope="row" class="check-column">
                                <em class="<?= Progress_Item::typeIcon($item->getType()) ?>"
                                    title="<?= Progress_Item::typeLabel($item->getType()) ?>"></em>
                            </th>
                            <td class="column-primary
                                <?= (current_user_can(WCS4_JOURNAL_MANAGE_CAPABILITY)) ? ' has-row-actions' : '' ?>">
                                <?= $item->getStartDate() ?>
                                -
                                <?= $item->getEndDate() ?>
                                <?php
                                if (current_user_can(WCS4_JOURNAL_MANAGE_CAPABILITY)): ?>
                                    <div class="row-actions">
                                        <?php
                                        if ($item->isTypePeriodic()): ?>
                                            <span class="download hide-if-no-js">
                                            <a href="<?= admin_url(
                                                'admin-ajax.php?action=wcs_download_progresses_html'
                                                . '&nonce=' . wp_create_nonce('progress')
                                                . '&id=' . $item->getId()
                                            ) ?>"
                                               target="_blank"
                                               class="wcs4-download-progress-button"
                                            >
                                                <?= __('Download Progresses as HTML', 'wcs4') ?>
                                            </a>
                                            |
                                        </span>
                                        <?php
                                        endif; ?>
                                        <span class="edit hide-if-no-js">
                                            <a href="#" class="wcs4-edit-button">
                                                <?= __('Edit', 'wcs4') ?>
                                            </a>
                                            |
                                        </span>
                                        <span class="copy hide-if-no-js">
                                            <a href="#" class="wcs4-copy-button">
                                                <?= __('Duplicate', 'wcs4') ?>
                                            </a>
                                            |
                                        </span>
                                        <span class="delete hide-if-no-js">
                                            <a href="#" class="wcs4-delete-button">
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
                                                Output::item_admin_link(
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
                                            Output::item_admin_link(
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
                                Output::item_admin_link(
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
        <div class="wcs4-no-progresses">
            <p><?= __('No progresses', 'wcs4') ?></p>
        </div>
    <?php
    endif; ?>
</div>
