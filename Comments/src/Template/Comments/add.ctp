<div class="form">
    <?=
        $this->cell('Croogo/Comments.Comments::commentFormNode', [
            'entity' => $entity,
            'type' => $typesForLayout[$entity->type],
            'comment' => $comment,
            'parentComment' => isset($parentComment) ? $parentComment : null,
        ]);
    ?>
</div>
