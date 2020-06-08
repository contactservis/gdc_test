<?php
use Bitrix\Main;
use Bitrix\Main\Entity;
use Bitrix\Main\Diag\Debug;
use Bitrix\Main\EventManager;
use Bitrix\Main\Type\DateTime;

$eventManager = EventManager::getInstance();
$eventManager->addEventHandler("", "SearchChengeOnBeforeUpdate", "ColorReferenceBeforeUpdate");

function ColorReferenceBeforeUpdate(Entity\Event $event)
{ 
    Debug::writeToFile("================================================="," ", "/upload/log.txt");
    Debug::writeToFile("Произошло изменения элементов в HL блоке "," ", "/upload/log.txt");
    Debug::writeToFile($event->getParameters()," ", "/upload/log.txt");

    $arrGetChengeParams = $event->getParameters();    
    $dateTime = new DateTime();
    $ID_ELEMENT = $arrGetChengeParams["id"]["ID"];
    $ChangesParams = array(
        "ID_TABLE" => 15,
        "ID_ELEMENT" => $arrGetChengeParams["id"]["ID"],
        "CHANGE" => "'".json_encode($arrGetChengeParams["fields"])."'",
        "DATE_TIME" => "'".$dateTime->format("Y-m-d H:i:s")."'"// strval(date("Y-m-d H:i:s"))
    );

    // записываем изменения в таблицу
    global $DB;
    
    // проверим есть ли изменяемый элемент в таблице
    $results_hlblocks_list = $DB->Query("SELECT * FROM __record_change_elements WHERE ID_ELEMENT =".$ID_ELEMENT);
    $arrID_EL = array();
    while ($row = $results_hlblocks_list->Fetch())
    {
        $arrID_EL = $row;
    }

    // определение измененного ИД Элемента
    $ID_EL = 0;
    if ( !empty($arrID_EL) ) {
        $ID_EL = $arrID_EL["ID"];
    }

        
    if ( $ID_EL > 0 ) {
        // обновление записи
        $DB->Update("__record_change_elements", $ChangesParams, "WHERE ID_ELEMENT='".$ID_EL."'", $err_mess.__LINE__);
        Debug::writeToFile("Обновление в таблице изменений"," ", "/upload/log.txt");
        Debug::writeToFile($ChangesParams," ", "/upload/log.txt");
    }else  {
        // если нет создаем новую запись    
        $ID = $DB->Insert("__record_change_elements", $ChangesParams, $err_mess.__LINE__);
        Debug::writeToFile("Добавление в таблицу изменений"," ", "/upload/log.txt");
        Debug::writeToFile($ChangesParams," ", "/upload/log.txt");
    }

    Debug::writeToFile("================================================="," ", "/upload/log.txt");
}
 