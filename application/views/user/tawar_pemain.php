<h1>Penawaran Pemain</h1>
<p>Anda akan menawar <?php echo $data->pemain->nama_pemain;?><br/>
	Pemilik: <?php echo $data->pemain->user_username?>
</p>
    <div class="alert" style="display: none;" id="pesan">
      <button type="button" class="close" id="dismiss_btn">&times;</button>
      <div class="isi_pesan">
      </div>
    </div>

	<form class="well" id="form_tawar_pemain">
	  <input type="hidden" value="<?php echo $data->pemain->id_user_pemain; ?>" name="id_pemain" >
      <div class="form-group">
        <label class="control-label">Harga Penawaran</label>
        <div class="controls">
          <input type="text" class="form-control" id="harga" name="harga" placeholder="Masukkan Harga Penawaran Untuk Pemain Ini">
        </div>
      </div>
      <div class="form-group">
        <label class="control-label">Pesan Penawaran (Optional)</label>
        <textarea class="form-control" id="pesan" name="pesan" placeholder="Isi Pesan Penawaran" rows="4"></textarea>	
      </div>
      
      <input type="submit" class="btn-primary btn" value="Kirim Penawaran"/>
    </form>
<script type="text/javascript" src="<?php echo base_url('assets/js/jquery.maskMoney.js'); ?>"></script>
<script type="text/javascript">
    $(document).ready(function() {

    	$("#harga").maskMoney({thousands:'.', decimal:',', precision:0});
    	
    	$('#dismiss_btn').click(function(){
	      $('#pesan').fadeOut(500);
	    });

		$('#form_tawar_pemain').submit(function() {
          $.post('<?php echo base_url('user/profil/tawar_pemain');?>', $(this).serialize(), function(data){
            $('#pesan').fadeOut(500);
            if (data.sukses==true)
            {
              $('#pesan').removeClass('alert-danger').addClass('alert-success');
              $('#form_tawar_pemain').trigger('reset');
              $('#form_tawar_pemain').fadeOut(500);
            }
            else
            {
              $('#pesan').removeClass('alert-success').addClass('alert-danger');
            }
            
            $('#pesan .isi_pesan').html(data.pesan);
            $('#pesan').fadeIn(500);
            
          }, "json");
          return false;
    });
	});
</script>