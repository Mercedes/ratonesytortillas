<!-- custom Thumbnails column -->
<?php if ($this->image): ?>
	<img src="<?php echo $this->image->getThumbnailHref(array('w=80','h=40', 'fltr[]=usm|60|0.5|3')) ?>">
<?php else: ?>
	--
<?php endif ?>
<!-- /custom Thumbnails column -->