<?php
/**
 * @var WCS_DB_Item $item
 * @var string $template_style
 * @var string $template_code
 */

?>
<!DOCTYPE html>
<html class="periodic">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title><?php
        _e('Progress', 'wcs4'); ?></title>
    <style><?php
        echo $template_style ?></style>
</head>
<body>
<?php
echo WCS_Output::process_template($item, $template_code); ?>
</body>
</html>