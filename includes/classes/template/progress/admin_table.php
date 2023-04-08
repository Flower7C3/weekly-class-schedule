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
        /** @var WCS_DB_Progress_Item $item */
        foreach ($items as $item) {
            $days[$item->getDate()][] = $item;
        }
        ?>
        <?php
        foreach ($days as $day => $dayData): ?>
            <section id="wcs4-progress-day-<?php
            echo $day; ?>">
                <h2>
                    <?php
                    echo $day; ?>
                    <span class="spinner"></span>
                </h2>
                <table class="wp-list-table widefat fixed striped wcs4-admin-progress-table">
                    <thead>
                    <tr>
                        <th class="column-primary sortable <?php
                        echo ($order === 'asc') ? 'asc' : 'desc'; ?><?php
                        if ('time' === $orderby): ?>sorted<?php
                        endif; ?>">
                            <a href="#" data-orderby="time" data-order="<?php
                            echo ($order === 'desc') ? 'asc' : 'desc'; ?>">
                            <span>
                                <?php
                                echo __('Type', 'wcs4'); ?> / <?php
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
                                echo __('Improvements', 'wcs4'); ?></span>
                        </th>
                        <th scope="col">
                            <span><?php
                                echo __('Indications', 'wcs4'); ?></span>
                        </th>
                        <th scope="col" class="sortable <?php
                        echo ($order === 'asc') ? 'asc' : 'desc'; ?><?php
                        if ('updated-at' === $orderby): ?>sorted<?php
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
                    /** @var WCS_DB_Progress_Item $item */
                    foreach ($dayData as $item): ?>
                        <tr id="progress-<?php
                        echo $item->getId(); ?>">
                            <td class="column-primary<?php
                            if (current_user_can(WCS4_JOURNAL_MANAGE_CAPABILITY)) { ?> has-row-actions<?php
                            } ?>">
                                <?php
                                if ($item->isTypePartial()): ?>
                                    <?php
                                    echo _x('Partial', 'item type', 'wcs4'); ?>
                                <?php
                                elseif ($item->isTypePeriodic()): ?>
                                    <?php
                                    echo _x('Periodic', 'item type', 'wcs4'); ?>
                                <?php
                                else: ?>
                                    <?php
                                    echo _x('undefined', 'item type', 'wcs4'); ?>
                                <?php
                                endif; ?>
                                <?php
                                if ( $item->isTypePeriodic()): ?>
                                    <?php
                                    echo $item->getStartDate(); ?>
                                    -
                                    <?php
                                    echo $item->getEndDate(); ?>
                                <?php
                                endif; ?>
                                <?php
                                if (current_user_can(WCS4_JOURNAL_MANAGE_CAPABILITY)): ?>
                                    <div class="row-actions">
                                        <?php
                                        if ($item->isTypePeriodic()): ?>
                                            <span class="download hide-if-no-js">
                                            <a href="<?php
                                            echo admin_url(
                                                'admin-ajax.php'
                                            ); ?>?action=wcs_download_progress_html&id=<?php
                                            echo $item->getId(); ?>"
                                               target="_blank"
                                               class="wcs4-download-progress-button"
                                            >
                                                <?php
                                                echo __('Download progresses as HTML', 'wcs4'); ?>
                                            </a>
                                            |
                                        </span>
                                        <?php
                                        endif; ?>
                                        <span class="edit hide-if-no-js">
                                            <a href="#" class="wcs4-edit-progress-button"
                                               id="wcs4-edit-button-<?php
                                               echo $item->getId(); ?>" data-progress-id="<?php
                                            echo $item->getId(); ?>">
                                                <?php
                                                echo __('Edit', 'wcs4'); ?>
                                            </a>
                                            |
                                        </span>
                                        <span class="copy hide-if-no-js">
                                            <a href="#" class="wcs4-copy-progress-button"
                                               id="wcs4-copy-button-<?php
                                               echo $item->getId(); ?>" data-progress-id="<?php
                                            echo $item->getId(); ?>">
                                                <?php
                                                echo __('Duplicate', 'wcs4'); ?>
                                            </a>
                                            |
                                        </span>
                                        <span class="delete hide-if-no-js">
                                            <a href="#" class="wcs4-delete-progress-button"
                                               id=wcs4-delete-<?php
                                               echo $item->getId(); ?>" data-progress-id="<?php
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
                            <td data-colname="<?php
                            echo __('Teacher', 'wcs4'); ?>">
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
                            <td data-colname="<?php
                            echo __('Student', 'wcs4'); ?>">
                                <?php
                                WCS_Output::item_admin_link(
                                    'search_wcs4_progress_student_id',
                                    $item->getStudent()
                                ); ?>
                            </td>
                            <td data-colname="<?php
                            echo __('Improvements', 'wcs4'); ?>">
                                <?php
                                echo $item->getImprovements(); ?>
                            </td>
                            <td data-colname="<?php
                            echo __('Indications', 'wcs4'); ?>">
                                <?php
                                echo $item->getIndications(); ?>
                            </td>
                            <td data-colname="<?php
                            echo __('Updated at', 'wcs4'); ?>">
                                <?php
                                if ($item->getUpdatedAt() instanceof DateTimeInterface): ?>
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
        <div class="wcs4-no-progresses"><p><?php
                echo __('No progresses', 'wcs4'); ?></p></div>
    <?php
    endif; ?>
</div>
