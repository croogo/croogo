<div class="pagination-wrapper mt-5">
    <p class="text-center my-3">
        <?php
        echo $this->Paginator->counter([
            'format' => __d('croogo',
                'Page {{page}} of {{pages}}, showing {{current}} records out of {{count}} total'),
        ]);
        ?>
    </p>
    <ul class="pagination justify-content-center">
        <?= $this->Paginator->first('< ' . __d('croogo', 'first')) ?>
        <?= $this->Paginator->prev('< ' . __d('croogo', 'prev')) ?>
        <?= $this->Paginator->numbers() ?>
        <?= $this->Paginator->next(__d('croogo', 'next') . ' >') ?>
        <?= $this->Paginator->last(__d('croogo', 'last') . ' >') ?>
    </ul>
</div>
