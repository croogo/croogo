<?php
namespace Croogo\Install\Config\Data;

class AcoData
{

    public $table = 'acos';

    public $records = [
        [
            'id' => '1',
            'parent_id' => '',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'controllers',
            'lft' => '1',
            'rght' => '386'
        ],
        [
            'id' => '2',
            'parent_id' => '1',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'Acl',
            'lft' => '2',
            'rght' => '25'
        ],
        [
            'id' => '3',
            'parent_id' => '2',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'AclActions',
            'lft' => '3',
            'rght' => '16'
        ],
        [
            'id' => '4',
            'parent_id' => '3',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_index',
            'lft' => '4',
            'rght' => '5'
        ],
        [
            'id' => '5',
            'parent_id' => '3',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_add',
            'lft' => '6',
            'rght' => '7'
        ],
        [
            'id' => '6',
            'parent_id' => '3',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_edit',
            'lft' => '8',
            'rght' => '9'
        ],
        [
            'id' => '7',
            'parent_id' => '3',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_delete',
            'lft' => '10',
            'rght' => '11'
        ],
        [
            'id' => '8',
            'parent_id' => '3',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_move',
            'lft' => '12',
            'rght' => '13'
        ],
        [
            'id' => '9',
            'parent_id' => '3',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_generate',
            'lft' => '14',
            'rght' => '15'
        ],
        [
            'id' => '10',
            'parent_id' => '2',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'AclPermissions',
            'lft' => '17',
            'rght' => '24'
        ],
        [
            'id' => '11',
            'parent_id' => '10',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_index',
            'lft' => '18',
            'rght' => '19'
        ],
        [
            'id' => '12',
            'parent_id' => '10',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_toggle',
            'lft' => '20',
            'rght' => '21'
        ],
        [
            'id' => '13',
            'parent_id' => '10',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_upgrade',
            'lft' => '22',
            'rght' => '23'
        ],
        [
            'id' => '14',
            'parent_id' => '1',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'Blocks',
            'lft' => '26',
            'rght' => '55'
        ],
        [
            'id' => '15',
            'parent_id' => '14',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'Blocks',
            'lft' => '27',
            'rght' => '44'
        ],
        [
            'id' => '16',
            'parent_id' => '15',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_toggle',
            'lft' => '28',
            'rght' => '29'
        ],
        [
            'id' => '17',
            'parent_id' => '15',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_index',
            'lft' => '30',
            'rght' => '31'
        ],
        [
            'id' => '18',
            'parent_id' => '15',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_add',
            'lft' => '32',
            'rght' => '33'
        ],
        [
            'id' => '19',
            'parent_id' => '15',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_edit',
            'lft' => '34',
            'rght' => '35'
        ],
        [
            'id' => '20',
            'parent_id' => '15',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_delete',
            'lft' => '36',
            'rght' => '37'
        ],
        [
            'id' => '21',
            'parent_id' => '15',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_moveup',
            'lft' => '38',
            'rght' => '39'
        ],
        [
            'id' => '22',
            'parent_id' => '15',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_movedown',
            'lft' => '40',
            'rght' => '41'
        ],
        [
            'id' => '23',
            'parent_id' => '15',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_process',
            'lft' => '42',
            'rght' => '43'
        ],
        [
            'id' => '24',
            'parent_id' => '14',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'Regions',
            'lft' => '45',
            'rght' => '54'
        ],
        [
            'id' => '25',
            'parent_id' => '24',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_index',
            'lft' => '46',
            'rght' => '47'
        ],
        [
            'id' => '26',
            'parent_id' => '24',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_add',
            'lft' => '48',
            'rght' => '49'
        ],
        [
            'id' => '27',
            'parent_id' => '24',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_edit',
            'lft' => '50',
            'rght' => '51'
        ],
        [
            'id' => '28',
            'parent_id' => '24',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_delete',
            'lft' => '52',
            'rght' => '53'
        ],
        [
            'id' => '29',
            'parent_id' => '1',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'Comments',
            'lft' => '56',
            'rght' => '73'
        ],
        [
            'id' => '30',
            'parent_id' => '29',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'Comments',
            'lft' => '57',
            'rght' => '72'
        ],
        [
            'id' => '31',
            'parent_id' => '30',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_index',
            'lft' => '58',
            'rght' => '59'
        ],
        [
            'id' => '32',
            'parent_id' => '30',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_edit',
            'lft' => '60',
            'rght' => '61'
        ],
        [
            'id' => '33',
            'parent_id' => '30',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_delete',
            'lft' => '62',
            'rght' => '63'
        ],
        [
            'id' => '34',
            'parent_id' => '30',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_process',
            'lft' => '64',
            'rght' => '65'
        ],
        [
            'id' => '35',
            'parent_id' => '30',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'index',
            'lft' => '66',
            'rght' => '67'
        ],
        [
            'id' => '36',
            'parent_id' => '30',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'add',
            'lft' => '68',
            'rght' => '69'
        ],
        [
            'id' => '37',
            'parent_id' => '30',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'delete',
            'lft' => '70',
            'rght' => '71'
        ],
        [
            'id' => '38',
            'parent_id' => '1',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'Contacts',
            'lft' => '74',
            'rght' => '97'
        ],
        [
            'id' => '39',
            'parent_id' => '38',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'Contacts',
            'lft' => '75',
            'rght' => '86'
        ],
        [
            'id' => '40',
            'parent_id' => '39',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_index',
            'lft' => '76',
            'rght' => '77'
        ],
        [
            'id' => '41',
            'parent_id' => '39',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_add',
            'lft' => '78',
            'rght' => '79'
        ],
        [
            'id' => '42',
            'parent_id' => '39',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_edit',
            'lft' => '80',
            'rght' => '81'
        ],
        [
            'id' => '43',
            'parent_id' => '39',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_delete',
            'lft' => '82',
            'rght' => '83'
        ],
        [
            'id' => '44',
            'parent_id' => '39',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'view',
            'lft' => '84',
            'rght' => '85'
        ],
        [
            'id' => '45',
            'parent_id' => '38',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'Messages',
            'lft' => '87',
            'rght' => '96'
        ],
        [
            'id' => '46',
            'parent_id' => '45',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_index',
            'lft' => '88',
            'rght' => '89'
        ],
        [
            'id' => '47',
            'parent_id' => '45',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_edit',
            'lft' => '90',
            'rght' => '91'
        ],
        [
            'id' => '48',
            'parent_id' => '45',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_delete',
            'lft' => '92',
            'rght' => '93'
        ],
        [
            'id' => '49',
            'parent_id' => '45',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_process',
            'lft' => '94',
            'rght' => '95'
        ],
        [
            'id' => '50',
            'parent_id' => '1',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'Croogo',
            'lft' => '98',
            'rght' => '99'
        ],
        [
            'id' => '51',
            'parent_id' => '1',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'Extensions',
            'lft' => '100',
            'rght' => '139'
        ],
        [
            'id' => '52',
            'parent_id' => '51',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'ExtensionsLocales',
            'lft' => '101',
            'rght' => '112'
        ],
        [
            'id' => '53',
            'parent_id' => '52',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_index',
            'lft' => '102',
            'rght' => '103'
        ],
        [
            'id' => '54',
            'parent_id' => '52',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_activate',
            'lft' => '104',
            'rght' => '105'
        ],
        [
            'id' => '55',
            'parent_id' => '52',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_add',
            'lft' => '106',
            'rght' => '107'
        ],
        [
            'id' => '56',
            'parent_id' => '52',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_edit',
            'lft' => '108',
            'rght' => '109'
        ],
        [
            'id' => '57',
            'parent_id' => '52',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_delete',
            'lft' => '110',
            'rght' => '111'
        ],
        [
            'id' => '58',
            'parent_id' => '51',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'ExtensionsPlugins',
            'lft' => '113',
            'rght' => '124'
        ],
        [
            'id' => '59',
            'parent_id' => '58',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_index',
            'lft' => '114',
            'rght' => '115'
        ],
        [
            'id' => '60',
            'parent_id' => '58',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_add',
            'lft' => '116',
            'rght' => '117'
        ],
        [
            'id' => '61',
            'parent_id' => '58',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_delete',
            'lft' => '118',
            'rght' => '119'
        ],
        [
            'id' => '62',
            'parent_id' => '58',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_toggle',
            'lft' => '120',
            'rght' => '121'
        ],
        [
            'id' => '63',
            'parent_id' => '58',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_migrate',
            'lft' => '122',
            'rght' => '123'
        ],
        [
            'id' => '64',
            'parent_id' => '51',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'ExtensionsThemes',
            'lft' => '125',
            'rght' => '138'
        ],
        [
            'id' => '65',
            'parent_id' => '64',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_index',
            'lft' => '126',
            'rght' => '127'
        ],
        [
            'id' => '66',
            'parent_id' => '64',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_activate',
            'lft' => '128',
            'rght' => '129'
        ],
        [
            'id' => '67',
            'parent_id' => '64',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_add',
            'lft' => '130',
            'rght' => '131'
        ],
        [
            'id' => '68',
            'parent_id' => '64',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_editor',
            'lft' => '132',
            'rght' => '133'
        ],
        [
            'id' => '69',
            'parent_id' => '64',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_save',
            'lft' => '134',
            'rght' => '135'
        ],
        [
            'id' => '70',
            'parent_id' => '64',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_delete',
            'lft' => '136',
            'rght' => '137'
        ],
        [
            'id' => '71',
            'parent_id' => '1',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'FileManager',
            'lft' => '140',
            'rght' => '175'
        ],
        [
            'id' => '72',
            'parent_id' => '71',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'Attachments',
            'lft' => '141',
            'rght' => '152'
        ],
        [
            'id' => '73',
            'parent_id' => '72',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_index',
            'lft' => '142',
            'rght' => '143'
        ],
        [
            'id' => '74',
            'parent_id' => '72',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_add',
            'lft' => '144',
            'rght' => '145'
        ],
        [
            'id' => '75',
            'parent_id' => '72',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_edit',
            'lft' => '146',
            'rght' => '147'
        ],
        [
            'id' => '76',
            'parent_id' => '72',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_delete',
            'lft' => '148',
            'rght' => '149'
        ],
        [
            'id' => '77',
            'parent_id' => '72',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_browse',
            'lft' => '150',
            'rght' => '151'
        ],
        [
            'id' => '78',
            'parent_id' => '71',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'FileManager',
            'lft' => '153',
            'rght' => '174'
        ],
        [
            'id' => '79',
            'parent_id' => '78',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_index',
            'lft' => '154',
            'rght' => '155'
        ],
        [
            'id' => '80',
            'parent_id' => '78',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_browse',
            'lft' => '156',
            'rght' => '157'
        ],
        [
            'id' => '81',
            'parent_id' => '78',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_editfile',
            'lft' => '158',
            'rght' => '159'
        ],
        [
            'id' => '82',
            'parent_id' => '78',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_upload',
            'lft' => '160',
            'rght' => '161'
        ],
        [
            'id' => '83',
            'parent_id' => '78',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_delete_file',
            'lft' => '162',
            'rght' => '163'
        ],
        [
            'id' => '84',
            'parent_id' => '78',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_delete_directory',
            'lft' => '164',
            'rght' => '165'
        ],
        [
            'id' => '85',
            'parent_id' => '78',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_rename',
            'lft' => '166',
            'rght' => '167'
        ],
        [
            'id' => '86',
            'parent_id' => '78',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_create_directory',
            'lft' => '168',
            'rght' => '169'
        ],
        [
            'id' => '87',
            'parent_id' => '78',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_create_file',
            'lft' => '170',
            'rght' => '171'
        ],
        [
            'id' => '88',
            'parent_id' => '78',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_chmod',
            'lft' => '172',
            'rght' => '173'
        ],
        [
            'id' => '89',
            'parent_id' => '1',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'Install',
            'lft' => '176',
            'rght' => '189'
        ],
        [
            'id' => '90',
            'parent_id' => '89',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'Install',
            'lft' => '177',
            'rght' => '188'
        ],
        [
            'id' => '91',
            'parent_id' => '90',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'index',
            'lft' => '178',
            'rght' => '179'
        ],
        [
            'id' => '92',
            'parent_id' => '90',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'database',
            'lft' => '180',
            'rght' => '181'
        ],
        [
            'id' => '93',
            'parent_id' => '90',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'data',
            'lft' => '182',
            'rght' => '183'
        ],
        [
            'id' => '94',
            'parent_id' => '90',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'adminuser',
            'lft' => '184',
            'rght' => '185'
        ],
        [
            'id' => '95',
            'parent_id' => '90',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'finish',
            'lft' => '186',
            'rght' => '187'
        ],
        [
            'id' => '96',
            'parent_id' => '1',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'Menus',
            'lft' => '190',
            'rght' => '219'
        ],
        [
            'id' => '97',
            'parent_id' => '96',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'Links',
            'lft' => '191',
            'rght' => '208'
        ],
        [
            'id' => '98',
            'parent_id' => '97',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_toggle',
            'lft' => '192',
            'rght' => '193'
        ],
        [
            'id' => '99',
            'parent_id' => '97',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_index',
            'lft' => '194',
            'rght' => '195'
        ],
        [
            'id' => '100',
            'parent_id' => '97',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_add',
            'lft' => '196',
            'rght' => '197'
        ],
        [
            'id' => '101',
            'parent_id' => '97',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_edit',
            'lft' => '198',
            'rght' => '199'
        ],
        [
            'id' => '102',
            'parent_id' => '97',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_delete',
            'lft' => '200',
            'rght' => '201'
        ],
        [
            'id' => '103',
            'parent_id' => '97',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_moveup',
            'lft' => '202',
            'rght' => '203'
        ],
        [
            'id' => '104',
            'parent_id' => '97',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_movedown',
            'lft' => '204',
            'rght' => '205'
        ],
        [
            'id' => '105',
            'parent_id' => '97',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_process',
            'lft' => '206',
            'rght' => '207'
        ],
        [
            'id' => '106',
            'parent_id' => '96',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'Menus',
            'lft' => '209',
            'rght' => '218'
        ],
        [
            'id' => '107',
            'parent_id' => '106',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_index',
            'lft' => '210',
            'rght' => '211'
        ],
        [
            'id' => '108',
            'parent_id' => '106',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_add',
            'lft' => '212',
            'rght' => '213'
        ],
        [
            'id' => '109',
            'parent_id' => '106',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_edit',
            'lft' => '214',
            'rght' => '215'
        ],
        [
            'id' => '110',
            'parent_id' => '106',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_delete',
            'lft' => '216',
            'rght' => '217'
        ],
        [
            'id' => '111',
            'parent_id' => '1',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'Meta',
            'lft' => '220',
            'rght' => '221'
        ],
        [
            'id' => '112',
            'parent_id' => '1',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'Migrations',
            'lft' => '222',
            'rght' => '223'
        ],
        [
            'id' => '113',
            'parent_id' => '1',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'Nodes',
            'lft' => '224',
            'rght' => '257'
        ],
        [
            'id' => '114',
            'parent_id' => '113',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'Nodes',
            'lft' => '225',
            'rght' => '256'
        ],
        [
            'id' => '115',
            'parent_id' => '114',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_toggle',
            'lft' => '226',
            'rght' => '227'
        ],
        [
            'id' => '116',
            'parent_id' => '114',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_index',
            'lft' => '228',
            'rght' => '229'
        ],
        [
            'id' => '117',
            'parent_id' => '114',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_create',
            'lft' => '230',
            'rght' => '231'
        ],
        [
            'id' => '118',
            'parent_id' => '114',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_add',
            'lft' => '232',
            'rght' => '233'
        ],
        [
            'id' => '119',
            'parent_id' => '114',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_edit',
            'lft' => '234',
            'rght' => '235'
        ],
        [
            'id' => '120',
            'parent_id' => '114',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_update_paths',
            'lft' => '236',
            'rght' => '237'
        ],
        [
            'id' => '121',
            'parent_id' => '114',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_delete',
            'lft' => '238',
            'rght' => '239'
        ],
        [
            'id' => '122',
            'parent_id' => '114',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_delete_meta',
            'lft' => '240',
            'rght' => '241'
        ],
        [
            'id' => '123',
            'parent_id' => '114',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_add_meta',
            'lft' => '242',
            'rght' => '243'
        ],
        [
            'id' => '124',
            'parent_id' => '114',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_process',
            'lft' => '244',
            'rght' => '245'
        ],
        [
            'id' => '125',
            'parent_id' => '114',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'index',
            'lft' => '246',
            'rght' => '247'
        ],
        [
            'id' => '126',
            'parent_id' => '114',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'term',
            'lft' => '248',
            'rght' => '249'
        ],
        [
            'id' => '127',
            'parent_id' => '114',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'promoted',
            'lft' => '250',
            'rght' => '251'
        ],
        [
            'id' => '128',
            'parent_id' => '114',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'search',
            'lft' => '252',
            'rght' => '253'
        ],
        [
            'id' => '129',
            'parent_id' => '114',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'view',
            'lft' => '254',
            'rght' => '255'
        ],
        [
            'id' => '130',
            'parent_id' => '1',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'Search',
            'lft' => '258',
            'rght' => '259'
        ],
        [
            'id' => '131',
            'parent_id' => '1',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'Settings',
            'lft' => '260',
            'rght' => '297'
        ],
        [
            'id' => '132',
            'parent_id' => '131',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'Languages',
            'lft' => '261',
            'rght' => '276'
        ],
        [
            'id' => '133',
            'parent_id' => '132',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_index',
            'lft' => '262',
            'rght' => '263'
        ],
        [
            'id' => '134',
            'parent_id' => '132',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_add',
            'lft' => '264',
            'rght' => '265'
        ],
        [
            'id' => '135',
            'parent_id' => '132',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_edit',
            'lft' => '266',
            'rght' => '267'
        ],
        [
            'id' => '136',
            'parent_id' => '132',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_delete',
            'lft' => '268',
            'rght' => '269'
        ],
        [
            'id' => '137',
            'parent_id' => '132',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_moveup',
            'lft' => '270',
            'rght' => '271'
        ],
        [
            'id' => '138',
            'parent_id' => '132',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_movedown',
            'lft' => '272',
            'rght' => '273'
        ],
        [
            'id' => '139',
            'parent_id' => '132',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_select',
            'lft' => '274',
            'rght' => '275'
        ],
        [
            'id' => '140',
            'parent_id' => '131',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'Settings',
            'lft' => '277',
            'rght' => '296'
        ],
        [
            'id' => '141',
            'parent_id' => '140',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_dashboard',
            'lft' => '278',
            'rght' => '279'
        ],
        [
            'id' => '142',
            'parent_id' => '140',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_index',
            'lft' => '280',
            'rght' => '281'
        ],
        [
            'id' => '143',
            'parent_id' => '140',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_view',
            'lft' => '282',
            'rght' => '283'
        ],
        [
            'id' => '144',
            'parent_id' => '140',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_add',
            'lft' => '284',
            'rght' => '285'
        ],
        [
            'id' => '145',
            'parent_id' => '140',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_edit',
            'lft' => '286',
            'rght' => '287'
        ],
        [
            'id' => '146',
            'parent_id' => '140',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_delete',
            'lft' => '288',
            'rght' => '289'
        ],
        [
            'id' => '147',
            'parent_id' => '140',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_prefix',
            'lft' => '290',
            'rght' => '291'
        ],
        [
            'id' => '148',
            'parent_id' => '140',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_moveup',
            'lft' => '292',
            'rght' => '293'
        ],
        [
            'id' => '149',
            'parent_id' => '140',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_movedown',
            'lft' => '294',
            'rght' => '295'
        ],
        [
            'id' => '150',
            'parent_id' => '1',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'Taxonomy',
            'lft' => '298',
            'rght' => '337'
        ],
        [
            'id' => '151',
            'parent_id' => '150',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'Terms',
            'lft' => '299',
            'rght' => '312'
        ],
        [
            'id' => '152',
            'parent_id' => '151',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_index',
            'lft' => '300',
            'rght' => '301'
        ],
        [
            'id' => '153',
            'parent_id' => '151',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_add',
            'lft' => '302',
            'rght' => '303'
        ],
        [
            'id' => '154',
            'parent_id' => '151',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_edit',
            'lft' => '304',
            'rght' => '305'
        ],
        [
            'id' => '155',
            'parent_id' => '151',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_delete',
            'lft' => '306',
            'rght' => '307'
        ],
        [
            'id' => '156',
            'parent_id' => '151',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_moveup',
            'lft' => '308',
            'rght' => '309'
        ],
        [
            'id' => '157',
            'parent_id' => '151',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_movedown',
            'lft' => '310',
            'rght' => '311'
        ],
        [
            'id' => '158',
            'parent_id' => '150',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'Types',
            'lft' => '313',
            'rght' => '322'
        ],
        [
            'id' => '159',
            'parent_id' => '158',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_index',
            'lft' => '314',
            'rght' => '315'
        ],
        [
            'id' => '160',
            'parent_id' => '158',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_add',
            'lft' => '316',
            'rght' => '317'
        ],
        [
            'id' => '161',
            'parent_id' => '158',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_edit',
            'lft' => '318',
            'rght' => '319'
        ],
        [
            'id' => '162',
            'parent_id' => '158',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_delete',
            'lft' => '320',
            'rght' => '321'
        ],
        [
            'id' => '163',
            'parent_id' => '150',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'Vocabularies',
            'lft' => '323',
            'rght' => '336'
        ],
        [
            'id' => '164',
            'parent_id' => '163',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_index',
            'lft' => '324',
            'rght' => '325'
        ],
        [
            'id' => '165',
            'parent_id' => '163',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_add',
            'lft' => '326',
            'rght' => '327'
        ],
        [
            'id' => '166',
            'parent_id' => '163',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_edit',
            'lft' => '328',
            'rght' => '329'
        ],
        [
            'id' => '167',
            'parent_id' => '163',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_delete',
            'lft' => '330',
            'rght' => '331'
        ],
        [
            'id' => '168',
            'parent_id' => '163',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_moveup',
            'lft' => '332',
            'rght' => '333'
        ],
        [
            'id' => '169',
            'parent_id' => '163',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_movedown',
            'lft' => '334',
            'rght' => '335'
        ],
        [
            'id' => '170',
            'parent_id' => '1',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'Ckeditor',
            'lft' => '338',
            'rght' => '339'
        ],
        [
            'id' => '171',
            'parent_id' => '1',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'Users',
            'lft' => '340',
            'rght' => '385'
        ],
        [
            'id' => '172',
            'parent_id' => '171',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'Roles',
            'lft' => '341',
            'rght' => '350'
        ],
        [
            'id' => '173',
            'parent_id' => '172',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_index',
            'lft' => '342',
            'rght' => '343'
        ],
        [
            'id' => '174',
            'parent_id' => '172',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_add',
            'lft' => '344',
            'rght' => '345'
        ],
        [
            'id' => '175',
            'parent_id' => '172',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_edit',
            'lft' => '346',
            'rght' => '347'
        ],
        [
            'id' => '176',
            'parent_id' => '172',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_delete',
            'lft' => '348',
            'rght' => '349'
        ],
        [
            'id' => '177',
            'parent_id' => '171',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'Users',
            'lft' => '351',
            'rght' => '384'
        ],
        [
            'id' => '178',
            'parent_id' => '177',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_index',
            'lft' => '352',
            'rght' => '353'
        ],
        [
            'id' => '179',
            'parent_id' => '177',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_add',
            'lft' => '354',
            'rght' => '355'
        ],
        [
            'id' => '180',
            'parent_id' => '177',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_edit',
            'lft' => '356',
            'rght' => '357'
        ],
        [
            'id' => '181',
            'parent_id' => '177',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_reset_password',
            'lft' => '358',
            'rght' => '359'
        ],
        [
            'id' => '182',
            'parent_id' => '177',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_delete',
            'lft' => '360',
            'rght' => '361'
        ],
        [
            'id' => '183',
            'parent_id' => '177',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_login',
            'lft' => '362',
            'rght' => '363'
        ],
        [
            'id' => '184',
            'parent_id' => '177',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'admin_logout',
            'lft' => '364',
            'rght' => '365'
        ],
        [
            'id' => '185',
            'parent_id' => '177',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'index',
            'lft' => '366',
            'rght' => '367'
        ],
        [
            'id' => '186',
            'parent_id' => '177',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'add',
            'lft' => '368',
            'rght' => '369'
        ],
        [
            'id' => '187',
            'parent_id' => '177',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'activate',
            'lft' => '370',
            'rght' => '371'
        ],
        [
            'id' => '188',
            'parent_id' => '177',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'edit',
            'lft' => '372',
            'rght' => '373'
        ],
        [
            'id' => '189',
            'parent_id' => '177',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'forgot',
            'lft' => '374',
            'rght' => '375'
        ],
        [
            'id' => '190',
            'parent_id' => '177',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'reset',
            'lft' => '376',
            'rght' => '377'
        ],
        [
            'id' => '191',
            'parent_id' => '177',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'login',
            'lft' => '378',
            'rght' => '379'
        ],
        [
            'id' => '192',
            'parent_id' => '177',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'logout',
            'lft' => '380',
            'rght' => '381'
        ],
        [
            'id' => '193',
            'parent_id' => '177',
            'model' => '',
            'foreign_key' => '',
            'alias' => 'view',
            'lft' => '382',
            'rght' => '383'
        ],
    ];
}
