<div class="jumbotron">
        <h2>Selamat Datang di TC Football Manager!</h2>
        <p class="text-info">Isi form yang tersedia berikut ini dan mainkan TCFM secara GRATIS bersama para pemain lain!</p>
        <p></p>
 </div>
	  <div class="container">
		<div class="row clearfix">
			<div class="col-md-6 column">
				<div class="alert alert-dismissable" style="display: none;" id="pesan">
			    </div>
				<form class="well" id="register_form">
					<div class="form-group">
						<label class="control-label">Username</label>
						<input type="text" class="form-control" placeholder="Username" id="username" name="username">
						
						<label class="control-label">Alamat Email</label>
						<input type="email" class="form-control" placeholder="Email" id="email" name="email">

						<label class="control-label">Password</label>
						<input type="password" class="form-control" placeholder="Password" id="password" name="password">

						<label class="control-label">Password (Tulis Ulang)</label>
						<input type="password" class="form-control" placeholder="Re-Password" id="repassword" name="repassword">
						<span class="label label-danger" id="label1"></span>
						<br/><br/>
						<label>
							<input type="checkbox" value="true" id="setuju" name="setuju"> Saya menyetujui 
							<a href="<?php echo base_url('register/syaratketentuan'); ?>" id="syaratketentuan">syarat &amp; ketentuan</a> 
							dan <a href="<?php echo base_url('register/kebijakanprivasi'); ?>" id="kebijakanprivasi">kebijakan privasi</a> yang berlaku
					  	</label>
					  	<button type="submit" id="register_btn" class="btn pull-right btn-primary btn" data-loading-text="Loading...">Daftar</button>
					  	<br/><br/>
					</div>
				</form>
		  </div>
		</div>
	  </div>

<div id="syaratketentuan_modal" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      
      <div class="modal-body">
      </div>
      
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="kebijakanprivasi_modal" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      
      <div class="modal-body">
      </div>
      
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

    <script type="text/javascript" src="<?php echo base_url('assets/js/pwstrength.js'); ?>"></script>
    <script type="text/javascript">
		jQuery(document).ready(function () {
			"use strict";
			var options = {
			minChar: 8,
			bootstrap3: true,
			};
			$('#password').pwstrength(options);

			$( "#repassword" ).keyup(function() {
				if($( "#repassword" ).val() == $('#password').val())
				{
					$('#label1').removeClass("label-danger").addClass("label-success");
					$('#label1').text("Password Sama");
				}
  				else
  				{
  					$('#label1').removeClass("label-success").addClass("label-danger");
  					$('#label1').text("Password Tidak Sama");
  				} 
			});

			$("#syaratketentuan").click(function() {
				$("#syaratketentuan_modal").modal();
				$.get("<?php echo base_url('register/syaratketentuan'); ?>", null, function(data) {
					$("#syaratketentuan_modal .modal-body").html(data);
				});
				return false;
			});

			$("#kebijakanprivasi").click(function() {
				$("#kebijakanprivasi_modal").modal();
				$.get("<?php echo base_url('register/kebijakanprivasi'); ?>", null, function(data) {
					$("#kebijakanprivasi_modal .modal-body").html(data);
				});
				return false;
			});

			$('#register_form').submit(function() {
				$('#register_btn').button('loading');
					$.post('<?php echo base_url('register')?>', $(this).serialize(), function(data){
						$('#pesan').fadeOut(500);
						if (data.sukses==true)
						{
							$('#pesan').removeClass('alert-danger').addClass('alert-success');
							$('#register_form').trigger('reset');
							$('#register_form').fadeOut(500);
						}
						else
						{
							$('#register_btn').button('reset');
							$('#pesan').removeClass('alert-success').addClass('alert-danger');
						}
						$('#pesan').html(data.pesan);
						$('#pesan').fadeIn(500);
						
					}, "json");
					return false;
				});
		});
	</script>