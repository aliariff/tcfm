<div class="container">
<div class="row clearfix">
	<div class="col-md-4 column">
		<div class="page-header">
		  <h1>Tambah Formasi</h1>
		  <p>Masukkan Formasi dengan Pemisah '-' dan harus 3 area</p>
		</div>
		<div class="alert" style="display: none;" id="pesan">
	      <button type="button" class="close" id="dismiss_btn">&times;</button>
	      <div class="isi_pesan">
	      </div>
	    </div>
		<form class="well" id="tambah_formasi_form">
			<div class="form-group">
				<label class="control-label">Nama Formasi</label>
				<input type="text" class="form-control" placeholder="Nama Formasi" id="nama_formasi" name="nama_formasi">

				<br/>
			  	<button type="submit" id="tambah_formasi_btn" class="btn pull-right btn-primary btn" data-loading-text="Loading...">Tambahkan</button>
			  	<br/><br/>
			</div>
		</form>
  </div>
  <div class="col-md-8 column">
		<div class="page-header">
		  <h1>Daftar Formasi</h1>
		</div>
		<table class="table table-striped table-hover">
      <thead>
        <tr>
          <th>Nama Formasi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach((array)$data->daftar_formasi as $value):?>
        <tr>
          <td><?php echo $value->nama_formasi;?></td>
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

    $('#tambah_formasi_form').submit(function() {
    	$('#tambah_formasi_btn').button('loading');
      	$.post('<?php echo base_url('admin/tambah_formasi'); ?>', $(this).serialize(), function(data){
        	$('#pesan').fadeOut(500);
	        if (data.sukses==true)
	        {
	          $('#pesan').removeClass('alert-danger').addClass('alert-success');
	          $('#tambah_formasi_form').trigger('reset');
	          $('#tambah_formasi_form').fadeOut(500);
	          setTimeout("location.reload()", 2000);
	        }
	        else
	        {
	          $('#pesan').removeClass('alert-success').addClass('alert-danger');
	        }
	        
	        $('#pesan .isi_pesan').html(data.pesan);
	        $('#pesan').fadeIn(500);
	        $('#tambah_formasi_btn').button('reset');
      	}, "json");
      	return false;
    });

});
</script>