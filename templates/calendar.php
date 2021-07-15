<?php
$post_id = get_the_id();
$post_type = get_post_type();

$classroom = null;
$teacher = null;
$student = null;
$subject = null;
switch ($post_type) {
    case 'wcs4_classroom':
        $classroom = '#' . $post_id;
        break;
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
$lessons = Schedule_Management::get_lessons($classroom, $teacher, $student, $subject, null, null, 1);
$endline = "\r\n";

header('Content-type: text/calendar; charset=utf-8');
header('Content-Disposition: inline; filename=calendar-' . $post_type . '-' . $post_id . '.ics');

echo 'BEGIN:VCALENDAR' . $endline;
echo 'VERSION:2.0' . $endline;
echo 'PRODID:-//hacksw/handcal//NONSGML v1.0//EN' . $endline . $endline;
/** @var WCS4_Lesson $lesson */
foreach ($lessons as $lesson) {
    $description = '';
    $description .= __('Teacher', 'wcs4') . ': ';
    $description .= $lesson->getTeacher()->getName() . '. ';
    $description .= __('Student', 'wcs4') . ': ';
    $description .= $lesson->getStudent()->getName() . '.';
    $description = wordwrap($description, 75, $endline . " ", true);
    echo 'BEGIN:VEVENT' . $endline;
    echo 'CATEGORIES:EDUCATION' . $endline;
    echo 'DTSTART:' . $lesson->getStartTime()->format('Ymd\THis') . $endline;
    echo 'DTEND:' . $lesson->getEndTime()->format('Ymd\THis') . $endline;
    echo 'SUMMARY:' . $lesson->getSubject()->getName() . $endline;
    echo 'DESCRIPTION:' . $description . $endline;
    echo 'LOCATION:' . $lesson->getClassroom()->getName() . $endline;
    echo 'END:VEVENT' . $endline . $endline;
}
echo 'END:VCALENDAR' . $endline;