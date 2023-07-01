<?php
/**
 * @var array $summary
 * @var string $subject_filter_field_id
 * @var string $teacher_filter_field_id
 * @var string $subject
 * @var string $teacher
 */

?>
<div class="search-box">
    <?php
    foreach ($summary as $filter): ?>
        <div class="alignleft">
            <label for="search_filter"><?= __('Quick filter', 'wcs4') ?> </label>
            <select id="search_filter" class="search-filter"
                    data-select-subject-id="<?= $subject_filter_field_id ?>"
                    data-select-teacher-id="<?= $teacher_filter_field_id ?>"
            >
                <option
                        data-option-subject-val=""
                        data-option-teacher-val=""
                ><?= __('Select option', 'wcs4') ?></option>
                <?php
                foreach ($filter['groups'] as $group): ?>
                    <optgroup label="<?= $group['name'] ?>">
                        <?php
                        foreach ($group['rows'] as $row): ?>
                            <option
                                    data-option-<?=$filter['types']['group']?>-val="<?= $group['id'] ?>"
                                    data-option-<?=$filter['types']['row']?>-val="<?= $row['id'] ?>"
                                <?= ('#' . $group['id'] === ${$filter['types']['group']} && '#' . $row['id'] === ${$filter['types']['row']}) ? 'selected' : '' ?>
                            >
                                <?= $row['name'] ?>
                            </option>
                        <?php
                        endforeach; ?>
                    </optgroup>
                <?php
                endforeach; ?>
            </select>
        </div>
    <?php
    endforeach; ?>
    <br class="clear">
</div>