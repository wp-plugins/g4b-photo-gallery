<?php
/**
 * Redirect form submissions based on action
 */
if(isset($_POST['action']) && $_POST['action'] == 'update_album')
    G4B_gallery_update_album($_POST);


/**
 * Load current album photos into local array for looping
 */
$album = G4B_gallery_get_album($_GET['album_id']);
?>


<div class="wrap">
    <h3>
        <a href="edit.php?page=G4B_gallery/G4B_gallery_manage.php">Albums</a> >
        <a href="edit.php?page=G4B_gallery/G4B_gallery_manage_album.php&album_id=<?php echo $_GET['album_id'] ?>">
            <?php echo $album['name']; ?>
        </a> >
        Edit
    </h3>
    <br />
    
    <h2>Update Album</h2>
    <form method="post" action="edit.php?page=G4B_gallery/G4B_gallery_manage_album_edit.php&album_id=<?php echo $album['id'] ?>">
        <?php wp_nonce_field('update-options'); ?>
        <input type="hidden" name="action" value="update_album" />
        <input type="hidden" name="album_id" value="<?php echo $album['id'] ?>" />
        
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Album Name</th>
                <td><input type="text" name="album_name" value="<?php echo $album['name'] ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Album Description</th>
                <td><textarea name="album_desc" rows="5" cols="50" style="width: 97%;"><?php echo $album['description'] ?></textarea></td>
            </tr>
        </table>
        
        <p class="submit">
            <input type="submit" name="Submit" value="Update Album" />
        </p>
    </form>
    <br /><br />
</div>