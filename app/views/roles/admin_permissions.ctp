<div class="roles permissions index">
    <h2><?php echo $this->pageTitle; ?></h2>

    <table cellpadding="0" cellspacing="0">
    <?php
        $headers = array();
        $headers[] = __('Aco', true);
        foreach ($roles AS $aroId => $roleTitle) {
            $headers[] = $roleTitle;
        }

        $tableHeaders =  $html->tableHeaders($headers);
        echo $tableHeaders;

        $i = 0;
        $rows = array();
        foreach ($acos AS $id => $alias) {
            $level = substr_count($alias, '_');
            $class = 'level-'.$level;

            $row = array();
            $row[] = $html->div($class, str_replace('_', '', $alias));

            foreach ($roles AS $aroId => $roleTitle) {
                if ($level == 0) {
                    $row[] = '';
                } else {
                    $cell  = $form->input("Permission.$i.status", array('type' => 'checkbox', 'label' => false));
                    $cell .= $form->input("Permission.$i.aro_id", array('type' => 'hidden', 'value' => $aroId));
                    $cell .= $form->input("Permission.$i.aco_id", array('type' => 'hidden', 'value' => $id));

                    $row[] = $cell;

                    $i++;
                }
            }

            $rows[] = $row;
        }

        echo $html->tableCells($rows);
        echo $tableHeaders;
    ?>
    </table>
</div>