<?php
if(isset($_GET['photo'])):
$photo = G4B_gallery_get_photo($_GET['photo']);
$photosArr = G4B_gallery_get_photos($_GET['album']);
$refArr = array();
foreach($photosArr as $key => $value)
    $refArr[$value['id']] = $key;
$nextPhoto = array_search($refArr[$photo['id']] + 1, $refArr);
$prevPhoto = array_search($refArr[$photo['id']] - 1, $refArr);
$loopPhoto = ($nextPhoto) ? $nextPhoto : array_search(0, $refArr);
?>


    <div id="g4b_gallery" class="g4b_photo">
        
        <div class="g4b_crumb">
            <a href="<?php the_permalink(); ?>">Albums</a> >
            <a href="<?php echo G4B_gallery_link('album', $_GET['album']); ?>"><?php echo G4B_gallery_get_album_name($_GET['album']) ?></a> >
            <?php echo $photo['name'] ?>
        </div>
        
        <div class="g4b_row">
            <div class="g4b_img">
                <a href="<?php echo G4B_gallery_link('photo', '', $loopPhoto); ?>" title="<?php echo $photo['name'] ?>">
                    <img src="<?php echo G4B_GALLERY_UPLOAD_URL . $photo['photo'] ?>" alt="<?php echo $photo['name']?>" />
                </a>
            </div>
            <div class="g4b_content">
                <h3>
                    <a href="<?php echo G4B_gallery_link('photo', $_GET['album'], $photo['id']) ?>" title="<?php echo $photo['name']?>">
                        <?php echo $photo['name'] ?>
                    </a>
                </h3>
                <p><?php echo $photo['description'] ?></p>
            </div>
            <div style="clear: both;"></div>
        </div>
        
        <?php if(count($photosArr) > 1): ?>
        <div class="g4b_nav">
            <?php if($prevPhoto): ?>
                <a class="g4b_prev" href="<?php echo G4B_gallery_link('photo', '', $prevPhoto) ?>">Prev</a>
            <?php endif; ?>

            <?php if($nextPhoto): ?>
                <a class="g4b_next" href="<?php echo G4B_gallery_link('photo', '', $nextPhoto); ?>">Next</a>
            <?php endif; ?>
            <div style="clear: both;"></div>
        </div>
        <?php endif; ?>
        
        <div style="clear: both;"></div>
    </div>
    
    
<?php elseif(isset($_GET['album'])): $photos = G4B_gallery_get_photos($_GET['album']) ?>


    <div id="g4b_gallery" class="g4b_photos">
        
        <div class="g4b_crumb">
            <a href="<?php the_permalink(); ?>">Albums</a> >
            <?php echo G4B_gallery_get_album_name($_GET['album']); ?>
        </div>
        
        <?php if($photos): ?>
            <?php foreach($photos as $photo): ?>
            <div class="g4b_row">
                <div class="g4b_img">
                    <a href="<?php echo G4B_gallery_link('photo', $_GET['album'], $photo['id']) ?>" title="<?php echo $photo['name']?>">
                        <img src="<?php echo G4B_GALLERY_UPLOAD_URL . $photo['thumbnail'] ?>" alt="<?php echo $photo['name']?>" />
                    </a>
                </div>
                <div class="g4b_content">
                    <h3>
                        <a href="<?php echo G4B_gallery_link('photo', $_GET['album'], $photo['id']) ?>" title="<?php echo $photo['name']?>">
                            <?php echo $photo['name'] ?>
                        </a>
                    </h3>
                    <p><?php echo $photo['description'] ?></p>
                </div>
                <div style="clear: both;"></div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="g4b_row">
                No photos.
            </div>
        <?php endif; ?>
        
        <div style="clear: both;"></div>
    </div>
    
    
<?php else: $albums = G4B_gallery_get_albums() ?>
    
    
    <div id="g4b_gallery" class="g4b_albums">
        
        <div class="g4b_crumb">Albums</div>
        
        <?php if($albums): ?>
            <?php foreach($albums as $album): if(!empty($album['thumbnail'])): ?>
            <div class="g4b_row">
                <div class="g4b_img">
                    <a href="<?php echo G4B_gallery_link('album', $album['id']) ?>" title="<?php echo $album['name']?>">
                        <img src="<?php echo G4B_GALLERY_UPLOAD_URL . $album['thumbnail'] ?>" alt="<?php echo $album['name']?>" />
                    </a>
                </div>
                <div class="g4b_content">
                    <h3>
                        <a href="<?php echo G4B_gallery_link('album', $album['id']) ?>" title="<?php echo $album['name']?>">
                            <?php echo $album['name'] ?>
                        </a>
                    </h3>
                    <p><?php echo $album['description'] ?></p>
                </div>
                <div style="clear: both;"></div>
            </div>
            <?php endif; endforeach; ?>
        <?php else: ?>
            <div class="g4b_row">
                No albums.
            </div>
        <?php endif; ?>
        
        <div style="clear: both;"></div>
    </div>
    
    
<?php endif; ?>