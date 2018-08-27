<?php
/**
 * CONTROL AND PROTECTION OF THE SITE SYSTEM (CPSS)
 *
 * @copyright  Copyright (C) 2016 Sergey Bunin. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 * 
 * REPORT - класс уведомлений о событиях. Выполняет отправку сообщений на e-mail.
 */
 
defined('_CPSS') or die;
 

/**
 * В целях контроля количества отправляемых сообщений в сутки данный класс имеет свою 
 * базу данных, в которой хранит отправляемые сообщения. Благодаря этому можно не только
 * получить количество отправленных сообщений на любую дату, но и просматривать их, например,
 * с помощью внешнего модуля, работающего через API.
 * 
 * Каждое сообщение, подготовленное для отправки, записывается в базу данных. Отправка сообщения
 * ограничивается двумя параметрами - интервалом и общим количеством сообщений в сутки.
 */
class CPSS_Report
{
    /**
     * Почтовый адрес получателя.
     *
     * Если не задан, то заполняется из файла конфигурации. Можно указать несколько адресов через запятую.
     * @var string
     */
    public $to = '';
    
    
    /**
     * Заголовок письма.
     *
     * Если не задан, то заполняется из файла конфигурации.
     * @var string
     */
    public $subject = '';
    
    
    /**
     * Тело письма.
     *
     * Если не задано, письмо не может быть отправлено. Устанавливается обычно методами других классов,
     * информирующих о натуплении каких либо событий. Но может заполняться и при использовании готовых
     * шаблонов.
     * @var string
     */
    public $message = '';
    
    
 
    /**
     * Выполняет отправку сообщения.
     * 
     * Перед вызовом данного метода должны быть предварительно установлены свойства 
     * to, subject и message.
     * 
     * Отправка сообщения возможно только при условии соблюдения интервала между 
     * сообщениями и дневного лимита. Если сообщение сразу отправить не удалось,
     * оно записывается в базу и становится в очередь на отправку. Обслуживание
     * очереди сообщений выполняется по крону отдельным модулем.
     */
    public function Send ()
    {
        
	
	
        /* Compose email */
	$subject = "WP_WAF - $blog_name";
	$body    = "== Attack Details ==\n\n";
	$body   .= "TYPE: $attack_type\n";
	$body   .= "MATCHED: \"$matched\"\n";
	$body   .= "ACTION: Blocked\n";
	$body   .= "$log";
	
        /* Send email */
	if((isset($_POST['name'])&&$_POST['name']!="")&&(isset($_POST['phone'])&&$_POST['phone']!=""))
        { 
            //Проверка отправилось ли наше поля name и не пустые ли они
        }
        
        $to = 'mail@yandex.ru'; //Почта получателя, через запятую можно указать сколько угодно адресов
        $subject = 'Обратный звонок'; //Загаловок сообщения
        $message = '
                <html>
                    <head>
                        <title>'.$subject.'</title>
                    </head>
                    <body>
                        <p>Имя: '.$_POST['name'].'</p>
                        <p>Телефон: '.$_POST['phone'].'</p>                        
                    </body>
                </html>'; //Текст нащего сообщения можно использовать HTML теги
        $headers  = "Content-type: text/html; charset=utf-8 \r\n"; //Кодировка письма
        $headers .= "From: Отправитель <from@example.com>\r\n"; //Наименование и почта отправителя
        mail($to, $subject, $message, $headers); //Отправка письма с помощью функции mail

    } // end function send()
    
    /**
     * Пытается использовать PHP встроенный валидатор в расширении фильтра (с PHP 5.2), 
     * возвращается к достаточно компетентным регулярное выражение валидатора
     * Соответствует примерно RFC2822
     * 
     * @link http://www.hexillion.com/samples/#Regex Original pattern found here
     * @param string $address The email address to check
     * @return boolean
     * @static
     * @access public
     */
    
    public static function ValidateAddress($address) {
        if (function_exists('filter_var')) { //Introduced in PHP 5.2
            if(filter_var($address, FILTER_VALIDATE_EMAIL) === FALSE) {
                return false;
            } else {
                return true;
            }
        } else {
            return preg_match('/^(?:[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+\.)*[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+@(?:(?:(?:[a-zA-Z0-9_](?:[a-zA-Z0-9_\-](?!\.)){0,61}[a-zA-Z0-9_-]?\.)+[a-zA-Z0-9_](?:[a-zA-Z0-9_\-](?!$)){0,61}[a-zA-Z0-9_]?)|(?:\[(?:(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\.){3}(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\]))$/', $address);
        }
    }

	
} // class CPSS_Report

?>