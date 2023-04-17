<form id="wcs4-lessons-filter" class="results-filter" method="get" action="">
    <input id="search_wcs4_page" type="hidden" name="page" value="<?php
    echo $_GET['page']; ?>"/>
    <div class="search-box">
        <span class="alignleft">
            <label class="screen-reader-text" for="search_wcs4_lesson_subject_id"><?php
                _e('Subject', 'wcs4'); ?></label>
            <?php
            echo WCS_Admin::generate_admin_select_list(
                'subject',
                'search_wcs4_lesson_subject_id',
                'subject',
                (int)$_GET['subject']
            ); ?>
        </span>
        <span class="alignleft">
            <label class="screen-reader-text" for="search_wcs4_lesson_teacher_id"><?php
                _e('Teacher', 'wcs4'); ?></label>
            <?php
            echo WCS_Admin::generate_admin_select_list(
                'teacher',
                'search_wcs4_lesson_teacher_id',
                'teacher',
                (int)$_GET['teacher']
            ); ?>
        </span>
        <span class="alignleft">
            <label class="screen-reader-text" for="search_wcs4_lesson_student_id"><?php
                _e('Student', 'wcs4'); ?></label>
            <?php
            echo WCS_Admin::generate_admin_select_list(
                'student',
                'search_wcs4_lesson_student_id',
                'student',
                (int)$_GET['student']
            ); ?>
        </span>
        <span class="alignleft">
            <label class="screen-reader-text" for="search_wcs4_lesson_classroom_id"><?php
                _e('Classroom', 'wcs4'); ?></label>
            <?php
            echo WCS_Admin::generate_admin_select_list(
                'classroom',
                'search_wcs4_lesson_classroom_id',
                'classroom',
                (int)$_GET['classroom']
            ); ?>
        </span>
        <button type="submit" id="wcs4-lessons-search"
                class="button button-primary">
            <span class="dashicons dashicons-filter"></span>
            <?php
            echo __('Search lessons', 'wcs4') ?>
        </button>
        <button type="reset" class="button button-secondary">
            <span class="dashicons dashicons-no"></span>
            <?php
            echo __('Reset form', 'wcs4') ?>
        </button>
    </div>
</form>
