<?php
    $b = $block['Block'];
    $class = 'block block-' . $b['alias'];
    if ($block['Block']['class'] != null) {
        $class .= ' ' . $b['class'];
    }
?>
<div id="block-<?php echo $b['id']; ?>" class="<?php echo $class; ?>">
<?php if ($b['show_title'] == 1) { ?>
    <h3><?php echo $b['title']; ?></h3>
<?php } ?>
    <div class="block-body">
        <form id="searchform" method="post" action="javascript: document.location.href=''+Croogo.basePath+'search/q:'+encodeURI($('#searchform #q').val());">
        <?php
            $qValue = null;
            if (isset($this->params['named']['q'])) {
                $qValue = $this->params['named']['q'];
            }
            echo $this->Form->input('q', array(
                'label' => false,
                'name' => 'q',
                'value' => $qValue,
            ));
            echo $this->Form->submit(__('Search', true));
        ?>
        </form>
    </div>
</div>