<div class="container">
	<div class="row clearfix">
		<div class="col-md-2 column">
			<img width="100%" src="<?php echo base_url('assets/img').'/limited.jpg';?>" class="img-rounded" /> 
			<br/><br/>
			<a href="<?php echo base_url('user/limited'); ?>" class="btn btn-info btn-block" type="button">Limited This Week</a>
		</div>
		<div class="col-md-6 column">
			<a href="<?php echo base_url('user/timsaya'); ?>" class="label label-info">Rating Tim Saya</a>
			<br/><br/>
			<div class="progress tooltipp" id="progress_rating" title="<?php echo $data->user_info->rating ?>">
				<div class="progress-bar">
				</div>
			</div> 

			<a href="<?php echo base_url('user/latihan'); ?>" class="label label-info">Latihan</a>
			<br/><br/>
			<div class="progress tooltipp" id="progress_training" title="<?php echo $data->user_info->kekompakan ?>">
				<div class="progress-bar">
				</div>
			</div>
			<div class="well">
				<div class="row">
		        <label class="col-sm-8"><span class="glyphicon glyphicon-info-sign"></span> Info Tim Saya</label>
		        </div>
				<div class="row clearfix">
					<div class="col-md-6 column">
						<label>Nama Tim</label><br/>
						<label>Nama Stadion</label><br/>
						<label>Level Stadion</label><br/>
						<label>Email</label><br/>
						<label>Nama Formasi</label><br/>
						<label>Nama Liga</label><br/>
					</div>
					<div class="col-md-6 column">
						<label>: <?php echo $data->user_info->nama_tim; ?></label><br/>
						<label>: <?php echo $data->user_info->nama_stadion; ?></label><br/>
						<label>: <?php echo $data->user_info->stadion_id_stadion; ?></label><br/>
						<label>: <?php echo $data->user_info->email; ?></label><br/>
						<label>: <?php echo $data->user_info->nama_formasi; ?></label><br/>
						<label>: <?php echo $data->user_info->nama_liga; ?></label><br/>
					</div>
				</div>
				<br/>
				<button type="button" class="btn btn-primary" id="ubah_profil_btn">Ubah Profil</button>
				<button type="button" class="btn btn-primary" id="ubah_password_btn">Ubah Password</button>
			</div>
		</div>
		<div class="col-md-4 column">
			<img width="100%" src="<?php echo base_url('assets/logo').'/ongoing_events.bmp';?>" class="img-rounded" /> 
			<br/><br/>
			<div class="panel-group" id="panel-396524">
				<div class="panel panel-default">
					<div class="panel-heading">
						 <a class="panel-title" data-toggle="collapse" data-parent="#panel-396524" href="#panel-element-17612">
						 	Uang Lebih Untuk 50 Pendaftar Pertama!
						 </a>
					</div>
					<div id="panel-element-17612" class="panel-collapse collapse in">
						<div class="panel-body">
							50 Pendaftar Pertama pada TCFM akan mendapatkan Uang awalan 55.000!
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="ubah_profil_modal" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      
      <div class="modal-body">
      </div>
      
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="ubah_password_modal" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      
      <div class="modal-body">
      </div>
      
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type="text/javascript">
	jQuery(document).ready(function () {
		$('.tooltipp').tooltip({placement: 'top'});

		var loaded = <?php echo $data->user_info->kekompakan ?>;
		var total = <?php echo $data->user_info->kekompakan_maksimal ?>;
		var progress = parseInt(loaded / total * 100, 10);
		if (progress<=25)
		{
			$('#progress_training').children().removeClass('progress-bar-success');
			$('#progress_training').children().removeClass('progress-bar-info');
			$('#progress_training').children().removeClass('progress-bar-warning');
			$('#progress_training').children().addClass('progress-bar-danger');
		}
		else if (progress<=50)
		{
			$('#progress_training').children().removeClass('progress-bar-success');
			$('#progress_training').children().removeClass('progress-bar-info');
			$('#progress_training').children().removeClass('progress-bar-danger');
			$('#progress_training').children().addClass('progress-bar-warning');
		}
		else if (progress<=75)
		{
			$('#progress_training').children().removeClass('progress-bar-success');
			$('#progress_training').children().removeClass('progress-bar-danger');
			$('#progress_training').children().removeClass('progress-bar-warning');
			$('#progress_training').children().addClass('progress-bar-info');
		}
		else
		{
			$('#progress_training').children().removeClass('progress-bar-danger');
			$('#progress_training').children().removeClass('progress-bar-info');
			$('#progress_training').children().removeClass('progress-bar-warning');
			$('#progress_training').children().addClass('progress-bar-success');
		}
        $('#progress_training .progress-bar').css(
            'width',
            progress + '%'
        );

        var loaded = <?php echo $data->user_info->rating ?>;
		var total = 100;
		var progress = parseInt(loaded / total * 100, 10);
		if (progress<=25)
		{
			$('#progress_rating').children().removeClass('progress-bar-success');
			$('#progress_rating').children().removeClass('progress-bar-info');
			$('#progress_rating').children().removeClass('progress-bar-warning');
			$('#progress_rating').children().addClass('progress-bar-danger');
		}
		else if (progress<=50)
		{
			$('#progress_rating').children().removeClass('progress-bar-success');
			$('#progress_rating').children().removeClass('progress-bar-info');
			$('#progress_rating').children().removeClass('progress-bar-danger');
			$('#progress_rating').children().addClass('progress-bar-warning');
		}
		else if (progress<=75)
		{
			$('#progress_rating').children().removeClass('progress-bar-success');
			$('#progress_rating').children().removeClass('progress-bar-danger');
			$('#progress_rating').children().removeClass('progress-bar-warning');
			$('#progress_rating').children().addClass('progress-bar-info');
		}
		else
		{
			$('#progress_rating').children().removeClass('progress-bar-danger');
			$('#progress_rating').children().removeClass('progress-bar-info');
			$('#progress_rating').children().removeClass('progress-bar-warning');
			$('#progress_rating').children().addClass('progress-bar-success');
		}
        $('#progress_rating .progress-bar').css(
            'width',
            progress + '%'
        );

		$("#ubah_profil_btn").click(function() {
			$("#ubah_profil_modal").modal();
			$.get("<?php echo base_url('user/dasbor/ubah_profil'); ?>", null, function(data) {
				$("#ubah_profil_modal .modal-body").html(data);
			});
			return false;
		});

		$("#ubah_password_btn").click(function() {
			$("#ubah_password_modal").modal();
			$.get("<?php echo base_url('user/dasbor/ubah_password'); ?>", null, function(data) {
				$("#ubah_password_modal .modal-body").html(data);
			});
			return false;
		});
	});
</script>