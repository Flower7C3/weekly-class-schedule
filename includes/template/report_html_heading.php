<?php

if (!empty($subject_item)): ?>
    <?php
    printf(_x('of %s subject', 'report template', 'wcs4'), $subject_item->getName()); ?>
<?php
endif; ?>
<?php
if (!empty($teacher_item)): ?>
    <?php
    printf(_x('by %s teacher', 'report template', 'wcs4'), $teacher_item->getName()); ?>
<?php
endif; ?>
<?php
if (!empty($student_item)): ?>
    <?php
    printf(_x('for %s student', 'report template', 'wcs4'), $student_item->getName()); ?>
<?php
endif; ?>
