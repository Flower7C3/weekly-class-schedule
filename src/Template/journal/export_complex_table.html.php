<?php
/**
 * @var array $thead_columns
 * @var array $tbody_columns
 * @var array $tfoot_columns
 * @var array $items
 * @var int $student
 */

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
            if (
                $studentItem instanceof Item
                &&
                !array_key_exists($studentItem->getId(), $studentsData)
                &&
                ('#'.$studentItem->getId() === $student || empty($student))
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
?>
<table>
    <thead>
    <tr>
        <?php
        foreach ($thead_columns as $key => $th): ?>
            <?php
            if ('student duration detailed' === $key): ?>
                <th colspan="<?= count($studentsData) ?>">
                    <?= $th ?>
                    (<?= count($studentsData) ?>)
                </th>
            <?php
            elseif ('teacher duration detailed' === $key): ?>
                <th colspan="<?= count($teachersData) ?>">
                    <?= $th ?>
                    (<?= count($teachersData) ?>)
                </th>
            <?php
            elseif ('type duration detailed' === $key || 'type duration simple' === $key): ?>
                <th colspan="<?= count($typesData) ?>">
                    <?= $th ?>
                    (<?= count($typesData) ?>)
                </th>
            <?php
            else: ?>
                <th data-key="<?= $key ?>" rowspan="<?= $rowspan ? 2 : 1 ?>">
                    <span><?= $th ?></span>
                </th>
            <?php
            endif; ?>
        <?php
        endforeach; ?>
    </tr>
    <?php
    if (true === $rowspan): ?>
        <tr>
            <?php
            foreach ($thead_columns as $key => $th): ?>
                <?php
                if ('student duration detailed' === $key): ?>
                    <?php
                    foreach ($studentsData as $studentId => $studentData): ?>
                        <th data-key="<?= $key ?>" data-student-id="<?= $studentId ?>">
                            <span><?= $studentData['short_name'] ?></span>
                        </th>
                    <?php
                    endforeach; ?>
                <?php
                elseif ('teacher duration detailed' === $key): ?>
                    <?php
                    foreach ($teachersData as $teacherId => $teacherData): ?>
                        <th data-key="<?= $key ?>" data-teacher-id="<?= $teacherId ?>">
                            <span><?= $teacherData['short_name'] ?></span>
                        </th>
                    <?php
                    endforeach; ?>
                <?php
                elseif ('type duration detailed' === $key || 'type duration simple' === $key): ?>
                    <?php
                    foreach ($typesData as $typeKey => $typeData): ?>
                        <th data-key="<?= $key ?>" data-type-key="<?= $typeKey ?>">
                            <span><?= $typeData['short_name'] ?></span>
                        </th>
                    <?php
                    endforeach; ?>
                <?php
                endif; ?>
            <?php
            endforeach; ?>
        </tr>
    <?php
    endif; ?>
    </thead>
    <tbody>
    <?php
    $index = 1;
    /** @var Journal_Item $item */
    foreach ($items as $item): ?>
        <tr>
            <?php
            foreach ($tbody_columns as $key => $td): ?>
                <?php
                if ('student duration detailed' === $key): ?>
                    <?php
                    foreach ($studentsData as $studentId => $studentData): ?>
                        <td data-key="<?= $key ?>" data-student-id="<?= $studentId ?>">
                            <span>
                                <?php
                                if (array_key_exists($studentId, $item->getStudents())) {
                                    $studentsData[$studentId]['duration'] += $item->getDurationTime();
                                    $studentsData[$studentId]['events']++;
                                    $row = Output::process_template($item, $td);
                                    echo $row;
                                }
                                ?>
                            </span>
                        </td>
                    <?php
                    endforeach; ?>
                <?php
                elseif ('teacher duration detailed' === $key): ?>
                    <?php
                    foreach ($teachersData as $teacherId => $teacherData): ?>
                        <td data-key="<?= $key ?>" data-teacher-id="<?= $teacherId ?>">
                            <span>
                                <?php
                                if (array_key_exists($teacherId, $item->getTeachers())) {
                                    $teachersData[$teacherId]['duration'] += $item->getDurationTime();
                                    $teachersData[$teacherId]['events']++;
                                    $row = Output::process_template($item, $td);
                                    echo $row;
                                }
                                ?>
                            </span>
                        </td>
                    <?php
                    endforeach; ?>
                <?php
                elseif ('type duration detailed' === $key || 'type duration simple' === $key): ?>
                    <?php
                    foreach ($typesData as $typeKey => $typeData): ?>
                        <td data-key="<?= $key ?>" data-type-key="<?= $typeKey ?>">
                            <span>
                                <?php
                                if (
                                    ('type duration detailed' === $key && $item->getType() === $typeKey)
                                    ||
                                    ('type duration simple' === $key && str_starts_with($item->getType(), $typeKey))
                                ) {
                                    $typesData[$typeKey]['duration'] += $item->getDurationTime();
                                    $typesData[$typeKey]['events']++;
                                    $row = Output::process_template($item, $td);
                                    echo $row;
                                }
                                ?>
                            </span>
                        </td>
                    <?php
                    endforeach; ?>
                <?php
                else: ?>
                    <td data-key="<?= $key ?>">
                        <span>
                            <?php
                            $row = Output::process_template($item, $td);
                            $row = str_replace(['{index}',], [$index,], $row);
                            echo $row;
                            ?>
                        </span>
                    </td>
                <?php
                endif; ?>
            <?php
            endforeach; ?>
        </tr>
        <?php
        $index++;
    endforeach; ?>
    </tbody>
    <tbody>
    <tr>
        <?php
        foreach ($tfoot_columns as $key => $th): ?>
            <?php
            if ('student duration detailed' === $key): ?>
                <?php
                foreach ($studentsData as $studentId => $studentData): ?>
                    <th data-key="<?= $key ?>" data-student-id="<?= $studentId ?>">
                        <span><?= Output::process_template($studentData, $th) ?></span>
                    </th>
                <?php
                endforeach; ?>
            <?php
            elseif ('teacher duration detailed' === $key): ?>
                <?php
                foreach ($teachersData as $teacherId => $teacherData): ?>
                    <th data-key="<?= $key ?>" data-teacher-id="<?= $teacherId ?>">
                        <span><?= Output::process_template($teacherData, $th) ?></span>
                    </th>
                <?php
                endforeach; ?>
            <?php
            elseif ('type duration detailed' === $key || 'type duration simple' === $key): ?>
                <?php
                foreach ($typesData as $typeKey => $typeData): ?>
                    <th data-key="<?= $key ?>" data-type-key="<?= $typeKey ?>">
                        <span><?= Output::process_template($typeData, $th) ?></span>
                    </th>
                <?php
                endforeach; ?>
            <?php
            else: ?>
                <th data-key="<?= $key ?>"></th>
            <?php
            endif; ?>
        <?php
        endforeach; ?>
    </tr>
    </tbody>
</table>
