<div id="content">
	<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div id="primary" class="content-area">
				<main id="main" class="site-main" role="main">
					<?php
						$this->load->view($template_part, array(
							'page_title' => $page_title
						)); 
					?>
					
				</main><!-- #main -->
			</div><!-- #primary -->
		</div><!-- .col-md-12 -->
	</div><!-- .row -->
</div>
</div><!-- .container -->