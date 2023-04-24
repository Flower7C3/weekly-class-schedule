<?php

use WCS4\Helper\Admin;

?>
<form id="wcs4-schedule-filter" class="results-filter" method="get" action="<?= admin_url('admin.php') ?>">
    <input id="search_wcs4_page" type="hidden" name="page" value="<?= $_GET['page'] ?>"/>
    <div class="search-box">
        <div class="alignleft">
            <label for="search_wcs4_schedule_subject_id">
                <?= __('Subject', 'wcs4') ?>
            </label>
            <?= Admin::generate_admin_select_list(
                'subject',
                'search_wcs4_schedule_subject_id',
                'subject',
                array_key_exists('subject', $_GET) ? (int)$_GET['subject'] : ''
            ) ?>
        </div>
        <div class="alignleft">
            <label for="search_wcs4_schedule_teacher_id">
                <?= __('Teacher', 'wcs4') ?>
            </label>
            <?= Admin::generate_admin_select_list(
                'teacher',
                'search_wcs4_schedule_teacher_id',
                'teacher',
                array_key_exists('teacher', $_GET) ? (int)$_GET['teacher'] : ''
            ) ?>
        </div>
        <div class="alignleft">
            <label for="search_wcs4_schedule_student_id">
                <?= __('Student', 'wcs4') ?>
            </label>
            <?= Admin::generate_admin_select_list(
                'student',
                'search_wcs4_schedule_student_id',
                'student',
                array_key_exists('student', $_GET) ? (int)$_GET['student'] : ''
            ) ?>
        </div>
        <div class="alignleft">
            <label for="search_wcs4_schedule_classroom_id">
                <?= __('Classroom', 'wcs4') ?>
            </label>
            <?= Admin::generate_admin_select_list(
                'classroom',
                'search_wcs4_schedule_classroom_id',
                'classroom',
                array_key_exists('classroom', $_GET) ? (int)$_GET['classroom'] : ''
            ) ?>
        </div>
        <div class="alignleft">
            <label for="search_wcs4_schedule_visibility">
                <?= __('Visibility', 'wcs4') ?>
            </label>
            <?= Admin::generate_admin_select_list_options(
                'visibility',
                'search_wcs4_schedule_visibility',
                'visibility',
                '',
            ) ?>
        </div>
        <div class="alignleft">
            <label for="search_wcs4_schedule_collision_detection">
                <?= __('Collision detection', 'wcs4') ?>
            </label>
            <?= Admin::generate_admin_select_list_options(
                'collision_detection',
                'search_wcs4_schedule_collision_detection',
                'collision_detection',
                '',
            ) ?>
        </div>
        <div class="alignleft buttons">
            <button type="submit" id="wcs4-schedule-filter-submit"
                    class="button button-primary">
                <span class="dashicons dashicons-filter"></span>
                <?= __('Search lessons', 'wcs4') ?>
            </button>
            <button type="reset" class="button button-secondary">
                <span class="dashicons dashicons-no"></span>
                <?= __('Reset form', 'wcs4') ?>
            </button>
        </div>
    </div>
</form>
