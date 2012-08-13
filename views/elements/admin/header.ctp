<div id="croogo-header" class="navbar no-mb">
  <div class="navbar-inner">
    <div class="container-fluid">
      <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </a>
      <a class="brand" href="<?php echo $this->Html->url('/') ?>">
        <?php echo $this->Html->image('croogo-white.png');?>
      </a>

      <div class="croogo-user-functions btn-group pull-right croogo-inset">
        <a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="#">
          <i class="icon-user"></i>
          <?php echo sprintf(__("You are logged in as: %s", true), $this->Session->read('Auth.User.username'))?>
          <span class="caret"></span>
        </a>
        <ul class="dropdown-menu">
          <li><a href="#">Editar perfil</a></li>
          <!-- <li class="divider"></li> -->
          <li><a href="#">Trocar senha</a></li>
        </ul>
      </div>

      
      <div class="croogo-user-functions nav-collapse pull-left croogo-inset">
        <ul class="nav">
          <li> <?php echo $this->Html->link(__('Visit website', true), '/')?></li>
          <li> <?php echo $this->Html->link(__('Log out', true), array('plugin' => 0, 'controller' => 'users', 'action' => 'logout'))?></li>
        </ul>
      </div><!--/.nav-collapse -->


    </div>
  </div>
</div>