<div id="header">
    <div class="container_16">
        <div class="grid_8 header-left">
        <?php
            echo $this->Html->link(__('Dashboard', true), '/admin');
            echo ' <span>|</span> ';
            echo $this->Html->link(__('Visit website', true), '/');
        ?>
        </div>

        <div class="grid_8 header-right">
        <?php
            echo sprintf(__("You are logged in as: %s", true), $this->Session->read('Auth.User.username'));
            echo ' <span>|</span> ';
            echo $this->Html->link(__("Log out", true), array('plugin' => 0, 'controller' => 'users', 'action' => 'logout'));
        ?>
        </div>

        <div class="clear">&nbsp;</div>
    </div>
</div>