<?php
/**
 * CONTROL AND PROTECTION OF THE SITE SYSTEM (CPSS)
 *
 * @copyright  Copyright (C) 2016 Sergey Bunin. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 *
 * Инициализация CPSS.
 * Выполняется подключение необходимых библиотек и создание рабочей среды.
 * 
 * @todo Пользователь не должен видеть названия файлов, ему достаточно видеть ошибку, а детали ошибки должны записываться в лог-файл.
 * @todo Поскольку первым подключается файл конфигурации, можно в нем указывать, следует ли отображать детали работы системы, например, на период отладки.
 */

defined('_CPSS') or die;

 
/**
 * Подключаем конфигурацию системы.
 */
if (!file_exists(PATH_BASE . '/config.php'))
{
	die('CPSS configuration file not found. Exiting...');
}

ob_start();
require_once PATH_BASE . '/config.php';
ob_end_clean();



/**
 * Подключаем класс системных сообщений. Данный класс содержит готовые пресеты сообщений,
 * показываемых клиенту, а так же функцию, выводящую сообщение с произвольным текстом по заданному шаблону.
 */
if (!file_exists(PATH_BASE . '/inc/messages.php'))
{
	die('Unable to run this site! No such file or directory: messages.php. Exiting...');
}

ob_start();
require_once PATH_BASE . '/inc/messages.php';
ob_end_clean();




/**
 * Подключаем класс уведомлений.
 */
if (!file_exists(PATH_BASE . '/inc/report.php'))
{
	die('Unable to run this site! No such file or directory: report.php. Exiting...');
}

ob_start();
require_once PATH_BASE . '/inc/report.php';
ob_end_clean();


/**
 * Подключаем класс журналирования событий.
 */
if (!file_exists(PATH_BASE . '/inc/logger.php'))
{
	die('Unable to run this site!  No such file or directory: logger.php. Exiting...');
}

ob_start();
require_once PATH_BASE . '/inc/logger.php';
ob_end_clean();


?>