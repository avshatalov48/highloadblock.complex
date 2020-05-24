<?php
if ( ! defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Highloadblock\HighloadBlockTable as HL;

Loc::loadMessages(__FILE__);

try {
    if ( ! Loader::includeModule('highloadblock')) {
        throw new \Exception(Loc::getMessage('HLCOMPLEX_COMPONENT_CHECK_HL_MODULE_ERROR'));
    }

    // Список HL-блоков
    $arListHL = [];

    $resHLBlock = HL::getList([
        'select' => [
            'ID',
            'NAME',
            'NAME_LANG' => 'LANG.NAME',
        ],
        'order'  => [
            'ID' => 'ASC',
        ],
    ]);

    while ($arHLBlock = $resHLBlock->Fetch()) {
        $iHlId = $arHLBlock['ID'];
        $sHlName = ($arHLBlock['NAME_LANG']) ?: $arHLBlock['NAME'];
        $arListHL[$iHlId] = "[{$iHlId}] {$sHlName}";
    }

    // Список полей выбранного HL-блока
    $arFieldsHL = ['ID' => 'ID'];

    $iHlBlockId = (int)$arCurrentValues['BLOCK_ID'];

    if (sizeof($arListHL) === 1) {
        $iHlBlockId = reset(array_keys($arListHL));
    }

    if ($iHlBlockId > 0) {
        $hlblock = HL::getById($iHlBlockId)->fetch();
        $oEntity = HL::compileEntity($hlblock);
        $oFieldsHL = $oEntity->getFields();
        foreach ($oFieldsHL as $sKey => $arValue) {
            $arFieldsHL[$sKey] = $sKey;
        }
    }
} catch (\Exception $e) {
    ShowError($e->getMessage());
    return;
}

// Параметры
$arComponentParameters = [
    'GROUPS'     => [
        'HLLIST' => [
            'NAME' => Loc::getMessage('HLLIST_COMPONENT_NAME'),
        ],
        'HLVIEW' => [
            'NAME' => Loc::getMessage('HLVIEW_COMPONENT_NAME'),
        ],
    ],
    'PARAMETERS' => [
        'VARIABLE_ALIASES'  => [
            'BLOCK_ID' => ['NAME' => Loc::getMessage('HLCOMPLEX_COMPONENT_BLOCK_ID_DESCRIPTION')],
            'ID'       => ['NAME' => Loc::getMessage('HLCOMPLEX_COMPONENT_ID_DESCRIPTION')],
        ],
        // ЧПУ
        'SEF_MODE'          => [
            'list' => [
                'NAME'      => Loc::getMessage('HLCOMPLEX_COMPONENT_LIST_PAGE'),
                'DEFAULT'   => '',
                'VARIABLES' => ['BLOCK_ID'],
            ],
            'view' => [
                'NAME'      => Loc::getMessage('HLCOMPLEX_COMPONENT_VIEW_PAGE'),
                'DEFAULT'   => '#BLOCK_ID#/#ID#/',
                'VARIABLES' => ['BLOCK_ID', 'ID'],
            ],
        ],
        // Общие
        'BLOCK_ID'          => [
            'PARENT'  => 'BASE',
            'NAME'    => Loc::getMessage('HLCOMPLEX_COMPONENT_BLOCK_ID_PARAM'),
            'TYPE'    => 'LIST',
            'REFRESH' => 'Y',
            'VALUES'  => $arListHL,
        ],
        'CHECK_PERMISSIONS' => [
            'PARENT' => 'BASE',
            'NAME'   => Loc::getMessage('HLCOMPLEX_COMPONENT_CHECK_PERMISSIONS_PARAM'),
            'TYPE'   => 'CHECKBOX',
        ],
        // Список
        'ROWS_PER_PAGE'     => [
            'PARENT'  => 'HLLIST',
            'NAME'    => Loc::getMessage('HLLIST_COMPONENT_ROWS_PER_PAGE_PARAM'),
            'TYPE'    => 'TEXT',
            'DEFAULT' => '10',
        ],
        'PAGEN_ID'          => [
            'PARENT'  => 'HLLIST',
            'NAME'    => Loc::getMessage('HLLIST_COMPONENT_PAGEN_ID_PARAM'),
            'TYPE'    => 'TEXT',
            'DEFAULT' => 'page',
        ],
        'FILTER_NAME'       => [
            'PARENT' => 'HLLIST',
            'NAME'   => Loc::getMessage('HLLIST_COMPONENT_FILTER_NAME_PARAM'),
            'TYPE'   => 'TEXT',
        ],
        'SORT_FIELD'        => [
            'PARENT'  => 'HLLIST',
            'NAME'    => Loc::getMessage('HLLIST_COMPONENT_SORT_FIELD_PARAM'),
            'TYPE'    => 'LIST',
            'DEFAULT' => 'ID',
            'VALUES'  => $arFieldsHL,
        ],
        'SORT_ORDER'        => [
            'PARENT'  => 'HLLIST',
            'NAME'    => Loc::getMessage('HLLIST_COMPONENT_SORT_ORDER_PARAM'),
            'TYPE'    => 'LIST',
            'DEFAULT' => 'DESC',
            'VALUES'  => [
                'DESC' => Loc::getMessage('HLLIST_COMPONENT_SORT_ORDER_PARAM_DESC'),
                'ASC'  => Loc::getMessage('HLLIST_COMPONENT_SORT_ORDER_PARAM_ASC'),
            ],
        ],
        // Детальная
        'ROW_KEY'           => [
            'PARENT'  => 'HLVIEW',
            'NAME'    => Loc::getMessage('HLVIEW_COMPONENT_KEY_PARAM'),
            'TYPE'    => 'LIST',
            'DEFAULT' => 'ID',
            'VALUES'  => $arFieldsHL,
        ],
    ],
];
