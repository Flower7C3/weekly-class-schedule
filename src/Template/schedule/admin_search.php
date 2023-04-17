<?php

use WCS4\Helper\Admin;

?>
<form id="wcs4-lessons-filter" class="results-filter" method="get" action="">
    <input id="search_wcs4_page" type="hidden" name="page" value="<?= $_GET['page'] ?>"/>
    <div class="search-box">
        <fieldset class="alignleft">
            <legend><?= __('Subject', 'wcs4') ?></legend>
            <div class="alignleft">
                <label class="screen-reader-text" for="search_wcs4_lesson_subject_id">
                    <?= __('Subject', 'wcs4') ?>
                </label>
                <?= Admin::generate_admin_select_list(
                    'subject',
                    'search_wcs4_lesson_subject_id',
                    'subject',
                    array_key_exists('subject', $_GET) ? (int)$_GET['subject'] : ''
                ) ?>
            </div>
        </fieldset>
        <fieldset class="alignleft">
            <legend><?= __('Teacher', 'wcs4') ?></legend>
            <div class="alignleft">
                <label class="screen-reader-text" for="search_wcs4_lesson_teacher_id">
                    <?= __('Teacher', 'wcs4') ?>
                </label>
                <?= Admin::generate_admin_select_list(
                    'teacher',
                    'search_wcs4_lesson_teacher_id',
                    'teacher',
                    array_key_exists('teacher', $_GET) ? (int)$_GET['teacher'] : ''
                ) ?>
            </div>
        </fieldset>
        <fieldset class="alignleft">
            <legend><?= __('Student', 'wcs4') ?></legend>
            <div class="alignleft">
                <label class="screen-reader-text" for="search_wcs4_lesson_student_id">
                    <?= __('Student', 'wcs4') ?>
                </label>
                <?= Admin::generate_admin_select_list(
                    'student',
                    'search_wcs4_lesson_student_id',
                    'student',
                    array_key_exists('student', $_GET) ? (int)$_GET['student'] : ''
                ) ?>
            </div>
        </fieldset>
        <fieldset class="alignleft">
            <legend><?= __('Classroom', 'wcs4') ?></legend>
            <div class="alignleft">
                <label class="screen-reader-text" for="search_wcs4_lesson_classroom_id">
                    <?= __('Classroom', 'wcs4') ?>
                </label>
                <?= Admin::generate_admin_select_list(
                    'classroom',
                    'search_wcs4_lesson_classroom_id',
                    'classroom',
                    array_key_exists('classroom', $_GET) ? (int)$_GET['classroom'] : ''
                ) ?>
            </div>
        </fieldset>
        <fieldset class="alignleft">
            <legend>&nbsp;</legend>
            <button type="submit" id="wcs4-lessons-search"
                    class="button button-primary">
                <span class="dashicons dashicons-filter"></span>
                <?= __('Search lessons', 'wcs4') ?>
            </button>
            <button type="reset" class="button button-secondary">
                <span class="dashicons dashicons-no"></span>
                <?= __('Reset form', 'wcs4') ?>
            </button>
        </fieldset>
    </div>
</form>
