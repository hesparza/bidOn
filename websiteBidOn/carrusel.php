<?php
session_start();
?>
<div class="wrapper-slides">
	<!-- SlidesJS Required: Start Slides -->
	<!-- The container is used to define the width of the slideshow -->
	<div class="container">
		<div id="slides">
			<img src="img/slide1.jpg" alt="Subasta 1"> <img src="img/slide2.jpg"
				alt="Subasta 2"> <img src="img/slide3.jpg" alt="Subasta 3"> <img
				src="img/slide4.jpg" alt="Subasta 4"> <a href="#"
				class="slidesjs-previous slidesjs-navigation"><i
				class="icon-chevron-left icon-large"></i></a> <a href="#"
				class="slidesjs-next slidesjs-navigation"><i
				class="icon-chevron-right icon-large"></i></a>
		</div>
	</div>
	<!-- End SlidesJS Required: Start Slides -->
	<!-- SlidesJS Required: Link to jQuery -->
	<script src="js/jquery.js"></script>
	<!-- End SlidesJS Required -->
	<!-- SlidesJS Required: Link to jquery.slides.js -->
	<script src="js/jquery.slides.min.js"></script>
	<!-- End SlidesJS Required -->
	<!-- SlidesJS Required: Initialize SlidesJS with a jQuery doc ready -->
	<script>
    $(function() {
      $('#slides').slidesjs({
        width: 960,
        height: 320,
        navigation: false
      });
    });
  </script>
	<!-- End SlidesJS Required -->
	<div class="searchbox">
		<input name="search" type="text" value="Buscar..." size="50"
			maxlength="50" />
	</div>
</div>