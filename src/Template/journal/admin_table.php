<?php
/**
 * @var array $items
 * @var string $order_field
 * @var string $order_direction
 */

use WCS4\Entity\Journal_Item;
use WCS4\Helper\Output;

$subject_filter_field_id = 'search_wcs4_journal_subject_id';
$teacher_filter_field_id = 'search_wcs4_journal_teacher_id';
?>
<div class="wcs4-day-content-wrapper" data-hash="<?= md5(serialize($items) . $order_field . $order_direction) ?>">
    <?php
    include __DIR__ . '/../_common/quick_filter.php'; ?>
    <?php
    if ($items): ?>
        <?php
        $groups = [];
        /** @var Journal_Item $item */
        foreach ($items as $item) {
            switch ($order_field) {
                case 'time':
                    $key = $item->getDate();
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
        ?>
        <?php
        foreach ($groups as $groupName => $groupData): ?>
            <section id="wcs4_journal_day-<?= $groupName ?>">
                <h2>
                    <?= $groupName ?>
                    <span class="spinner"></span>
                </h2>
                <table class="wp-list-table widefat fixed striped wcs4-admin-journal-table">
                    <thead>
                    <tr>
                        <th title="<?= __('Type', 'wcs4') ?>" class="manage-column column-cb check-column"></th>
                        <?php
                        admin_th(
                            __('Start', 'wcs4') . ' - ' . __('End', 'wcs4'),
                            'time',
                            $order_direction,
                            $order_field,
                        );
                        admin_th(
                            __('Subject', 'wcs4'),
                            'subject',
                            $order_direction,
                            $order_field,
                        );
                        admin_th(
                            __('Teacher', 'wcs4'),
                            'teacher',
                            $order_direction,
                            $order_field,
                        );
                        admin_th(
                            __('Student', 'wcs4'),
                            'student',
                            $order_direction,
                            $order_field,
                        );
                        admin_th(
                            __('Topic', 'wcs4'),
                        );
                        admin_th(
                            __('Updated at', 'wcs4'),
                            'updated-at',
                            $order_direction,
                            $order_field,
                        ); ?>
                    </tr>
                    </thead>
                    <tbody id="the-list-<?= $groupName ?>">
                    <?php
                    /** @var Journal_Item $item */
                    foreach ($groupData as $item): ?>
                        <tr data-scope="journal"
                            data-id="<?= $item->getId() ?>">
                            <th scope="row" class="check-column">
                                <em class="<?= Journal_Item::typeIcon($item->getType()) ?>"
                                    title="<?= Journal_Item::typeLabel($item->getType()) ?>"></em>
                            </th>
                            <td class="column-primary
                                <?= (current_user_can(WCS4_JOURNAL_MANAGE_CAPABILITY)) ? ' has-row-actions' : '' ?>">
                                <?= $item->getStartTime() ?> – <?= $item->getEndTime() ?>
                                <?php
                                if (current_user_can(WCS4_JOURNAL_MANAGE_CAPABILITY)): ?>
                                    <div class="row-actions">
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
                                <button type="button" class="toggle-row"><span class="screen-reader-text"><?php
                                        _e('Show more details'); ?></span></button>
                            </td>
                            <td data-colname="<?= __('Subject', 'wcs4') ?>">
                                <?php
                                Output::item_admin_link(
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
                                            Output::item_admin_link(
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
                                            Output::item_admin_link(
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
        <div class="wcs4-no-journals">
            <p><?= __('No journals', 'wcs4') ?></p>
        </div>
    <?php
    endif; ?>
</div>
