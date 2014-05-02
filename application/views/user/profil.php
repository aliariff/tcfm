<?php
  if($data->user_info==null)
    redirect(base_url('user/dasbor'));
  $ctr=1;
  $line = explode("-", $data->user_info->nama_formasi);
?>
<div id="content">
<div class="container">
  <div class="row clearfix">
    <div class="col-md-6 column">
      <div id="lapangan">
        <br/><br/><br/>
        <?php
          for($i=0; $i<$line[2]; $i++)
          {
        ?>
          <div class="dropo<?php echo $line[2]; ?>">
          <div id="<?php echo $ctr; $ctr++; ?>" class="player">
            <?php 
              $temp = $ctr - 1;
              $flag = 0; 
              foreach ((array)$data->daftar_lineup_user as $value) 
              {
                if($value->aktif==$temp)
                {
                    echo "<img class=\"tooltipp\" data-toggle=\"tooltip\" title=\"".$value->nama_pemain."\" height=10% width=10% src=\"" . base_url("assets/img/pemain/$value->foto_pemain"). "\" />";
                    $flag = 1;
                    break;
                }
              }
              if (!$flag)
                  echo "<img height=10% width=10% src=\"" . base_url("assets/img/no-pic.png"). "\" />";
            ?>
          </div>
          </div>
        <?php
          }
        ?>


        <br/><br/><br/><br/><br/><br/>

        <?php
          for($i=0; $i<$line[1]; $i++)
          {
        ?>
          <div class="dropo<?php echo $line[1]; ?>">
          <div id="<?php echo $ctr; $ctr++; ?>" class="player">
            <?php
              $temp = $ctr - 1;
              $flag = 0; 
              foreach ((array)$data->daftar_lineup_user as $value) 
              {
                if($value->aktif==$temp) 
                {
                    echo "<img class=\"tooltipp\" data-toggle=\"tooltip\" title=\"".$value->nama_pemain."\" height=10% width=10% src=\"" . base_url("assets/img/pemain/$value->foto_pemain"). "\" />";
                    $flag = 1;
                    break;
                }
              }
              if (!$flag)
                  echo "<img height=10% width=10% src=\"" . base_url("assets/img/no-pic.png"). "\" />";
            ?>
          </div>
          </div>
        <?php
          }
        ?>

        <br/><br/><br/><br/><br/><br/>

        <?php
          for($i=0; $i<$line[0]; $i++)
          {
        ?>
          <div class="dropo<?php echo $line[0]; ?>">
          <div id="<?php echo $ctr; $ctr++; ?>" class="player">
            <?php
              $temp = $ctr - 1;
              $flag = 0; 
              foreach ((array)$data->daftar_lineup_user as $value) 
              {
                if($value->aktif==$temp) 
                {
                    echo "<img class=\"tooltipp\" data-toggle=\"tooltip\" title=\"".$value->nama_pemain."\" height=10% width=10% src=\"" . base_url("assets/img/pemain/$value->foto_pemain"). "\" />";
                    $flag = 1;
                    break;
                }
              }
              if (!$flag)
                  echo "<img height=10% width=10% src=\"" . base_url("assets/img/no-pic.png"). "\" />";
            ?>
          </div>
          </div>
        <?php
          }
        ?>

        <br/><br/><br/>

        <div class="dropo1">
          <div id="11" class="player">
            <?php
              $temp = 11;
              $flag = 0; 
              foreach ((array)$data->daftar_lineup_user as $value) 
              {
                if($value->aktif==$temp) 
                {
                    echo "<img class=\"tooltipp\" data-toggle=\"tooltip\" title=\"".$value->nama_pemain."\" height=10% width=10% src=\"" . base_url("assets/img/pemain/$value->foto_pemain"). "\" />";
                    $flag = 1;
                    break;
                }
              }
              if (!$flag)
                  echo "<img height=10% width=10% src=\"" . base_url("assets/img/no-pic.png"). "\" />";
            ?>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-6 column">
      <span class="label label-primary">Formasi</span>
      &nbsp;
      <span class="label label-primary"><?php echo $data->user_info->nama_formasi;?></span>
      <br/>
      <div style="height:200px;overflow:auto;">
        <table class="table table-hover table-condensed" id="tabel_pemain">
        <thead>
          <tr>
            <th>
              Nama
            </th>
            <th>
              Rating
            </th>
            <th>
              Posisi
            </th>
            <th>
              Aksi
            </th>
          </tr>
        </thead>
        <tbody id="body_tabel_pemain">
          <?php
            foreach ((array)$data->daftar_pemain_user as $pemain) {
          ?>
          <tr name="<?php echo $pemain->id_user_pemain; ?>" id="<?php echo $pemain->id_user_pemain; ?>" data-nama="<?php echo $pemain->nama_pemain; ?>" data-foto="<?php echo $pemain->foto_pemain; ?>">
            <td>
              <?php echo $pemain->nama_pemain; ?>
            </td>
            <td>
              <?php echo $pemain->rating; ?>
            </td>
            <td>
              <?php echo $pemain->posisi; ?>
            </td>
            <td>
              <button type="button" class="tawar btn btn-success">Tawar</button>
            </td>
          </tr>
          <?php 
            }
          ?>
        </tbody>
      </table>
      </div>
      <br/>
      <div class="well">
        <div class="row">
        <label class="col-sm-8"><span class="glyphicon glyphicon-info-sign"></span> Detail Info</label>
        </div>

        <div class="row">
        <label class="col-sm-2">Nama</label>
        <label class="col-sm-5" id="nama_pemain">: </label>

        <label class="col-sm-2">Attack</label>
        <label class="col-sm-3" id="att">: </label>
        </div>

        <div class="row">
        <label class="col-sm-2">Tim Asal</label>
        <label class="col-sm-5" id="tim_asal">: </label>

        <label class="col-sm-2">Defense</label>
        <label class="col-sm-3" id="def">: </label>
        </div>

        <div class="row">
        <label class="col-sm-2">Posisi</label>
        <label class="col-sm-5" id="posisi">: </label>

        <label class="col-sm-2">Speed</label>
        <label class="col-sm-3" id="speed">: </label>
        </div>

        <div class="row">
        <label class="col-sm-2">Rating</label>
        <label class="col-sm-5" id="rating">: </label>

        <label class="col-sm-2">Stamina</label>
        <label class="col-sm-3" id="stamina">: </label>
        </div>
        <div class="row">
            <div class="col-sm-4">
            </div>
            <img id="foto_pemain" src="<?php echo base_url('assets/img/no-pic.png'); ?>" />
        </div>

      </div>
    </div>
  </div>
</div>
</div>

<div id="tawar_pemain_modal" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      
      <div class="modal-body">
      </div>
      
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type="text/javascript" src="<?php echo base_url('assets/js/jquery.tableSelect.js'); ?>"></script>
<script type="text/javascript">
$(document).ready(function() {

        $(".tawar").click(function() {
          var id = $(this).parent().parent().attr('id');
        $("#tawar_pemain_modal").modal();
          $.get("<?php echo base_url('user/profil/tawar_pemain'); ?>", {id: id}, function(data) {
            $("#tawar_pemain_modal .modal-body").html(data);
          });
          return false;
        });

        $('.tooltipp').tooltip({placement: 'top',trigger: 'manual'}).tooltip('show');
        
        $('#tabel_pemain').tableSelect({
          onClick: function(row) {
            $.post('<?php echo base_url('helper/detail_pemain')?>', {id:$(row).attr('id')}, function(data){
              if(data.sukses == true)
              {
                $('#nama_pemain').text(": " + data.pesan.nama_pemain);
                $('#tim_asal').text(": " + data.pesan.tim_asal);
                $('#posisi').text(": " + data.pesan.posisi);
                $('#rating').text(": " + data.pesan.rating);
                $('#att').text(": " + data.pesan.nilai_att);
                $('#def').text(": " + data.pesan.nilai_def);
                $('#speed').text(": " + data.pesan.nilai_speed);
                $('#stamina').text(": " + data.pesan.nilai_stamina);
                $("#foto_pemain").attr("src", "<?php echo base_url('assets/img/pemain'); ?>" + "/" + data.pesan.foto_pemain);
              }
            }, "json");
          },
          onChange: function(row) {
            $.post('<?php echo base_url('helper/detail_pemain')?>', {id:$(row).attr('id')}, function(data){
              if(data.sukses == true)
              {
                $('#nama_pemain').text(": " + data.pesan.nama_pemain);
                $('#tim_asal').text(": " + data.pesan.tim_asal);
                $('#posisi').text(": " + data.pesan.posisi);
                $('#rating').text(": " + data.pesan.rating);
                $('#att').text(": " + data.pesan.nilai_att);
                $('#def').text(": " + data.pesan.nilai_def);
                $('#speed').text(": " + data.pesan.nilai_speed);
                $('#stamina').text(": " + data.pesan.nilai_stamina);
                $("#foto_pemain").attr("src", "<?php echo base_url('assets/img/pemain'); ?>" + "/" + data.pesan.foto_pemain);
              }
            }, "json");
          }
        });
});
</script>