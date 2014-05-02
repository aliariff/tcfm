<div class="container">
	<div id="pesan" class="alert alert-dismissable" style="display: none">
	    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	    <div class="isi_pesan text-center">
	    </div>
  	</div>
	<div class="row clearfix">
		<div class="col-md-12 column">
			<h3 class="text-primary text-center">
				<img width="10%" src="<?php echo base_url('assets/logo').'/'.$data->user_info->gambar_liga;?>" class="img-rounded" /> 
				<br/>
				<?php echo $data->user_info->nama_liga;?>
			</h3>
			<table class="table table-striped table-hover">
				<thead>
					<tr>
						<th>
							No
						</th>
						<th>
							Nama Tim AI
						</th>
						<th>
							Kekompakan
						</th>
						<th>
							Formasi
						</th>
						<th>
							Exp Gain
						</th>
						<th>
							Uang Gain
						</th>
						<th>
							Aksi
						</th>
					</tr>
				</thead>
				<tbody>
					<?php $ctr=1;foreach((array)$data->daftar_tim_ai as $value):?>
					<tr>
						<td>
							<?php echo $ctr++;?>
						</td>
						<td>
							<a class="tim_ai" data-id="<?php echo $value->id_tim_ai;?>">
								<?php echo $value->nama_tim_ai;?>
							</a>
						</td>
						<td>
							<?php echo $value->kekompakan_tim_ai;?>
						</td>
						<td>
							<?php echo $value->nama_formasi;?>
						</td>
						<td>
							<?php echo $value->exp_gain;?>
						</td>
						<td>
							<?php echo $value->uang_gain;?>
						</td>
						<td>
							<?php
							if ($value->stat == 1) {
							?>
							<h5 style="color:green">Sudah Dikalahkan</h5>
							<?php
							}
							else {
							?>
							<button type="button" data-id="<?php echo $value->id_tim_ai;?>" class="btn btn-success tantang">Tantang</button>
							<?php }?>
						</td>
					</tr>
					<?php endforeach;?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<div id="pemain_tim_ai_modal" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content" style="width: 543px;">
      
      <div class="modal-body">
      </div>
      
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="simulasi_tanding" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      
      <div class="modal-body">
      </div>
      
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type="text/javascript">
$(document).ready(function() {

        $(".tantang").click(function() {
          $.post('<?php echo base_url('user/liga/tantang')?>', {id: $(this).data("id")}, function(data){
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
              setTimeout("location.reload()", 3000);
            }, "json");
        });

        $(".tim_ai").click(function() {
		    $("#pemain_tim_ai_modal").modal();
		    $.post('<?php echo base_url('user/liga/lihat_pemain')?>', {id: $(this).data("id")}, function(data){
		      $("#pemain_tim_ai_modal .modal-body").html(data);
		    });
		  });
});
</script>