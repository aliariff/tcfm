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
            <li class="<?php if (strpos($_SERVER['REQUEST_URI'],'home') !== false) echo 'active';?> menu">
              <a href="<?php echo base_url('home'); ?>">Beranda</a>
            </li>
            <li class="<?php if (strpos($_SERVER['REQUEST_URI'],'about') !== false) echo 'active';?> menu">
              <a href="<?php echo base_url('about'); ?>">Tentang TCFM</a>
            </li>
            <li class="<?php if (strpos($_SERVER['REQUEST_URI'],'developers') !== false) echo 'active';?> menu">
              <a href="<?php echo base_url('developers'); ?>">Developers</a>
            </li>
          </ul>
          <form class="navbar-form navbar-right" style="padding-top:10px;" method="post" id="login_form" action="<?php echo base_url('login'); ?>">
            <div class="form-group" id="login_username">
              <input type="text" placeholder="Username" class="form-control" id="username1" name="username1">
            </div>
            <div class="form-group" id="login_password">
              <input type="password" placeholder="Password" class="form-control" id="password1" name="password1">
            </div>
            <button type="submit" id="login_btn" class="btn btn-danger" data-loading-text="Loading...">Masuk</button>
            <div class="error-login" style="display: none" id="login_pesan">
              <?php
                //echo $data->message;
                //if (isset($data->message))
                //  echo $data->message;
              ?>
            </div>
          </form>
        </div>
        <!--/.navbar-collapse -->
      </div>
    </div>

    <script type="text/javascript">
    jQuery(document).ready(function () {
      $('#login_form').submit(function() {
        $('#login_btn').button('loading');
          $.post('<?php echo base_url('login')?>', $(this).serialize(), function(data){
            if(data.sukses==false)
            {
              $('#login_pesan').fadeOut(500);
              $('#login_username').addClass('has-error');
              $('#login_password').addClass('has-error');
              $('#login_pesan').html(data.pesan);
              $('#login_pesan').fadeIn(500);
              $('#login_btn').button('reset');
            }
            else if (data.sukses=="admin")
            {
              window.location.replace('<?php echo base_url('admin'); ?>');
            }
            else
            {
              window.location.replace('<?php echo base_url('user/dasbor'); ?>'); 
            }
          }, "json");
          return false;
        })
    });
  </script>