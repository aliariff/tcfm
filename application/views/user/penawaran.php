<div class="alert" style="display: none;" id="pesan">
  <button type="button" class="close" id="dismiss_btn">&times;</button>
  <div class="isi_pesan">
  </div>
</div>
<ul class="nav nav-tabs nav-justified" id="myTab">
  <li class="active"><a href="#penawaran_masuk" data-toggle="tab">Penawaran Masuk <span class="badge"><?php if($data->notif_masuk!=0)echo $data->notif_masuk;?></span></a></li>
  <li><a href="#penawaran_keluar" id="tab_penawaran_keluar" data-toggle="tab">Penawaran Keluar <span class="badge"><?php if($data->notif_keluar!=0)echo $data->notif_keluar;?></span></a></li>
</ul>
<div class="tab-content">
  <div class="tab-pane active" id="penawaran_masuk">
    <table class="table table-striped table-hover">
      <thead>
        <tr>
          <th>Waktu Penawaran</th>
          <th>Nama Pemain</th>
          <th>Posisi Pemain</th>
          <th>Rating</th>
          <th>Harga Penawaran</th>
          <th>Penawar</th>
          <th>Pesan Penawaran</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach((array)$data->daftar_penawaran_masuk as $value):?>
        <tr >
          <td><?php echo $value->waktu_penawaran;?></td>
          <td><?php echo $value->nama_pemain;?></td>
          <td><?php echo $value->posisi;?></td>
          <td><?php echo $value->rating;?></td>
          <td><?php echo number_format($value->harga_penawaran, 0, ',', '.');?></td>
          <td><a href="<?php echo base_url('user/profil').'/'.$value->penawar;?>"><?php echo $value->penawar;?></a></td>
          <td><?php echo $value->pesan_penawaran;?></td>
          <td>
            <?php
              if ($value->jawaban==99):
            ?>
            <button data-id="<?php echo $value->id_penawaran; ?>" class="btn btn-success btn-terima">Terima</button>
            <button data-id="<?php echo $value->id_penawaran; ?>" class="btn btn-danger btn-tolak">Tolak</button>
            <?php
              endif;
              if($value->jawaban==1):
            ?>
            <h5 style="color:green">Diterima</h5>
            <?php
              endif;
              if($value->jawaban==0):
            ?>
            <h5 style="color:red">Ditolak</h5>
            <?php
              endif;
            ?>
          </td>
        </tr>
        <?php endforeach;?>
      </tbody>
    </table>
  </div>
  <div class="tab-pane" id="penawaran_keluar">
    <table class="table table-striped table-hover">
      <thead>
        <tr>
          <th>Waktu Penawaran</th>
          <th>Nama Pemain</th>
          <th>Posisi Pemain</th>
          <th>Rating</th>
          <th>Harga Penawaran</th>
          <th>Pemilik</th>
          <th>Pesan Penawaran</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach((array)$data->daftar_penawaran_keluar as $value):?>
        <tr style="color: <?php
          if ($value->jawaban==99)
            echo "grey";
          else if ($value->jawaban==0)
            echo "red";
          else if ($value->jawaban==1)
            echo "green";
          else
            echo "red";
        ?>">
          <td><?php echo $value->waktu_penawaran;?></td>
          <td><?php echo $value->nama_pemain;?></td>
          <td><?php echo $value->posisi;?></td>
          <td><?php echo $value->rating;?></td>
          <td><?php echo number_format($value->harga_penawaran, 0, ',', '.');?></td>
          <td><a href="<?php echo base_url('user/profil').'/'.$value->pemilik;?>"><?php echo $value->pemilik;?></a></td>
          <td><?php echo $value->pesan_penawaran;?></td>
          <td>
            <?php
              if ($value->jawaban==99)
                echo "Belum Dijawab";
              else if ($value->jawaban==0)
                echo "Ditolak";
              else if ($value->jawaban==1)
                echo "Diterima";
              else
                echo "Gagal"
            ?>
          </td>
          <td>
            <?php if($value->jawaban==99):?>
            <button data-id="<?php echo $value->id_penawaran; ?>" class="btn btn-danger btn-batal">Batalkan</button>
            <?php endif;?>
          </td>
        </tr>
        <?php endforeach;?>
      </tbody>
    </table>
  </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
  
  $(function () {
    $('#myTab a:first').tab('show')
  });

  $('#tab_penawaran_keluar').click(function(){
    var url = '<?php echo base_url('user/penawaran/baca'); ?>';
    $.post(url);
  });

  $(".btn-terima").click(function() {
    var btn = $(this);
    var id = $(this).data('id');
    var url = '<?php echo base_url('user/penawaran/terima'); ?>';
    $.post(url, {id: id}, function(data) {
      $('#pesan').fadeOut(500);
      if (data.kode==0)
      {
        $('#pesan').removeClass('alert-danger').addClass('alert-success');
        btn.parent().html("<h5 style=\"color:green\">Diterima</h5>");
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

  $(".btn-tolak").click(function() {
    var btn = $(this);
    var id = $(this).data('id');
    var url = '<?php echo base_url('user/penawaran/tolak'); ?>';
    $.post(url, {id: id}, function(data) {
      $('#pesan').fadeOut(500);
      if (data.kode==0)
      {
        $('#pesan').removeClass('alert-danger').addClass('alert-success');
        btn.parent().html("<h5 style=\"color:red\">Ditolak</h5>");
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

  $(".btn-batal").click(function() {
    var btn = $(this);
    var id = $(this).data('id');
    var url = '<?php echo base_url('user/penawaran/batalkan'); ?>';
    $.post(url, {id: id}, function(data) {
      $('#pesan').fadeOut(500);
      if (data.kode==0)
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