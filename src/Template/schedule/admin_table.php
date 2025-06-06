<?php
/**
 * @var array $items
 * @var string $weekday
 * @var string $subject
 * @var string $teacher
 * @var string $collisionDetection
 * @var string $orderField
 * @var string $orderDirection
 */

use WCS4\Entity\Lesson_Item;
use WCS4\Helper\Output;

$subject_filter_field_id = 'search_wcs4_schedule_subject_id';
$teacher_filter_field_id = 'search_wcs4_schedule_teacher_id';
?>
<div class="wcs4-day-content-wrapper"
     data-hash="<?= md5(serialize($items) . $collisionDetection. $subject .$teacher  . $orderField . $orderDirection) ?>">
    <?php
    include __DIR__ . '/../_common/quick_filter.php'; ?>
    <?php
    if ($items): ?>
        <table class="wp-list-table widefat fixed striped wcs4-admin-schedule-table"
               id="wcs4-admin-table-day-<?= $weekday ?>">
            <thead>
            <tr>
                <th title="<?= __('Collision detection', 'wcs4') ?>" class="manage-column column-cb check-column"></th>
                <th title="<?= __('Visibility', 'wcs4') ?>" class="manage-column column-cb check-column"></th>
                <th class="column-primary">
                    <span><?= __('Start', 'wcs4') ?> – <?= __('End', 'wcs4') ?></span>
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
                    <span><?= __('Classroom', 'wcs4') ?></span>
                </th>
                <th scope="col">
                    <span><?= __('Notes', 'wcs4') ?></span>
                </th>
                <th scope="col">
                    <span><?= __('Updated at', 'wcs4') ?></span>
                </th>
            </tr>
            </thead>
            <tbody id="the-list-<?= $weekday ?>">
            <?php
            /** @var Lesson_Item $item */
            foreach ($items as $item): ?>
                <tr data-scope="schedule"
                    data-day="<?= $item->getWeekday() ?>"
                    data-id="<?= $item->getId() ?>"
                    class="<?= $item->isVisible() ? 'active' : 'inactive' ?>">
                    <th scope="row" class="check-column">
                        <em class="<?= Lesson_Item::collisionDetectionIcon($item->isCollisionDetection()) ?>"
                            title="<?= Lesson_Item::collisionDetectionLabel($item->isCollisionDetection()) ?>"></em>
                    </th>
                    <th scope="row" class="check-column">
                        <a href="#" class="wcs4-visibility-button"
                           id="wcs4-<?= ($item->isVisible()) ? 'hide' : 'show' ?>-button-<?= $item->getId() ?>"
                           data-visible="<?= ($item->isVisible()) ? 'true' : 'false' ?>"
                        >
                            <em class="<?= Lesson_Item::visibilityIcon($item->isVisible()) ?>"
                                title="<?= Lesson_Item::visibilityLabel($item->isVisible()) ?>"></em>
                        </a>
                    </th>
                    <td class="column-primary
                    <?= (current_user_can(WCS4_SCHEDULE_MANAGE_CAPABILITY)) ? 'has-row-actions' : '' ?>">
                        <?= $item->getStartTime() ?> – <?= $item->getEndTime() ?>
                        <?php
                        if (current_user_can(WCS4_SCHEDULE_MANAGE_CAPABILITY)): ?>
                            <div class="row-actions">
                                <span class="edit hide-if-no-js">
                                    <a href="#" class="wcs4-edit-button"
                                       data-day="<?= $item->getWeekday() ?>">
                                        <?= __('Edit', 'wcs4') ?>
                                    </a>
                                    |
                                </span>
                                <span class="copy hide-if-no-js">
                                    <a href="#" class="wcs4-copy-button"
                                       data-day="<?= $item->getWeekday() ?>">
                                        <?= __('Duplicate', 'wcs4') ?>
                                    </a>
                                    |
                                </span>
                                <span class="delete hide-if-no-js">
                                    <a href="#" class="wcs4-delete-button"
                                       data-day="<?= $item->getWeekday() ?>">
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
                        Output::item_admin_link(
                            'search_wcs4_schedule_subject_id',
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
                                        'search_wcs4_schedule_teacher_id',
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
                                        'search_wcs4_schedule_student_id',
                                        $item_student
                                    ); ?>
                                </li>
                            <?php
                            endforeach; ?>
                        </ul>
                    </td>
                    <td data-colname="<?= __('Classroom', 'wcs4') ?>">
                        <?php
                        Output::item_admin_link(
                            'search_wcs4_schedule_classroom_id',
                            $item->getClassroom
                            ()
                        ); ?>
                    </td>
                    <td data-colname="<?= __('Notes', 'wcs4') ?>">
                        <?= $item->getNotes() ?>
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
    <?php
    else: ?>
        <div class="wcs4-no-lessons">
            <p><?= __('No lessons', 'wcs4') ?></p>
        </div>
    <?php
    endif; ?>
</div>
