<footer class="navbar-dark">
    <div class="navbar-inner">

        <div class="footer-content">
            <?php

            use Cake\Core\Configure;

            $link = $this->Html->link(
                __d('croogo', 'Croogo %s', (string)Configure::read('Croogo.version')),
                'http://www.croogo.org'
            );
            ?>
            <?= __d('croogo', 'Powered by %s', $link) ?>
        </div>

    </div>
</footer>
