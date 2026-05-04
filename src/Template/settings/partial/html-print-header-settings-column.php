<?php
/**
 * Print header fields for HTML export (template placeholders documented in Help).
 *
 * @var array<string, mixed> $wcs4_options
 */

$basename = \WCS4\Helper\Output::HTML_PRINT_HEADER_OPTION_BASE;

$img1 = isset($wcs4_options[$basename . '_img1_id']) ? absint($wcs4_options[$basename . '_img1_id']) : 0;
$img2 = isset($wcs4_options[$basename . '_img2_id']) ? absint($wcs4_options[$basename . '_img2_id']) : 0;
$input_img1 = 'wcs4_' . $basename . '_img1_id';
$input_img2 = 'wcs4_' . $basename . '_img2_id';
$editor_heading_id = 'wcs4_' . $basename . '_heading';
$editor_address_id = 'wcs4_' . $basename . '_address';
?>
<fieldset class="wcs4-settings-fieldset">
    <legend>
        <strong><?= esc_html(_x('Header Settings', 'print header options', 'wcs4')) ?></strong>
    </legend>
    <table class="form-table" role="presentation">
        <tbody>
        <tr>
            <th scope="row"><?= esc_html(_x('Logo 1', 'print header options', 'wcs4')) ?></th>
            <td>
                <input type="hidden" name="<?= esc_attr($input_img1) ?>" id="<?= esc_attr($input_img1) ?>"
                       value="<?= esc_attr((string) $img1) ?>">
                <p>
                    <button type="button" class="button wcs4-pick-print-header-image"
                            data-target="<?= esc_attr($input_img1) ?>">
                        <?= esc_html(_x('Select image', 'print header options', 'wcs4')) ?>
                    </button>
                    <button type="button" class="button button-secondary wcs4-clear-print-header-image"
                            data-target="<?= esc_attr($input_img1) ?>">
                        <?= esc_html(_x('Clear', 'print header options', 'wcs4')) ?>
                    </button>
                </p>
                <div class="wcs4-print-header-preview" data-for="<?= esc_attr($input_img1) ?>">
                    <?php
                    if ($img1 > 0) {
                        echo wp_get_attachment_image($img1, 'thumbnail', false);
                    }
                    ?>
                </div>
            </td>
        </tr>
        <tr>
            <th scope="row"><?= esc_html(_x('Address', 'print header options', 'wcs4')) ?></th>
            <td>
                <?php
                wp_editor(
                    wp_unslash($wcs4_options[$basename . '_address'] ?? ''),
                    $editor_address_id,
                    array(
                        'wpautop' => true,
                        'media_buttons' => false,
                        'textarea_name' => 'wcs4_' . $basename . '_address',
                        'textarea_rows' => 8,
                    )
                );
                ?>
            </td>
        </tr>
        <tr>
            <th scope="row"><?= esc_html(_x('Logo 2', 'print header options', 'wcs4')) ?></th>
            <td>
                <input type="hidden" name="<?= esc_attr($input_img2) ?>" id="<?= esc_attr($input_img2) ?>"
                       value="<?= esc_attr((string) $img2) ?>">
                <p>
                    <button type="button" class="button wcs4-pick-print-header-image"
                            data-target="<?= esc_attr($input_img2) ?>">
                        <?= esc_html(_x('Select image', 'print header options', 'wcs4')) ?>
                    </button>
                    <button type="button" class="button button-secondary wcs4-clear-print-header-image"
                            data-target="<?= esc_attr($input_img2) ?>">
                        <?= esc_html(_x('Clear', 'print header options', 'wcs4')) ?>
                    </button>
                </p>
                <div class="wcs4-print-header-preview" data-for="<?= esc_attr($input_img2) ?>">
                    <?php
                    if ($img2 > 0) {
                        echo wp_get_attachment_image($img2, 'thumbnail', false);
                    }
                    ?>
                </div>
            </td>
        </tr>
        <tr>
            <th scope="row"><?= esc_html(_x('Heading', 'print header options', 'wcs4')) ?></th>
            <td>
                <?php
                wp_editor(
                    wp_unslash($wcs4_options[$basename . '_heading'] ?? ''),
                    $editor_heading_id,
                    array(
                        'wpautop' => true,
                        'media_buttons' => false,
                        'textarea_name' => 'wcs4_' . $basename . '_heading',
                        'textarea_rows' => 6,
                    )
                );
                ?>
            </td>
        </tr>
        </tbody>
    </table>
</fieldset>
