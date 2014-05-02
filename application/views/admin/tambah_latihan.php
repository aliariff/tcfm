<div class="container">
<div class="row clearfix">
	<div class="col-md-4 column">
		<div class="page-header">
		  <h1>Tambah Latihan</h1>
		</div>
		<div class="alert" style="display: none;" id="pesan">
	      <button type="button" class="close" id="dismiss_btn">&times;</button>
	      <div class="isi_pesan">
	      </div>
	    </div>
		<form class="well" id="tambah_latihan_form">
			<div class="form-group">
				<label class="control-label">Nama Latihan</label>
				<input type="text" class="form-control" placeholder="Nama Latihan" id="nama_latihan" name="nama_latihan">
				
				<label class="control-label">Durasi Latihan (TU)</label>
				<input type="text" class="form-control" placeholder="Durasi Latihan" id="durasi_latihan_tu" name="durasi_latihan_tu">

				<label class="control-label">Penambahan Kekompakan</label>
				<input type="text" class="form-control" placeholder="Penambahan Kekompakan" id="penambahan_kekompakan" name="penambahan_kekompakan">

				<br/>
				<label class="control-label">Gambar Latihan</label><br/>
		        <span class="btn btn-success fileinput-button">
		            <i class="glyphicon glyphicon-plus"></i>
		            <span>Unggah Gambar...</span>
		            <input id="fileupload" type="file" name="files[]" accept="image/*"/>
		            <input type="hidden" name="gambar_latihan" id="gambar_latihan"/>
		        </span>
		        <br/><br/>
		        <div id="progress" class="progress">
		          <div class="progress-bar progress-bar-success"></div>
		        </div>
		        <img class="media-object" id="gambar_latihan_thumbnail" width="25%">

				<br/>
			  	<button type="submit" id="tambah_latihan_btn" class="btn pull-right btn-primary btn" data-loading-text="Loading...">Tambahkan</button>
			  	<br/><br/>
			</div>
		</form>
  </div>
  <div class="col-md-8 column">
		<div class="page-header">
		  <h1>Daftar Latihan</h1>
		</div>
		<table class="table table-striped table-hover">
      <thead>
        <tr>
          <th>Nama Latihan</th>
          <th>Durasi Latihan (TU)</th>
          <th>Penambahan Kekompakan</th>
          <th>Gambar Latihan</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach((array)$data->daftar_latihan as $value):?>
        <tr >
          <td><?php echo $value->nama_latihan;?></td>
          <td><?php echo $value->durasi_latihan_tu;?></td>
          <td><?php echo $value->penambahan_kekompakan;?></td>
          <td><a href="<?php echo base_url('assets/img/latihan').'/'.$value->gambar_latihan;?>"><?php echo $value->gambar_latihan;?></a></td>
        </tr>
        <?php endforeach;?>
      </tbody>
    </table>
  </div>
</div>
</div>

<script>
$(document).ready(function() {

    $('#dismiss_btn').click(function(){
      $('#pesan').fadeOut(500);
    });

    $('#tambah_latihan_form').submit(function() {
    	$('#tambah_latihan_btn').button('loading');
      	$.post('<?php echo base_url('admin/tambah_latihan'); ?>', $(this).serialize(), function(data){
        	$('#pesan').fadeOut(500);
	        if (data.sukses==true)
	        {
	          $('#pesan').removeClass('alert-danger').addClass('alert-success');
	          $('#tambah_latihan_form').trigger('reset');
	          $('#tambah_latihan_form').fadeOut(500);
	          setTimeout("location.reload()", 2000);
	        }
	        else
	        {
	          $('#pesan').removeClass('alert-success').addClass('alert-danger');
	        }
	        
	        $('#pesan .isi_pesan').html(data.pesan);
	        $('#pesan').fadeIn(500);
	        $('#tambah_latihan_btn').button('reset');
      	}, "json");
      	return false;
    });

    var url = '<?php echo base_url('upload'); ?>';
    $('#fileupload').fileupload({
    	formData: {param: 'latihan'},
        maxNumberOfFiles: 1,
        url: url,
        dataType: 'json',
        done: function (e, data) {
           $.each(data.result.files, function (index, file) {
                $('#gambar_latihan').val(file.name);
                var src = '<?php echo base_url('assets/img/latihan/thumbnail'); ?>' + '/' + file.name;
                $('#gambar_latihan_thumbnail').attr("src", src);
                $('#gambar_latihan_thumbnail').fadeIn(500);
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