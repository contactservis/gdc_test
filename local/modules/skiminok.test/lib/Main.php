<?php
namespace Skiminok\Test;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Page\Asset;
use Bitrix\Main\Diag\Debug;

class Main {

    /*
    *  Метод записывает изменения при наступлении события "Изменения элемента инфоблока"
    */
    public function RecordChangeElement(){
        Debug::writeToFile("Write chenge in table", "", "/upload/log.txt");
        $connection = \Bitrix\Main\Application::getConnection();
        $connection->queryExecute("INSERT INTO __record_change_elements (ID_TABLE, ID_ELEMENT, CHANGE, DATE_TIME) VALUES (1, 1, 'yes chenge', date(yyyy-mm-dd hh:mm:ss) )");
    }

}