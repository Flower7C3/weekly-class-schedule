<?php
/**
 * @var array $items
 * @var string $weekday
 * @var string $orderby
 * @var string $order
 */

?>
<div class="wcs4-day-content-wrapper" data-hash="<?php
echo md5(serialize($items) . $orderby . $order) ?>">
    <?php
    if ($items): ?>
        <table class="wp-list-table widefat fixed striped wcs4-admin-schedule-table"
               id="wcs4-admin-table-day-<?php
               echo $weekday; ?>">
            <thead>
            <tr>
                <th title="<?php
                echo __('Visibility', 'wcs4'); ?>" class="manage-column column-cb check-column"></th>
                <th class="column-primary">
                    <span><?php
                        echo __('Start', 'wcs4'); ?> – <?php
                        echo __('End', 'wcs4'); ?></span>
                </th>
                <th scope="col">
                    <span><?php
                        echo __('Subject', 'wcs4'); ?></span>
                </th>
                <th scope="col">
                    <span><?php
                        echo __('Teacher', 'wcs4'); ?></span>
                </th>
                <th scope="col">
                    <span><?php
                        echo __('Student', 'wcs4'); ?></span>
                </th>
                <th scope="col">
                    <span><?php
                        echo __('Classroom', 'wcs4'); ?></span>
                </th>
                <th scope="col">
                    <span><?php
                        echo __('Notes', 'wcs4'); ?></span>
                </th>
                <th scope="col">
                    <span><?php
                        echo __('Date', 'wcs4'); ?></span>
                </th>
            </tr>
            </thead>
            <tbody id="the-list-<?php
            echo $weekday; ?>">
            <?php
            /** @var WCS_DB_Lesson_Item $item */
            foreach ($items as $item): ?>
                <tr id="lesson-<?php
                echo $item->getId(); ?>"
                    data-day="<?php
                    echo $item->getWeekday(); ?>"
                    class="<?php
                    if ($item->isVisible()) { ?>active<?php
                    } else { ?>inactive<?php
                    } ?>">
                    <th scope="row" class="check-column">
                        <a href="#" class="wcs4-visibility-lesson-button"
                           id="wcs4-<?php
                           if ($item->isVisible()): ?>hide<?php
                           else: ?>show<?php
                           endif; ?>-button-<?php
                           echo $item->getId(); ?>"
                           data-visible="<?php
                           if ($item->isVisible()): ?>true<?php
                           else: ?>false<?php
                           endif; ?>"
                           data-lesson-id="<?php
                           echo $item->getId(); ?>"
                           data-day="<?php
                           echo $item->getWeekday(); ?>">
                            <em class="dashicons dashicons-<?php
                            if ($item->isVisible()): ?>visibility<?php
                            else: ?>hidden<?php
                            endif; ?>"
                                title="<?php
                                echo $item->getVisibleText(); ?>"></em>
                        </a>
                    </th>
                    <td class="column-primary<?php
                    if (current_user_can(WCS4_SCHEDULE_MANAGE_CAPABILITY)) { ?> has-row-actions<?php
                    } ?>">
                        <?php
                        echo $item->getStartTime(); ?> – <?php
                        echo $item->getEndTime(); ?>
                        <?php
                        if (current_user_can(WCS4_SCHEDULE_MANAGE_CAPABILITY)): ?>
                            <div class="row-actions">
                                <span class="edit hide-if-no-js">
                                    <a href="#" class="wcs4-edit-lesson-button" id="wcs4-edit-button-<?php
                                    echo $item->getId(); ?>" data-lesson-id="<?php
                                    echo $item->getId(); ?>"
                                       data-day="<?php
                                       echo $item->getWeekday(); ?>">
                                        <?php
                                        echo __('Edit', 'wcs4'); ?>
                                    </a>
                                    |
                                </span>
                                <span class="copy hide-if-no-js">
                                    <a href="#" class="wcs4-copy-lesson-button" id="wcs4-copy-button-<?php
                                    echo $item->getId(); ?>" data-lesson-id="<?php
                                    echo $item->getId(); ?>"
                                       data-day="<?php
                                       echo $item->getWeekday(); ?>">
                                        <?php
                                        echo __('Duplicate', 'wcs4'); ?>
                                    </a>
                                    |
                                </span>
                                <span class="delete hide-if-no-js">
                                    <a href="#" class="wcs4-delete-lesson-button" id=wcs4-delete-<?php
                                    echo $item->getId(); ?>" data-lesson-id="<?php
                                    echo $item->getId(); ?>"
                                       data-day="<?php
                                       echo $item->getWeekday(); ?>">
                                        <?php
                                        echo __('Delete', 'wcs4'); ?>
                                    </a>
                                </span>
                            </div>
                        <?php
                        endif; ?>
                        <button type="button" class="toggle-row"><span class="screen-reader-text"><?php
                                _e('Show more details'); ?></span></button>
                    </td>
                    <td data-colname="<?php
                    echo __('Subject', 'wcs4'); ?>">
                        <?php
                        WCS_Output::item_admin_link(
                            'search_wcs4_lesson_subject_id',
                            $item->getSubject()
                        ); ?>
                    </td>
                    <td data-colname="<?php
                    echo __('Teacher', 'wcs4'); ?>">
                        <ul>
                            <?php
                            foreach ($item->getTeachers() as $item_teacher): ?>
                                <li>
                                    <?php
                                    WCS_Output::item_admin_link(
                                        'search_wcs4_lesson_teacher_id',
                                        $item_teacher
                                    ); ?>
                                </li>
                            <?php
                            endforeach; ?>
                        </ul>
                    </td>
                    <td data-colname="<?php
                    echo __('Student', 'wcs4'); ?>">
                        <ul>
                            <?php
                            foreach ($item->getStudents() as $item_student): ?>
                                <li>
                                    <?php
                                    WCS_Output::item_admin_link(
                                        'search_wcs4_lesson_student_id',
                                        $item_student
                                    ); ?>
                                </li>
                            <?php
                            endforeach; ?>
                        </ul>
                    </td>
                    <td data-colname="<?php
                    echo __('Classroom', 'wcs4'); ?>">
                        <?php
                        WCS_Output::item_admin_link(
                            'search_wcs4_lesson_classroom_id',
                            $item->getClassroom
                            ()
                        ); ?>
                    </td>
                    <td data-colname="<?php
                    echo __('Notes', 'wcs4'); ?>">
                        <?php
                        echo $item->getNotes(); ?>
                    </td>
                    <td data-colname="<?php
                    echo __('Updated at', 'wcs4'); ?>">
                        <?php
                        if ($item->getUpdatedAt()): ?>
                            <span title="<?php
                            printf(
                                __('Updated at %s by %s', 'wcs4'),
                                $item->getUpdatedAt()->format('Y-m-d H:i:s'),
                                $item->getUpdatedBy()->display_name ?: 'nn'
                            ); ?>">
                                <?php
                                echo $item->getUpdatedAt()->format('Y-m-d H:i:s'); ?>
                                <?php
                                echo $item->getUpdatedBy()->display_name; ?>
                            </span>
                        <?php
                        else: ?>
                            <span title="<?php
                            printf(
                                __('Created at %s by %s', 'wcs4'),
                                $item->getCreatedAt()->format('Y-m-d H:i:s'),
                                $item->getCreatedBy()->display_name ?: 'nn'
                            ); ?>">
                                <?php
                                echo $item->getCreatedAt()->format('Y-m-d H:i:s'); ?>
                                <?php
                                echo $item->getCreatedBy()->display_name; ?>
                            </span>
                        <?php
                        endif; ?>
                    </td>
                </tr>
            <?php
            endforeach; ?>
            </tbody>
        </table>
    <?php
    else: ?>
        <div class="wcs4-no-lessons"><p><?php
                echo __('No lessons', 'wcs4'); ?></p></div>
    <?php
    endif; ?>
</div>
