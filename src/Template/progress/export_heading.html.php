<?php

if (!empty($subject_item)): ?>
    <?php
    printf(_x('of %s subject', 'journal template', 'wcs4'), $subject_item->getName()); ?>
<?php
endif; ?>
<?php
if (!empty($teacher_item)): ?>
    <?php
    printf(_x('by %s teacher', 'journal template', 'wcs4'), $teacher_item->getName()); ?>
<?php
endif; ?>
<?php
if (!empty($student_item)): ?>
    <?php
    printf(_x('for %s student', 'journal template', 'wcs4'), $student_item->getName()); ?>
<?php
endif; ?>
<?php
if (!empty($date_from)): ?>
    <?php
    printf(_x('from %s', 'journal template', 'wcs4'), $date_from); ?>
<?php
endif; ?>
<?php
if (!empty($date_upto)): ?>
    <?php
    printf(_x('upto %s', 'journal template', 'wcs4'), $date_upto); ?>
<?php
endif; ?>
<?php
if (!empty($created_at_from)): ?>
    <?php
    printf(_x('created from %s', 'journal template', 'wcs4'), $created_at_from); ?>
<?php
endif; ?>
<?php
if (!empty($created_at_upto)): ?>
    <?php
    printf(_x('created upto %s', 'journal template', 'wcs4'), $created_at_upto); ?>
<?php
endif; ?>
