<footer class="footer mt-auto">
    <div class="copyright bg-dark text-center">

            <?php

            use Cake\Core\Configure;

            $link = $this->Html->link(
                __d('croogo', 'Croogo %s', (string)Configure::read('Croogo.version')),
                'http://www.croogo.org'
            );
            ?>
            <?= __d('croogo', 'Powered by %s', $link) ?>

    </div>
</footer>
