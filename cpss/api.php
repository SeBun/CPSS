<?php
/**
 * CONTROL AND PROTECTION OF THE SITE SYSTEM (CPSS)
 *
 * @copyright  Copyright (C) 2016 Sergey Bunin. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 *
 * APPLICATION PROGRAMMING INTERFACE (API)
 */

/**
 * Объявляем константы
 */
define('_CPSS', 1);           // константа для проверки прямого доступа к файлам 
define('_START',microtime()); // счетчик времени выполнения

 
/**
 * Проверяем на совместимость с используемой версией PHP.
 */
if (version_compare(PHP_VERSION, '5.3.10', '<'))
{
	die('Your host needs to use PHP 5.3.10 or higher to run this site!');
}


/**
 * Подключаем конфигурацию системы
 */
if (file_exists(__DIR__ . '/includes/config.php'))
{
	include_once __DIR__ . '/includes/config.php';
}

if (!class_exists('CPSS_Config', false)) {
	die('Unable to run this site!');
}



