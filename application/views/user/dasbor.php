<?php
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
    <?php echo "player $ctr"; ?>
  </div>
  </div>
<?php
  }
?>


<br/><br/><br/><br/><br/><br/><br/><br/><br/>

<?php
  for($i=0; $i<$line[1]; $i++)
  {
?>
  <div class="dropo<?php echo $line[1]; ?>">
  <div id="<?php echo $ctr; $ctr++; ?>" class="player">
    <?php echo "player $ctr-1"; ?>
  </div>
  </div>
<?php
  }
?>

<br/><br/><br/><br/><br/><br/><br/>

<?php
  for($i=0; $i<$line[0]; $i++)
  {
?>
  <div class="dropo<?php echo $line[0]; ?>">
  <div id="<?php echo $ctr; $ctr++; ?>" class="player">
    <?php echo "player $ctr-1"; ?>
  </div>
  </div>
<?php
  }
?>

<br/><br/><br/><br/><br/>

<div class="dropo1">
  <div id="player11" class="player">
    player 11
  </div>
</div>
</div>

    </div>
    <div class="col-md-6 column">
      <div style="height:200px;overflow:auto;">
        <table class="table table-hover table-condensed" id="tabel_pemain">
        <thead>
          <tr>
            <th>
              #
            </th>
            <th>
              Nama
            </th>
            <th>
              Rating
            </th>
            <th>
              Posisi
            </th>
          </tr>
        </thead>
        <tbody id="body_tabel_pemain">
          <?php
            foreach ($data->daftar_pemain_user as $pemain) {
          ?>
          <tr name="<?php echo $pemain->id_user_pemain; ?>" id="<?php echo $pemain->id_user_pemain; ?>">
            <td>
              <?php echo $pemain->id_user_pemain; ?>
            </td>
            <td>
              <?php echo $pemain->nama_pemain; ?>
            </td>
            <td>
              <?php echo $pemain->rating; ?>
            </td>
            <td>
              <?php echo $pemain->posisi; ?>
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
            <img alt="140x140" id="foto_pemain" src="<?php echo base_url('assets/img/no-pic.png'); ?>" />
        </div>

      </div>
    </div>
  </div>
</div>
</div>

<script type="text/javascript" src="<?php echo base_url('assets/js/jquery.tableSelect.js'); ?>"></script>
<script type="text/javascript">
      $(document).ready(function() {
        $('#tabel_pemain').tableSelect({
          onClick: function(row) {
            $.post('<?php echo base_url('helper/detail_pemain')?>', {id:$(row).children('td').eq(0).html()}, function(data){
              if(data.sukses == true)
              {
                $('#nama_pemain').text(": " + data.pesan.nama_pemain);
                $('#tim_asal').text(": " + data.pesan.tim_asal);
                $('#posisi').text(": " + data.pesan.posisi);
                $('#rating').text(": " + data.pesan.rating);
                $('#att').text(": " + data.pesan.att);
                $('#def').text(": " + data.pesan.def);
                $('#speed').text(": " + data.pesan.speed);
                $('#stamina').text(": " + data.pesan.stamina);
                $("#foto_pemain").attr("src", "<?php echo base_url('assets/img'); ?>" + "/" + data.pesan.foto_pemain);
              }
            }, "json");
          },
          onChange: function(row) {
            $.post('<?php echo base_url('helper/detail_pemain')?>', {id:$(row).children('td').eq(0).html()}, function(data){
              if(data.sukses == true)
              {
                $('#nama_pemain').text(": " + data.pesan.nama_pemain);
                $('#tim_asal').text(": " + data.pesan.tim_asal);
                $('#posisi').text(": " + data.pesan.posisi);
                $('#rating').text(": " + data.pesan.rating);
                $('#att').text(": " + data.pesan.att);
                $('#def').text(": " + data.pesan.def);
                $('#speed').text(": " + data.pesan.speed);
                $('#stamina').text(": " + data.pesan.stamina);
                $("#foto_pemain").attr("src", "<?php echo base_url('assets/img'); ?>" + "/" + data.pesan.foto_pemain);
              }
            }, "json");
          }
        });

        $("#body_tabel_pemain tr").draggable({
                helper: "clone"
        });
        
        <?php for($i=1; $i<=5; $i++) { ?>
        $( ".dropo<?php echo $i; ?>" ).droppable({
          activeClass: 'droppable-active',
          hoverClass: 'droppable-hover',
          drop: function( event, ui ) {
            var $el = ui.draggable;
            var name = $el.attr("name");
            alert(name);
            $( this )
              .find( "div" )
                .html( "aa!" );
          }
        });
        <?php }?>
      });
</script>