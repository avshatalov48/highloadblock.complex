<? if ( ! defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
?>

<? $APPLICATION->IncludeComponent(
    'bitrix:highloadblock.list',
    '',
    [
        'BLOCK_ID'           => $arParams['BLOCK_ID'],
        'CHECK_PERMISSIONS'  => $arParams['CHECK_PERMISSIONS'],
        'DETAIL_URL'         => $arResult['URL_TEMPLATES']['view'],
        'FILTER_NAME'        => $arParams['FILTER_NAME'],
        'PAGEN_ID'           => $arParams['PAGEN_ID'],
        'ROWS_PER_PAGE'      => $arParams['ROWS_PER_PAGE'],
        'SORT_FIELD'         => $arParams['SORT_FIELD'],
        'SORT_ORDER'         => $arParams['SORT_ORDER'],
        'COMPONENT_TEMPLATE' => $arParams['COMPONENT_TEMPLATE'],
    ],
    false
); ?>
