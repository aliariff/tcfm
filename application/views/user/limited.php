<?php
  $ctr=1;
  $line = explode("-", "3-4-3");
?>
<div id="content">
<div class="container">
  <div class="row clearfix">
    <h4 class="text-center text-primary">
        <a href="<?php echo base_url('user/store');?>">Beli Sekarang</a>
    </h4>
      <br/>
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
              foreach ((array)$data->limited as $value) 
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
              foreach ((array)$data->limited as $value) 
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
              foreach ((array)$data->limited as $value) 
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
              foreach ((array)$data->limited as $value) 
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
      <div>
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
              Tim Asal
            </th>
          </tr>
        </thead>
        <tbody id="body_tabel_pemain">
          <?php
            foreach ((array)$data->limited as $pemain) {
          ?>
          <tr>
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
              <?php echo $pemain->tim_asal; ?>
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
});
</script>