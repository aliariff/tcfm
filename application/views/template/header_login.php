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
              <a href="<?php echo base_url(); ?>"><img alt="" src="<?php echo base_url('assets/logo/logo_tcfm.png'); ?>"></a>
            </li>
            <li class="active menu">
              <a href="<?php echo base_url('/user/dasbor'); ?>">Dasbor</a>
            </li>
            <li class="menu">
              <a href="<?php echo base_url('about'); ?>">Tim Saya</a>
            </li>
            <li class="menu">
              <a href="<?php echo base_url('kontak'); ?>">Fasilitas</a>
            </li>
            <li class="menu">
              <a href="<?php echo base_url('kontak'); ?>">Latihan</a>
            </li>
          </ul>
          <div class="navbar-right logout">
            <a href="<?php echo base_url('logout'); ?>">Logout</a>
          </div>
        </div>
        <!--/.navbar-collapse -->
      </div>
    </div>