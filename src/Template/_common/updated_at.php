<?php
/**
 * @var Lesson_Item|Journal_Item|Progress_Item $item
 */

use WCS4\Entity\Journal_Item;
use WCS4\Entity\Lesson_Item;
use WCS4\Entity\Progress_Item;

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