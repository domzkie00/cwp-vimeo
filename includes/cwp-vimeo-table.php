<!-- <h1>Vimeo</h1> -->
<div class="row">
	<?php  
		if($result['body']['data']) {
			foreach($result['body']['data'] as $video) {
	?>
				<div class="col-md-12 video-row">
					<?= $video['embed']['html'] ?>
				</div>
	<?php 
			}
		} 
	?>
</div>