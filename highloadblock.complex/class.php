<?php
if ( ! defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class HighLoadBlockComplexComponent 'Комплексный компонент для работы с HL-блоками Битрикс'
 */
class HighLoadBlockComplexComponent extends CBitrixComponent
{
    /** @var int Разбить по страницам количеством */
    public $iRowsPerPage = 10;

    /** @var string Идентификатор страницы (название переменной) */
    public $sPagenId = 'page';

    /** @var string Поле сортировки */
    public $sSortField = 'ID';

    /** @var string Ключ записи (наименование поля) */
    public $sRowKey = 'ID';

    /** @var string Направление сортировки */
    public $sSortOrder = 'DESC';

    /** @var array Массив для задания псевдонимов по умолчанию переменных в режиме ЧПУ */
    public $arDefaultVariableAliases404 = [];

    /** @var array Массив для задания псевдонимов по умолчанию переменных в режиме не ЧПУ */
    public $arDefaultVariableAliases = [];

    /** @var array Массив для задания путей по умолчанию для работы в ЧПУ режиме */
    public $arDefaultUrlTemplates404 = [
        'list' => '',
        'view' => '#BLOCK_ID#/#ID#/',
    ];

    /** @var array Массив имен переменных, которые компонент может получать из запроса */
    public $arComponentVariables = [
        'BLOCK_ID',
        'ID',
    ];

    /** @var array Массив шаблонов путей комплексного компонента для режима ЧПУ */
    public $arUrlTemplates = [];

    /** @var string Код шаблона, которому соответствует запрошенный адрес */
    public $componentPage = '';

    /** @var array Массив псевдонимов переменных */
    public $arVariables = [];

    /**
     * @param $arParams
     * @return array
     */
    public function onPrepareComponentParams($arParams)
    {
        $arParams['BLOCK_ID'] = (int)$arParams['BLOCK_ID'];

        $arParams['ROWS_PER_PAGE'] = (int)$arParams['ROWS_PER_PAGE'];
        if ($arParams['ROWS_PER_PAGE'] <= 0) {
            $arParams['ROWS_PER_PAGE'] = $this->iRowsPerPage;
        }

        $arParams['PAGEN_ID'] = trim($arParams['PAGEN_ID']);
        if (strlen($arParams['PAGEN_ID']) <= 0) {
            $arParams['PAGEN_ID'] = $this->sPagenId;
        }

        $arParams['SORT_FIELD'] = trim($arParams['SORT_FIELD']);
        if (strlen($arParams['SORT_FIELD']) <= 0) {
            $arParams['SORT_FIELD'] = $this->sSortField;
        }

        $arParams['SORT_ORDER'] = trim($arParams['SORT_ORDER']);
        if (strlen($arParams['SORT_ORDER']) <= 0) {
            $arParams['SORT_ORDER'] = $this->sRowKey;
        }

        $arParams['ROW_KEY'] = trim($arParams['ROW_KEY']);
        if (strlen($arParams['ROW_KEY']) <= 0) {
            $arParams['ROW_KEY'] = $this->sSortOrder;
        }

        return parent::onPrepareComponentParams($arParams);
    }

    /**
     * @return mixed|void
     * @throws \Bitrix\Main\LoaderException
     */
    public function executeComponent()
    {
        global $APPLICATION;

        $arComponentVariables = $this->arComponentVariables;
        $arVariables = $this->arVariables;

        if ( ! Loader::includeModule('highloadblock')) {
            ShowError(Loc::getMessage('HLCOMPLEX_COMPONENT_CHECK_HL_MODULE_ERROR'));
            return;
        }

        if ($this->arParams['BLOCK_ID'] <= 0) {
            ShowError(Loc::getMessage('HLCOMPLEX_COMPONENT_CHECK_BLOCK_ID_ERROR'));
            return;
        }

        if ($this->arParams['SEF_MODE'] == 'Y') {
            $arDefaultUrlTemplates404 = $this->arDefaultUrlTemplates404;
            $arDefaultVariableAliases404 = $this->arDefaultVariableAliases404;

            $arUrlTemplates = CComponentEngine::MakeComponentUrlTemplates(
                $arDefaultUrlTemplates404,
                $this->arParams['SEF_URL_TEMPLATES']
            );

            $arVariableAliases = CComponentEngine::MakeComponentVariableAliases(
                $arDefaultVariableAliases404,
                $this->arParams['VARIABLE_ALIASES']
            );

            $componentPage = CComponentEngine::ParseComponentPath(
                $this->arParams['SEF_FOLDER'],
                $arUrlTemplates,
                $arVariables
            );

            if ( ! $componentPage) {
                $componentPage = 'list';
            }

            CComponentEngine::InitComponentVariables(
                $componentPage,
                $arComponentVariables,
                $arVariableAliases,
                $arVariables
            );

            $this->arResult['FOLDER'] = $this->arParams['SEF_FOLDER'];
            $this->arResult['URL_TEMPLATES'] = $arUrlTemplates;
        } else {
            $arDefaultVariableAliases = $this->arDefaultVariableAliases;

            $arVariableAliases = CComponentEngine::MakeComponentVariableAliases(
                $arDefaultVariableAliases,
                $this->arParams['VARIABLE_ALIASES']
            );

            CComponentEngine::InitComponentVariables(
                false,
                $arComponentVariables,
                $arVariableAliases,
                $arVariables
            );

            if (isset($arVariables['ID']) && intval($arVariables['ID']) > 0) {
                $componentPage = 'view';
            } else {
                $componentPage = 'list';
            }

            $sGetCurPage = htmlspecialchars($APPLICATION->GetCurPage());

            $this->arResult['FOLDER'] = '';
            $this->arResult['URL_TEMPLATES'] = [
                'list' => $sGetCurPage,
                'view' => $sGetCurPage . '?' . $arVariableAliases['BLOCK_ID'] . '=#BLOCK_ID#' . '&' . $arVariableAliases['ID'] . '=#ID#',
            ];
        }

        $this->arResult['VARIABLES'] = $arVariables;
        $this->arResult['ALIASES'] = $arVariableAliases;
        $this->arResult['CURRENT_TEMPLATE'] = $componentPage;

        $this->includeComponentTemplate($componentPage);
    }
}
