<?php
/**
 * Redirect form submissions based on action
 */
if(isset($_POST['action']))
{
    switch($_POST['action'])
    {
        case 'add_album':
            G4B_gallery_add_album($_POST);
            break;
        case 'del_album':
            G4B_gallery_del_album($_POST['album_id']);
            break;
    }
}

/**
 * Check upload dir
 */
G4B_gallery_check();

/**
 * Load albums into local array for looping
 */
$albumsArr = G4B_gallery_get_albums();
?>


<div class="wrap">
    <h2>Create New Album</h2>
    <form method="post" action="edit.php?page=G4B_gallery/G4B_gallery_manage.php">
        <?php wp_nonce_field('update-options'); ?>
        <input type="hidden" name="action" value="add_album" />
        
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Album Name</th>
                <td><input type="text" name="album_name" value="<?php echo (isset($_POST['album_name'])) ? stripslashes($_POST['album_name']) : '' ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Album Description</th>
                <td><textarea name="album_desc" rows="5" cols="50" style="width: 97%;"><?php echo (isset($_POST['album_desc'])) ? stripslashes($_POST['album_desc']) : '' ?></textarea></td>
            </tr>
        </table>
        
        <p class="submit">
            <input type="submit" name="Submit" value="Create Album" />
        </p>
    </form>
    <br /><br />
    
    <h2>Manage Albums</h2>
    <form method="post" action="edit.php?page=G4B_gallery/G4B_gallery_manage.php">
        <?php wp_nonce_field('update-options'); ?>
        <input type="hidden" name="action" value="del_album" />
        
        <table class="form-table">
            <?php if($albumsArr): ?>
                <?php foreach($albumsArr as $album): ?>
                <tr valign="top">
                    <td width="10"><input type="checkbox" name="album_id[]" value="<?php echo $album['id'] ?>" /></td>
                    <?php if(!empty($album['thumbnail'])): ?>
                        <td width="<?php echo get_option('g4b_gallery_thumb_width') + 5 ?>">
                            <a href="edit.php?page=G4B_gallery/G4B_gallery_manage_album.php&album_id=<?php echo $album['id'] ?>">
                                <img src="<?php echo G4B_GALLERY_UPLOAD_URL . $album['thumbnail'] ?>" alt="Thumbnail" /></td>
                            </a>
                        <td>
                    <?php else: ?>
                        <td colspan="2">
                    <?php endif; ?>
                        <a href="edit.php?page=G4B_gallery/G4B_gallery_manage_album.php&album_id=<?php echo $album['id'] ?>">
                            <strong><?php echo $album['name'] ?></strong>
                        </a><br />
                        <?php if(!empty($album['description'])): ?>
                            <?php echo $album['description'] ?>
                        <?php else: ?>
                            <i>No description.</i>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
            <tr valign="top">
                <td colspan="2">Oops, no albums! Create one with the form above.</td>
            </tr>
            <?php endif; ?>
        </table>
        
        <p class="submit">
            <input type="submit" name="Submit" value="Deleted Selected Albums" onclick="return confirm('Deleting an album will also delete all photos within it. Continue?');" />
        </p>
    </form>
</div>