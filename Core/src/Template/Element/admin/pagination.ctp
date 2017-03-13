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
        <?php echo $this->Paginator->first('< ' . __d('croogo', 'first')); ?>
        <?php echo $this->Paginator->prev('< ' . __d('croogo', 'prev')); ?>
        <?php echo $this->Paginator->numbers(); ?>
        <?php echo $this->Paginator->next(__d('croogo', 'next') . ' >'); ?>
        <?php echo $this->Paginator->last(__d('croogo', 'last') . ' >'); ?>
    </ul>
</div>
