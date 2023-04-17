<?php
/**
 * @var array $thead_columns
 * @var array $tbody_columns
 * @var array $items
 */

use WCS4\Entity\Item;
use WCS4\Entity\Journal_Item;
use WCS4\Helper\Output;

$studentsData = [];
$teachersData = [];
if (array_key_exists('student duration detailed', $thead_columns)) {
    /** @var Journal_Item $item */
    foreach ($items as $item) {
        /** @var Item $student */
        foreach ($item->getStudents() as $student) {
            if (!array_key_exists($student->getId(), $studentsData)) {
                $studentsData[$student->getId()] = [
                    'short_name' => $student->getNameShort(),
                    'duration' => 0,
                    'events' => 0,
                ];
            }
        }
    }
    asort($studentsData);
}
if (array_key_exists('teacher duration detailed', $thead_columns)) {
    /** @var Journal_Item $item */
    foreach ($items as $item) {
        /** @var Item $teacher */
        foreach ($item->getTeachers() as $teacher) {
            if (!array_key_exists($teacher->getId(), $teachersData)) {
                $teachersData[$teacher->getId()] = [
                    'short_name' => $teacher->getNameShort(),
                    'duration' => 0,
                    'events' => 0,
                ];
            }
        }
    }
    asort($teachersData);
}
?>
<table>
    <thead>
    <tr>
        <?php
        foreach ($thead_columns as $key => $th): ?>
            <?php
            if ('student duration detailed' === $key): ?>
                <?php
                foreach ($studentsData as $studentId => $studentData): ?>
                    <th>
                        <?= $studentData['short_name'] ?>
                    </th>
                <?php
                endforeach; ?>
            <?php
            elseif ('teacher duration detailed' === $key): ?>
                <?php
                foreach ($teachersData as $teacherId => $teacherData): ?>
                    <th>
                        <?= $teacherData['short_name'] ?>
                    </th>
                <?php
                endforeach; ?>
            <?php
            else: ?>
                <th>
                    <?= $th ?>
                </th>
            <?php
            endif; ?>
        <?php
        endforeach; ?>
    </tr>
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
                        <td>
                            <?php
                            if (array_key_exists($studentId, $item->getStudents())) {
                                $studentsData[$studentId]['duration'] += $item->getDurationTime();
                                $studentsData[$studentId]['events']++;
                                $row = Output::process_template($item, $td);
                                echo $row;
                            }
                            ?>
                        </td>
                    <?php
                    endforeach; ?>
                <?php
                elseif ('teacher duration detailed' === $key): ?>
                    <?php
                    foreach ($teachersData as $teacherId => $teacherData): ?>
                        <td>
                            <?php
                            if (array_key_exists($teacherId, $item->getTeachers())) {
                                $teachersData[$teacherId]['duration'] += $item->getDurationTime();
                                $teachersData[$teacherId]['events']++;
                                $row = Output::process_template($item, $td);
                                echo $row;
                            }
                            ?>
                        </td>
                    <?php
                    endforeach; ?>
                <?php
                else: ?>
                    <td>
                        <?php
                        $row = Output::process_template($item, $td);
                        $row = str_replace(['{index}',], [$index,], $row);
                        echo $row;
                        ?>
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
    <tfoot>
    <tr>
        <?php
        foreach ($thead_columns as $key => $th): ?>
            <?php
            if ('student duration detailed' === $key): ?>
                <?php
                foreach ($studentsData as $studentId => $studentData): ?>
                    <th>
                        <?= Output::process_template($studentData, $th) ?>
                    </th>
                <?php
                endforeach; ?>
            <?php
            elseif ('teacher duration detailed' === $key): ?>
                <?php
                foreach ($teachersData as $teacherId => $teacherData): ?>
                    <th>
                        <?= Output::process_template($teacherData, $th) ?>
                    </th>
                <?php
                endforeach; ?>
            <?php
            else: ?>
                <th></th>
            <?php
            endif; ?>
        <?php
        endforeach; ?>
    </tr>
    </tfoot>
</table>
