<?php
/**
 * Redirect form submissions based on action
 */
if(isset($_POST['action']))
{
    switch($_POST['action'])
    {
        case 'upload_photo':
            G4B_gallery_upload_photo($_POST, $_FILES);
            break;
        case 'del_photo':
            G4B_gallery_del_photo($_POST['photo_id']);
            break;
    }
}


/**
 * Load current album photos into local array for looping
 */
$photosArr = G4B_gallery_get_photos($_GET['album_id']);
?>


<div class="wrap">
    <h3>
        <a href="edit.php?page=G4B_gallery/G4B_gallery_manage.php">Albums</a> >
        <?php echo G4B_gallery_get_album_name($_GET['album_id']) ?>
        <a href="edit.php?page=G4B_gallery/G4B_gallery_manage_album_edit.php&album_id=<?php echo $_GET['album_id'] ?>">(Edit)</a>
    </h3>
    <br />
    
    <h2>Upload Photo</h2>
    <form method="post" action="edit.php?page=G4B_gallery/G4B_gallery_manage_album.php&album_id=<?php echo $_GET['album_id'] ?>"  enctype="multipart/form-data">
        <?php wp_nonce_field('update-options'); ?>
        <input type="hidden" name="action" value="upload_photo" />
        <input type="hidden" name="album_id" value="<?php echo $_GET['album_id'] ?>" />
        
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Photo Name</th>
                <td><input type="text" name="photo_name" value="<?php echo (isset($_POST['photo_name'])) ? stripslashes($_POST['photo_name']) : '' ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Photo Description</th>
                <td><textarea name="photo_desc" rows="5" cols="50" style="width: 97%;"><?php echo (isset($_POST['photo_desc'])) ? stripslashes($_POST['photo_desc']) : '' ?></textarea></td>
            </tr>
            <tr valign="top">
                <th scope="row">Photo File</th>
                <td><input type="file" name="photo" /></td>
            </tr>
        </table>
                
        <p class="submit">
            <input type="submit" name="Submit" value="Upload Photo" />
        </p>
    </form>    
    <br /><br />
    
    <h2>Manage Photos</h2>
    <form method="post" action="edit.php?page=G4B_gallery/G4B_gallery_manage_album.php&album_id=<?php echo $_GET['album_id'] ?>">
        <input type="hidden" name="action" value="del_photo" />
        <?php wp_nonce_field('update-options'); ?>
        <table class="form-table">
            <?php if($photosArr): ?>
                <?php foreach($photosArr as $photo): ?>
                <tr valign="top">
                    <td width="10"><input type="checkbox" name="photo_id[]" value="<?php echo $photo['id'] ?>" /></td>
                    <td width="<?php echo get_option('g4b_gallery_thumb_width') + 5 ?>">
                        <?php wp_load_image($photo['thumbnail']); ?>
                        <a href="edit.php?page=G4B_gallery/G4B_gallery_manage_photo.php&photo_id=<?php echo $photo['id']?>&album_id=<?php echo $_GET['album_id'] ?>" title="Edit Photo" />
                            <img src="<?php echo G4B_GALLERY_UPLOAD_URL . $photo['thumbnail'] ?>" alt="Thumbnail" />
                        </a>
                    </td>
                    <td>
                        <a href="edit.php?page=G4B_gallery/G4B_gallery_manage_photo.php&photo_id=<?php echo $photo['id']?>&album_id=<?php echo $_GET['album_id'] ?>"><strong><?php echo $photo['name'] ?></strong></a><br />
                        <?php if(!empty($photo['description'])): ?>
                            <?php echo $photo['description'] ?>
                        <?php else: ?>
                            <i>No description.</i>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
            <tr valign="top">
                <td>Yikes, no photos! Upload some with the form above.</td>
            </tr>
            <?php endif; ?>
        </table>
        <p class="submit">
            <input type="submit" name="Submit" value="Delete Selected Photos" onclick="return confirm('Delete selected photos?');" />
        </p>
    </form>
</div>