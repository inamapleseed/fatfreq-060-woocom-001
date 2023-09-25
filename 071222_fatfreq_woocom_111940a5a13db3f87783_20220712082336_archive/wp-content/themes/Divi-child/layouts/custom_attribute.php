<?php
    $repeater = get_field('repeater');
	if($repeater):
		foreach ($repeater as $rep){
?>
    <div class="attr-row img-<?= $rep['image_position']; ?>">
        <div class="image">
            <img src="<?= $rep['image']; ?>" alt="<?= $rep['title']; ?>">
        </div>
        <div class="infos">
            <div class="title"><?= $rep['title']; ?></div>
            <div class="description"><?= $rep['description']; ?></div>
        </div>
    </div>
<?php
    }
	endif
?>

<script>
	if(navigator.userAgent.match(/Trident\/7\./)) {
		document.body.addEventListener("mousewheel", function() {
			event.preventDefault();
			var wd = event.wheelDelta;
			var csp = window.pageYOffset;
			window.scrollTo(0, csp - wd);
		});
	}

	jQuery(window).on('load', function(){
		
		AOS.init({
			duration: 1000
		});
	});	

	jQuery(".attr-row .image").attr('data-aos', 'fade-left');
	jQuery(".attr-row .infos").attr('data-aos', 'fade-right');
</script>