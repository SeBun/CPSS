# CPSS (CONTROL AND PROTECTION OF THE SITE SYSTEM)

## О программе
CPSS является комплексной системой обеспечения безопасности сайтов, которая может работать независимо от сайта и выполнять ряд задач по предотвращению взлома, обнаружению взлома, контролю файловой структуры сайта и многое другое. 

Система может устанавливаться в любой каталог сайта и, после выполнения простых настроек готова к работе.

## Установка
Установка системы на сайт производиться путем распаковки папки cpss в любой каталог сайта. Рекомендуется размещать папку cpss за пределами корневой папки сайта. 

После того, как система распакована, ее нужно подключить. Сделать это лучше всего в файле **php.ini** с помощью директивы *php_value auto_prepend_file*, указав путь к исполняемому файлу *cpss/cpss.php*.

На многих хостингах для пользователей доступ к *php.ini* закрыт. В этом случае подключить систему можно с помощью файла **.htaccess**, задав в нем директиву *php_value auto_prepend_file*.

**Пример:**
```ini
php_value auto_prepend_file "/cpss/cpss.php"
```

## Настройка
Настройка системы выполняется в файле конфигурации *cpss/config.php*.
