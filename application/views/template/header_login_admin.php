<!doctype html>
<html>
  
  <head>
    <title>TC Football Manager</title>
    <meta name="viewport" content="width=device-width">
    <?php $this->load->view("template/head", array('scripts' => $scripts)); ?>
  </head>
<body>
    <div class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li>
              <a href="<?php echo base_url(); ?>"><img width="73px" src="<?php echo base_url('assets/logo/logo_tcfm.png'); ?>"></a>
            </li>
            <li class="<?php if (strpos($_SERVER['REQUEST_URI'],'tambah_pemain') !== false) echo 'active';?> menu">
              <a href="<?php echo base_url('admin/tambah_pemain'); ?>">Tambah Pemain</a>
            </li>
            <li class="<?php if (strpos($_SERVER['REQUEST_URI'],'tambah_latihan') !== false) echo 'active';?> menu">
              <a href="<?php echo base_url('admin/tambah_latihan'); ?>">Tambah Latihan</a>
            </li>
            <li class="<?php if (strpos($_SERVER['REQUEST_URI'],'tambah_formasi') !== false) echo 'active';?> menu">
              <a href="<?php echo base_url('admin/tambah_formasi'); ?>">Tambah Formasi</a>
            </li>
            <li class="<?php if (strpos($_SERVER['REQUEST_URI'],'tambah_rumus_formasi') !== false) echo 'active';?> menu">
              <a href="<?php echo base_url('admin/tambah_rumus_formasi'); ?>">Tambah Rumus Formasi</a>
            </li>
            <li class="<?php if (strpos($_SERVER['REQUEST_URI'],'setting') !== false) echo 'active';?> menu">
              <a href="<?php echo base_url('admin/setting'); ?>">Setting</a>
            </li>
            <li class="<?php if (strpos($_SERVER['REQUEST_URI'],'ubah_limited') !== false) echo 'active';?> menu">
              <a href="<?php echo base_url('admin/ubah_limited'); ?>">Limited</a>
            </li>
          </ul>
          <div class="navbar-right logout">
            <a href="<?php echo base_url('logout'); ?>">Logout</a>
          </div>
        </div>
        <!--/.navbar-collapse -->
      </div>
    </div>
    <div class='notifications top-right'></div>