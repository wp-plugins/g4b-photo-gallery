<?php
if(isset($_POST['action']) && $_POST['action'] == 'update_settings')
{
    update_option('g4b_gallery_name', $_POST['gallery_name']);
    update_option('g4b_gallery_thumb_width', $_POST['thumbnail_width']);
    update_option('g4b_gallery_thumb_height', $_POST['thumbnail_height']);
    update_option('g4b_gallery_photo_width', $_POST['photo_width']);
    update_option('g4b_gallery_photo_height', $_POST['photo_height']);
    G4B_gallery_msg('updated', 'Settings updated successfully.');
}
?>

<div class="wrap">
<h2>G4B Gallery Settings</h2>
<p>
    All images are sized proportionally. If the width and height settings are
    the same (square), the image will be cropped.
</p>
<form method="post" action="admin.php?page=G4B_gallery/G4B_gallery_options.php">
    <input type="hidden" name="action" value="update_settings" />
    <?php wp_nonce_field('update-options'); ?>
    
    <table class="form-table">
        <tr valign="top">
            <th scope="row">Thumbnail Width</th>
            <td>
                <input type="text" name="thumbnail_width" value="<?php echo get_option('g4b_gallery_thumb_width') ?>" />
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">Thumbnail Height</th>
            <td>
                <input type="text" name="thumbnail_height" value="<?php echo get_option('g4b_gallery_thumb_height') ?>" />
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">Photo Width</th>
            <td>
                <input type="text" name="photo_width" value="<?php echo get_option('g4b_gallery_photo_width') ?>" />
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">Photo Height</th>
            <td>
                <input type="text" name="photo_height" value="<?php echo get_option('g4b_gallery_photo_height') ?>" />
            </td>
        </tr>
    </table>
    
    <p class="submit">
        <input type="submit" name="Submit" value="<?php _e('Save Settings') ?>" />
    </p>
</form>
</div>