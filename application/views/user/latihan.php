<div id="content">
<div class="container">
	<div id="pesan" class="alert alert-dismissable" style="display: none">
	    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	    <div class="isi_pesan text-center">
	    </div>
  	</div>
	<div class="row clearfix">
		<div class="col-md-12 column">
			<h3 class="text-center text-primary">
				Pilih Latihan yang Akan Dilakukan Untuk Menambah Kekompakan
			</h3>
			<br/><br/>
			<br/><br/>
			<div class="panel-group" id="panel-fasilitas">
				<?php foreach ((array)$data->daftar_latihan as $value):?>
				<div class="panel panel-default">
					<div class="panel-heading">
						 <a class="panel-title" data-toggle="collapse" data-parent="#panel-fasilitas" 
						 href="#panel-element-<?php echo $value->id_latihan; ?>">
						 	Latihan <?php echo $value->nama_latihan; ?>
						 </a>
					</div>
					<div id="panel-element-<?php echo $value->id_latihan; ?>" class="panel-collapse collapse">
						<div class="panel-body">
							Durasi Latihan: <?php echo $value->durasi_latihan_tu;?> menit.
							<br/>
							Penambahan Kekompakan: <?php echo $value->penambahan_kekompakan;?>
							<br/><br/>
							<button type="button" data-id="<?php echo $value->id_latihan;?>" class="btn btn-success lakukan_latihan">Lakukan</button>
							<br/><br/>
							<img width="25%" src="<?php echo base_url('assets/img/latihan') . '/' . $value->gambar_latihan; ?>" />
						</div>
					</div>
				</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</div>
</div>

<script type="text/javascript">
$(document).ready(function() {

        $(".lakukan_latihan").click(function() {
          $.post('<?php echo base_url('user/latihan/lakukan_latihan')?>', {id_latihan: $(this).data("id")}, function(data){
          	  $('#pesan').fadeOut(500);
              if(data.sukses == true)
              {
                  $('#pesan').removeClass('alert-danger').addClass('alert-success');
              }
              else
              {
                $('#pesan').removeClass('alert-success').addClass('alert-danger');
              }
              $('#pesan .isi_pesan').html(data.pesan);
              $('#pesan').fadeIn(500);
            }, "json");
        });
});
</script>