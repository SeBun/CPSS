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
 * КОМПЛЕКСНАЯ СИСТЕМА ОБЕСПЕЧЕНИЯ БЕЗОПАСНОСТИ САЙТОВ
 * 
 */


define('_CPSS', 1);            // константа для проверки прямого доступа к файлам 
define('_START', microtime()); // время запуска (для счетчика времени выполнения)
define('PATH_BASE', __DIR__);  // текущая директория системы


/**
 * Инициализация CPSS.
 * Выполняется подключение файла конфигурации и библиотек классов.
 * 
 * @todo Возможно, здесь при возникновении ошибки следует выводить другое сообщение или незаметно завершать работу,
 * @todo информируя только админа?
 */
if (file_exists(PATH_BASE . '/inc/init.php')) {
    include_once PATH_BASE . '/inc/init.php';
}
else {
    die ('CPSS is damaged!');
}


/**
 * Объект ядра
 */
$cpss = new CPSS_Helper();


/**
 * Отключаем сайт, если указано в настройках конфигурации
 */
$cpss->offSite();


/**
 * Принудительная пауза, если указано в настройках конфигурации
 */
$cpss->pullSite();


/**
 * Проверка обращения к запрещенным файлам или каталогам.
 */
$cpss->filesMatch();
include PATH_BASE . '/ips/ipscaptcha.php';
die(' Good');


/**
 * Фиксируем обращение к несуществующим страницам с IP.
 * При частых запросах несуществующих страниц IP блокируем.
 */

/**
 * Выполняем проверку IP пользователя.
 * Проверка проходит в несколько этапов. В начале проверяется наличие адреса в базе заблокированных IP.
 * Следующий этап проверки - обнаружение прокси-сервера. Если обнаружено подключение
 * через прокси, выполняется задержка выполнения скрипта или прерывание работы. Следующий шаг - проверка
 * адреса на наличие в черных списках DNSBL-систем. Если IP прошел проверку, проверяется USER-AGENT. На
 * последнем этапе проверяется геолокация. Любой из этапов проверки настраивается в файле конфигурации.
 */
if ($config->ips_on) {
    
    //Подключаем класс IPS (Internet Protocol Secure)
    if (!file_exists(PATH_BASE . '/ips/ips.php'))
    {
	die('Unable to run this site!  No such file or directory: ips.php. Exiting...');
    }

    ob_start();
    require_once PATH_BASE . '/ips/ips.php';
    ob_end_clean();
    
    // Создаем объект класса и запускаем проверку IP
    $ips = new CPSS_IPSecure();
    
    // Время задержки выполнения
    if (isset($config->ips_sleep)) {
        $ips->sleep = $config->ips_sleep;
    }

    // Проверка портов прокси-сервера
    if (isset($config->ips_checkport)) {
        $ips->checkport = $config->ips_checkport;
    }
    
    // Вариант действия при обнаружении - выводить капчу вместо паузы
    if (isset($config->ips_captcha)) {
        $ips->captcha = $config->ips_captcha;
    }
    
    // Запускаем проверку IP и выполняем действие при обнаружении подозрительных действий
    if ($ips->detect()) {
        $ips->action();
    }
    
    // Выполняем проверку наличия IP-адреса в черных списках DNSBL хостов.
    if ($config->ips_dnsblon == TRUE) {
        $ips->dnsbl = $config->ips_dnsbl; // подгружаем список DNSBL-хостов    
    }
    
    // Проверяем USER-AGENT
    if ($config->ips_useragent_on == TRUE){
        
        //Подключаем класс CPSS_UAgent
        if (!file_exists(PATH_BASE . '/ips/uagent.php'))
        {
            die('Unable to run this site!  No such file or directory: uagent.php. Exiting...');
        }

        ob_start();
        require_once PATH_BASE . '/ips/uagent.php';
        ob_end_clean();
        
        $uagent = new CPSS_UAgent(); // расширение для класса CPSS_IPSecure().
        
        // запускаем детект
        $uagent->uaDetect();
    }
    
    // Система анти-ДДОС
    if ($config->ips_antiddos_on == TRUE){
        
        //Подключаем класс CPSS_UAgent
        if (!file_exists(PATH_BASE . '/ips/antiddos.php'))
        {
            die('Unable to run this site!  No such file or directory: antiddos.php. Exiting...');
        }

        ob_start();
        require_once PATH_BASE . '/ips/antiddos.php';
        ob_end_clean();
        
        $antiddos = new CPSS_antiDDOS();
        
        // запускаем анти-ДДОС
        //$antiddos->uaDetect();
    }

    // Параметры блокировки стран
    if (isset($config->ips_geocontrol) and $config->ips_geocontrol == TRUE) {
        if (isset($config->ips_geocountry) and is_array($config->ips_geocountry ) and !empty($config->ips_geocountry)) {
            $ips->geocontrol = $config->ips_geocontrol;
            $ips->geocountry = $config->ips_geocountry;
            $ips->detectGeo(); // блокировка стран
        }
        
        else {
            $ips->geocontrol = false;
        }
    }
}





/**
 * В зависимости от типа запроса выполняем проверку WAF
 */
if ($config->waf_on) {
    //$filter = new CPSS_Filter(); // объект фильтра
    //$result = $filter->Run();    // получаем результат проверки запроса    
}

unset($config);

$time_request = round(microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"], 4); // время с момента получения запроса
$time = microtime() - _START;

//echo "request: $time_request<br/>\r\n";
//echo('time:'.$time);