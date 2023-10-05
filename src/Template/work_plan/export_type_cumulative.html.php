<?php
/**
 * @var Item $item
 * @var string $template_style
 * @var string $template_code
 */

use WCS4\Entity\Item;
use WCS4\Helper\Output;

?>
<!DOCTYPE html>
<html class="cumulative">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title><?= __('Work Plan', 'wcs4') ?></title>
    <style><?= $template_style ?></style>
</head>
<body>
<?= Output::process_template($item, $template_code) ?>
</body>
</html>