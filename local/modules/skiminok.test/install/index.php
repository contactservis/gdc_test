<?php
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Config\Option;
use Bitrix\Main\EventManager;
use Bitrix\Main\Application;
use Bitrix\Main\IO\Directory;

Loc::loadMessages(__FILE__);

class skiminok_test extends CModule{
    
    public function __construct(){

        if(file_exists(__DIR__."/version.php")){
      
            $arModuleVersion = array();
      
            include_once(__DIR__."/version.php");
      
            $this->MODULE_ID            = str_replace("_", ".", get_class($this));
            $this->MODULE_VERSION       = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE  = $arModuleVersion["VERSION_DATE"];
            $this->MODULE_NAME          = Loc::getMessage("SKIMINOK_TEST_NAME");
            $this->MODULE_DESCRIPTION   = Loc::getMessage("SKIMINOK_TEST_DESCRIPTION");
            $this->PARTNER_NAME         = Loc::getMessage("SKIMINOK_TEST_PARTNER_NAME");
            $this->PARTNER_URI          = Loc::getMessage("SKIMINOK_TEST_PARTNER_URI");
       }
      
         return false;
      }
} 