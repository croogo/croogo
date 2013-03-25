<?php
class AcoData {

	public $table = 'acos';

	public $records = array(
		array(
			'id' => '1',
			'parent_id' => '',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'controllers',
			'lft' => '1',
			'rght' => '386'
		),
		array(
			'id' => '2',
			'parent_id' => '1',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'Acl',
			'lft' => '2',
			'rght' => '25'
		),
		array(
			'id' => '3',
			'parent_id' => '2',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'AclActions',
			'lft' => '3',
			'rght' => '16'
		),
		array(
			'id' => '4',
			'parent_id' => '3',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_index',
			'lft' => '4',
			'rght' => '5'
		),
		array(
			'id' => '5',
			'parent_id' => '3',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_add',
			'lft' => '6',
			'rght' => '7'
		),
		array(
			'id' => '6',
			'parent_id' => '3',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_edit',
			'lft' => '8',
			'rght' => '9'
		),
		array(
			'id' => '7',
			'parent_id' => '3',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_delete',
			'lft' => '10',
			'rght' => '11'
		),
		array(
			'id' => '8',
			'parent_id' => '3',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_move',
			'lft' => '12',
			'rght' => '13'
		),
		array(
			'id' => '9',
			'parent_id' => '3',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_generate',
			'lft' => '14',
			'rght' => '15'
		),
		array(
			'id' => '10',
			'parent_id' => '2',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'AclPermissions',
			'lft' => '17',
			'rght' => '24'
		),
		array(
			'id' => '11',
			'parent_id' => '10',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_index',
			'lft' => '18',
			'rght' => '19'
		),
		array(
			'id' => '12',
			'parent_id' => '10',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_toggle',
			'lft' => '20',
			'rght' => '21'
		),
		array(
			'id' => '13',
			'parent_id' => '10',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_upgrade',
			'lft' => '22',
			'rght' => '23'
		),
		array(
			'id' => '14',
			'parent_id' => '1',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'Blocks',
			'lft' => '26',
			'rght' => '55'
		),
		array(
			'id' => '15',
			'parent_id' => '14',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'Blocks',
			'lft' => '27',
			'rght' => '44'
		),
		array(
			'id' => '16',
			'parent_id' => '15',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_toggle',
			'lft' => '28',
			'rght' => '29'
		),
		array(
			'id' => '17',
			'parent_id' => '15',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_index',
			'lft' => '30',
			'rght' => '31'
		),
		array(
			'id' => '18',
			'parent_id' => '15',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_add',
			'lft' => '32',
			'rght' => '33'
		),
		array(
			'id' => '19',
			'parent_id' => '15',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_edit',
			'lft' => '34',
			'rght' => '35'
		),
		array(
			'id' => '20',
			'parent_id' => '15',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_delete',
			'lft' => '36',
			'rght' => '37'
		),
		array(
			'id' => '21',
			'parent_id' => '15',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_moveup',
			'lft' => '38',
			'rght' => '39'
		),
		array(
			'id' => '22',
			'parent_id' => '15',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_movedown',
			'lft' => '40',
			'rght' => '41'
		),
		array(
			'id' => '23',
			'parent_id' => '15',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_process',
			'lft' => '42',
			'rght' => '43'
		),
		array(
			'id' => '24',
			'parent_id' => '14',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'Regions',
			'lft' => '45',
			'rght' => '54'
		),
		array(
			'id' => '25',
			'parent_id' => '24',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_index',
			'lft' => '46',
			'rght' => '47'
		),
		array(
			'id' => '26',
			'parent_id' => '24',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_add',
			'lft' => '48',
			'rght' => '49'
		),
		array(
			'id' => '27',
			'parent_id' => '24',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_edit',
			'lft' => '50',
			'rght' => '51'
		),
		array(
			'id' => '28',
			'parent_id' => '24',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_delete',
			'lft' => '52',
			'rght' => '53'
		),
		array(
			'id' => '29',
			'parent_id' => '1',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'Comments',
			'lft' => '56',
			'rght' => '73'
		),
		array(
			'id' => '30',
			'parent_id' => '29',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'Comments',
			'lft' => '57',
			'rght' => '72'
		),
		array(
			'id' => '31',
			'parent_id' => '30',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_index',
			'lft' => '58',
			'rght' => '59'
		),
		array(
			'id' => '32',
			'parent_id' => '30',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_edit',
			'lft' => '60',
			'rght' => '61'
		),
		array(
			'id' => '33',
			'parent_id' => '30',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_delete',
			'lft' => '62',
			'rght' => '63'
		),
		array(
			'id' => '34',
			'parent_id' => '30',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_process',
			'lft' => '64',
			'rght' => '65'
		),
		array(
			'id' => '35',
			'parent_id' => '30',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'index',
			'lft' => '66',
			'rght' => '67'
		),
		array(
			'id' => '36',
			'parent_id' => '30',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'add',
			'lft' => '68',
			'rght' => '69'
		),
		array(
			'id' => '37',
			'parent_id' => '30',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'delete',
			'lft' => '70',
			'rght' => '71'
		),
		array(
			'id' => '38',
			'parent_id' => '1',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'Contacts',
			'lft' => '74',
			'rght' => '97'
		),
		array(
			'id' => '39',
			'parent_id' => '38',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'Contacts',
			'lft' => '75',
			'rght' => '86'
		),
		array(
			'id' => '40',
			'parent_id' => '39',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_index',
			'lft' => '76',
			'rght' => '77'
		),
		array(
			'id' => '41',
			'parent_id' => '39',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_add',
			'lft' => '78',
			'rght' => '79'
		),
		array(
			'id' => '42',
			'parent_id' => '39',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_edit',
			'lft' => '80',
			'rght' => '81'
		),
		array(
			'id' => '43',
			'parent_id' => '39',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_delete',
			'lft' => '82',
			'rght' => '83'
		),
		array(
			'id' => '44',
			'parent_id' => '39',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'view',
			'lft' => '84',
			'rght' => '85'
		),
		array(
			'id' => '45',
			'parent_id' => '38',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'Messages',
			'lft' => '87',
			'rght' => '96'
		),
		array(
			'id' => '46',
			'parent_id' => '45',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_index',
			'lft' => '88',
			'rght' => '89'
		),
		array(
			'id' => '47',
			'parent_id' => '45',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_edit',
			'lft' => '90',
			'rght' => '91'
		),
		array(
			'id' => '48',
			'parent_id' => '45',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_delete',
			'lft' => '92',
			'rght' => '93'
		),
		array(
			'id' => '49',
			'parent_id' => '45',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_process',
			'lft' => '94',
			'rght' => '95'
		),
		array(
			'id' => '50',
			'parent_id' => '1',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'Croogo',
			'lft' => '98',
			'rght' => '99'
		),
		array(
			'id' => '51',
			'parent_id' => '1',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'Extensions',
			'lft' => '100',
			'rght' => '139'
		),
		array(
			'id' => '52',
			'parent_id' => '51',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'ExtensionsLocales',
			'lft' => '101',
			'rght' => '112'
		),
		array(
			'id' => '53',
			'parent_id' => '52',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_index',
			'lft' => '102',
			'rght' => '103'
		),
		array(
			'id' => '54',
			'parent_id' => '52',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_activate',
			'lft' => '104',
			'rght' => '105'
		),
		array(
			'id' => '55',
			'parent_id' => '52',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_add',
			'lft' => '106',
			'rght' => '107'
		),
		array(
			'id' => '56',
			'parent_id' => '52',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_edit',
			'lft' => '108',
			'rght' => '109'
		),
		array(
			'id' => '57',
			'parent_id' => '52',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_delete',
			'lft' => '110',
			'rght' => '111'
		),
		array(
			'id' => '58',
			'parent_id' => '51',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'ExtensionsPlugins',
			'lft' => '113',
			'rght' => '124'
		),
		array(
			'id' => '59',
			'parent_id' => '58',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_index',
			'lft' => '114',
			'rght' => '115'
		),
		array(
			'id' => '60',
			'parent_id' => '58',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_add',
			'lft' => '116',
			'rght' => '117'
		),
		array(
			'id' => '61',
			'parent_id' => '58',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_delete',
			'lft' => '118',
			'rght' => '119'
		),
		array(
			'id' => '62',
			'parent_id' => '58',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_toggle',
			'lft' => '120',
			'rght' => '121'
		),
		array(
			'id' => '63',
			'parent_id' => '58',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_migrate',
			'lft' => '122',
			'rght' => '123'
		),
		array(
			'id' => '64',
			'parent_id' => '51',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'ExtensionsThemes',
			'lft' => '125',
			'rght' => '138'
		),
		array(
			'id' => '65',
			'parent_id' => '64',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_index',
			'lft' => '126',
			'rght' => '127'
		),
		array(
			'id' => '66',
			'parent_id' => '64',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_activate',
			'lft' => '128',
			'rght' => '129'
		),
		array(
			'id' => '67',
			'parent_id' => '64',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_add',
			'lft' => '130',
			'rght' => '131'
		),
		array(
			'id' => '68',
			'parent_id' => '64',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_editor',
			'lft' => '132',
			'rght' => '133'
		),
		array(
			'id' => '69',
			'parent_id' => '64',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_save',
			'lft' => '134',
			'rght' => '135'
		),
		array(
			'id' => '70',
			'parent_id' => '64',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_delete',
			'lft' => '136',
			'rght' => '137'
		),
		array(
			'id' => '71',
			'parent_id' => '1',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'FileManager',
			'lft' => '140',
			'rght' => '175'
		),
		array(
			'id' => '72',
			'parent_id' => '71',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'Attachments',
			'lft' => '141',
			'rght' => '152'
		),
		array(
			'id' => '73',
			'parent_id' => '72',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_index',
			'lft' => '142',
			'rght' => '143'
		),
		array(
			'id' => '74',
			'parent_id' => '72',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_add',
			'lft' => '144',
			'rght' => '145'
		),
		array(
			'id' => '75',
			'parent_id' => '72',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_edit',
			'lft' => '146',
			'rght' => '147'
		),
		array(
			'id' => '76',
			'parent_id' => '72',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_delete',
			'lft' => '148',
			'rght' => '149'
		),
		array(
			'id' => '77',
			'parent_id' => '72',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_browse',
			'lft' => '150',
			'rght' => '151'
		),
		array(
			'id' => '78',
			'parent_id' => '71',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'FileManager',
			'lft' => '153',
			'rght' => '174'
		),
		array(
			'id' => '79',
			'parent_id' => '78',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_index',
			'lft' => '154',
			'rght' => '155'
		),
		array(
			'id' => '80',
			'parent_id' => '78',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_browse',
			'lft' => '156',
			'rght' => '157'
		),
		array(
			'id' => '81',
			'parent_id' => '78',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_editfile',
			'lft' => '158',
			'rght' => '159'
		),
		array(
			'id' => '82',
			'parent_id' => '78',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_upload',
			'lft' => '160',
			'rght' => '161'
		),
		array(
			'id' => '83',
			'parent_id' => '78',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_delete_file',
			'lft' => '162',
			'rght' => '163'
		),
		array(
			'id' => '84',
			'parent_id' => '78',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_delete_directory',
			'lft' => '164',
			'rght' => '165'
		),
		array(
			'id' => '85',
			'parent_id' => '78',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_rename',
			'lft' => '166',
			'rght' => '167'
		),
		array(
			'id' => '86',
			'parent_id' => '78',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_create_directory',
			'lft' => '168',
			'rght' => '169'
		),
		array(
			'id' => '87',
			'parent_id' => '78',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_create_file',
			'lft' => '170',
			'rght' => '171'
		),
		array(
			'id' => '88',
			'parent_id' => '78',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_chmod',
			'lft' => '172',
			'rght' => '173'
		),
		array(
			'id' => '89',
			'parent_id' => '1',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'Install',
			'lft' => '176',
			'rght' => '189'
		),
		array(
			'id' => '90',
			'parent_id' => '89',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'Install',
			'lft' => '177',
			'rght' => '188'
		),
		array(
			'id' => '91',
			'parent_id' => '90',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'index',
			'lft' => '178',
			'rght' => '179'
		),
		array(
			'id' => '92',
			'parent_id' => '90',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'database',
			'lft' => '180',
			'rght' => '181'
		),
		array(
			'id' => '93',
			'parent_id' => '90',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'data',
			'lft' => '182',
			'rght' => '183'
		),
		array(
			'id' => '94',
			'parent_id' => '90',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'adminuser',
			'lft' => '184',
			'rght' => '185'
		),
		array(
			'id' => '95',
			'parent_id' => '90',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'finish',
			'lft' => '186',
			'rght' => '187'
		),
		array(
			'id' => '96',
			'parent_id' => '1',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'Menus',
			'lft' => '190',
			'rght' => '219'
		),
		array(
			'id' => '97',
			'parent_id' => '96',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'Links',
			'lft' => '191',
			'rght' => '208'
		),
		array(
			'id' => '98',
			'parent_id' => '97',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_toggle',
			'lft' => '192',
			'rght' => '193'
		),
		array(
			'id' => '99',
			'parent_id' => '97',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_index',
			'lft' => '194',
			'rght' => '195'
		),
		array(
			'id' => '100',
			'parent_id' => '97',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_add',
			'lft' => '196',
			'rght' => '197'
		),
		array(
			'id' => '101',
			'parent_id' => '97',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_edit',
			'lft' => '198',
			'rght' => '199'
		),
		array(
			'id' => '102',
			'parent_id' => '97',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_delete',
			'lft' => '200',
			'rght' => '201'
		),
		array(
			'id' => '103',
			'parent_id' => '97',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_moveup',
			'lft' => '202',
			'rght' => '203'
		),
		array(
			'id' => '104',
			'parent_id' => '97',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_movedown',
			'lft' => '204',
			'rght' => '205'
		),
		array(
			'id' => '105',
			'parent_id' => '97',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_process',
			'lft' => '206',
			'rght' => '207'
		),
		array(
			'id' => '106',
			'parent_id' => '96',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'Menus',
			'lft' => '209',
			'rght' => '218'
		),
		array(
			'id' => '107',
			'parent_id' => '106',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_index',
			'lft' => '210',
			'rght' => '211'
		),
		array(
			'id' => '108',
			'parent_id' => '106',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_add',
			'lft' => '212',
			'rght' => '213'
		),
		array(
			'id' => '109',
			'parent_id' => '106',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_edit',
			'lft' => '214',
			'rght' => '215'
		),
		array(
			'id' => '110',
			'parent_id' => '106',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_delete',
			'lft' => '216',
			'rght' => '217'
		),
		array(
			'id' => '111',
			'parent_id' => '1',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'Meta',
			'lft' => '220',
			'rght' => '221'
		),
		array(
			'id' => '112',
			'parent_id' => '1',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'Migrations',
			'lft' => '222',
			'rght' => '223'
		),
		array(
			'id' => '113',
			'parent_id' => '1',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'Nodes',
			'lft' => '224',
			'rght' => '257'
		),
		array(
			'id' => '114',
			'parent_id' => '113',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'Nodes',
			'lft' => '225',
			'rght' => '256'
		),
		array(
			'id' => '115',
			'parent_id' => '114',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_toggle',
			'lft' => '226',
			'rght' => '227'
		),
		array(
			'id' => '116',
			'parent_id' => '114',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_index',
			'lft' => '228',
			'rght' => '229'
		),
		array(
			'id' => '117',
			'parent_id' => '114',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_create',
			'lft' => '230',
			'rght' => '231'
		),
		array(
			'id' => '118',
			'parent_id' => '114',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_add',
			'lft' => '232',
			'rght' => '233'
		),
		array(
			'id' => '119',
			'parent_id' => '114',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_edit',
			'lft' => '234',
			'rght' => '235'
		),
		array(
			'id' => '120',
			'parent_id' => '114',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_update_paths',
			'lft' => '236',
			'rght' => '237'
		),
		array(
			'id' => '121',
			'parent_id' => '114',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_delete',
			'lft' => '238',
			'rght' => '239'
		),
		array(
			'id' => '122',
			'parent_id' => '114',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_delete_meta',
			'lft' => '240',
			'rght' => '241'
		),
		array(
			'id' => '123',
			'parent_id' => '114',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_add_meta',
			'lft' => '242',
			'rght' => '243'
		),
		array(
			'id' => '124',
			'parent_id' => '114',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_process',
			'lft' => '244',
			'rght' => '245'
		),
		array(
			'id' => '125',
			'parent_id' => '114',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'index',
			'lft' => '246',
			'rght' => '247'
		),
		array(
			'id' => '126',
			'parent_id' => '114',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'term',
			'lft' => '248',
			'rght' => '249'
		),
		array(
			'id' => '127',
			'parent_id' => '114',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'promoted',
			'lft' => '250',
			'rght' => '251'
		),
		array(
			'id' => '128',
			'parent_id' => '114',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'search',
			'lft' => '252',
			'rght' => '253'
		),
		array(
			'id' => '129',
			'parent_id' => '114',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'view',
			'lft' => '254',
			'rght' => '255'
		),
		array(
			'id' => '130',
			'parent_id' => '1',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'Search',
			'lft' => '258',
			'rght' => '259'
		),
		array(
			'id' => '131',
			'parent_id' => '1',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'Settings',
			'lft' => '260',
			'rght' => '297'
		),
		array(
			'id' => '132',
			'parent_id' => '131',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'Languages',
			'lft' => '261',
			'rght' => '276'
		),
		array(
			'id' => '133',
			'parent_id' => '132',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_index',
			'lft' => '262',
			'rght' => '263'
		),
		array(
			'id' => '134',
			'parent_id' => '132',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_add',
			'lft' => '264',
			'rght' => '265'
		),
		array(
			'id' => '135',
			'parent_id' => '132',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_edit',
			'lft' => '266',
			'rght' => '267'
		),
		array(
			'id' => '136',
			'parent_id' => '132',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_delete',
			'lft' => '268',
			'rght' => '269'
		),
		array(
			'id' => '137',
			'parent_id' => '132',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_moveup',
			'lft' => '270',
			'rght' => '271'
		),
		array(
			'id' => '138',
			'parent_id' => '132',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_movedown',
			'lft' => '272',
			'rght' => '273'
		),
		array(
			'id' => '139',
			'parent_id' => '132',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_select',
			'lft' => '274',
			'rght' => '275'
		),
		array(
			'id' => '140',
			'parent_id' => '131',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'Settings',
			'lft' => '277',
			'rght' => '296'
		),
		array(
			'id' => '141',
			'parent_id' => '140',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_dashboard',
			'lft' => '278',
			'rght' => '279'
		),
		array(
			'id' => '142',
			'parent_id' => '140',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_index',
			'lft' => '280',
			'rght' => '281'
		),
		array(
			'id' => '143',
			'parent_id' => '140',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_view',
			'lft' => '282',
			'rght' => '283'
		),
		array(
			'id' => '144',
			'parent_id' => '140',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_add',
			'lft' => '284',
			'rght' => '285'
		),
		array(
			'id' => '145',
			'parent_id' => '140',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_edit',
			'lft' => '286',
			'rght' => '287'
		),
		array(
			'id' => '146',
			'parent_id' => '140',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_delete',
			'lft' => '288',
			'rght' => '289'
		),
		array(
			'id' => '147',
			'parent_id' => '140',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_prefix',
			'lft' => '290',
			'rght' => '291'
		),
		array(
			'id' => '148',
			'parent_id' => '140',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_moveup',
			'lft' => '292',
			'rght' => '293'
		),
		array(
			'id' => '149',
			'parent_id' => '140',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_movedown',
			'lft' => '294',
			'rght' => '295'
		),
		array(
			'id' => '150',
			'parent_id' => '1',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'Taxonomy',
			'lft' => '298',
			'rght' => '337'
		),
		array(
			'id' => '151',
			'parent_id' => '150',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'Terms',
			'lft' => '299',
			'rght' => '312'
		),
		array(
			'id' => '152',
			'parent_id' => '151',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_index',
			'lft' => '300',
			'rght' => '301'
		),
		array(
			'id' => '153',
			'parent_id' => '151',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_add',
			'lft' => '302',
			'rght' => '303'
		),
		array(
			'id' => '154',
			'parent_id' => '151',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_edit',
			'lft' => '304',
			'rght' => '305'
		),
		array(
			'id' => '155',
			'parent_id' => '151',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_delete',
			'lft' => '306',
			'rght' => '307'
		),
		array(
			'id' => '156',
			'parent_id' => '151',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_moveup',
			'lft' => '308',
			'rght' => '309'
		),
		array(
			'id' => '157',
			'parent_id' => '151',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_movedown',
			'lft' => '310',
			'rght' => '311'
		),
		array(
			'id' => '158',
			'parent_id' => '150',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'Types',
			'lft' => '313',
			'rght' => '322'
		),
		array(
			'id' => '159',
			'parent_id' => '158',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_index',
			'lft' => '314',
			'rght' => '315'
		),
		array(
			'id' => '160',
			'parent_id' => '158',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_add',
			'lft' => '316',
			'rght' => '317'
		),
		array(
			'id' => '161',
			'parent_id' => '158',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_edit',
			'lft' => '318',
			'rght' => '319'
		),
		array(
			'id' => '162',
			'parent_id' => '158',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_delete',
			'lft' => '320',
			'rght' => '321'
		),
		array(
			'id' => '163',
			'parent_id' => '150',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'Vocabularies',
			'lft' => '323',
			'rght' => '336'
		),
		array(
			'id' => '164',
			'parent_id' => '163',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_index',
			'lft' => '324',
			'rght' => '325'
		),
		array(
			'id' => '165',
			'parent_id' => '163',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_add',
			'lft' => '326',
			'rght' => '327'
		),
		array(
			'id' => '166',
			'parent_id' => '163',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_edit',
			'lft' => '328',
			'rght' => '329'
		),
		array(
			'id' => '167',
			'parent_id' => '163',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_delete',
			'lft' => '330',
			'rght' => '331'
		),
		array(
			'id' => '168',
			'parent_id' => '163',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_moveup',
			'lft' => '332',
			'rght' => '333'
		),
		array(
			'id' => '169',
			'parent_id' => '163',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_movedown',
			'lft' => '334',
			'rght' => '335'
		),
		array(
			'id' => '170',
			'parent_id' => '1',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'Ckeditor',
			'lft' => '338',
			'rght' => '339'
		),
		array(
			'id' => '171',
			'parent_id' => '1',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'Users',
			'lft' => '340',
			'rght' => '385'
		),
		array(
			'id' => '172',
			'parent_id' => '171',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'Roles',
			'lft' => '341',
			'rght' => '350'
		),
		array(
			'id' => '173',
			'parent_id' => '172',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_index',
			'lft' => '342',
			'rght' => '343'
		),
		array(
			'id' => '174',
			'parent_id' => '172',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_add',
			'lft' => '344',
			'rght' => '345'
		),
		array(
			'id' => '175',
			'parent_id' => '172',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_edit',
			'lft' => '346',
			'rght' => '347'
		),
		array(
			'id' => '176',
			'parent_id' => '172',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_delete',
			'lft' => '348',
			'rght' => '349'
		),
		array(
			'id' => '177',
			'parent_id' => '171',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'Users',
			'lft' => '351',
			'rght' => '384'
		),
		array(
			'id' => '178',
			'parent_id' => '177',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_index',
			'lft' => '352',
			'rght' => '353'
		),
		array(
			'id' => '179',
			'parent_id' => '177',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_add',
			'lft' => '354',
			'rght' => '355'
		),
		array(
			'id' => '180',
			'parent_id' => '177',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_edit',
			'lft' => '356',
			'rght' => '357'
		),
		array(
			'id' => '181',
			'parent_id' => '177',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_reset_password',
			'lft' => '358',
			'rght' => '359'
		),
		array(
			'id' => '182',
			'parent_id' => '177',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_delete',
			'lft' => '360',
			'rght' => '361'
		),
		array(
			'id' => '183',
			'parent_id' => '177',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_login',
			'lft' => '362',
			'rght' => '363'
		),
		array(
			'id' => '184',
			'parent_id' => '177',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'admin_logout',
			'lft' => '364',
			'rght' => '365'
		),
		array(
			'id' => '185',
			'parent_id' => '177',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'index',
			'lft' => '366',
			'rght' => '367'
		),
		array(
			'id' => '186',
			'parent_id' => '177',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'add',
			'lft' => '368',
			'rght' => '369'
		),
		array(
			'id' => '187',
			'parent_id' => '177',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'activate',
			'lft' => '370',
			'rght' => '371'
		),
		array(
			'id' => '188',
			'parent_id' => '177',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'edit',
			'lft' => '372',
			'rght' => '373'
		),
		array(
			'id' => '189',
			'parent_id' => '177',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'forgot',
			'lft' => '374',
			'rght' => '375'
		),
		array(
			'id' => '190',
			'parent_id' => '177',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'reset',
			'lft' => '376',
			'rght' => '377'
		),
		array(
			'id' => '191',
			'parent_id' => '177',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'login',
			'lft' => '378',
			'rght' => '379'
		),
		array(
			'id' => '192',
			'parent_id' => '177',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'logout',
			'lft' => '380',
			'rght' => '381'
		),
		array(
			'id' => '193',
			'parent_id' => '177',
			'model' => '',
			'foreign_key' => '',
			'alias' => 'view',
			'lft' => '382',
			'rght' => '383'
		),
	);

}
