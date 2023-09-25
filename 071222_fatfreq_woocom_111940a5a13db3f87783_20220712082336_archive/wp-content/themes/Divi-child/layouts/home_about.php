<?php
    $image = get_field('about_image');
    $title = get_field('about_title');
    $description = get_field('about_description');
    $button = get_field('about_button');
    $button_url = get_field('about_button_url');

    $repeater = get_field('slideshow_banner');
?>
<div class="slideshow-con my-slideshow" style="position: fixed; width: 100%">
    <?php foreach ($repeater as $re) :?>
        <div class="slideshow-inner" style="background: url('<?php echo $re['banner_image'];?>'); background-repeat: no-repeat; background-size: cover">
            <img style="opacity: 0; " src="<?php echo $re['banner_image'];?>" alt="img">

            <div class="description">
                <div class="title"><?php echo $re['banner_title'];?></div>
                <div class="desc"><?php echo $re['banner_description'];?></div>
                <a href="<?php echo $re['button_url'];?>" class="btn-"> <?php echo $re['button_name'];?></a>
            </div>
        </div>
    <?php endforeach?>
</div>

<div class="slideshow-con2 my-slideshow" style="opacity: 0">
    <?php foreach ($repeater as $re) :?>
        <div class="slideshow-inner">
            <img style="opacity: 0" src="<?php echo $re['banner_image'];?>" alt="img">

            <div class="description">
                <div class="title"><?php echo $re['banner_title'];?></div>
                <div class="desc"><?php echo $re['banner_description'];?></div>
                <a href="<?php echo $re['button_url'];?>" class="btn-"> <?php echo $re['button_name'];?></a>
            </div>
        </div>
    <?php endforeach?>
</div>
<div class="home-about">
    <div class="image" style="background: url(<?php echo $image; ?>); background-size: contain; background-attachment: fixed; background-repeat: no-repeat">
        <img style="opacity: 0" src="<?php echo $image; ?>" alt="image">
    </div>
    <div class="text">
        <h3><?php echo $title; ?></h3>
        <p><?php echo $description; ?></p>
        <a href="<?php echo $button_url; ?>" class="btn-"><?php echo $button; ?></a>
    </div>
</div>


<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
<script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

<script type="text/javascript">
  function initSlick() {
    jQuery('.slideshow-con').slick({
      dots: false,
      infinite: true,
      speed: 500,
      arrows: true,
      asNavFor: '.slideshow-con2',
      pauseOnHover: false,
      autoplay: false,
      slidesToShow: 1,
		prevArrow: "<div class='pointer slick-nav left prev absolute'><div class='absolute position-center-center'><i class='fa fa-chevron-left'></i></div></div>",
		nextArrow: "<div class='pointer slick-nav right next absolute'><div class='absolute position-center-center'><i class='fa fa-chevron-right'></i></div></div>",

    });
  }
  initSlick();
</script>

<script>
  function initSlick2() {
    jQuery('.slideshow-con2').slick({
      dots: false,
      infinite: true,
      speed: 500,
      arrows: true,
      asNavFor: '.slideshow-con',
      pauseOnHover: false,
      autoplay: false,
      slidesToShow: 1,
		prevArrow: "<div class='pointer slick-nav left prev absolute'><div class='absolute position-center-center'><i class='fa fa-chevron-left'></i></div></div>",
		nextArrow: "<div class='pointer slick-nav right next absolute'><div class='absolute position-center-center'><i class='fa fa-chevron-right'></i></div></div>",

    });
  }
  initSlick2();
</script>

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

	jQuery(".slideshow-con").attr('data-aos', 'fade-right');
	jQuery(".home-about .image").attr('data-aos', 'fade-left');
	jQuery(".home-about .text").attr('data-aos', 'fade-right');
</script>