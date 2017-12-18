<!-- Redirect browsers with JavaScript disabled to the origin page -->
<?php

$uploadIcon = $this->Html->icon('upload');
$addIcon = $this->Html->icon('create');
$cancelIcon = $this->Html->icon('delete');

?>
<!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
<div class="input text clearfix fileupload-buttonbar">
    <div class="col-8">

        <!-- The fileinput-button span is used to style the file input field as button -->
        <span class="btn btn-success fileinput-button">
            <?php echo $addIcon; ?>
            <span><?php echo __d('assets', 'Add files'); ?></span>
            <?php echo $this->Form->input('asset.file', [
                'label' => false,
                'div' => false,
                'type' => 'file',
                'multiple' => true,
                'required' => false,
                'templates' => [
                    'inputContainer' => '{{content}}',
                ],
            ]); ?>
        </span>

        <button type="reset" class="btn btn-warning cancel">
            <?php echo $cancelIcon; ?>
            <span>Cancel</span>
        </button>

            <!-- The global file processing state -->
            <span class="fileupload-process"></span>
    </div>

    <!-- The global progress state -->
    <div class="col-4 fileupload-progress fade">
        <!-- The global progress bar -->
        <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
            <div class="progress-bar progress-bar-success" style="width:0%;"></div>
        </div>

        <!-- The extended global progress state -->
        <div class="progress-extended">&nbsp;</div>
    </div>
</div>

<!-- The table listing the files available for upload/download -->
<table role="presentation" class="table table-striped"><tbody class="files"></tbody></table>

<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
        <td>
            <span class="preview"></span>
        </td>
        <td>
            <p class="name">{%=file.name%}</p>
            <strong class="error text-danger"></strong>
        </td>
        <td>
            <p class="size">Processing...</p>
            <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>
        </td>
        <td>
            {% if (!i && !o.options.autoUpload) { %}
                <button class="btn btn-sm btn-primary start" disabled
                    title="<?php echo __d('assets', 'Start'); ?>"
                >
                    <?php echo $uploadIcon; ?>
                </button>
            {% } %}
            {% if (!i) { %}
                <button class="btn btn-sm btn-warning cancel"
                    title="<?php echo __d('assets', 'Cancel'); ?>"
                >
                    <?php echo $cancelIcon; ?>
                </button>
            {% } %}
        </td>
    </tr>
{% } %}
</script>

<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-download fade">
        <td>
            <span class="preview">
                {% if (file.thumbnailUrl) { %}
                    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="{%=file.thumbnailUrl%}"></a>
                {% } %}
            </span>
        </td>
        <td>
            <p class="name">
                {% if (file.url) { %}
                    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.name%}</a>
                {% } else { %}
                    <span>{%=file.name%}</span>
                {% } %}
            </p>
            {% if (file.error) { %}
                <div><span class="badge badge-danger">Error</span> {%=file.error%}</div>
            {% } %}
        </td>
        <td>
            <span class="size">{%=o.formatFileSize(file.size)%}</span>
        </td>
        <td>
            {% if (file.deleteUrl) { %}
                <button class="btn btn-danger delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}
                    title="<?php echo __d('assets', 'Delete'); ?>"
                >
                    <?= $cancelIcon ?>
                </button>
                <input type="checkbox" name="delete" value="1" class="toggle">
            {% } else { %}
                <button class="btn btn-sm btn-warning cancel"
                    title="<?= __d('croogo', 'Cancel') ?>"
                >
                    <?= $cancelIcon ?>
                </button>
            {% } %}
        </td>
    </tr>
{% } %}
</script>
