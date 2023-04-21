<?php
/**
 * @var array $items
 * @var string $weekday
 * @var string $independent
 * @var string $order_field
 * @var string $order_direction
 */

use WCS4\Entity\Lesson_Item;
use WCS4\Helper\Output;

?>
<div class="wcs4-day-content-wrapper"
     data-hash="<?= md5(serialize($items) .$independent. $order_field . $order_direction) ?>">
    <?php
    if ($items): ?>
        <table class="wp-list-table widefat fixed striped wcs4-admin-schedule-table"
               id="wcs4-admin-table-day-<?= $weekday ?>">
            <thead>
            <tr>
                <th title="<?= __('Visibility', 'wcs4') ?>" class="manage-column column-cb check-column"></th>
                <th title="<?= __('Independence', 'wcs4') ?>" class="manage-column column-cb check-column"></th>
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
                <tr id="lesson-<?= $item->getId() ?>"
                    data-type="schedule"
                    data-day="<?= $item->getWeekday() ?>"
                    data-id="<?= $item->getId() ?>"
                    class="<?= $item->isVisible() ? 'active' : 'inactive' ?>">
                    <th scope="row" class="check-column">
                        <a href="#" class="wcs4-visibility-button"
                           id="wcs4-<?= ($item->isVisible()) ? 'hide' : 'show' ?>-button-<?= $item->getId(); ?>"
                           data-visible="<?= ($item->isVisible()) ? 'true' : 'false' ?>"
                           data-lesson-id="<?= $item->getId(); ?>"
                           data-day="<?= $item->getWeekday(); ?>">
                            <em class="dashicons dashicons-<?= ($item->isVisible()) ? 'visibility' : 'hidden' ?>"
                                title="<?= $item->getVisibleText(); ?>"></em>
                        </a>
                    </th>
                    <th scope="row" class="check-column">
                        <?php if($item->isIndependent()): ?>
                            <span class="dashicons dashicons-calendar"></span>
                        <?php else: ?>
                            <span class="dashicons dashicons-shield-alt"></span>
                        <?php endif; ?>
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
                            'search_wcs4_lesson_subject_id',
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
                                        'search_wcs4_lesson_teacher_id',
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
                                        'search_wcs4_lesson_student_id',
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
                            'search_wcs4_lesson_classroom_id',
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
