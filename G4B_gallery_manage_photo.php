<?php
/**
 * Handle form submission
 */
if(isset($_POST['action']) && $_POST['action'] == 'update_photo')
    G4B_gallery_update_photo($_POST);


/**
 * Load current albums photos into local array for looping
 */
$photo = G4B_gallery_get_photo($_GET['photo_id']);
?>

<div class="wrap">
    <h3>
        <a href="edit-pages.php?page=G4B_gallery/G4B_gallery_manage.php">Albums</a> >
        <a href="edit-pages.php?page=G4B_gallery/G4B_gallery_manage_album.php&album_id=<?php echo $_GET['album_id'] ?>">
            <?php echo G4B_gallery_get_album_name($_GET['album_id']) ?>
        </a> >
        <?php echo G4B_gallery_get_photo_name($_GET['photo_id']) ?>
    </h3>
    <br />
    
    <h2>Manage Photo</h2>
    <form method="post" action="edit-pages.php?page=G4B_gallery/G4B_gallery_manage_photo.php&album_id=<?php echo $_GET['album_id'] ?>&photo_id=<?php echo $_GET['photo_id'] ?>">
        <?php wp_nonce_field('update-options'); ?>
        <input type="hidden" name="action" value="update_photo" />
        <input type="hidden" name="photo_id" value="<?php echo $_GET['photo_id'] ?>" />
        
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Photo</th>
                <td>
                    <a href="<?php echo G4B_GALLERY_UPLOAD_URL . $photo['photo'] ?>">
                        <img src="<?php echo G4B_GALLERY_UPLOAD_URL . $photo['thumbnail'] ?>" alt="Thumbnail" />
                    </a>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Photo Title</th>
                <td><input type="text" name="photo_name" value="<?php echo $photo['name'] ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Photo Description</th>
                <td><textarea name="photo_desc" rows="5" cols="50" style="width: 97%;"><?php echo $photo['description'] ?></textarea></td>
            </tr>
        </table>
                
        <p class="submit">
            <input type="submit" name="Submit" value="<?php _e('Update Photo') ?>" />
        </p>
    </form>    
</div>