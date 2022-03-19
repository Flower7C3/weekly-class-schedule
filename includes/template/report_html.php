<?php
/**
 * @var string $template_style
 * @var string $template_code
 */
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <title><?php
            _e('Report', 'wcs4'); ?></title>
        <style><?php
            echo $template_style ?></style>
    </head>
    <body>
        <?php
        echo $template_code; ?>
    </body>
</html>