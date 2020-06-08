<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\HttpApplication;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Diag\Debug;


Loader::includeModule("iblock");

Loc::loadMessages(__FILE__);

$request = HttpApplication::getInstance()->getContext()->getRequest();

$module_id = htmlspecialcharsbx($request["mid"] != "" ? $request["mid"] : $request["id"]);
Loader::includeModule($module_id);
$MESS_ERR =  "NONE";

// запросим список таблиц HL Блоков классы
$results_hlblocks_list = $DB->Query("SELECT * FROM `b_hlblock_entity`");
$arrListInfobloks[] = Loc::getMessage("SKIMINOK_TEST_OPTIONS_TAB_ACTION");

while ($row = $results_hlblocks_list->Fetch())
{
    $arrListInfobloks[] = array(
        $row['TABLE_NAME'],
        $row['NAME'],
        "N",
        array("checkbox")
    );
}

// Параметры вкладок на странице настройки модуля
$aTabs = array(
    // вкладка настройки
    array (
        "DIV"       => "edit",
        "TAB"       => Loc::getMessage("SKIMINOK_TEST_OPTIONS_TAB_NAME"),
        "TITLE"     => "",
        
        "OPTIONS" => $arrListInfobloks

    )
);

// сохранение параметров модуля
if( $request->isPost() && check_bitrix_sessid() ) {

    foreach( $aTabs as $aTab ){

        foreach( $aTab["OPTIONS"] as $arOption ) {
 
            if( !is_array($arOption) ) {  
                continue;
            }
 
            if( $arOption["note"] ) { 
                 continue;
            }
 
            if( $request["apply"] ) { 
                $optionValue = $request->getPost($arOption[0]);
                Option::set($module_id, $arOption[0], is_array($optionValue) ? implode(",", $optionValue) : $optionValue);             

            } elseif ( $request["default"] ){ 
              Option::set($module_id, $arOption[0], $arOption[2]);
              
            }
        }
    }    
    LocalRedirect($APPLICATION->GetCurPage()."?mid=".$module_id."&lang=".LANG);

}

// Вывод вкладок на странице настроек
$tabControl = new CAdminTabControl(
    "tabControl",
    $aTabs
);  
$tabControl->Begin();

//Debug::dump($aTabs);
Debug::writeToFile("Открыта страница с настройками", "", "/upload/log.txt");
?>

<form action="<? echo($APPLICATION->GetCurPage()); ?>?mid=<? echo($module_id); ?>&lang=<? echo(LANG); ?>" method="post">

  <?
   foreach($aTabs as $aTab){

        if($aTab["OPTIONS"]){

            $tabControl->BeginNextTab();
            __AdmSettingsDrawList($module_id, $aTab["OPTIONS"]);
        }
   }

   $tabControl->Buttons();
  ?>
    <input type="submit" name="apply" value="<? echo(Loc::GetMessage("SKIMINOK_TEST_OPTIONS_INPUT_APPLY")); ?>" class="adm-btn-save" />
    <input type="submit" name="default" value="<? echo(Loc::GetMessage("SKIMINOK_TEST_OPTIONS_INPUT_DEFAULT")); ?>" />

   <?
   echo(bitrix_sessid_post());
 ?>

</form>