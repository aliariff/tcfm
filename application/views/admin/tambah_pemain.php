<div class="container">
<div class="row clearfix">
	<div class="col-md-4 column">
		<div class="page-header">
		  <h1>Tambah Pemain</h1>
		</div>
		<div class="alert" style="display: none;" id="pesan">
	      <button type="button" class="close" id="dismiss_btn">&times;</button>
	      <div class="isi_pesan">
	      </div>
	    </div>
		<form class="well" id="tambah_pemain_form">
			<div class="form-group">
				<label class="control-label">Nama Pemain</label>
				<input type="text" class="form-control" placeholder="Nama Pemain" id="nama_pemain" name="nama_pemain">
				
				<label class="control-label">Tim Asal</label>
				<input type="text" class="form-control" placeholder="Tim Asal" id="tim_asal" name="tim_asal">

				<label class="control-label">Posisi</label>
				<select class="form-control" id="posisi" name="posisi">
					<option></option>
					<option value="G">G</option>
					<option value="D">D</option>
					<option value="M">M</option>
					<option value="F">F</option>
				</select>

				<label class="control-label">Nilai Attack</label>
				<input type="text" class="form-control" placeholder="Nilai Attack" id="nilai_att" name="nilai_att">

				<label class="control-label">Nilai Defense</label>
				<input type="text" class="form-control" placeholder="Nilai Defense" id="nilai_def" name="nilai_def">

				<label class="control-label">Nilai Speed</label>
				<input type="text" class="form-control" placeholder="Nilai Speed" id="nilai_speed" name="nilai_speed">

				<label class="control-label">Nilai Stamina</label>
				<input type="text" class="form-control" placeholder="Nilai Stamina" id="nilai_stamina" name="nilai_stamina">
				
				<label class="control-label">Limited</label>
				<input type="checkbox" id="flag_limited" name="flag_limited"/>

				<label class="control-label">Tersedia</label>
				<input type="checkbox" id="flag_tersedia" name="flag_tersedia"/>

				<label class="control-label">Paket</label>
				<input type="checkbox" id="flag_paket" name="flag_paket"/>
				<br/>
				<label class="control-label">Foto Pemain</label><br/>
		        <span class="btn btn-success fileinput-button">
		            <i class="glyphicon glyphicon-plus"></i>
		            <span>Unggah Foto...</span>
		            <input id="fileupload" type="file" name="files[]" accept="image/*"/>
		            <input type="hidden" name="foto_pemain" id="foto_pemain"/>
		        </span>
		        <br/><br/>
		        <div id="progress" class="progress">
		          <div class="progress-bar progress-bar-success"></div>
		        </div>
		        <img class="media-object" id="foto_pemain_thumbnail" width="25%">

				<br/>
			  	<button type="submit" id="tambah_pemain_btn" class="btn pull-right btn-primary btn" data-loading-text="Loading...">Tambahkan</button>
			  	<br/><br/>
			</div>
		</form>
  </div>
  <div class="col-md-8 column">
		<div class="page-header">
		  <h1>Daftar Pemain</h1>
		</div>
		<table class="table table-striped table-hover">
      <thead>
        <tr>
          <th>Nama Pemain</th>
          <th>Tim Asal</th>
          <th>Posisi Pemain</th>
          <th>Nilai Attack</th>
          <th>Nilai Defense</th>
          <th>Nilai Speed</th>
          <th>Nilai Stamina</th>
          <th>Rating</th>
          <th>Limited</th>
          <th>Tersedia</th>
          <th>Paket</th>
          <th>Foto Pemain</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach((array)$data->daftar_pemain as $value):?>
        <tr >
          <td><?php echo $value->nama_pemain;?></td>
          <td><?php echo $value->tim_asal;?></td>
          <td><?php echo $value->posisi;?></td>
          <td><?php echo $value->nilai_att;?></td>
          <td><?php echo $value->nilai_def?></td>
          <td><?php echo $value->nilai_speed;?></td>
          <td><?php echo $value->nilai_stamina;?></td>
          <td><?php echo $value->rating;?></td>
          <td><?php echo $value->flag_limited;?></td>
          <td><?php echo $value->flag_tersedia;?></td>
          <td><?php echo $value->flag_paket;?></td>
          <td><a href="<?php echo base_url('assets/img/pemain').'/'.$value->foto_pemain;?>"><?php echo $value->foto_pemain;?></a></td>
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

    $('#tambah_pemain_form').submit(function() {
    	$('#tambah_pemain_btn').button('loading');
      	$.post('<?php echo base_url('admin/tambah_pemain'); ?>', $(this).serialize(), function(data){
        	$('#pesan').fadeOut(500);
	        if (data.sukses==true)
	        {
	          $('#pesan').removeClass('alert-danger').addClass('alert-success');
	          $('#tambah_pemain_form').trigger('reset');
	          $('#tambah_pemain_form').fadeOut(500);
	          setTimeout("location.reload()", 2000);
	        }
	        else
	        {
	          $('#pesan').removeClass('alert-success').addClass('alert-danger');
	        }
	        
	        $('#pesan .isi_pesan').html(data.pesan);
	        $('#pesan').fadeIn(500);
	        $('#tambah_pemain_btn').button('reset');
      	}, "json");
      	return false;
    });

    var url = '<?php echo base_url('upload'); ?>';
    $('#fileupload').fileupload({
    	formData: {param: 'pemain'},
        maxNumberOfFiles: 1,
        url: url,
        dataType: 'json',
        done: function (e, data) {
           $.each(data.result.files, function (index, file) {
                $('#foto_pemain').val(file.name);
                var src = '<?php echo base_url('assets/img/thumbnail'); ?>' + '/' + file.name;
                $('#foto_pemain_thumbnail').attr("src", src);
                $('#foto_pemain_thumbnail').fadeIn(500);
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