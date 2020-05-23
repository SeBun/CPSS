<?php
/**
 * CONTROL AND PROTECTION OF THE SITE SYSTEM (CPSS)
 *
 * @copyright  Copyright (C) 2016 Sergey Bunin. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 *
 * Содержит класс, выводящий различные сообщения. Имеет как готовые пресеты, так и
 * функцию вывода подготовленного сообщения.
 */

defined('_CPSS') or die;

class CPSS_Message {
	
	
	/**
	 * Вывод ошибки 500 (Internal Server Error) с последующим завершением работы
	 */
	public static function msg_error_500 () {
		
            ob_start();
            header('HTTP/1.0 500 Internal Server Error');
            ob_end_flush();
            exit;
	
	}
        
        
        /**
	 * Вывод ошибки 502 (Bad Gateway) с последующим завершением работы
	 */
	public static function msg_error_502() 
        {
		
            ob_start();
            header('HTTP/1.0 502 Bad Gateway');
            echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
            echo '<html xmlns="http://www.w3.org/1999/xhtml">';
            echo '<head>';
            echo '<title>502 Bad Gateway</title>';
            echo '<meta http-equiv="content-type" content="text/html; charset=utf-8" />';
            echo '</head>';
            echo '<body>';
            echo '<h1 style="text-align:center">502 Bad Gateway</h1>';
            echo '<p style="background:#ccc;border:solid 1px #aaa;margin:30px au-to;padding:20px;text-align:center;width:700px">';
            echo 'К сожалению, Вы временно заблокированы, из-за частого запроса страниц сайта.<br />';
            echo 'Вам придется подождать. Через ' . $time_block . ' секунд(ы) Вы будете автоматически разблокированы.';
            echo '</p>';
            echo '</body>';
            echo '</html>';
            exit;
            ob_end_flush();
		exit;
	
	}
	
	
	/**
	 * Вывод сообщения об ошибке 503 с последующим завершением работы
	 */
	public static function msg_error_503 () {
		
		ob_start();
		header('HTTP/1.1 503 Service Temporarily Unavailable');
		header('Status: 503 Service Temporarily Unavailable');
		header('Retry-After: 3600');
		header('X-Powered-By:');
		echo<<<EOF
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>503 Service Temporarily Unavailable</title>
</head><body>
<h1>Service Temporarily Unavailable</h1>
<p>The server is temporarily unable to service your request due to maintenance downtime or capacity problems. Please try again later.</p>
</body></html>
EOF;
		ob_end_flush();
		exit;
	
	}
	
	
	/**
	 * Вывод собственного сообщения об ошибке с последующим завершением работы
	 * @var string 
	*/
	public function msg_peculiar($message)
	{
		if (empty($message)) {
			$this->msg_error_500; // сообщение не задано, выдаем ошибку 500 (Internal Server Error)
			exit;
		}
		
		ob_start();
		header('HTTP/1.1 500 Internal Server Error');
		header('Status: 500 Internal Server Error');
		header('Retry-After: 3600');
		header('Pragma: no-cache');
		echo<<<EOF
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>ERROR</title>
</head><body>
<h1>ERROR</h1>
<p>The server is temporarily unable to service your request due to maintenance downtime or capacity problems. Please try again later.</p>
</body></html>
EOF;
		ob_end_flush();
		exit;
	}
}


?>