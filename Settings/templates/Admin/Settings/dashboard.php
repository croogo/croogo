<?php
/**
 * @var \App\View\AppView $this
 * @var string $title_for_layout
 */
?>
<h2 class="d-md-none"><?= $title_for_layout ?></h2>
<?php
$this->Breadcrumbs
    ->add(__d('croogo', 'Dashboard'), $this->getRequest()->getRequestTarget());
?>
