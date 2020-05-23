<?php
/**
 * CONTROL AND PROTECTION OF THE SITE SYSTEM (CPSS)
 *
 *    ______    ____    _____   _____           __     _    __       
 *   / ____/   / __ \  / ___/  / ___/          / /    (_)  / /_  ___ 
 *  / /       / /_/ /  \__ \   \__ \          / /    / /  / __/ / _ \
 * / /___    / ____/  ___/ /  ___/ /         / /___ / /  / /_  /  __/
 * \____/   /_/      /____/  /____/         /_____//_/   \__/  \___/ 
 * 
 * @copyright  Copyright (C) 2016 Sergey Bunin. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 * 
 * Конфигурация CPSS
 */

class CPSS_Config {
	
    /* Управление сайтом */
    
    public $offline = 0;        // Если этот параметр установить в TRUE, доступ к сайту будет отключен.
    public $pullon = 0;         // Включение принудительной паузы перед запуском системы.
    public $pulltime = 1;       // Длительность принудительной паузы в секундах, но не более 25.
    public $operating_mode = 0; // Боевой режим. Если FALSE, то система не блокирует адреса.
    public $pullurl = array();  // Массив адресов, где не применяется принудительная пауза.


    /* Настройка уведомлений */
        
    public $mail_from = 'admin@site.ru'; // Адрес, с которого отправляются уведомления (отправитель)
    public $mail_to = 'admin@mysite.ru'; // Адрес получателя письма
    public $mail_send = false; // Включить/отключить отправку сообщений на почту
    public $mail_period = 10;  // Определяет максимальное количество писем в час
	
    /* Настройка логирования */
        
    public $log_get = 0;       // логирование GET-запросов
    public $log_post = 0;      // логирование POST-запросов
    public $log_cookies = 0;   // логирование COOKIES
    public $log_path = 'logs'; // Путь к папке логов относительно корня
    public $log_timezone = 'Europe/Moscow'; // Временная зона. Список зон временных зон IANA: http://www.iana.org/time-zones
    public $log_maxsize = ''; // Максимальный размер файла лога. При превышении этого порога будет создан новый файл.
	
    /* Настройка WAF */
        
    public $waf_on = 0;      // Включение или отключение фильтра WAF.
    public $waf_viewerr = 0; // Отображать ключ массива паттернов, на котором сработал фильтр. Используется для отладки.
    public $waf_msgerr  = "Request denied."; // Сообщение, выводимое при срабатывании фильтра.
    public $waf_hidemsg = 0; // Определяет метод показа сообщений. Если TRUE, то вместо сообщений будет показана ошибка 503 (Service Temporarily Unavailable).

    /* Настройка IPS */
        
    public $ips_on = 0;    // Включение/выключение проверки IP пользователя. TRUE - проверка включена.
    public $ips_sleep = 5; // Время (сек.) задержки выполнения скрипта при обнаружении прокси. От 0 до 30.
    public $ips_checkport = 1;       // Включить или выключить проверку популярных для прокси портов (может замедлить работу).
    public $ips_captcha = 0;         // Включить или выключить страницу ввода капчи. Если включено, при обнаружении вместо паузы будет выводится страничка с капчой.
    public $ips_useragent_on = 1;    // Включить или выключить проверку USER-AGENT
    public $ips_antiddos_on = 0;     // Включить или выключить систему анти-DDOS.
    public $ips_geocontrol = 0;      // Включить блокировку стран по IP
    public $ips_geocountry = array(  // Массив разрешенных стран
        '', 'RU', 'UA'
    );
    public $ips_dnsblon = 0;    // Включить проверку DNSBL
    public $ips_dnsbl = array(  // Список серверов, на которых проходит проверка
        'b.barracudacentral.org', 
        'xbl.spamhaus.org', 
        'zen.spamhaus.org',
        'cbl.spamhaus.org',
        'pbl.spamhaus.org',
        'sbl.spamhaus.org'
    );


    /* SCF configuration */
        
    public $scf_files = '(\.php.?|\.htaccess|\.txt)$'; // files pattern
    public $scf_log = true; // write logs to ./logs/Ym/d-m-y.log
	
}


