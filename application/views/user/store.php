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
				Beli Pemain dengan Uang/Balen
			</h3>
			<br/><br/>
			<br/><br/>
			<div class="panel-group" id="panel-fasilitas">
				<?php foreach ((array)$data->daftar_paket as $value):?>
				<div class="panel panel-default">
					<div class="panel-heading">
						 <a class="panel-title" data-toggle="collapse" data-parent="#panel-fasilitas" 
						 href="#panel-element-<?php echo $value->id_paket; ?>">
						 	Paket <?php echo $value->nama_paket; ?>
						 </a>
					</div>
					<div id="panel-element-<?php echo $value->id_paket; ?>" class="panel-collapse collapse">
						<div class="panel-body">
							<?php echo $value->deskripsi_paket; ?>
							<br/>
							<?php if ($value->id_paket!=4):?>
							Harga Uang: <?php echo number_format($value->harga_uang, 0, '', '.'); ?>
							<br/>
							<?php endif;?>
							Harga Balen: <?php echo number_format($value->harga_balen, 0, '', '.'); ?>
							<br/><br/>
							<?php if ($value->id_paket!=4):?>
							<button type="button" data-id="<?php echo $value->id_paket;?>" class="btn btn-success beli_uang">Beli (Uang)</button>
							<?php endif;?>
							<button type="button" data-id="<?php echo $value->id_paket;?>" class="btn btn-success beli_balen">Beli (Balen)</button>
							<br/><br/>
						</div>
					</div>
				</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
  <br/><br/>
  <div class="text-center">
    <h2 class="text-info">
      Topup Balen
    </h2>
    <p>Masukkan Kode Voucher</p>
    <input type="text" id="kode_voucher"/>
    <button type="button" class="btn btn-primary" id="confirm_btn">Confirm</button>
  </div>
</div>
</div>

<div id="galeri_foto" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      
      <div class="modal-body">
      </div>
      
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- The Bootstrap Image Gallery lightbox, should be a child element of the document body -->
<div id="blueimp-gallery" class="blueimp-gallery">
    <!-- The container for the modal slides -->
    <div class="slides"></div>
    <!-- Controls for the borderless lightbox -->
    <h3 class="title"></h3>
    <a class="prev">‹</a>
    <a class="next">›</a>
    <a class="close">×</a>
    <a class="play-pause"></a>
    <ol class="indicator"></ol>
    <!-- The modal dialog, which will be used to wrap the lightbox content -->
    <div class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body next"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left prev">
                        <i class="glyphicon glyphicon-chevron-left"></i>
                        Previous
                    </button>
                    <button type="button" class="btn btn-primary next">
                        Next
                        <i class="glyphicon glyphicon-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {

        $(".beli_uang").click(function() {
          $.post('<?php echo base_url('user/store/beli_uang')?>', {id_paket: $(this).data("id")}, function(data) {
              	$("#galeri_foto").modal();
              	$("#galeri_foto .modal-body").html(data);
            });
        });

        $(".beli_balen").click(function() {
          $.post('<?php echo base_url('user/store/beli_balen')?>', {id_paket: $(this).data("id")}, function(data){
                $("#galeri_foto").modal();
                $("#galeri_foto .modal-body").html(data);
            });
        });

        $("#confirm_btn").click(function() {
          $.post('<?php echo base_url('user/store/topup_balen')?>', {kode: $("#kode_voucher").val()}, function(data){
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