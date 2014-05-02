<?php
	$daftar_stadion = array();
	foreach ((array)$data->daftar_stadion as $value) 
	{
		array_push($daftar_stadion, (array)$value);
	}
?>
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
				Pilih Fasilitas Stadion yang Akan Ditingkatkan Sesuai Kemampuanmu
			</h3>
			<br/><br/>
			<button type="button" class="btn btn-success" id="upgrade_stadion">Upgrade</button>
			<button type="button" class="btn btn-warning" id="downgrade_stadion">Downgrade</button>
			<br/><br/>
			<?php
				if ($data->user_info->stadion_id_stadion==1) echo "<h3>Stadion Anda adalah Training Ground</h3> <br/>";
			?>
			<div class="panel-group" id="panel-605028">
				<div class="panel panel-default">
					<div class="panel-heading">
						 <a class="panel-title" data-toggle="collapse" data-parent="#panel-605028" href="#panel-element-106137">Tribun<?php if ($data->user_info->stadion_id_stadion==2)echo " (Stadion Sekarang)"; ?></a>
					</div>
					<div id="panel-element-106137" class="panel-collapse collapse <?php if ($data->user_info->stadion_id_stadion==2)echo "in"; ?>">
						<div class="panel-body">
							Upgrade tribun sehingga pemasukan klub sepakbolamu meningkat.
							<br/>
							Biaya Pembangunan: <?php echo number_format($daftar_stadion[1]['harga_stadion'], 0, '', '.'); ?>
							<br/>
							Biaya Perawatan Perminggu: <?php echo number_format($daftar_stadion[1]['biaya_perawatan'], 0, '', '.'); ?>
							<br/><br/>
							<img width="25%" src="<?php echo base_url('assets/img/stadion') . '/' . $daftar_stadion[1]['gambar_stadion']; ?>" />
						</div>
					</div>
				</div>
				<div class="panel panel-default">
					<div class="panel-heading">
						 <a class="panel-title" data-toggle="collapse" data-parent="#panel-605028" href="#panel-element-106138">Grandstand<?php if ($data->user_info->stadion_id_stadion==3)echo " (Stadion Sekarang)"; ?></a>
					</div>
					<div id="panel-element-106138" class="panel-collapse collapse <?php if ($data->user_info->stadion_id_stadion==3)echo "in"; ?>">
						<div class="panel-body">
							Upgrade tribun sehingga pemasukan klub sepakbolamu...
							<br/>
							Biaya Pembangunan: <?php echo number_format($daftar_stadion[2]['harga_stadion'], 0, '', '.'); ?>
							<br/>
							Biaya Perawatan Perminggu: <?php echo number_format($daftar_stadion[2]['biaya_perawatan'], 0, '', '.'); ?>
							<br/><br/>
							<img width="25%" src="<?php echo base_url('assets/img/stadion') . '/' . $daftar_stadion[2]['gambar_stadion']; ?>" />
						</div>
					</div>
				</div>
				<div class="panel panel-default">
					<div class="panel-heading">
						 <a class="panel-title" data-toggle="collapse" data-parent="#panel-605028" href="#panel-element-458329">Full Granstand<?php if ($data->user_info->stadion_id_stadion==4)echo " (Stadion Sekarang)"; ?></a>
					</div>
					<div id="panel-element-458329" class="panel-collapse collapse <?php if ($data->user_info->stadion_id_stadion==4)echo "in"; ?>">
						<div class="panel-body">
							Upgrade tribun bertingkat sehingga pemasukan klub sepakbolamu...
							<br/>
							Biaya Pembangunan: <?php echo number_format($daftar_stadion[3]['harga_stadion'], 0, '', '.'); ?>
							<br/>
							Biaya Perawatan Perminggu: <?php echo number_format($daftar_stadion[3]['biaya_perawatan'], 0, '', '.'); ?>
							<br/><br/>
							<img width="25%" src="<?php echo base_url('assets/img/stadion') . '/' . $daftar_stadion[3]['gambar_stadion']; ?>" />
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>

<script type="text/javascript">
$(document).ready(function() {

        $("#upgrade_stadion").click(function() {
          $.post('<?php echo base_url('user/fasilitas/upgrade_stadion')?>', null, function(data){
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
              setTimeout("location.reload()", 1000);
            }, "json");
        });

        $("#downgrade_stadion").click(function() {
          $.post('<?php echo base_url('user/fasilitas/downgrade_stadion')?>', null, function(data){
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
              setTimeout("location.reload()", 1000);
            }, "json");
        });
});
</script>