<h1 class="page-header">Ubah Profil</h1>
<p>Melalui laman berikut, Anda dapat mengubah profil Anda.</p>

<div class="row clearfix">
	<div class="col-md-12 column">
		<div class="alert alert-dismissable" style="display: none;" id="pesan">
	    </div>
		<form class="well" id="ubah_profil_form">
			<div class="form-group">
				<label class="control-label">Nama Tim</label>
				<input type="text" class="form-control" placeholder="Nama Tim" id="nama_tim" name="nama_tim" value="<?php
					echo $data->user_info->nama_tim;
				?>">
				
				<label class="control-label">Nama Stadion</label>
				<input type="text" class="form-control" placeholder="Nama Stadion" id="nama_stadion" name="nama_stadion" value="<?php
					echo $data->user_info->nama_stadion;
				?>">

				<label class="control-label">Email</label>
				<input type="email" class="form-control" placeholder="Email" id="email" name="email" value="<?php
					echo $data->user_info->email;
				?>">
				<br/>

				<label class="control-label">Foto User</label><br/>
		        <span class="btn btn-success fileinput-button">
		            <i class="glyphicon glyphicon-plus"></i>
		            <span>Unggah Foto...</span>
		            <input id="fileupload" type="file" name="files[]" accept="image/*"/>
		            <input type="hidden" name="foto_user" id="foto_user"/>
		        </span>
		        <br/><br/>
		        <div id="progress" class="progress">
		          <div class="progress-bar progress-bar-success"></div>
		        </div>
		        <img class="media-object" id="foto_user_thumbnail" width="25%">

			  	<button type="submit" id="submit_ubah_profil_btn" class="btn pull-right btn-primary btn" data-loading-text="Loading...">Ubah Profil</button>
				<br/>
			</div>
		</form>
  </div>
</div>
<script type="text/javascript">
	jQuery(document).ready(function () {

		$('#ubah_profil_form').submit(function() {
			$('#submit_ubah_profil_btn').button('loading');
				$.post('<?php echo base_url('user/dasbor/ubah_profil')?>', $(this).serialize(), function(data){
					$('#pesan').fadeOut(500);
					if (data.sukses==true)
					{
						$('#pesan').removeClass('alert-danger').addClass('alert-success');
						$('#ubah_profil_form').trigger('reset');
						$('#ubah_profil_form').fadeOut(500);
					}
					else
					{
						$('#submit_ubah_profil_btn').button('reset');
						$('#pesan').removeClass('alert-success').addClass('alert-danger');
					}
					$('#pesan').html(data.pesan);
					$('#pesan').fadeIn(500);
					
				}, "json");
				return false;
			});

		var url = '<?php echo base_url('upload'); ?>';
	    $('#fileupload').fileupload({
	    	formData: {param: 'user'},
	        maxNumberOfFiles: 1,
	        url: url,
	        dataType: 'json',
	        done: function (e, data) {
	           $.each(data.result.files, function (index, file) {
	                $('#foto_user').val(file.name);
	                var src = '<?php echo base_url('assets/img/user/thumbnail'); ?>' + '/' + file.name;
	                $('#foto_user_thumbnail').attr("src", src);
	                $('#foto_user_thumbnail').fadeIn(500);
	            });
	        },
	        progressall: function (e, data) {
	            var progress = parseInt(data.loaded / data.total * 100, 10);
	            $('#progress .progress-bar').css(
	                'width',
	                progress + '%'
	            );
	        }
	    }).prop('disabled', !$.support.fileInput)
	        .parent().addClass($.support.fileInput ? undefined : 'disabled');

	});
</script>