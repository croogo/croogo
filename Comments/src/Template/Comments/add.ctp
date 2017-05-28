<div class="form">
    <?=
        $this->cell('Croogo/Comments.Comments::commentFormNode', [
            'node' => $entity,
            'type' => $typesForLayout[$entity->type],
            'comment' => $comment
        ]);
    ?>
</div>
