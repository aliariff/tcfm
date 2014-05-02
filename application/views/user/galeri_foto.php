<?php if($data->sukses==false) {?>

<h1 class="page-header" style="color: red">
  <?php echo "Galat!<br>" . $data->daftar_pemain;?>
</h1>

<?php } else {?>
<h1 class="page-header">
  Pemain Yang Didapatkan
</h1>
<div id="links">
          <?php
          foreach ($data->daftar_pemain as $value)
          {
            $linkb = base_url('assets/img/pemain') . '/' . $value->foto_pemain;
            $links = base_url('assets/img/pemain') . '/' . $value->foto_pemain;
            if($value->foto_pemain)
            echo "<a href=\"$linkb\" title=\"$value->nama_pemain (Rating: $value->rating)\" data-gallery>
                <img src=\"$links\">
            </a>";
          }
          ?>
</div>
<?php }?>