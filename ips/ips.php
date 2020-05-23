<?php
/**
 * CONTROL AND PROTECTION OF THE SITE SYSTEM (CPSS)
 *
 * @author     Сергей Бунин
 * @copyright  Copyright (C) 2016 Sergey Bunin. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 *
 * IPS (Internet Protocol Secure) - защита сайта посредством фильтрации IP-адресов.
 * Защищает сайт от ботов, брута, сканирования и прочих действий, которые могут нанести вред
 * или замедлить работу путем создания неоправданно высокой нагрузки на ресурсы сервера.
 */
 

defined('_CPSS') or die;


class CPSS_IPSecure {
    
    /**
     * Текущий IP-адрес клиента. Устанавливается при первом вызове метода получения IP _getIp().
     * Впоследствии IP адрес больше не расчитывается, а берется из этой переменной.
     * @var string
     */
    protected $ip = NULL;
    
    /**
     * Свойство содержит время задержки выполнения скрипта при обнаружении проксти. По умолчанию 0.
     * @var integer 
     */
    public $sleep = 0; 
    
    /**
     * Вариант реакции на обнаружение подозрительного IP. Если установлено в TRUE, то вместо паузы
     * клиенту будет показана страница с капчой, которую необходимо будет ввести.
     * @var boolean
     */
    public $captcha = 0;
    
    /**
     * Свойство определяет, следует ли выполнять дополнительную проверку путем прозвона порта возможного прокси-сервера.
     * @var boolean
     */
    public $checkport = 0;


    /**
     * Свойство содержит значение true или false, определяющее включение блокировки IP по странам.
     * @var boolean
     */
    public $geocontrol = 0;
    
    /**
     * Свойство содержит массив обозначений стран.
     * @var array Массив стран (двухсимвольное обозначение)
     */
    public $geocountry = array();
    
    /**
     * Список DNSBL хостов для проверки IP
     * @var array
     */
    public $dnsbl = array();

    
    
    /**
     * Конструктор класса загружает данные из файла конфигурации.
     */
    function __construct()
    {
        // Проверка диапазона времени задержки выполнения (не более 30 сек.)
        if ($this->sleep <= 0) $this->sleep = 0;
        if ($this->sleep > 30) $this->sleep = 30;
    }
    
    
    
    /**
     * Метод получения текущего ip-адреса из переменных сервера.
     */
    private function _getIp() {

        // Если адрес уже был получен ранее, он возвращается
        if (empty($this->ip)) {
            return $this->ip;
        }
        
        // Присваиваем ip-адрес по умолчанию (localhost)
        $ip_address = '127.0.0.1';

        // Массив возможных ip-адресов
        $addrs = array();

        // Сбор данных возможных ip-адресов
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            
            // Проверяется массив ip-клиента установленных прозрачными прокси-серверами
            foreach (array_reverse(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])) as $value) {
                $value = trim($value);
                // Собирается ip-клиента
                if (preg_match('#^\d{1,3}.\d{1,3}.\d{1,3}.\d{1,3}$#', $value)) {
                    $addrs[] = $value;
                }
            }
        }

        // Собирается ip-клиента
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $addrs[] = $_SERVER['HTTP_CLIENT_IP'];
        }
        // Собирается ip-клиента
        if (isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])) {
            $addrs[] = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
        }
        // Собирается ip-клиента
        if (isset($_SERVER['HTTP_PROXY_USER'])) {
            $addrs[] = $_SERVER['HTTP_PROXY_USER'];
        }
        // Собирается ip-клиента
        if (isset($_SERVER['REMOTE_ADDR'])) {
            $addrs[] = $_SERVER['REMOTE_ADDR'];
        }

        // Фильтрация возможных ip-адресов, для выявление нужного
        foreach ($addrs as $value) {
            
            // Выбирается ip-клиента
            if (preg_match('#^(\d{1,3}).(\d{1,3}).(\d{1,3}).(\d{1,3})$#', $value, $matches)) {
                $value = $matches[1] . '.' . $matches[2] . '.' . $matches[3] . '.' . $matches[4];
                if ('...' != $value) {
                    $ip_address = $value;
                    break;
                }
            }
        }

        // Запись и возврат полученного ip-адреса
        $this->ip = $ip_address;
        return $ip_address;
    }
    

    /**
     * Если посетитель пришел с прокси-сервера, то в переменной $_SERVER['HTTP_VIA']
     * хранится имя, версия программного обеспечения и номер порта.
     * 
     * @return boolean Возвращает TRUE, если есть признак принадлежности к прокси.
     */
    private function _detectVIA() 
    {
        
        if (!empty($_SERVER['HTTP_VIA'])) {
          return true;
        }
        
        return FALSE; //строка пуста.
    
    }
    
    
    /**
     * Проверяет IP на наличие в черных списках DNSBL-серверов.
     * Список серверов задается в файле конфигурации системы.
     */
    private function _checkDNSBL() {
        
        if(!is_array($this->dnsbl) || !count($this->dnsbl)) {
            return FALSE; // нет списков DNSBL-серверов
        }
        
        // Подготавливаем массив для результатов проверки
        $result = array('dnsbl_hosts' => array(), 'inblack' => 0);
        
        if (self::_getIp() == '127.0.0.1' || empty(self::_getIp())) {
            return FALSE; // отключить для localhost
        }
        
        $reverse_ip = implode(".", array_reverse(explode(".", self::_getIp())));
        
        foreach($this->dnsbl as $dnsbl_host) {
            $is_listed = checkdnsrr($reverse_ip.".".$dnsbl_host.".", "A") ? 1 : 0;
            $result['dnsbl_hosts'][$dnsbl_host] = $is_listed;
            
            if($is_listed) {
                $result['inblack']++;
            }                
        }
        
        return $result;
    }
    
    public function checkBL($param)
    {
        
    }

    
    
    /**
     * Мы можем попробовать подключиться к этому ip адресу как к прокси (на часто используемые для прокси порты).
     * Если соединение установлено, значит за этим ip адресом прокси сервер, если нет, то скорее всего это настоящий
     * клиент (или у этого прокси сервера не популярный порт для подключения).
     * 
     * Основными портами открытых прокси-серверов являются следующие:
     *   80
     *   81
     *   8000
     *   8080 (т. н. HTTP CONNECT-прокси)
     *   1080 (SOCKS-прокси)
     *   3128 (стандартный порт для squid, WinGate, WinRoute и многих других)
     *   6588 (AnalogX)
     * 
     * @return boolean Возвращает TRUE, если есть признак принадлежности к прокси.
     */
    private function _detectPort()
    {
        $ports = array(8080,80,81,1080,6588,8000,3128,553,554,4480);
        
        if (self::_getIp() == '127.0.0.1') {
            return FALSE; // отключить для localhost
        }
        
        foreach($ports as $port) {
            if (@fsockopen(self::_getIp(), $port, $errno, $errstr, 5)) {
                return true;
            } 
        }
    }
    
    
    
    /**
     * Определение местоположения по IP-адресу, от создателей Sypex Dumper.
     * Проводится блокировка доступа к сайту для заданных стран.
     * https://sypexgeo.net
     */
    public function detectGeo()
    {
        include_once(PATH_BASE . '/ips/SxGeo.php'); // класс SxGeo
        
        $SxGeo = new SxGeo(PATH_BASE . '/ips/SxGeo.dat');
        
        $ip = self::_getIp(); // определение IP
        
        if ($ip == '127.0.0.1') {
            return FALSE; // отключить для localhost
        }
                
        $ip_country = $SxGeo->getCountry($ip); //возвращает двухзначный ISO-код страны
        unset($SxGeo);
         
        foreach($this->geocountry as $country) {
            if($country == $ip_country) { die('Доступ запрещен!'); }
        }
        
    } // End function detectGeo()

    
    
    /**
     * Запуск процесса обнаружения PROXY.
     * @return boolean Если обнаружено, что пользователь зашел с прокси-сервера, возвращается TRUE.
     */
    public function detect() 
    {
        if (self::_detectVIA()) {
            return TRUE; // обнаружен признак прокси (HTTP_VIA заполнено).
        }
        
        // Если разрешена проверка порта, выполнить дополнительную проверку
        if ($this->checkport) {
            if (self::_detectPort()) {
                return TRUE; // обнаружен признак прокси (получен ответ от прокси).
            }
        }
        
        return FALSE;
        
    } // End function detect() 
    
    
    /**
     * Выполнение действия при обнаружении входа через прокси.
     * 
     * @todo Пока это лишь банальная задержка времени выполнения скрипта.
     * @todo Здесь лучше выводить капчу по аналогии с популярными механизмами защиты.
     */
    public function action()
    {
        if ($this->captcha == FALSE) {
            sleep($this->sleep); // пауза
        }
        
        else {
            // Выдаем страницу для ввода капчи
            //include('ipscaptcha.php');
        }
       
    } // End function action()
    
} // End class

?>