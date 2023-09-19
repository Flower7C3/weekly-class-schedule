<?php
/**
 * @var array $thead_columns
 * @var array $tbody_columns
 * @var array $tfoot_columns
 * @var array $items
 * @var int $student
 * */

use WCS4\Entity\Item;
use WCS4\Entity\Journal_Item;
use WCS4\Helper\Output;

$studentsData = [];
$teachersData = [];
$typesData = [];
$rowspan = false;
if (array_key_exists('student duration detailed', $thead_columns)) {
    /** @var Journal_Item $item */
    foreach ($items as $item) {
        /** @var Item $studentItem */
        foreach ($item->getStudents() as $studentItem) {
            if ($studentItem instanceof Item
                &&
                !array_key_exists($studentItem->getId(), $studentsData)
            ) {
                $studentsData[$studentItem->getId()] = [
                    'short_name' => $studentItem->getName(),
                    'duration' => 0,
                    'events' => 0,
                ];
            }
        }
    }
    asort($studentsData);
    $rowspan = true;
}
if (array_key_exists('teacher duration detailed', $thead_columns)) {
    /** @var Journal_Item $item */
    foreach ($items as $item) {
        /** @var Item $teacherItem */
        foreach ($item->getTeachers() as $teacherItem) {
            if (!array_key_exists($teacherItem->getId(), $teachersData)) {
                $teachersData[$teacherItem->getId()] = [
                    'short_name' => $teacherItem->getName(),
                    'duration' => 0,
                    'events' => 0,
                ];
            }
        }
    }
    asort($teachersData);
    $rowspan = true;
}
if (array_key_exists('type duration detailed', $thead_columns)
    || array_key_exists('type duration simple', $thead_columns)) {
    $types = [];
    if (array_key_exists('type duration detailed', $thead_columns)) {
        $types = [
            Journal_Item::TYPE_NORMAL,
            Journal_Item::TYPE_ABSENT_TEACHER_FREE_VACATION,
            Journal_Item::TYPE_ABSENT_TEACHER_PAID_VACATION,
            Journal_Item::TYPE_ABSENT_TEACHER_SICK_CHILDCARE,
            Journal_Item::TYPE_ABSENT_TEACHER_HEALTHY_CHILDCARE,
            Journal_Item::TYPE_ABSENT_TEACHER_SICK_LEAVE,
            Journal_Item::TYPE_ABSENT_TEACHER,
            Journal_Item::TYPE_ABSENT_STUDENT,
            Journal_Item::TYPE_TEACHER_OFFICE_WORKS,
        ];
    } elseif (array_key_exists('type duration simple', $thead_columns)) {
        $types = [
            Journal_Item::TYPE_NORMAL,
            Journal_Item::TYPE_ABSENT_TEACHER,
            Journal_Item::TYPE_ABSENT_STUDENT,
            Journal_Item::TYPE_TEACHER_OFFICE_WORKS,
        ];
    }
    foreach ($types as $typeKey) {
        $label = Journal_Item::typeLabel($typeKey);
        $typesData[$typeKey] = [
            'short_name' => $label,
            'duration' => 0,
            'events' => 0,
        ];
    }
    $rowspan = true;
}
if (false === $rowspan) {
    exit;
}
?>
<?php
/** @var Journal_Item $item */
foreach ($items as $item):
    foreach ($tbody_columns as $key => $td):
        if ('student duration detailed' === $key):
            foreach ($studentsData as $studentId => $studentData):
                if (array_key_exists($studentId, $item->getStudents())) {
                    $studentsData[$studentId]['duration'] += $item->getDurationTime();
                    $studentsData[$studentId]['events']++;
                }
            endforeach;
        elseif ('teacher duration detailed' === $key):
            foreach ($teachersData as $teacherId => $teacherData):
                if (array_key_exists($teacherId, $item->getTeachers())) {
                    $teachersData[$teacherId]['duration'] += $item->getDurationTime();
                    $teachersData[$teacherId]['events']++;
                }
            endforeach;
        elseif ('type duration detailed' === $key || 'type duration simple' === $key):
            foreach ($typesData as $typeKey => $typeData):
                if (
                    ('type duration detailed' === $key && $item->getType() === $typeKey)
                    ||
                    ('type duration simple' === $key && str_starts_with($item->getType(), $typeKey))
                ) {
                    $typesData[$typeKey]['duration'] += $item->getDurationTime();
                    $typesData[$typeKey]['events']++;
                }
            endforeach;
        endif;
    endforeach;
endforeach;
?>
<table>
    <?php
    foreach ($tfoot_columns as $key => $th): ?>
        <?php
        if ('student duration detailed' === $key): ?>
            <thead>
            <tr>
                <th colspan="3">
                    <?= $thead_columns[$key] ?>
                    (<?= count($studentsData) ?>)
                </th>
            </tr>
            </thead>
            <tbody>
            <?php
            $index = 1;
            foreach ($studentsData as $studentId => $studentData): ?>
                <tr>
                    <th><?= $index++ ?></th>
                    <td data-key="<?= $key ?>" data-student-id="<?= $studentId ?>">
                        <?= $studentData['short_name'] ?>
                    </td>
                    <td data-key="<?= $key ?>" data-student-id="<?= $studentId ?>">
                        <?= Output::process_template($studentData, $th) ?>
                    </td>
                </tr>
            <?php
            endforeach; ?>
            </tbody>
        <?php
        elseif ('teacher duration detailed' === $key): ?>
            <thead>
            <tr>
                <th colspan="3">
                    <?= $thead_columns[$key] ?>
                    (<?= count($teachersData) ?>)
                </th>
            </tr>
            </thead>
            <tbody>
            <?php
            $index = 1;
            foreach ($teachersData as $teacherId => $teacherData): ?>
                <tr>
                    <th><?= $index++ ?></th>
                    <td data-key="<?= $key ?>" data-teacher-id="<?= $teacherId ?>">
                        <?= $teacherData['short_name'] ?>
                    </td>
                    <td data-key="<?= $key ?>" data-teacher-id="<?= $teacherId ?>">
                        <?= Output::process_template($teacherData, $th) ?>
                    </td>
                </tr>
            <?php
            endforeach; ?>
            </tbody>
        <?php
        elseif ('type duration detailed' === $key || 'type duration simple' === $key): ?>
            <thead>
            <tr>
                <th colspan="3">
                    <?= $thead_columns[$key] ?>
                    (<?= count($typesData) ?>)
                </th>
            </tr>
            </thead>
            <tbody>
            <?php
            $index = 1;
            foreach ($typesData as $typeKey => $typeData): ?>
                <tr>
                    <th><?= $index++ ?></th>
                    <td data-key="<?= $key ?>" data-type-key="<?= $typeKey ?>">
                        <?= $typeData['short_name'] ?>
                    </td>
                    <td data-key="<?= $key ?>" data-type-key="<?= $typeKey ?>">
                        <?= Output::process_template($typeData, $th) ?>
                    </td>
                </tr>
            <?php
            endforeach; ?>
            </tbody>
        <?php
        endif; ?>
    <?php
    endforeach; ?>
</table>
