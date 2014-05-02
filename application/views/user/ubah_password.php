<h1 class="page-header">Ubah Password</h1>
<p>Melalui laman berikut, Anda dapat mengubah password Anda.</p>

<div class="row clearfix">
	<div class="col-md-12 column">
		<div class="alert alert-dismissable" style="display: none;" id="pesan">
	    </div>
		<form class="well" id="ubah_password_form">
			<div class="form-group">
				<label class="control-label">Password Lama</label>
				<input type="password" class="form-control" placeholder="Password Lama" id="password_lama" name="password_lama" />
				
				<label class="control-label">Password Baru</label>
				<input type="password" class="form-control" placeholder="Password Baru" id="password_baru" name="password_baru" />

				<label class="control-label">Ulangi Password Baru</label>
				<input type="password" class="form-control" placeholder="Ulangi Password Baru" id="repassword_baru" name="repassword_baru" />
				<br/>
			  	<button type="submit" id="submit_ubah_password_btn" class="btn pull-right btn-primary" data-loading-text="Loading...">Ubah password</button>
				<br/>
			</div>
		</form>
  </div>
</div>
<script type="text/javascript">
	jQuery(document).ready(function () {

		$('#ubah_password_form').submit(function() {
			$('#submit_ubah_password_btn').button('loading');
				$.post('<?php echo base_url('user/dasbor/ubah_password')?>', $(this).serialize(), function(data){
					$('#pesan').fadeOut(500);
					if (data.sukses==true)
					{
						$('#pesan').removeClass('alert-danger').addClass('alert-success');
						$('#ubah_password_form').trigger('reset');
						$('#ubah_password_form').fadeOut(500);
					}
					else
					{
						$('#submit_ubah_password_btn').button('reset');
						$('#pesan').removeClass('alert-success').addClass('alert-danger');
					}
					$('#pesan').html(data.pesan);
					$('#pesan').fadeIn(500);
					
				}, "json");
				return false;
			});
	});
</script>