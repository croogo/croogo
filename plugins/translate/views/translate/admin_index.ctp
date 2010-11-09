<div class="translate index">
    <h2><?php echo $title_for_layout; ?></h2>

    <div class="actions">
        <ul>
            <li>
            <?php
                echo $this->Html->link(__('Translate in a new language', true), array(
                    'plugin' => null,
                    'controller' => 'languages',
                    'action'=>'select',
                    $record[$modelAlias]['id'],
                    $modelAlias,
                ));
            ?>
            </li>
        </ul>
    </div>

    <?php if (count($translations) > 0) { ?>
    <table cellpadding="0" cellspacing="0">
    <?php
        $tableHeaders =  $this->Html->tableHeaders(array(
            '',
            //__('Id', true),
            __('Title', true),
            __('Locale', true),
            __('Actions', true),
        ));
        echo $tableHeaders;

        $rows = array();
        foreach ($translations AS $translation) {
            $actions  = $this->Html->link(__('Edit', true), array(
                'action' => 'edit',
                $id,
                $modelAlias,
                'locale' => $translation[$runtimeModelAlias]['locale'],
            ));
            $actions .= ' ' . $this->Html->link(__('Delete', true), array(
                'action' => 'delete',
                $id,
                $modelAlias,
                $translation[$runtimeModelAlias]['locale'],
                'token' => $this->params['_Token']['key'],
            ), null, __('Are you sure?', true));

            $rows[] = array(
                '',
                //$translation[$RuntimeModelAlias]['id'],
                $translation[$runtimeModelAlias]['content'],
                $translation[$runtimeModelAlias]['locale'],
                $actions,
            );
        }

        echo $this->Html->tableCells($rows);
        echo $tableHeaders;
    ?>
    </table>
    <?php 
        } else {
            echo '<p>' . __('No translations available.', true) . '</p>';
        }
    ?>
</div>