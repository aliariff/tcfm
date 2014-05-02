<div class="container">
	<div id="pesan" class="alert alert-dismissable" style="display: none">
	    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	    <div class="isi_pesan text-center">
	    </div>
  	</div>
	<div class="row clearfix">
		<div class="col-md-12 column">
			<h3 class="text-primary text-center">
				World League
			</h3>
			<table class="table table-striped table-hover">
				<thead>
					<tr>
						<th>
							Rank
						</th>
						<th>
							Avatar
						</th>
						<th>
							Username
						</th>
						<th>
							Nama Tim
						</th>
						<th>
							Rating
						</th>
						<th>
							EXP
						</th>
						<th>
							Aksi
						</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach((array)$data->all_user as $value):?>
					<tr>
						<td>
							<?php echo $data->start_rank;?>
						</td>
						<td width="15%">
							<img width="45%" src="<?php 
				              if ($value->foto_user)
				                echo base_url('assets/img/user/thumbnail'.'/'.$value->foto_user);
				              else
				                echo base_url('assets/img'.'/no-pic.png');?>" class="img-rounded" />
						</td>
						<td>
							<a href="<?php echo base_url('user/profil').'/'.$value->username;?>"><?php echo $value->username;?></a>
						</td>
						<td>
							<?php echo $value->nama_tim;?>
						</td>
						<td>
							<?php echo $value->rating_tim;?>
						</td>
						<td>
							<?php echo number_format($value->exp, 0, '', '.')?>
						</td>
						<td>
							<?php
							if ($value->status_proteksi == 2) {}
							else if (!$value->status_proteksi) {
							?>
							<button type="button" data-id="<?php echo $value->username;?>" class="btn btn-success tantang">Tantang</button>
							<?php }else {?>
							<h5 style="color:red">Dalam Proteksi</h5>
							<?php }?>
						</td>
					</tr>
					<?php $data->start_rank++; endforeach;?>
				</tbody>
			</table>

			<div class="text-center">
				<?php echo $data->pagination_links;?>
			</div>

		</div>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function() {

        $(".tantang").click(function() {
          $.post('<?php echo base_url('user/rank/tantang')?>', {username: $(this).data("id")}, function(data){
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
});
</script>