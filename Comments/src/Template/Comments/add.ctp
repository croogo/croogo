<div class="form">
    <?=
        $this->cell('Croogo/Comments.Comments::commentFormNode', [
            'node' => $entity,
            'type' => $typesForLayout[$entity->type],
            'comment' => $comment,
            'parentComment' => isset($parentComment) ? $parentComment : null,
        ]);
    ?>
</div>
