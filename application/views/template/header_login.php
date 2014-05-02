<!doctype html>
<html>
  
  <head>
    <title>TC Football Manager</title>
    <meta name="viewport" content="width=device-width">
    <?php $this->load->view("template/head", array('scripts' => $scripts)); ?>
  </head>
  <?php

    $notif_masuk = 0;
    $notif_keluar = 0;
    $username = $this->sesilogin_model->username_login();
    $daftar_penawaran_masuk = $this->penawaran_model->daftar_penawaran_masuk($username);
    $daftar_penawaran_keluar = $this->penawaran_model->daftar_penawaran_keluar($username);
    $user_info = $this->user_model->user_info($username);

    foreach((array)$daftar_penawaran_masuk as $value)
    {
      if ($value->jawaban==99) $notif_masuk++;
    }

    foreach((array)$daftar_penawaran_keluar as $value)
    {
      if ($value->jawaban!=99 && $value->terbaca==0) $notif_keluar++;
    }

    $notif_total = $notif_masuk + $notif_keluar;

  ?>
<body>
    <div class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li>
              <a href="<?php echo base_url(); ?>"><img width="73px" src="<?php echo base_url('assets/logo/logo_tcfm.png'); ?>"></a>
            </li>
            <li class="<?php if (strpos($_SERVER['REQUEST_URI'],'dasbor') !== false) echo 'active';?> menu">
              <a href="<?php echo base_url('user/dasbor'); ?>">Dasbor</a>
            </li>
            <li class="<?php if (strpos($_SERVER['REQUEST_URI'],'timsaya') !== false) echo 'active';?> menu">
              <a href="<?php echo base_url('user/timsaya'); ?>">Tim Saya</a>
            </li>
            <li class="<?php if (strpos($_SERVER['REQUEST_URI'],'fasilitas') !== false) echo 'active';?> menu">
              <a href="<?php echo base_url('user/fasilitas'); ?>">Fasilitas</a>
            </li>
            <li class="<?php if (strpos($_SERVER['REQUEST_URI'],'latihan') !== false) echo 'active';?> menu">
              <a href="<?php echo base_url('user/latihan'); ?>">Latihan</a>
            </li>
            <li class="<?php if (strpos($_SERVER['REQUEST_URI'],'penawaran') !== false) echo 'active';?> menu">
              <a href="<?php echo base_url('user/penawaran'); ?>">Penawaran <span class="badge"><?php if($notif_total!=0)echo $notif_total;?></span></a>
            </li>
            <li class="<?php if (strpos($_SERVER['REQUEST_URI'],'rank') !== false) echo 'active';?> menu">
              <a href="<?php echo base_url('user/rank'); ?>">World League</a>
            </li>
            <li class="<?php if (strpos($_SERVER['REQUEST_URI'],'liga') !== false) echo 'active';?> menu">
              <a href="<?php echo base_url('user/liga'); ?>">Liga</a>
            </li>
            <li class="<?php if (strpos($_SERVER['REQUEST_URI'],'store') !== false) echo 'active';?> menu">
              <a href="<?php echo base_url('user/store'); ?>">Store</a>
            </li>
          </ul>

          <div class="navbar-right logout">
            <table>
              <tr>
                <td></td>
                <td></td>
                <td><span class="glyphicon glyphicon-log-out"></span></td>
                <td>
                  <a href="<?php echo base_url('logout'); ?>">Logout</a>
                </td>
              </tr>
              <tr>
                <td>
                  <span class="glyphicon glyphicon-user"></span>
                </td>
                <td>
                  <?php echo $username?>
                </td>
                <td>
                  &nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-usd"></span>
                </td>
                <td>
                  <?php echo number_format($user_info->uang, 0, '', '.')?>
                </td>
              </tr>
              <tr>
                <td>
                  <span class="glyphicon glyphicon-flash"></span>
                </td>
                <td>
                  <?php echo $user_info->ap . '/' . $user_info->ap_maksimal?>
                </td>
                <td>
                  &nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-star"></span>
                </td>
                <td>
                  <?php echo number_format($user_info->exp, 0, '', '.')?>
                </td>
              </tr>
              <tr>
                <td>
                  <span class="glyphicon glyphicon-euro"></span>
                </td>
                <td>
                  <?php echo number_format($user_info->balen, 0, '', '.')?>
                </td>
                <td>
                  &nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-tower"></span>
                </td>
                <td>
                  <?php echo $user_info->nama_liga;?>
                </td>
              </tr>
            </table>
          </div>

          <div class="navbar-right avatar">
            <img width="45%" src="<?php 
              if ($user_info->foto_user)
                echo base_url('assets/img/user/thumbnail'.'/'.$user_info->foto_user);
              else
                echo base_url('assets/img'.'/no-pic.png');?>" class="img-rounded" />
          </div>

        </div>
        <!--/.navbar-collapse -->
      </div>
    </div>
    <div class='notifications top-right'></div>