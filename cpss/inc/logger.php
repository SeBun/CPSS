<?php
/**
 * CONTROL AND PROTECTION OF THE SITE SYSTEM (CPSS)
 *
 * @copyright  Copyright (C) 2016 Sergey Bunin. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 * 
 * LOGGER - содержит класс журналирования событий. Выполняет ведение логов.
 * Для вызова логирования в коде используется конструкция:
 *   Logger::getLog($name)->log($data); в качестве $data передаем информацию, которуб нужно записать в файл. Время записи добавиться автоматически
 * Пример строки для логирования со страницы personal.php:
 *   Logger::getLog(GlobFunc::$loggerName)->log("Ошибка!!!");
 */
 
defined('_CPSS') or die;
 

/**
 * Данный класс просто открывает файл с нужным названием и делает в него запись. Если файла нет, он создается.
 * @link https://github.com/Dimau/HC/blob/master/models/Logger.php Оригинал этого класса.
 * @link http://easy-code.ru/lesson/logger-php Описание этого класса
 * 
 * @TODO: Кроме того, класс будет содержать средства чтения файлов логов.
 */
class CPSS_Logger
{   
    protected static $logs = array(); // Массив с именами файлов логов и их параметрами
    protected static $path = "logs";   // Адрес для папки с файлами логов
    protected $name;                    // Имя текущего лога
    protected $file;                    // Имя файла лога с расширением
    protected $fp;                      // Файловый поток, через который осуществляется запись
    protected $timezone;                // Временная зона, установленная в файле конфигурации
    
    
    /**
     * Конструктор будет использоваться внутри класса, непосредственно при логировании мы будем пользоваться функцией getLog
     * 
     * @param type $name Имя логгера
     * @param type $file Путь к файлу
     */
    public function __construct($name, $file = NULL) {
        
        $config = new CPSS_Config(); // объект конфигурации системы
        
        $this->timezone = $config->log_timezone ?: new DateTimeZone(date_default_timezone_get() ?: 'UTC'); // Установка временной зоны
        $this->path = $config->log_path; // Путь к папке логов из файла конфигурации
        $this->name = $name;
        $this->file = $file;
        $this->open();
    }
    
    
    /**
     *  ДЕСТРУКТОР
     */
    public function __destruct() {
        fclose($this->fp);
    }
    
    
    /**
     * Метод инициализирует файловый поток.
     * Если свойство $file не задано, то будет открыт файл с тем же именем, что и логгер.
     */
    public function open() {
        
        if (self::$path == null) {
            return;
        }
        
        // Убираем крайний слеш в пути к файлу, если задан:
        $path = preg_replace("#/$#", "", $path); 
        
        $this->fp = fopen($this->file == null ? self::$path . '/' . $this->name . '.log' : self::$path . '/' . $this->file, 'a+');
    }
    
    
    /**
     * Функция возвращает нам логгер, имя которого мы указали
     * @param string $name имя логгера, который нужно вернуть
     * @param null|string $file имя файла логгера, который нужно создать/вернуть
     * @return Logger возвращает объект класса Logger
     */
    public static function getLog($name = 'root', $file = null) {
        if (!isset(self::$loggers[$name])) {
            self::$loggers[$name] = new Logger($name, $file);
        }
        return self::$loggers[$name];
    }
    
    
    /**
     * Метод заносит в лог файл сообщение, переданное в качестве аргумента
     * @param type $message
     * @return type
     */
    public function log($message) {
        
        if (!is_string($message)) {
            // если мы хотим вывести, к примеру, массив
            $this->logPrint($message);
            return;
        }
        
        $log = '';
        $log .= "\r\n"; // Добавим перенос строки для виндовс (смотреть в блокноте)
        
        // зафиксируем дату и время происходящего
        $currentDate = new DateTime(NULL, new DateTimeZone($this->timezone));
        $currentDate = $currentDate->format("D M d H:i:s Y");
        $log .= "[" . $currentDate . "] ";
        
        // если мы отправили в функцию больше одного параметра, выведем их тоже
        if (func_num_args() > 1) {
            $params = func_get_args();
            $message = call_user_func_array('sprintf', $params);
        }
        $log .= $message;
        
        // запись в файл
        $this->_write($log);
    }
    
    /**
     * 
     * @param type $obj
     */
    public function logPrint($obj) {
        // заносим все выводимые данные в буфер
        ob_start();
        print_r($obj);
        
        // очищаем буфер
        $ob = ob_get_clean();
        
        // записываем
        $this->log($ob);
    }
    
    
    /**
     * Метод осуществляет непосредственную запись в файл лоигруемой строки
     * @param type $string 
     */
    protected function _write($string) {
        fwrite($this->fp, $string);
    }
    
    
} // class CPSS_Logger

?>