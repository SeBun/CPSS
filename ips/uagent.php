<?php

/**
 * CONTROL AND PROTECTION OF THE SITE SYSTEM (CPSS)
 *
 * @author     Сергей Бунин
 * @copyright  Copyright (C) 2016 Sergey Bunin. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 *
 * IPS (Internet Protocol Secure) - защита сайта посредством фильтрации IP-адресов.
 * 
 * UAGENT - фильтрация запросов клиента по USER_AGENT.
 * 
 * Известно, что USER_AGENT легко подделать. Тем не менее некоторые боты специально прописывают
 * себя в USER_AGENT для идентификации своих копий. Кроме того, если данные USER_AGENT сохраняются
 * в базу данных, то может быть произведен взлом сайта путем добавления SQL-конструкций в USER_AGENT.
 * 
 * В данном файле находится расширение класса CPSS_IPSecure, содержащий методы фильтрации USER_AGENT.
 * Эти методы определяющт нежелательные подключения и блокируют их. Кроме того, в данном классе предусмотрена
 * возможность перезаписи массива $_SERVER['HTTP_USER_AGENT'] безопасными данными во избежании внедрения в них
 * конструкций, позволяющих получить управление базой данных.
 */
 

defined('_CPSS') or die;


class CPSS_UAgent extends CPSS_IPSecure
{
    
    /**
     * Массив паттернов, участвующих в работе фильтра (список нежелательных элементов)
     * @var array 
     */
    private $uapatterns = Array();

    /**
     * Конструктор формирует массив паттернов
     * Для удобства поиска все паттерны расположены в алфавитном порядке
     */
    function __construct()
    {
        $this->uapatterns[0] = 'almaden';
        $this->uapatterns[1] = '^Anarchie';
        $this->uapatterns[2] = '^ASPSeek';
        $this->uapatterns[3] = '^attach';
        $this->uapatterns[4] = '^autoemailspider';
        $this->uapatterns[5] = '^BackWeb';
        $this->uapatterns[6] = '^Bandit';
        $this->uapatterns[7] = '^BatchFTP';
        $this->uapatterns[8] = '^BlackWidow';
        $this->uapatterns[9] = '^Bot\ mailto:craftbot@yahoo.com';
        $this->uapatterns[10] = '^Buddy';
        $this->uapatterns[11] = '^bumblebee';
        $this->uapatterns[12] = '^CherryPicker';
        $this->uapatterns[13] = '^ChinaClaw';
        $this->uapatterns[14] = '^CICC';
        $this->uapatterns[15] = '^Collector';
        $this->uapatterns[16] = '^Copier';
        $this->uapatterns[17] = '^Crescent';
        $this->uapatterns[18] = '^Custo';
        $this->uapatterns[19] = '^DA';
        $this->uapatterns[20] = '^DIIbot';
        $this->uapatterns[21] = '^DISCo';
        $this->uapatterns[22] = '^DISCo\ Pump';
        $this->uapatterns[23] = '^Download\ Demon';
        $this->uapatterns[24] = '^Download\ Wonder';
        $this->uapatterns[25] = '^Downloader';
        $this->uapatterns[26] = '^Drip';
        $this->uapatterns[27] = '^DSurf15a';
        $this->uapatterns[28] = '^eCatch';
        //$this->uapatterns[29] = '^EasyDL/2.99'; // Неверно
        $this->uapatterns[30] = '^EirGrabber';
        //$this->uapatterns[31] = 'email'; // без учета регистра
        $this->uapatterns[32] = '^EmailCollector';
        $this->uapatterns[33] = '^EmailSiphon';
        $this->uapatterns[34] = '^EmailWolf';
        $this->uapatterns[35] = '^Express\ WebPictures';
        $this->uapatterns[36] = '^ExtractorPro';
        $this->uapatterns[37] = '^EyeNetIE';
        $this->uapatterns[38] = '^FileHound';
        $this->uapatterns[39] = '^FlashGet';
        $this->uapatterns[40] = 'FrontPage'; // без учета регистра
        $this->uapatterns[41] = '^GetRight';
        $this->uapatterns[42] = '^GetSmart';
        $this->uapatterns[43] = '^GetWeb!';
        $this->uapatterns[44] = '^gigabaz';
        $this->uapatterns[45] = '^Go\!Zilla';
        $this->uapatterns[46] = '^Go!Zilla';
        $this->uapatterns[47] = '^Go-Ahead-Got-It';
        $this->uapatterns[48] = '^gotit';
        $this->uapatterns[49] = '^Grabber';
        $this->uapatterns[50] = '^GrabNet';
        $this->uapatterns[51] = '^Grafula';
        $this->uapatterns[52] = '^grub-client';
        $this->uapatterns[53] = '^HMView';
        $this->uapatterns[54] = '^HTTrack';
        $this->uapatterns[55] = '^httpdown';
        $this->uapatterns[56] = '.*httrack.*'; // без учета регистра
        $this->uapatterns[57] = '^ia_archiver';
        $this->uapatterns[58] = '^Image\ Stripper';
        $this->uapatterns[59] = '^Image\ Sucker';
        $this->uapatterns[60] = '^Indy*Library';
        $this->uapatterns[61] = 'Indy\ Library'; // без учета регистра
        $this->uapatterns[62] = '^InterGET';
        $this->uapatterns[63] = '^InternetLinkagent';
        $this->uapatterns[64] = '^Internet\ Ninja';
        $this->uapatterns[65] = '^InternetSeer.com';
        $this->uapatterns[66] = '^Iria';
        $this->uapatterns[67] = '^JBH*agent';
        $this->uapatterns[68] = '^JetCar';
        $this->uapatterns[69] = '^JOC\ Web\ Spider';
        $this->uapatterns[70] = '^JustView';
        $this->uapatterns[71] = '^larbin';
        $this->uapatterns[72] = '^LeechFTP';
        $this->uapatterns[73] = '^LexiBot';
        $this->uapatterns[74] = '^lftp';
        $this->uapatterns[75] = '^Link*Sleuth';
        $this->uapatterns[76] = '^likse';
        $this->uapatterns[77] = '^Link';
        $this->uapatterns[78] = '^LinkWalker';
        $this->uapatterns[79] = '^Mag-Net';
        $this->uapatterns[80] = '^Magnet';
        $this->uapatterns[81] = '^Mass\ Downloader';
        $this->uapatterns[82] = '^Memo';
        $this->uapatterns[83] = '^Microsoft.URL';
        $this->uapatterns[84] = '^MIDown\ tool';
        $this->uapatterns[85] = '^Mirror';
        $this->uapatterns[86] = '^Mister\ PiX';
        $this->uapatterns[87] = '^Mozilla.*Indy';
        $this->uapatterns[88] = '^Mozilla.*NEWT';
        $this->uapatterns[89] = '^Mozilla*MSIECrawler';
        $this->uapatterns[90] = '^MS\ FrontPage*';
        $this->uapatterns[91] = '^MSFrontPage';
        $this->uapatterns[92] = '^MSIECrawler';
        $this->uapatterns[93] = '^MSProxy';
        $this->uapatterns[94] = '^Navroad';
        $this->uapatterns[95] = '^NearSite';
        $this->uapatterns[96] = '^NetAnts';
        $this->uapatterns[97] = '^NetMechanic';
        $this->uapatterns[98] = '^NetSpider';
        $this->uapatterns[99] = '^Net\ Vampire';
        $this->uapatterns[100] = '^NetZIP';
        $this->uapatterns[101] = '^NICErsPRO';
        $this->uapatterns[102] = '^Ninja';
        $this->uapatterns[103] = '^Octopus';
        $this->uapatterns[104] = '^Offline\ Explorer';
        $this->uapatterns[105] = '^Offline\ Navigator';
        $this->uapatterns[106] = '^Openfind';
        $this->uapatterns[107] = '^PageGrabber';
        $this->uapatterns[108] = '^Papa\ Foto';
        $this->uapatterns[109] = '^pavuk';
        $this->uapatterns[110] = '^pcBrowser';
        //$this->uapatterns[111] = '^[anchor=http://likbezz.ru/viewtopic.php?t=1343|Ping pingomatic.com services with PHP]Ping[/anchor]'; // неверно
        $this->uapatterns[112] = '^PingALink';
        $this->uapatterns[113] = '^Pockey';
        $this->uapatterns[114] = '^psbot';
        $this->uapatterns[115] = '^Pump';
        $this->uapatterns[116] = '^QRVA';
        $this->uapatterns[117] = '^RealDownload';
        $this->uapatterns[118] = '^Reaper';
        $this->uapatterns[119] = '^Recorder';
        $this->uapatterns[120] = '^ReGet';
        $this->uapatterns[121] = '^Scooter';
        $this->uapatterns[122] = '^Seeker';
        $this->uapatterns[123] = '^Siphon';
        $this->uapatterns[124] = '^sitecheck.internetseer.com';
        $this->uapatterns[125] = '^SiteSnagger';
        $this->uapatterns[126] = '^SlySearch';
        $this->uapatterns[127] = '^SmartDownload';
        $this->uapatterns[128] = '^Snake';
        $this->uapatterns[129] = '^SpaceBison';
        $this->uapatterns[130] = '^sproose';
        $this->uapatterns[131] = '^Stripper';
        $this->uapatterns[132] = '^Sucker';
        $this->uapatterns[133] = '^SuperBot';
        $this->uapatterns[134] = '^SuperHTTP';
        $this->uapatterns[135] = '^Surfbot';
        $this->uapatterns[136] = '^Szukacz';
        $this->uapatterns[137] = '^tAkeOut';
        $this->uapatterns[138] = '^Teleport\ Pro';
        $this->uapatterns[139] = '^URLSpiderPro';
        $this->uapatterns[140] = '^Vacuum';
        $this->uapatterns[141] = '^VoidEYE';
        $this->uapatterns[142] = '^Web\ Image\ Collector';
        $this->uapatterns[143] = '^Web\ Sucker';
        $this->uapatterns[144] = '^WebAuto';
        $this->uapatterns[145] = '^[Ww]eb[Bb]andit';
        $this->uapatterns[146] = '^webcollage';
        $this->uapatterns[147] = '^WebCopier';
        $this->uapatterns[148] = '^Web\ Downloader';
        $this->uapatterns[149] = '^WebEMailExtrac.*';
        $this->uapatterns[150] = '^WebFetch';
        $this->uapatterns[151] = '^WebGo\ IS';
        $this->uapatterns[152] = '^WebHook';
        $this->uapatterns[153] = '^WebLeacher';
        $this->uapatterns[154] = '^WebMiner';
        $this->uapatterns[155] = '^WebMirror';
        $this->uapatterns[156] = '^WebReaper';
        $this->uapatterns[157] = '^WebSauger';
        $this->uapatterns[158] = '^Website';
        $this->uapatterns[159] = '^Website\ eXtractor';
        $this->uapatterns[160] = '^Website\ Quester';
        $this->uapatterns[161] = '^Webster';
        $this->uapatterns[162] = '^WebStripper';
        $this->uapatterns[163] = 'WebWhacker';
        $this->uapatterns[164] = '^WebZIP';
        $this->uapatterns[165] = '^Wget';
        $this->uapatterns[166] = '^Whacker';
        $this->uapatterns[167] = '^Widow';
        $this->uapatterns[168] = '^WWWOFFLE';
        $this->uapatterns[169] = '^x-Tractor';
        $this->uapatterns[170] = '^Xaldon\ WebSpider';
        $this->uapatterns[171] = '^Xenu';
        $this->uapatterns[172] = '^Zeus.*Webster';
        $this->uapatterns[173] = '^Zeus';
        
        $this->uapatterns[174] = '^Black Hole';
        $this->uapatterns[175] = '^Titan';
        $this->uapatterns[176] = '^CopyRightCheck';
        $this->uapatterns[177] = '^ProWebWalker';
        $this->uapatterns[178] = '^CheeseBot';
        $this->uapatterns[179] = '^Teleport';
        $this->uapatterns[180] = '^TeleportPro'; // Есть аналогичное название 
        $this->uapatterns[181] = '^WebBandit';
        //$this->uapatterns[182] = '^WebBandit/3.50'; неверно
        $this->uapatterns[183] = '^MIIxpc';
        //$this->uapatterns[184] = '^MIIxpc/4.2'; неверно
        $this->uapatterns[185] = '^Telesoft';
        $this->uapatterns[186] = '^Website Quester';
        $this->uapatterns[187] = '^moget';
        $this->uapatterns[188] = '^Mister PiX';
        $this->uapatterns[189] = '^TheNomad';
        $this->uapatterns[190] = '^WWW-Collector-E';
        $this->uapatterns[191] = '^spanner';
        $this->uapatterns[192] = '^InfoNaviRobot';
        $this->uapatterns[193] = '^Harvest';
        //$this->uapatterns[194] = '^Bullseye/1.0'; неверно
        //$this->uapatterns[195] = '^Mozilla/4.0 (compatible; BullsEye; Windows 95)'; неверно
        $this->uapatterns[196] = '^Crescent Internet ToolPak HTTP OLE Control v.1.0';
        //$this->uapatterns[197] = '^CherryPickerSE/1.0'; неверно
        //$this->uapatterns[198] = '^CherryPicker /1.0'; неверно
        $this->uapatterns[199] = '^RMA';
        //$this->uapatterns[200] = '^libWeb/clsHTTP'; неверно
        $this->uapatterns[201] = '^asterias';
        $this->uapatterns[202] = '^httplib';
        $this->uapatterns[203] = '^turingos';
        $this->uapatterns[204] = '^Microsoft URL Control - 5.01.4511';
        $this->uapatterns[205] = '^Microsoft URL Control - 6.00.8169';
        $this->uapatterns[206] = '^DittoSpyder';
        $this->uapatterns[207] = '^Foobot';
        $this->uapatterns[208] = '^WebmasterWorldForumBot';
        $this->uapatterns[209] = '^SpankBot';
        $this->uapatterns[210] = '^BotALot';
        //$this->uapatterns[211] = '^lwp-trivial/1.34'; неверно
        $this->uapatterns[212] = '^lwp-trivial';
        $this->uapatterns[213] = '^BunnySlippers';
        $this->uapatterns[214] = '^humanlinks';
        $this->uapatterns[215] = '^LinkextractorPro';
        $this->uapatterns[216] = '^Offline Explorer';
        $this->uapatterns[217] = '^Mata Hari';
        $this->uapatterns[218] = '^Web Image Collector';
        $this->uapatterns[219] = '^The Intraformant';
        //$this->uapatterns[220] = '^True_Robot/1.0'; неверно
        $this->uapatterns[221] = '^True_Robot';
        $this->uapatterns[222] = '^URLy Warning';
        //$this->uapatterns[223] = '^Wget/1.5.3'; неверно
        $this->uapatterns[224] = '^cosmos';
        $this->uapatterns[225] = '^hloader';
        //$this->uapatterns[226] = '^BlowFish/1.0'; неверно
        $this->uapatterns[227] = '^JennyBot';
        $this->uapatterns[228] = '^BuiltBotTough';
        //$this->uapatterns[229] = '^ProPowerBot/2.14'; неверно
        //$this->uapatterns[230] = '^BackDoorBot/1.0'; неверно
        //$this->uapatterns[231] = '^toCrawl/UrlDispatcher'; неверно
        $this->uapatterns[232] = '^WebEnhancer';
        $this->uapatterns[233] = '^TightTwatBot';
        $this->uapatterns[234] = '^suzuran';
        $this->uapatterns[235] = '^VCI';
        $this->uapatterns[236] = '^VCI WebViewer VCI WebViewer Win32';
        $this->uapatterns[237] = "^Xenu's Link Sleuth 1.1c";
        $this->uapatterns[238] = '^Zeus';
        //$this->uapatterns[239] = '^RepoMonkey Bait & Tackle/v1.01'; неверно
        $this->uapatterns[240] = '^RepoMonkey';
        $this->uapatterns[241] = '^Webster Pro';
        $this->uapatterns[242] = '^EroCrawler';
        //$this->uapatterns[243] = '^LinkScan/8.1a Unix'; неверно
        //$this->uapatterns[244] = '^Keyword Density/0.9'; неверно
        $this->uapatterns[245] = '^QueryN Metasearch';
        $this->uapatterns[246] = '^Kenjin Spider';
        $this->uapatterns[247] = '^Cegbfeieh';
        
        $this->uapatterns[248] = '^nmap';
        $this->uapatterns[249] = '^nikto1';
        $this->uapatterns[250] = '^wikto';
        $this->uapatterns[251] = '^GuzzleHttp';
        $this->uapatterns[252] = '^Paros';
        $this->uapatterns[253] = '^sqlmap';
        $this->uapatterns[254] = '^bsqlbf';
        $this->uapatterns[255] = '^acunetix';
        $this->uapatterns[256] = '^havij';
        $this->uapatterns[257] = '^HTTP_Request2';
        $this->uapatterns[258] = '^Synapse';
        $this->uapatterns[259] = '^Python-urllib';
        $this->uapatterns[260] = '^appscan';
        
        $this->uapatterns[261] = '^visionutils';
        $this->uapatterns[262] = '^wpscan';
        $this->uapatterns[263] = '^mj12bot';
        $this->uapatterns[264] = '^wget'; // повтор с маленькой буквы
        $this->uapatterns[265] = '^ApacheBench';
        $this->uapatterns[266] = '^WordPress';
        //$this->uapatterns[267] = '^Mozilla/4 0'; неверно
        $this->uapatterns[268] = '^curl';
        $this->uapatterns[269] = '^DirBuster';
        $this->uapatterns[270] = '^perl';
        $this->uapatterns[271] = '^PhpStorm';
        $this->uapatterns[272] = '^python';
        $this->uapatterns[273] = '^w3af';
        $this->uapatterns[274] = '^WhatWeb';
        $this->uapatterns[275] = '^Arachni';
        $this->uapatterns[276] = '^XSpider';
        $this->uapatterns[277] = '^Hydra';
        $this->uapatterns[278] = '^Evasions';
        $this->uapatterns[279] = '^OpenVas';
        
    } // ENF  function __construct()
    
    
    /**
     * Функция проверки USER_AGENT по массиву паттернов
     */
    private function _uacheck() {
        
        if (empty($_SERVER['HTTP_USER_AGENT'])) {
            return FALSE; // пустой USER_AGENT, нечего проверять.
        }
        
	if (empty($this->uapatterns) OR !is_array($this->uapatterns)) {
            return FALSE; // паттерны отсутствуют, нечего проверять.
	}
	
        //echo "USER_AGENT: {$_SERVER['HTTP_USER_AGENT']} <br/><br />\r\n";
        
	foreach($this->uapatterns as $key => $pattern) 
	{
            //echo  $key." || ";
            if (preg_match("/".$pattern."/i", $_SERVER['HTTP_USER_AGENT'])) 
            {
                return $key; // вхождение найдено
            }
			
	}
	
        return FALSE; // все чисто
	
    } // End function _uacheck()
    
    /**
     * Метод проверки USER-AGENT
     * @todo Возврат FALSE при отсутствии признаков нежелательных элементов или выполнение действия, предусмотренного при обнаружении.
     */
    public function uaDetect() {
        
        
        return $this->_uacheck();
    }
    
} // End class CPSS_UAgent