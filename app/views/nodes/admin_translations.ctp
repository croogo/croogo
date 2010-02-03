<div class="nodes index">
    <h2><?php echo $title_for_layout; ?></h2>

    <div class="actions">
        <ul>
            <li>
            <?php
                echo $html->link(__('Translate in a new language', true), array(
                    'controller' => 'languages',
                    'action'=>'select',
                    'nodes',
                    'translate',
                    $node['Node']['id'],
                ));
            ?>
            </li>
        </ul>
    </div>

    <?php if (count($translations) > 0) { ?>
    <table cellpadding="0" cellspacing="0">
    <?php
        $tableHeaders =  $html->tableHeaders(array(
            '',
            //__('Id', true),
            __('Title', true),
            __('Locale', true),
            __('Actions', true),
        ));
        echo $tableHeaders;

        $rows = array();
        foreach ($translations AS $translation) {
            $actions  = $html->link(__('Edit', true), array('action' => 'translate', $id, 'locale' => $translation[$runtimeModelAlias]['locale']));
            $actions .= ' ' . $html->link(__('Delete', true), array('action' => 'delete_translation', $translation[$runtimeModelAlias]['locale'], $id), null, __('Are you sure?', true));

            $rows[] = array(
                '',
                //$translation[$RuntimeModelAlias]['id'],
                $translation[$runtimeModelAlias]['content'],
                $translation[$runtimeModelAlias]['locale'],
                $actions,
            );
        }

        echo $html->tableCells($rows);
        echo $tableHeaders;
    ?>
    </table>
    <?php 
        } else {
            echo '<p>' . __('No translations available.', true) . '</p>';
        }
    ?>
</div>