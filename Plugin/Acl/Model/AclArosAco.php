<?php
class AclArosAco extends AppModel {

	var $name = 'AclArosAco';
    var $useTable = 'aros_acos';
    
    var $belongsTo = array(
        'AclAro' => array(
            'className' => 'Acl.AclAro',
            'foreignKey' => 'aro_id',
        ),
        'AclAco' => array(
            'className' => 'Acl.AclAco',
            'foreignKey' => 'aco_id',
        ),
    );

}
?>