<?php
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Config\Option;
use Bitrix\Main\EventManager;
use Bitrix\Main\Application;
use Bitrix\Main\IO\Directory;
use Bitrix\Main\Diag\Debug;

Loc::loadMessages(__FILE__);

class skiminok_test extends CModule{
    
    public function __construct(){

        if ( file_exists(__DIR__."/version.php") ) {
      
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

    public function DoInstall(){
        global $APPLICATION;

        // проверяем версию битрикс чтобы использовать функционал ядра D7
        if( CheckVersion( ModuleManager::getVersion("main"), "14.00.00") ) {
            $this->InstallFiles();
            $this->InstallDB();    
            ModuleManager::registerModule($this->MODULE_ID);    
            $this->InstallEvents();
        } else {    
            $APPLICATION->ThrowException(
                Loc::getMessage("SKIMINOK_TEST_INSTALL_ERROR_VERSION")
            );
        }
        
        $APPLICATION->IncludeAdminFile(
            Loc::getMessage("SKIMINOK_TEST_INSTALL_TITLE")." - \"".Loc::getMessage("SKIMINOK_TEST_NAME")."\"",
            __DIR__."/step.php"
        );
    
      return false;


    }

    public function InstallFiles(){
        // CopyDirFiles(
        //     __DIR__."/assets/scripts", // откуда копируем
        //     Application::getDocumentRoot()."/bitrix/js/".$this->MODULE_ID."/", // куда копируем 
        //     true,
        //     true
        // );
     
        return true;
    }

    public function InstallEvents(){

        // регистрация обработчика событий обновления HL блоков
        $eventManager = EventManager::getInstance(); 
        $eventManager->registerEventHandler(
            "",            
            "OnAfterUpdate",
            'Skiminok\Test\Main', 
            "RecordChangeElement"
        );
        Debug::writeToFile("Регистрация обработчика событий изменения элементов"," ", "/upload/log.txt");
      return false;
    }


    public function InstallDB(){
        // регистрация обработчика событий обновления HL блоков
        $eventManager = \Bitrix\Main\EventManager::getInstance(); 
        $eventManager->addEventHandler($this->MODULE_ID,"OnAfterUpdate", "function");
        
        // создаем таблицу для фиксирования изменений элементов стправочника
        global $DB;
        $this->errors = false;
        $this->errors = $DB->RunSQLBatch(dirname(__FILE__) . "/db/install.sql");
        if ( !$this->errors ) { 
            return true;
        } else {
            return $this->errors;
        }
        return false;
    }


    public function DoUninstall(){

        global $APPLICATION;
      
        $this->UnInstallFiles();
        $this->UnInstallDB();
        $this->UnInstallEvents();
      
        ModuleManager::unRegisterModule($this->MODULE_ID);
      
        $APPLICATION->IncludeAdminFile(
            Loc::getMessage("SKIMINOK_TEST_UNINSTALL_TITLE")." - \"".Loc::getMessage("SKIMINOK_TEST_NAME")."\"",
            __DIR__."/unstep.php"
        );
      
        return false;
    }

    public function UnInstallFiles(){

        Directory::deleteDirectory(
            Application::getDocumentRoot()."/bitrix/js/".$this->MODULE_ID
        );
    
        Directory::deleteDirectory(
            Application::getDocumentRoot()."/bitrix/css/".$this->MODULE_ID
        );
    
        return false;
    }

    public function UnInstallDB(){
        global $DB;
        Option::delete($this->MODULE_ID);
        if ( file_exists($f = dirname(__FILE__) . '/db/uninstall.sql') ) {
            foreach($DB->ParseSQLBatch(file_get_contents($f)) as $sql)
            $DB->Query($sql);
        }
        return false;
    }

    public function UnInstallEvents(){

        EventManager::getInstance()->unRegisterEventHandler(
            "",
            "OnAfterUpdate",            
            'Skiminok\Test\Main', 
            "RecordChangeElement"
        );
      
        return false;
    }

} 