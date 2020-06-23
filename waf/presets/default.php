<?php
/**
 * CONTROL AND PROTECTION OF THE SITE SYSTEM (CPSS)
 *
 * @copyright  Copyright (C) 2016 Sergey Bunin. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 *
 * Пресет по умолчанию для метода блокировки доступа к запрещенным файлам и папкам.
 *
 */

defined('_CPSS') or die;

// Массив файлов, директорий и расширений, доступ к которым запрещен.
// * - любое содержимое.

$filesMatch = array(
    '*.htaccess',
    '*.htpasswd',
    '*.ini',
    '*.phps',
    '*.fla',
    '*.log',
    '*.sh',
    '*/readme.txt'
);