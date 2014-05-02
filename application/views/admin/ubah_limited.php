<?php
  $ctr=1;
  $line = explode("-", "3-4-3");
?>
<div id="content">
<div class="container">
  <div id="pesan" class="alert alert-dismissable" style="display: none">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <div class="isi_pesan text-center">
    </div>
  </div>
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
              foreach ((array)$data->daftar_lineup_limited as $value) 
              {
                if($value->flag_tersedia==$temp)
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
              foreach ((array)$data->daftar_lineup_limited as $value) 
              {
                if($value->flag_tersedia==$temp) 
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
              foreach ((array)$data->daftar_lineup_limited as $value) 
              {
                if($value->flag_tersedia==$temp) 
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
              foreach ((array)$data->daftar_lineup_limited as $value) 
              {
                if($value->flag_tersedia==$temp) 
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
      <div style="height:500px;overflow:auto;">
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
          </tr>
        </thead>
        <tbody id="body_tabel_pemain">
          <?php
            foreach ((array)$data->daftar_pemain_limited as $pemain) {
          ?>
          <tr name="<?php echo $pemain->id_pemain; ?>" id="<?php echo $pemain->id_pemain; ?>" data-nama="<?php echo $pemain->nama_pemain; ?>" data-foto="<?php echo $pemain->foto_pemain; ?>">
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
    </div>
  </div>
</div>
</div>

<script type="text/javascript">
$(document).ready(function() {

        $('.tooltipp').tooltip({placement: 'top',trigger: 'manual'}).tooltip('show');

        $("#body_tabel_pemain tr").draggable({
                helper: "clone"
        });
        
        <?php for($i=1; $i<=5; $i++) { ?>
        $( ".dropo<?php echo $i; ?>" ).droppable({
          activeClass: 'droppable-active',
          hoverClass: 'droppable-hover',
          drop: function( event, ui ) {
            var $el = ui.draggable;
            var nama = $el.attr("data-nama");
            var foto = $el.attr("data-foto");
            var id_pemain = $el.attr("name");
            var posisi = $( this ).find("div").attr("id");
            
            $( this ).find("div").find("img").attr("title", nama);
            $( this ).find("div").find("img").attr("src", "<?php echo base_url('assets/img/pemain'); ?>" + "/" + foto);
            $.post('<?php echo base_url('helper/update_lineup_limited')?>', {id_pemain:id_pemain, posisi:posisi}, function(data){
              if(data.pesan.kode == 0)
              {
                  $('#pesan').removeClass('alert-danger').addClass('alert-success');
              }
              else
              {
                $('#pesan').removeClass('alert-success').addClass('alert-danger');
              }
              $('#pesan .isi_pesan').html(data.pesan.pesan);
              $('#pesan').fadeIn(500);
              setTimeout("location.reload()", 1000);
            }, "json");
          }
        });
        <?php }?>
});
</script>