<?php
/**
 * @var Lesson_Item|Journal_Item|Progress_Item $item
 */

use WCS4\Entity\Journal_Item;
use WCS4\Entity\Lesson_Item;
use WCS4\Entity\Progress_Item;

if (null !== $item->getUpdatedAt() && $item->getUpdatedAt()->format('Y-m-d H:i:s') !== $item->getCreatedAt()->format('Y-m-d H:i:s')): ?>
    <span title="<?= sprintf(
        __('Updated at %s by %s', 'wcs4'),
        $item->getUpdatedAt()->format('Y-m-d H:i:s'),
        $item->getUpdatedBy()?->display_name ?: 'nn'
    ) ?>">
        <i class="fa-regular fa-calendar-check"></i>
        <?= $item->getUpdatedAt()->format('Y-m-d H:i:s') ?>
        <?php
        if (!empty($item->getUpdatedBy()?->display_name)): ?>
            <small>
            <i class="fa-regular fa-user"></i>
            <?= $item->getUpdatedBy()?->display_name ?>
        </small>
        <?php
        endif; ?>
    </span>
<?php
else: ?>
    <span title="<?= sprintf(
        __('Created at %s by %s', 'wcs4'),
        $item->getCreatedAt()->format('Y-m-d H:i:s'),
        $item->getCreatedBy()?->display_name ?: 'nn'
    ) ?>">
        <i class="fa-regular fa-calendar-plus"></i>
        <?= $item->getCreatedAt()->format('Y-m-d H:i:s') ?>
        <?php
        if (!empty($item->getCreatedBy()?->display_name)): ?>
            <small>
              <i class="fa-regular fa-user"></i>
      <?= $item->getCreatedBy()?->display_name ?>
        </small>
        <?php
        endif; ?>
    </span>
<?php
endif;