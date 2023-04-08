<?php
/**
 * @var array $items
 * @var string $orderby
 * @var string $order
 */

?>
<div class="wcs4-day-content-wrapper" data-hash="<?php
echo md5(serialize($items) . $orderby . $order) ?>">
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
            <section id="wcs4-journal-day-<?php
            echo $day; ?>">
                <h2>
                    <?php
                    echo $day; ?>
                    <span class="spinner"></span>
                </h2>
                <table class="wp-list-table widefat fixed striped wcs4-admin-journal-table">
                    <thead>
                    <tr>
                        <th class="column-primary sortable <?php
                        echo ($order === 'asc') ? 'asc' : 'desc'; ?><?php
                        if ('time' === $orderby): ?>
                                    sorted<?php
                        endif; ?>">
                            <a href="#" data-orderby="time" data-order="<?php
                            echo ($order === 'desc') ? 'asc' : 'desc'; ?>">
                                            <span><?php
                                                echo __('Start', 'wcs4'); ?> – <?php
                                                echo __('End', 'wcs4'); ?></span>
                                <span class="sorting-indicator"></span>
                            </a>
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
                                            echo __('Topic', 'wcs4'); ?></span>
                        </th>
                        <th scope="col" class="sortable <?php
                        echo ($order === 'asc') ? 'asc' : 'desc'; ?><?php
                        if ('updated-at' === $orderby): ?>
                                    sorted<?php
                        endif; ?>">
                            <a href="#" data-orderby="updated-at" data-order="<?php
                            echo ($order === 'desc') ? 'asc' : 'desc'; ?>">
                                            <span><?php
                                                echo __('Date', 'wcs4'); ?></span>
                                <span class="sorting-indicator"></span>
                            </a>
                        </th>
                    </tr>
                    </thead>
                    <tbody id="the-list-<?php
                    echo $day; ?>">
                    <?php
                    /** @var WCS_DB_Journal_Item $item */
                    foreach ($dayData as $item): ?>
                        <tr id="journal-<?php
                        echo $item->getId(); ?>">
                            <td class="column-primary<?php
                            if (current_user_can(WCS4_JOURNAL_MANAGE_CAPABILITY)) { ?> has-row-actions<?php
                            } ?>">
                                <?php
                                echo $item->getStartTime(); ?> – <?php
                                echo $item->getEndTime(); ?>
                                <?php
                                if (current_user_can(WCS4_JOURNAL_MANAGE_CAPABILITY)): ?>
                                    <div class="row-actions">
                                                    <span class="edit hide-if-no-js">
                                                        <a href="#" class="wcs4-edit-journal-button"
                                                           id="wcs4-edit-button-<?php
                                                           echo $item->getId(); ?>" data-journal-id="<?php
                                                        echo $item->getId(); ?>">
                                                            <?php
                                                            echo __('Edit', 'wcs4'); ?>
                                                        </a>
                                                        |
                                                    </span>
                                        <span class="copy hide-if-no-js">
                                                        <a href="#" class="wcs4-copy-journal-button"
                                                           id="wcs4-copy-button-<?php
                                                           echo $item->getId(); ?>" data-journal-id="<?php
                                                        echo $item->getId(); ?>">
                                                            <?php
                                                            echo __('Duplicate', 'wcs4'); ?>
                                                        </a>
                                                        |
                                                    </span>
                                        <span class="delete hide-if-no-js">
                                                        <a href="#" class="wcs4-delete-journal-button"
                                                           id=wcs4-delete-<?php
                                                           echo $item->getId(); ?>" data-journal-id="<?php
                                                        echo $item->getId(); ?>"
                                                           data-date="<?php
                                                           echo $item->getDate(); ?>">
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
                                    'search_wcs4_journal_subject_id',
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
                                                'search_wcs4_journal_teacher_id',
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
                                                'search_wcs4_journal_student_id',
                                                $item_student
                                            ); ?>
                                        </li>
                                    <?php
                                    endforeach; ?>
                                </ul>
                            </td>
                            <td data-colname="<?php
                            echo __('Topic', 'wcs4'); ?>">
                                <?php
                                echo $item->getTopic(); ?>
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
            </section>
        <?php
        endforeach; ?>
    <?php
    else: ?>
        <div class="wcs4-no-journals"><p><?php
                echo __('No journals', 'wcs4'); ?></p></div>
    <?php
    endif; ?>
</div>
