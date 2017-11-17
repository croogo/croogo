<div class="pagination-wrapper">
    <p>
        <?php
        echo $this->Paginator->counter([
            'format' => __d('croogo',
                'Page {{page}} of {{pages}}, showing {{current}} records out of {{count}} total'),
        ]);
        ?>
    </p>
    <ul class="pagination justify-content-center pagination-sm">
        <?= $this->Paginator->first('< ' . __d('croogo', 'first')) ?>
        <?= $this->Paginator->prev('< ' . __d('croogo', 'prev')) ?>
        <?= $this->Paginator->numbers() ?>
        <?= $this->Paginator->next(__d('croogo', 'next') . ' >') ?>
        <?= $this->Paginator->last(__d('croogo', 'last') . ' >') ?>
    </ul>
</div>
