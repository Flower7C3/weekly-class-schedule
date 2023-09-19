<?php
/**
 * @var Progress_Item $item
 * @var string $template_style
 * @var string $template_code
 */

use WCS4\Entity\Progress_Item;

?>
<!DOCTYPE html>
<html class="periodic">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title><?= __('Progress', 'wcs4') ?></title>
    <style><?= $template_style ?></style>
</head>
<body>
<?= $template_code ?>
</body>
</html>