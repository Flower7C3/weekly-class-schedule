<?php
/**
 * @var array $thead_columns
 * @var array $tbody_columns
 * @var array $reports
 */

?>
<table>
    <thead>
        <tr>
            <?php
            foreach ($thead_columns as $th): ?>
                <th>
                    <?php
                    echo $th; ?>
                </th>
            <?php
            endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php
        $index = 1;
        foreach ($reports as $report): ?>
            <tr>
                <?php
                foreach ($tbody_columns as $td): ?>
                    <td>
                        <?php
                        $row = WCS_Output::process_template($report, $td);
                        $row = str_replace([
                            '{index}',
                        ], [
                            $index,
                        ], $row);
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
