<?php
/**
 * @var WCS_DB_Lesson_Item|WCS_DB_Journal_Item|WCS_DB_Progress_Item $item
 */

if ($item->getUpdatedAt()): ?>
    <span title="<?= sprintf(
        __('Updated at %s by %s', 'wcs4'),
        $item->getUpdatedAt()->format('Y-m-d H:i:s'),
        $item->getUpdatedBy()?->display_name ?: 'nn'
    ) ?>">
        <?= $item->getUpdatedAt()->format('Y-m-d H:i:s') ?>
        <small><?= $item->getUpdatedBy()?->display_name ?: '' ?></small>
    </span>
<?php
else: ?>
    <span title="<?= sprintf(
        __('Created at %s by %s', 'wcs4'),
        $item->getCreatedAt()->format('Y-m-d H:i:s'),
        $item->getCreatedBy()?->display_name ?: 'nn'
    ) ?>">
        <?= $item->getCreatedAt()->format('Y-m-d H:i:s') ?>
        <small><?= $item->getCreatedBy()?->display_name ?: '' ?></small>
    </span>
<?php
endif;