<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\HttpApplication;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;

Loc::loadMessages(__FILE__);

$request = HttpApplication::getInstance()->getContext()->getRequest();

$module_id = htmlspecialcharsbx($request["mid"] != "" ? $request["mid"] : $request["id"]);
Loader::includeModule($module_id);
$MESS_ERR =  "NONE";
if( CModule::IncludeModule("iblock") )
{ 
   //здесь можно использовать функции и классы модуля
   $res = CIBlock::GetList(
    Array(), 
    Array(
       'TYPE'=>'catalog', 
       'SITE_ID'=>SITE_ID, 
       'ACTIVE'=>'Y', 
       "CNT_ACTIVE"=>"Y", 
       "!CODE"=>'my_products'
    ), true
 );
 while($ar_res = $res->Fetch())
 {
    echo $ar_res['NAME'].': '.$ar_res['ELEMENT_CNT'];
 }
} else {
    $MESS_ERR =  "ERROR LIST IB0";
}


$aTabs = array(
    array(
        "DIV"       => "edit",
        "TAB"       => Loc::getMessage("SKIMINOK_TEST_OPTIONS_TAB_NAME"),
        "TITLE"     => "",
        
        "OPTIONS" => array(
            // название вкладки            
            Loc::getMessage("SKIMINOK_TEST_OPTIONS_TAB_ACTION"),
            // параметры вкладки   
            array(
                "speed",
                Loc::getMessage("SKIMINOK_TEST_OPTIONS_TAB_SPEED"),
                    "normal",
                array("selectbox", 
                    array(
                        "slow"   => Loc::getMessage("SKIMINOK_TEST_OPTIONS_TAB_SPEED_SLOW"),
                        "normal" => Loc::getMessage("SKIMINOK_TEST_OPTIONS_TAB_SPEED_NORMAL"),
                        "fast"   => Loc::getMessage("SKIMINOK_TEST_OPTIONS_TAB_SPEED_FAST")
                    )
                )
            )
       )
   )
);

// вкладка настроек
$tabControl = new CAdminTabControl(
    "tabControl",
    $aTabs
);  
$tabControl->Begin();
?>

<form action="<? echo($APPLICATION->GetCurPage()); ?>?mid=<? echo($module_id); ?>&lang=<? echo(LANG); ?>" method="post">
    <? print_r($ar_res); ?>
  <?
   foreach($aTabs as $aTab){

       if($aTab["OPTIONS"]){

         $tabControl->BeginNextTab();

         __AdmSettingsDrawList($module_id, $aTab["OPTIONS"]);
      }
   }

   $tabControl->Buttons();
  ?>
    <?=$MESS_ERR?>
    <input type="submit" name="apply" value="<? echo(Loc::GetMessage("SKIMINOK_TEST_OPTIONS_INPUT_APPLY")); ?>" class="adm-btn-save" />
    <input type="submit" name="default" value="<? echo(Loc::GetMessage("SKIMINOK_TEST_OPTIONS_INPUT_DEFAULT")); ?>" />

   <?
   echo(bitrix_sessid_post());
 ?>

</form>