<?php

define("_UPGRADER_TITLE", "Docebo 3.6.0.3 - Обновление");
define("_JUMP_TO_CONTENT", "Переход к содержанию");
define("_CHOOSE_LANG", "Выбор языка");
define("_LANG_SELECTION", "Выбор языка");

// choose begin
define("_TITLE_1OF2", "Step 1 of 2 : Выберите начальную версию");
define("_IS_PRESENT_DIRECTORIES","Есть директория, которая больше не используется, мы рекомендуем её удалить: ");
define("_LACKING_DIRECTORIES","Отсутствуют некоторые директории без которых Вы не сможете установить часть приложения или использовать приложение корректно : ");
define("_CANT_CONNECT_WITH_DB", "Произошла ошибка при соединении с базой данных, проверьте параметры в config.php");
define("_CANT_CONNECT_WITH_FTP","Произошла ошибка при соединении с FTP сервером, проверьте параметры в config.php");
define("_CHECKED_DIRECTORIES","Каталог, в котором храняться данные, не существует или у Вас нет прав на доступ");
define("_EMPTY_DIRECTORIES","Каталог для хранения данных пуст, Вы уверены, что нет старых файлов для импорта?");
define("_START_VERSION","Начальная версия");
define("_END_VERSION","Конечная версия");
define("_DOUPGRADE", "Продолжить обновление");

// result 
define("_TITLE_2OF2","Step 2 of 2 : Обновление системы");
define("_UPGRADING_VERSION","Версия обновления : ");
define("_FAILED_OPERATION","Ошибка операции, код ошибки : ");
define("_SUCCESSFULL_OPERATION", "Операция успешна : ");
define("_CRITICAL_ERROR_UPGRADE_SUSPENDED","При обновлении возникла критическая ошибка, обновление было остановлено");
define("_TITLE_STEP3", "Обновить языковые файлы");
define("_LANG_INSTALLED", "Обновление языковых файлов");
define("_LANGUAGE", "");
define("_PLATFORM", " для платформы");
define("_LANGUAGE_NOT_FOUND", "Языковой файл не найден");
define("_NEXT", "Дальше");
define("_NEXTSTEP", "Дальше");
define("_ENDSTEP", "Конец");
define("_END_PHRASE", "Операция успешно завершена");
define("_CRITICAL_ERROR","Критическая ошибка ");
define("_NOTSCORM","Этот сервер не поддерживает domxml или php5, Вы не сможете обновить docebo, попросите Вшего провайдера установить domxml extension на этом сервере");
define("_YOU_DONT_HAVE_FUNCTION_OVERLOAD","перегруженная функция не активна, это означает что Вы должны установить php версии 4.3.0 или более позднюю. Linux mandriva скомпилирована без перегрузки, поищите пакет с именем похожим на: php4-overload-xxxxx.mdk и установите модуль, на Linux fedora core 4 иногда бывают ошибки с перегрузкой, <a href=\"http://download.fedora.redhat.com/pub/fedora/linux/core/updates/4/\" target=\"_blank\">please patch it</a>. Если Вы используете Windows мы советуем использовать <a href=\"http://www.easyphp.org\" target=\"_blank\">easyphp 1.8</a>.");
define("_NEXT_OVERWRITELANG", "Продолжить с обновлением стандартных языковых файлов");
define("_NEXT_ONLY_ADD", "Продолжить с обновлением языковых файлов, будут добавлены только новые слова");
define("_CONVERT_TO_UTF", "производится преобразование в формат utf-8 ...");
define("_CONVERT_TO_UTF_COMMENT", "производится обновление языковых данных и содержания в формат utf-8d, не останавливайте эту операцию");
?>
