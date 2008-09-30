<?php
/*
================================================================================
MISC FUNCTIONS
================================================================================
*/


/**
 * Check if the upload directory exists, and is writable
 */
function G4B_gallery_check()
{
    if(!file_exists(G4B_GALLERY_UPLOAD_DIR))
    {
        G4B_gallery_msg('error', 'The upload directory <strong>doesn\'t exist</strong>. Read the install instructions.');
        return false;
    }
    if(!is_writable(G4B_GALLERY_UPLOAD_DIR))
    {
        G4B_gallery_msg('error', 'The upload directory <strong>isn\'t writable</strong>. Read the install instructions.');
        return false;
    }
    return true;
}


/**
 * Central location for update and error notifications
 */
function G4B_gallery_msg($type, $message)
{
    echo '<div id="message" class="' . $type . '"><p>' . $message . '</p></div>';
}


/**
 * Shorthand method for getting view links
 */
function G4B_gallery_link($type = 'base', $albumId = '', $photoId = '')
{
    $base = get_permalink();
    $albumId = (!empty($albumId)) ? $albumId : $_GET['album'];
    $photoId = (!empty($photoId)) ? $photoId : $_GET['photo'];
    $types = array(
        'base' => $base,
        'album' => $base . G4B_GALLERY_SEP . 'album=' . $albumId,
        'photo' => $base . G4B_GALLERY_SEP . 'album=' . $albumId . '&photo=' . $photoId
    );
    return $types[$type];
}


/*
================================================================================
ALBUM FUNCTIONS
================================================================================
*/


/**
 * Handle album creation
 */
function G4B_gallery_add_album($postArr)
{
    if(empty($postArr['album_name']))
    {
        G4B_gallery_msg('error', 'Please enter an album name.');
        return;
    }

    global $wpdb;
    $sql = "
        INSERT INTO " . G4B_GALLERY_ALBUM_TBL . "
        (
            name,
            description
        ) VALUES (
            '" . $wpdb->escape(stripcslashes($postArr['album_name'])) . "',
            '" . $wpdb->escape(stripcslashes($postArr['album_desc'])) . "'
        );
    ";
    $wpdb->query($sql);
    echo G4B_gallery_msg('updated', '<strong>' . $postArr['album_name'] . '</strong> album added successfully.');
}


/**
 * Handle album deletion
 */
function G4B_gallery_del_album($albumArr, $noMsg = false)
{
    if(empty($albumArr) && !$noMsg)
    {
        G4B_gallery_msg('error', 'You didn\'t select an album.');
        return;
    }
    
    global $wpdb;
    if(!is_array($albumArr))
        $albumArr = array($albumArr);
    foreach($albumArr as $albumId)
    {
        // Get photos in album and delete them
        $photoArr = $wpdb->get_results("SELECT id FROM " . G4B_GALLERY_PHOTO_TBL . " WHERE album_id = '" . $wpdb->escape($albumId) . "'", 'ARRAY_A');
        if($photoArr)
            foreach($photoArr as $photo)
                G4B_gallery_del_photo($photo['id'], true);
        
        // Then delete the actual album
        $sql = "DELETE FROM " . G4B_GALLERY_ALBUM_TBL . " WHERE id = '" . $wpdb->escape($albumId) . "'";
        $wpdb->query($sql);
    }
    if(!$noMsg)
        echo G4B_gallery_msg('updated', 'The selected albums where deleted successfully.');
}


/**
 * Handle album updates
 */
function G4B_gallery_update_album($postArr)
{
    if(empty($postArr['album_name']))
    {
        G4B_gallery_msg('error', 'The album name can\'t be empty.');
        return;
    }
    
    global $wpdb;
    $sql = "
        UPDATE " . G4B_GALLERY_ALBUM_TBL . " SET
            name = '" . $wpdb->escape(stripcslashes($postArr['album_name'])) . "',
            description = '" . $wpdb->escape(stripcslashes($postArr['album_desc'])) . "'
        WHERE
            id = '" . $wpdb->escape($postArr['album_id']) . "'
    ";
    $wpdb->query($sql);
    
    G4B_gallery_msg('updated', 'Album updated successfully.');
}


/**
 * Get available albums
 */
function G4B_gallery_get_albums()
{
    global $wpdb;
    $return = $wpdb->get_results("
        SELECT
            albums.id,
            albums.name,
            albums.description,
            photos.thumbnail
        FROM " . G4B_GALLERY_ALBUM_TBL . " AS albums
        LEFT JOIN " . G4B_GALLERY_PHOTO_TBL . " AS photos ON albums.id = photos.album_id
        ORDER BY albums.name, photos.id ASC
        "
    , 'ARRAY_A');
    
    // Left joins don't play nicely with DISTINCT or GROUP BY, so clear
    // duplicates with this function
    $added = array();
    $new = $return;
    if($new)
    {
        $new = array();
        foreach($return as $row)
        {
            if(!isset($added[$row['id']]))
            {
                $new[] = $row;
                $added[$row['id']] = true;
            }
        }
    }
    return $new;
}


/**
 * Get all album details for updates
 */
function G4B_gallery_get_album($albumId)
{
    global $wpdb;
    return $wpdb->get_row("SELECT * FROM " . G4B_GALLERY_ALBUM_TBL . " WHERE id = '" . $wpdb->escape($albumId) . "'", 'ARRAY_A');   
}


/**
 * Get album name for cookie crumb
 */
function G4B_gallery_get_album_name($albumId)
{
    global $wpdb;
    return $wpdb->get_var("SELECT name FROM " . G4B_GALLERY_ALBUM_TBL . " WHERE id = '" . $wpdb->escape($albumId) . "'");   
}


/*
================================================================================
PHOTO FUNCTIONS
================================================================================
*/


/**
 * Handle photo deletions
 */
function G4B_gallery_del_photo($photoId, $noMsg = false)
{
    if(empty($photoId) && !$noMsg)
    {
        G4B_gallery_msg('error', 'You didn\'t select a photo to delete.');
        return;    
    }
    
    global $wpdb;
    if(!is_array($photoId)) $photoId = array($photoId);
    foreach($photoId as $id)
    {
        // First delete the actual files
        $row = $wpdb->get_row("SELECT thumbnail, photo FROM " . G4B_GALLERY_PHOTO_TBL . " WHERE id = '" . $wpdb->escape($id) . "'", 'ARRAY_A');
        @unlink(G4B_GALLERY_UPLOAD_DIR . $row['thumbnail']);
        @unlink(G4B_GALLERY_UPLOAD_DIR . $row['photo']);
        
        // Then delete the database record
        $wpdb->query("DELETE FROM " . G4B_GALLERY_PHOTO_TBL . " WHERE id = '" . $wpdb->escape($id) . "'");
        
        // Notify
        if(!$noMsg)
            G4B_gallery_msg('updated', 'The selected photos where deleted successfully.');
    }
}


/**
 * Get photos within the current album
 */
function G4B_gallery_get_photo($photoId)
{
    global $wpdb;
    $wpdb->show_errors();
    return $wpdb->get_row(
        "SELECT id, name, description, thumbnail, photo FROM " . G4B_GALLERY_PHOTO_TBL . " WHERE id = '" . $wpdb->escape($photoId) . "'",
        'ARRAY_A'
    );
}


/**
 * Update photo
 */
function G4B_gallery_update_photo($postArr)
{
    if(empty($postArr['photo_name']))
    {
        G4B_gallery_msg('error', 'The photo name can\'t be empty.');
        return;
    }
    
    global $wpdb;
    $sql = "
        UPDATE " . G4B_GALLERY_PHOTO_TBL . " SET
            name = '" . $wpdb->escape(stripcslashes($postArr['photo_name'])) . "',
            description = '" . $wpdb->escape(stripcslashes($postArr['photo_desc'])) . "'
        WHERE
            id = '" . $wpdb->escape($postArr['photo_id']) . "'
    ";
    $wpdb->query($sql);
    
    G4B_gallery_msg('updated', 'Photo updated successfully.');
}


/**
 * Get photos within the current album
 */
function G4B_gallery_get_photos($albumId)
{
    global $wpdb;
    return $wpdb->get_results(
        "SELECT * FROM " . G4B_GALLERY_PHOTO_TBL . " WHERE album_id = '" . $wpdb->escape($albumId) . "' ORDER BY name ASC",
        'ARRAY_A'
    );
}


/**
 * Get photo name for cookie crumb
 */
function G4B_gallery_get_photo_name($photoId)
{
    global $wpdb;
    return $wpdb->get_var("SELECT name FROM " . G4B_GALLERY_PHOTO_TBL . " WHERE id = '" . $wpdb->escape($photoId) . "'");   
}


/**
 * Handle file upload
 */
function G4B_gallery_upload_photo($postArr, $fileArr)
{
    if(!G4B_gallery_check())
        return;
    
    global $wpdb;
    if(empty($postArr['photo_name']))
    {
        G4B_gallery_msg('error', 'Please enter a photo name.');
        return;
    }
    
    if(empty($fileArr['photo']['name']))
    {
        G4B_gallery_msg('error', 'Please select a photo.');
        return;
    }
    
    // Check supported file types
    $imageTypes = array (
        '.jpg' => 'image/jpeg',
        '.gif' => 'image/gif',
        '.png' => 'image/png'
    );
    $imageType = array_search(strtolower($fileArr['photo']['type']), $imageTypes);
    if(!$imageType)
    {
        G4B_gallery_msg('error', 'Invalid file type.');
        return;
    }
    
    $uploadName = G4B_GALLERY_UPLOAD_DIR . substr(md5(uniqid(rand(), true)), 0, 8) . $imageType;
    if(!move_uploaded_file($fileArr['photo']['tmp_name'], $uploadName))
        G4B_gallery_msg('error', 'Error: Unable to upload file');
    else
    {   
        // Get photo settings
        $photoWidth = get_option('g4b_gallery_photo_width');
        $photoHeight = get_option('g4b_gallery_photo_height');
        $thumbWidth = get_option('g4b_gallery_thumb_width');
        $thumbHeight = get_option('g4b_gallery_thumb_height');
        
        // Resize images, crop if width and height match
        $photoFile = image_resize($uploadName, $photoWidth, $photoHeight, ($photoWidth == $photoHeight) ? true : false);
        $thumbFile = image_resize($uploadName, $thumbWidth, $thumbHeight, ($thumbWidth == $thumbHeight) ? true : false);
        
        // Delete original only if thumbs returned
        if($photoFile != '' && $thumbFile != '')
            @unlink($uploadName);
        
        // Get only returned file name (remove path)
        // Passback original if image wasn't sized (returned empty)
        $photoBits = explode('/', ($photoFile == '') ? $uploadName : $photoFile);
        $photoFile = $photoBits[count($photoBits) - 1];
        $thumbBits = explode('/', ($thumbFile == '') ? $uploadName : $thumbFile);
        $thumbFile = $thumbBits[count($thumbBits) - 1];
        
        // Insert into database
        $sql = "
            INSERT INTO " . G4B_GALLERY_PHOTO_TBL . "
            (
                album_id,
                name,
                description,
                thumbnail,
                photo
            ) VALUES (
                '" . $wpdb->escape($postArr['album_id']) . "',
                '" . $wpdb->escape(stripslashes($postArr['photo_name'])) . "',
                '" . $wpdb->escape(stripslashes($postArr['photo_desc'])) . "',
                '" . $wpdb->escape($thumbFile) . "',
                '" . $wpdb->escape($photoFile) . "'
            );
        ";
        $wpdb->query($sql);
        G4B_gallery_msg('updated', 'Photo uploaded successfully');
    }
}
?>