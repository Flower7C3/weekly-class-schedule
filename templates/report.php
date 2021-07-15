<?php
$post_id = get_the_id();
$post_type = get_post_type();

$teacher = null;
$student = null;
$subject = null;
switch ($post_type) {
    case 'wcs4_teacher':
        $teacher = '#' . $post_id;
        break;
    case 'wcs4_student':
        $student = '#' . $post_id;
        break;
    case 'wcs4_subject':
        $subject = '#' . $post_id;
        break;
}
$reports = Report_Management::get_reports($teacher, $student, $subject);

header('Content-Type: application/csv');
header('Content-Disposition: attachment; filename="' . $post_type . '-report-' . $post_id . '.csv";');

$handle = fopen('php://memory', 'w');
$delimiter = ";";

$header = [];
$header[] = __('ID', 'wcs4');
$header[] = __('Start Time', 'wcs4');
$header[] = __('End Time', 'wcs4');
$header[] = __('Subject', 'wcs4');
$header[] = __('Teacher', 'wcs4');
$header[] = __('Student', 'wcs4');
$header[] = __('Topic', 'wcs4');
$header[] = __('Created at', 'wcs4');
$header[] = __('Created by', 'wcs4');
$header[] = __('Updated at', 'wcs4');
$header[] = __('Updated by', 'wcs4');
fputcsv($handle, $header, $delimiter);

/** @var WCS4_Report $report */
foreach ($reports as $report) {
    $line = [];
    $line[] = $report->getId();
    $line[] = $report->getStartDateTime()->format('Y-m-d H:i');
    $line[] = $report->getEndDateTime()->format('Y-m-d H:i');
    $line[] = $report->getSubject()->getName();
    $line[] = $report->getTeacher()->getName();
    $line[] = $report->getStudent()->getName();
    $line[] = $report->getTopic();
    $line[] = $report->getCreatedAt() ? $report->getCreatedAt()->format('Y-m-d H:i:s') : null;
    $line[] = $report->getCreatedBy() ? $report->getCreatedBy()->display_name : null;
    $line[] = $report->getUpdatedAt() ? $report->getUpdatedAt()->format('Y-m-d H:i:s') : null;
    $line[] = $report->getUpdatedBy() ? $report->getUpdatedBy()->display_name : null;
    fputcsv($handle, $line, $delimiter);
}

fseek($handle, 0);
fpassthru($handle);
