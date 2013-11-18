<div class="content-wrapper  js-content-wrapper">
	<h1 class="visually-hidden">Main Section</h1>
	<section id="main" role="main">
		<div id="header-region">
			<header role="banner">
				<nav role="navigation" class="top-bar  top-bar-login" data-options="is_hover:false">
					<ul class="title-area">
						<!-- retina ready logo is a css background image -->
						<li class="name">
							<div class="logo-image"></div>
							<h1><a href="<?php echo URL::site(''); ?>">Deployment Name</a></h1>
						</li> <!-- end .name -->
					</ul> <!-- end .title-area -->

			    <section class="top-bar-section  top-bar-section-login">
						<!-- Right Nav Section -->
						<ul class="right">
							<?php if ($logged_in) : ?>
								<li class="show-for-medium-up"><a class="inverse" href="<?php echo URL::site('user/logout'); ?>"><i class="fa  fa-user  fa-lg"></i> LOGOUT</a></li>
								<li class="show-for-small"><a class="inverse" href="<?php echo URL::site('user/logout'); ?>"><i class="fa  fa-user  fa-lg"></i></a></li>
							<?php else: ?>
								<li class="show-for-medium-up"><a class="inverse" href="<?php echo URL::site('user'); ?>"><i class="fa  fa-user  fa-lg"></i> LOGIN/REGISTER</a></li>
								<li class="show-for-small"><a class="inverse" href="<?php echo URL::site('user'); ?>"><i class="fa  fa-user  fa-lg"></i></a></li>
							<?php endif; ?>
						</ul>
					</section> <!-- end .top-bar-section -->
				</nav> <!-- end .navigation -->
			</header> <!-- end .banner -->
		</div>