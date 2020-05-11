<?php
if ( ! defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arComponentDescription = [
    'NAME'        => Loc::getMessage('HLCOMPLEX_COMPONENT_NAME'),
    'DESCRIPTION' => Loc::getMessage('HLCOMPLEX_COMPONENT_DESCRIPTION'),
    'SORT'        => 30,
    'CACHE_PATH'  => 'Y',
    'COMPLEX'     => 'Y',
    'PATH'        => [
        'ID'    => 'content',
        'CHILD' => [
            'ID'    => 'hlblock',
            'NAME'  => Loc::getMessage('HLCOMPLEX_COMPONENT_CATEGORY_TITLE'),
            'CHILD' => [
                'ID' => 'hlblock_complex',
            ],
        ],
    ],
];
