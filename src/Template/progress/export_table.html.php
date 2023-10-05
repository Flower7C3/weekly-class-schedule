<?php
/**
 * @var array $thead_columns
 * @var array $tbody_columns
 * @var array $items
 */

use WCS4\Entity\Progress_Item;
use WCS4\Helper\Output;

?>
<table>
    <thead>
    <tr>
        <?php
        foreach ($thead_columns as $key => $th): ?>
            <th data-key="<?= $key ?>">
                <?= $th ?>
            </th>
        <?php
        endforeach; ?>
    </tr>
    </thead>
    <tbody>
    <?php
    $index = 1;
    /** @var Progress_Item $item */
    foreach ($items as $item):
        if ($item->isTypePeriodic()) {
            continue;
        }
        ?>
        <tr>
            <?php
            foreach ($tbody_columns as $key => $td): ?>
                <td data-key="<?= $key ?>">
                    <?php
                    $row = Output::process_template($item, $td);
                    $row = str_replace(['{index}',], [$index,], $row);
                    echo $row;
                    ?>
                </td>
            <?php
            endforeach; ?>
        </tr>
        <?php
        $index++;
    endforeach; ?>
    </tbody>
</table>
