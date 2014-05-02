<?php
  $ctr=1;
  $line = explode("-", $data->formasi_ai->nama_formasi);
?>
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
              foreach ((array)$data->daftar_lineup_ai as $value) 
              {
                if($value->aktif==$temp)
                {
                    echo "<img class=\"tooltipp\" data-toggle=\"tooltip\" title=\"".'('.$value->rating.')'.$value->nama_pemain."\" height=10% width=10% src=\"" . base_url("assets/img/pemain/$value->foto_pemain"). "\" />";
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
              foreach ((array)$data->daftar_lineup_ai as $value) 
              {
                if($value->aktif==$temp) 
                {
                    echo "<img class=\"tooltipp\" data-toggle=\"tooltip\" title=\"".'('.$value->rating.')'.$value->nama_pemain."\" height=10% width=10% src=\"" . base_url("assets/img/pemain/$value->foto_pemain"). "\" />";
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
              foreach ((array)$data->daftar_lineup_ai as $value) 
              {
                if($value->aktif==$temp) 
                {
                    echo "<img class=\"tooltipp\" data-toggle=\"tooltip\" title=\"".'('.$value->rating.')'.$value->nama_pemain."\" height=10% width=10% src=\"" . base_url("assets/img/pemain/$value->foto_pemain"). "\" />";
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
              foreach ((array)$data->daftar_lineup_ai as $value) 
              {
                if($value->aktif==$temp) 
                {
                    echo "<img class=\"tooltipp\" data-toggle=\"tooltip\" title=\"".'('.$value->rating.')'.$value->nama_pemain."\" height=10% width=10% src=\"" . base_url("assets/img/pemain/$value->foto_pemain"). "\" />";
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

<script type="text/javascript">
$(document).ready(function() {
    $('.tooltipp').tooltip({placement: 'top',trigger: 'manual'}).tooltip('show');      
});
</script>