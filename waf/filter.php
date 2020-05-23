<?php
/**
 * CONTROL AND PROTECTION OF THE SITE SYSTEM (CPSS)
 *
 * @copyright  Copyright (C) 2016 Sergey Bunin. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 *
 * FILTER - класс фильтрации запросов. Содержит свойства паттернов и методы обработки запросов.
 * Выполняет проверку входящих данных и обнаруживает известные приемы хакерских атак. При
 * обнаружении факта взлома выполняет отправку уведомления на указанный адрес и запись лога.
 */
 

defined('_CPSS') or die;


class CPSS_Filter
{
    /**
     * Массив паттернов, участвующих в работе фильтра
     * @var array 
     */
    private $patterns = Array();
    
    /**
     * Массив, содержащий данные запроса
     * @var Array
     */
    public $request = Array();
    
    private $xss, $ua, $traversal, $sql, $rfi;
    
    
    function __construct()
    {
        $this->patterns[0] = '(?:"[^"]*[^-]?>)|(?:[^\w\s]\s*\/>)|(?:>")'; //	finds html breaking injections including whitespace attacks
        $this->patterns[1]  = '(?:"+.*[<=]\s*"[^"]+")|(?:"\s*\w+\s*=)|(?:>\w=\/)|(?:#.+\)["\s]*>)|(?:"\s*(?:src|style|on\w+)\s*=\s*")|(?:[^"]?"[,;\s]+\w*[\[\(])'; //	finds attribute breaking injections including whitespace attacks	Array
        $this->patterns[2]  = '(?:^>[\w\s]*<\/?\w{2,}>)'; //	finds unquoted attribute breaking injections	Array
        $this->patterns[3]  = '(?:[+\/]\s*name[\W\d]*[)+])|(?:;\W*url\s*=)|(?:[^\w\s\/?:>]\s*(?:location|referrer|name)\s*[^\/\w\s-])'; //	Detects url-, name-, JSON, and referrer-contained payload attacks	Array
        $this->patterns[4]  = '(?:\W\s*hash\s*[^\w\s-])|(?:\w+=\W*[^,]*,[^\s(]\s*\()|(?:\?"[^\s"]":)|(?:(?<!\/)__[a-z]+__)|(?:(?:^|[\s)\]\}])(?:s|g)etter\s*=)'; //	Detects hash-contained xss payload attacks, setter usage and property overloading	Array
        $this->patterns[5]  = '(?:with\s*\(\s*.+\s*\)\s*\w+\s*\()|(?:(?:do|while|for)\s*\([^)]*\)\s*\{)|(?:\/[\w\s]*\[\W*\w)'; //	Detects self contained xss via with(), common loops and regex to string conversion	Array
        $this->patterns[6]  = '(?:[=(].+\?.+:)|(?:with\([^)]*\)\))|(?:\.\s*source\W)'; //	Detects JavaScript with(), ternary operators and XML predicate attacks	Array
        $this->patterns[7]  = '(?:\/\w*\s*\)\s*\()|(?:\([\w\s]+\([\w\s]+\)[\w\s]+\))|(?:(?<!(?:mozilla\/\d\.\d\s))\([^)[]+\[[^\]]+\][^)]*\))|(?:[^\s!][{([][^({[]+[{([][^}\])]+[}\])][\s+",\d]*[}\])])|(?:"\)?\]\W*\[)|(?:=\s*[^\s:;]+\s*[{([][^}\])]+[}\])];)'; //	Detects self-executing JavaScript functions	Array
        //$this->patterns[8]  = '(?:\\u00[a-f0-9]{2})|(?:\\x0*[a-f0-9]{2})|(?:\\\d{2,3})'; //	Detects the IE octal, hex and unicode entities	Array
        //$this->patterns[9]  = '(?:(?:\/|\\)?\.+(\/|\\)(?:\.+)?)|(?:\w+\.exe\??\s)|(?:;\s*\w+\s*\/[\w*-]+\/)|(?:\d\.\dx\|)|(?:%(?:c0\.|af\.|5c\.))|(?:\/(?:%2e){2})'; //	Detects basic directory traversal	Array
        //$this->patterns[10] = '(?:%c0%ae\/)|(?:(?:\/|\\)(home|conf|usr|etc|proc|opt|s?bin|local|dev|tmp|kern|[br]oot|sys|system|windows|winnt|program|%[a-z_-]{3,}%)(?:\/|\\))|(?:(?:\/|\\)inetpub|localstart\.asp|boot\.ini)'; //	Detects specific directory and path traversal	Array
        $this->patterns[11] = '(?:etc\/\W*passwd)'; //	Detects etc/passwd inclusion attempts	Array
        $this->patterns[12] = '(?:%u(?:ff|00|e\d)\w\w)|(?:(?:%(?:e\w|c[^3\W]|))(?:%\w\w)(?:%\w\w)?)'; //	Detects halfwidth/fullwidth encoded unicode HTML breaking attempts	Array
        $this->patterns[13] = '(?:#@~\^\w+)|(?:\w+script:|@import[^\w]|;base64|base64,)|(?:\w+\s*\([\w\s]+,[\w\s]+,[\w\s]+,[\w\s]+,[\w\s]+,[\w\s]+\))'; //	Detects possible includes, VBSCript/JScript encodeed and packed functions	Array
        $this->patterns[14] = '([^*:\s\w,.\/?+-]\s*)?(?<![a-z]\s)(?<![a-z\/_@\-\|])(\s*return\s*)?(?:create(?:element|attribute|textnode)|[a-z]+events?|setattribute|getelement\w+|appendchild|createrange|createcontextualfragment|removenode|parentnode|decodeuricomponent|\wettimeout|(?:ms)?setimmediate|option|useragent)(?(1)[^\w%"]|(?:\s*[^@\s\w%",.+\-]))'; //	Detects JavaScript DOM/miscellaneous properties and methods	Array
        $this->patterns[15] = '([^*\s\w,.\/?+-]\s*)?(?<![a-mo-z]\s)(?<![a-z\/_@])(\s*return\s*)?(?:alert|inputbox|showmod(?:al|eless)dialog|showhelp|infinity|isnan|isnull|iterator|msgbox|executeglobal|expression|prompt|write(?:ln)?|confirm|dialog|urn|(?:un)?eval|exec|execscript|tostring|status|execute|window|unescape|navigate|jquery|getscript|extend|prototype)(?(1)[^\w%"]|(?:\s*[^@\s\w%",.:\/+\-]))'; //	Detects possible includes and typical script methods	Array
        $this->patterns[16] = '([^*:\s\w,.\/?+-]\s*)?(?<![a-z]\s)(?<![a-z\/_@])(\s*return\s*)?(?:hash|name|href|navigateandfind|source|pathname|close|constructor|port|protocol|assign|replace|back|forward|document|ownerdocument|window|top|this|self|parent|frames|_?content|date|cookie|innerhtml|innertext|csstext+?|outerhtml|print|moveby|resizeto|createstylesheet|stylesheets)(?(1)[^\w%"]|(?:\s*[^@\/\s\w%.+\-]))'; //	Detects JavaScript object properties and methods	Array
        $this->patterns[17] = '([^*:\s\w,.\/?+-]\s*)?(?<![a-z]\s)(?<![a-z\/_@\-\|])(\s*return\s*)?(?:join|pop|push|reverse|reduce|concat|map|shift|sp?lice|sort|unshift)(?(1)[^\w%"]|(?:\s*[^@\s\w%,.+\-]))'; //	Detects JavaScript array properties and methods	Array
        $this->patterns[18] = '([^*:\s\w,.\/?+-]\s*)?(?<![a-z]\s)(?<![a-z\/_@\-\|])(\s*return\s*)?(?:set|atob|btoa|charat|charcodeat|charset|concat|crypto|frames|fromcharcode|indexof|lastindexof|match|navigator|toolbar|menubar|replace|regexp|slice|split|substr|substring|escape|\w+codeuri\w*)(?(1)[^\w%"]|(?:\s*[^@\s\w%,.+\-]))'; //	Detects JavaScript string properties and methods	Array
        $this->patterns[19] = '(?:\)\s*\[)|([^*":\s\w,.\/?+-]\s*)?(?<![a-z]\s)(?<![a-z_@\|])(\s*return\s*)?(?:globalstorage|sessionstorage|postmessage|callee|constructor|content|domain|prototype|try|catch|top|call|apply|url|function|object|array|string|math|if|for\s*(?:each)?|elseif|case|switch|regex|boolean|location|(?:ms)?setimmediate|settimeout|setinterval|void|setexpression|namespace|while)(?(1)[^\w%"]|(?:\s*[^@\s\w%".+\-\/]))'; //	Detects JavaScript language constructs	Array
        $this->patterns[20] = '(?:,\s*(?:alert|showmodaldialog|eval)\s*,)|(?::\s*eval\s*[^\s])|([^:\s\w,.\/?+-]\s*)?(?<![a-z\/_@])(\s*return\s*)?(?:(?:document\s*\.)?(?:.+\/)?(?:alert|eval|msgbox|showmod(?:al|eless)dialog|showhelp|prompt|write(?:ln)?|confirm|dialog|open))\s*(?:[^.a-z\s\-]|(?:\s*[^\s\w,.@\/+-]))|(?:java[\s\/]*\.[\s\/]*lang)|(?:\w\s*=\s*new\s+\w+)|(?:&\s*\w+\s*\)[^,])|(?:\+[\W\d]*new\s+\w+[\W\d]*\+)|(?:document\.\w)'; //	Detects very basic XSS probings	Array
        $this->patterns[21] = '(?:=\s*(?:top|this|window|content|self|frames|_content))|(?:\/\s*[gimx]*\s*[)}])|(?:[^\s]\s*=\s*script)|(?:\.\s*constructor)|(?:default\s+xml\s+namespace\s*=)|(?:\/\s*\+[^+]+\s*\+\s*\/)'; //	Detects advanced XSS probings via Script(), RexExp, constructors and XML namespaces	Array
        $this->patterns[22] = '(?:\.\s*\w+\W*=)|(?:\W\s*(?:location|document)\s*\W[^({[;]+[({[;])|(?:\(\w+\?[:\w]+\))|(?:\w{2,}\s*=\s*\d+[^&\w]\w+)|(?:\]\s*\(\s*\w+)'; //	Detects JavaScript location/document property access and window access obfuscation	Array
        $this->patterns[23] = '(?:[".]script\s*\()|(?:\$\$?\s*\(\s*[\w"])|(?:\/[\w\s]+\/\.)|(?:=\s*\/\w+\/\s*\.)|(?:(?:this|window|top|parent|frames|self|content)\[\s*[(,"]*\s*[\w\$])|(?:,\s*new\s+\w+\s*[,;)])'; //	Detects basic obfuscated JavaScript script injections	Array
        $this->patterns[24] = '(?:=\s*[$\w]\s*[\(\[])|(?:\(\s*(?:this|top|window|self|parent|_?content)\s*\))|(?:src\s*=s*(?:\w+:|\/\/))|(?:\w+\[("\w+"|\w+\|\|))|(?:[\d\W]\|\|[\d\W]|\W=\w+,)|(?:\/\s*\+\s*[a-z"])|(?:=\s*\$[^([]*\()|(?:=\s*\(\s*")'; //	Detects obfuscated JavaScript script injections	Array
        $this->patterns[25] = '(?:[^:\s\w]+\s*[^\w\/](href|protocol|host|hostname|pathname|hash|port|cookie)[^\w])'; //	Detects JavaScript cookie stealing and redirection attempts	Array
        $this->patterns[26] = '(?:(?:vbs|vbscript|data):.*[,+])|(?:\w+\s*=\W*(?!https?)\w+:)|(jar:\w+:)|(=\s*"?\s*vbs(?:ript)?:)|(language\s*=\s?"?\s*vbs(?:ript)?)|on\w+\s*=\*\w+\-"?'; //	Detects data: URL injections, VBS injections and common URI schemes	Array
        $this->patterns[27] = '(?:firefoxurl:\w+\|)|(?:(?:file|res|telnet|nntp|news|mailto|chrome)\s*:\s*[%&#xu\/]+)|(wyciwyg|firefoxurl\s*:\s*\/\s*\/)'; //	Detects IE firefoxurl injections, cache poisoning attempts and local file inclusion/execution	Array
        //$this->patterns[28] = '(?:binding\s?=|moz-binding|behavior\s?=)|(?:[\s\/]style\s*=\s*[-\\])'; //	Detects bindings and behavior injections	Array
        $this->patterns[29] = '(?:=\s*\w+\s*\+\s*")|(?:\+=\s*\(\s")|(?:!+\s*[\d.,]+\w?\d*\s*\?)|(?:=\s*\[s*\])|(?:"\s*\+\s*")|(?:[^\s]\[\s*\d+\s*\]\s*[;+])|(?:"\s*[&|]+\s*")|(?:\/\s*\?\s*")|(?:\/\s*\)\s*\[)|(?:\d\?.+:\d)|(?:]\s*\[\W*\w)|(?:[^\s]\s*=\s*\/)'; //	Detects common XSS concatenation patterns 1/2	Array
        $this->patterns[30] = '(?:=\s*\d*\.\d*\?\d*\.\d*)|(?:[|&]{2,}\s*")|(?:!\d+\.\d*\?")|(?:\/:[\w.]+,)|(?:=[\d\W\s]*\[[^]]+\])|(?:\?\w+:\w+)'; //	Detects common XSS concatenation patterns 2/2	Array
        $this->patterns[31] = '(?:[^\w\s=]on(?!g\&gt;)\w+[^=_+-]*=[^$]+(?:\W|\&gt;)?)'; //	Detects possible event handlers	Array
        $this->patterns[32] = '(?:\<\w*:?\s(?:[^\>]*)t(?!rong))|(?:\<scri)|(<\w+:\w+)'; //	Detects obfuscated script tags and XML wrapped HTML	Array
        $this->patterns[33] = '(?:\<\/\w+\s\w+)|(?:@(?:cc_on|set)[\s@,"=])'; //	Detects attributes in closing tags and conditional compilation tokens	Array
        $this->patterns[34] = '(?:--[^\n]*$)|(?:\<!-|-->)|(?:[^*]\/\*|\*\/[^*])|(?:(?:[\W\d]#|--|{)$)|(?:\/{3,}.*$)|(?:<!\[\W)|(?:\]!>)'; //	Detects common comment types	Array
        $this->patterns[35] = '(?:\<base\s+)|(?:<!(?:element|entity|\[CDATA))'; //	Detects base href injections and XML entity injections	Array
        $this->patterns[36] = '(?:\<[\/]?(?:[i]?frame|applet|isindex|marquee|keygen|script|audio|video|input|button|textarea|style|base|body|meta|link|object|embed|param|plaintext|xm\w+|image|im(?:g|port)))'; //	Detects possibly malicious html elements including some attributes	Array
        $this->patterns[37] = '(?:\\x[01fe][\db-ce-f])|(?:%[01fe][\db-ce-f])|(?:&#[01fe][\db-ce-f])|(?:\\[01fe][\db-ce-f])|(?:&#x[01fe][\db-ce-f])'; //	Detects nullbytes and other dangerous characters	Array
        $this->patterns[38] = '(?:\)\s*when\s*\d+\s*then)|(?:"\s*(?:#|--|{))|(?:\/\*!\s?\d+)|(?:ch(?:a)?r\s*\(\s*\d)|(?:(?:(n?and|x?or|not)\s+|\|\||\&\&)\s*\w+\()'; //	Detects MySQL comments, conditions and ch(a)r injections	Array
        $this->patterns[39] = '(?:[\s()]case\s*\()|(?:\)\s*like\s*\()|(?:having\s*[^\s]+\s*[^\w\s])|(?:if\s?\([\d\w]\s*[=<>~])'; //	Detects conditional SQL injection attempts	Array
        //$this->patterns[40] = '(?:"\s*or\s*"?\d)|(?:\\x(?:23|27|3d))|(?:^.?"$)|(?:(?:^["\\]*(?:[\d"]+|[^"]+"))+\s*(?:n?and|x?or|not|\|\||\&\&)\s*[\w"[+&!@(),.-])|(?:[^\w\s]\w+\s*[|-]\s*"\s*\w)|(?:@\w+\s+(and|or)\s*["\d]+)|(?:@[\w-]+\s(and|or)\s*[^\w\s])|(?:[^\w\s:]\s*\d\W+[^\w\s]\s*".)|(?:\Winformation_schema|table_name\W)'; //	Detects classic SQL injection probings 1/2	Array
        $this->patterns[41] = '(?:"\s*\*.+(?:or|id)\W*"\d)|(?:\^")|(?:^[\w\s"-]+(?<=and\s)(?<=or\s)(?<=xor\s)(?<=nand\s)(?<=not\s)(?<=\|\|)(?<=\&\&)\w+\()|(?:"[\s\d]*[^\w\s]+\W*\d\W*.*["\d])|(?:"\s*[^\w\s?]+\s*[^\w\s]+\s*")|(?:"\s*[^\w\s]+\s*[\W\d].*(?:#|--))|(?:".*\*\s*\d)|(?:"\s*or\s[^\d]+[\w-]+.*\d)|(?:[()*<>%+-][\w-]+[^\w\s]+"[^,])'; //	Detects classic SQL injection probings 2/2	Array
        $this->patterns[42] = '(?:\d"\s+"\s+\d)|(?:^admin\s*"|(\/\*)+"+\s?(?:--|#|\/\*|{)?)|(?:"\s*or[\w\s-]+\s*[+<>=(),-]\s*[\d"])|(?:"\s*[^\w\s]?=\s*")|(?:"\W*[+=]+\W*")|(?:"\s*[!=|][\d\s!=+-]+.*["(].*$)|(?:"\s*[!=|][\d\s!=]+.*\d+$)|(?:"\s*like\W+[\w"(])|(?:\sis\s*0\W)|(?:where\s[\s\w\.,-]+\s=)|(?:"[<>~]+")'; //	Detects basic SQL authentication bypass attempts 1/3	Array
        $this->patterns[43] = '(?:union\s*(?:all|distinct|[(!@]*)?\s*[([]*\s*select)|(?:\w+\s+like\s+\")|(?:like\s*"\%)|(?:"\s*like\W*["\d])|(?:"\s*(?:n?and|x?or|not |\|\||\&\&)\s+[\s\w]+=\s*\w+\s*having)|(?:"\s*\*\s*\w+\W+")|(?:"\s*[^?\w\s=.,;)(]+\s*[(@"]*\s*\w+\W+\w)|(?:select\s*[\[\]()\s\w\.,"-]+from)|(?:find_in_set\s*\()'; //	Detects basic SQL authentication bypass attempts 2/3	Array
        $this->patterns[44] = '(?:in\s*\(+\s*select)|(?:(?:n?and|x?or|not |\|\||\&\&)\s+[\s\w+]+(?:regexp\s*\(|sounds\s+like\s*"|[=\d]+x))|("\s*\d\s*(?:--|#))|(?:"[%&<>^=]+\d\s*(=|or))|(?:"\W+[\w+-]+\s*=\s*\d\W+")|(?:"\s*is\s*\d.+"?\w)|(?:"\|?[\w-]{3,}[^\w\s.,]+")|(?:"\s*is\s*[\d.]+\s*\W.*")'; //	Detects basic SQL authentication bypass attempts 3/3	Array
        $this->patterns[45] = '(?:[\d\W]\s+as\s*["\w]+\s*from)|(?:^[\W\d]+\s*(?:union|select|create|rename|truncate|load|alter|delete|update|insert|desc))|(?:(?:select|create|rename|truncate|load|alter|delete|update|insert|desc)\s+(?:(?:group_)concat|char|load_file)\s?\(?)|(?:end\s*\);)|("\s+regexp\W)|(?:[\s(]load_file\s*\()'; //	Detects concatenated basic SQL injection and SQLLFI attempts	Array
        $this->patterns[46] = '(?:@.+=\s*\(\s*select)|(?:\d+\s*or\s*\d+\s*[\-+])|(?:\/\w+;?\s+(?:having|and|or|select)\W)|(?:\d\s+group\s+by.+\()|(?:(?:;|#|--)\s*(?:drop|alter))|(?:(?:;|#|--)\s*(?:update|insert)\s*\w{2,})|(?:[^\w]SET\s*@\w+)|(?:(?:n?and|x?or|not |\|\||\&\&)[\s(]+\w+[\s)]*[!=+]+[\s\d]*["=()])'; //	Detects chained SQL injection attempts 1/2	Array
        $this->patterns[47] = '(?:"\s+and\s*=\W)|(?:\(\s*select\s*\w+\s*\()|(?:\*\/from)|(?:\+\s*\d+\s*\+\s*@)|(?:\w"\s*(?:[-+=|@]+\s*)+[\d(])|(?:coalesce\s*\(|@@\w+\s*[^\w\s])|(?:\W!+"\w)|(?:";\s*(?:if|while|begin))|(?:"[\s\d]+=\s*\d)|(?:order\s+by\s+if\w*\s*\()|(?:[\s(]+case\d*\W.+[tw]hen[\s(])'; //	Detects chained SQL injection attempts 2/2	Array
        $this->patterns[48] = '(?:(select|;)\s+(?:benchmark|if|sleep)\s*?\(\s*\(?\s*\w+)'; //	Detects SQL benchmark and sleep injection attempts including conditional queries	Array
        $this->patterns[49] = '(?:create\s+function\s+\w+\s+returns)|(?:;\s*(?:select|create|rename|truncate|load|alter|delete|update|insert|desc)\s*[\[(]?\w{2,})'; //	Detects MySQL UDF injection and other data/structure manipulation attempts	Array
        $this->patterns[50] = '(?:alter\s*\w+.*character\s+set\s+\w+)|(";\s*waitfor\s+time\s+")|(?:";.*:\s*goto)'; //	Detects MySQL charset switch and MSSQL DoS attempts	Array
        $this->patterns[51] = '(?:procedure\s+analyse\s*\()|(?:;\s*(declare|open)\s+[\w-]+)|(?:create\s+(procedure|function)\s*\w+\s*\(\s*\)\s*-)|(?:declare[^\w]+[@#]\s*\w+)|(exec\s*\(\s*@)'; //	Detects MySQL and PostgreSQL stored procedure/function injections	Array
        $this->patterns[52] = '(?:select\s*pg_sleep)|(?:waitfor\s*delay\s?"+\s?\d)|(?:;\s*shutdown\s*(?:;|--|#|\/\*|{))'; //	Detects Postgres pg_sleep injection, waitfor delay attacks and database shutdown attempts	Array
        $this->patterns[53] = '(?:\sexec\s+xp_cmdshell)|(?:"\s*!\s*["\w])|(?:from\W+information_schema\W)|(?:(?:(?:current_)?user|database|schema|connection_id)\s*\([^\)]*)|(?:";?\s*(?:select|union|having)\s*[^\s])|(?:\wiif\s*\()|(?:exec\s+master\.)|(?:union select @)|(?:union[\w(\s]*select)|(?:select.*\w?user\()|(?:into[\s+]+(?:dump|out)file\s*")'; //	Detects MSSQL code execution and information gathering attempts	Array
        $this->patterns[54] = '(?:merge.*using\s*\()|(execute\s*immediate\s*")|(?:\W+\d*\s*having\s*[^\s\-])|(?:match\s*[\w(),+-]+\s*against\s*\()'; //	Detects MATCH AGAINST, MERGE, EXECUTE IMMEDIATE and HAVING injections	Array
        $this->patterns[55] = '(?:,.*[)\da-f"]"(?:".*"|\Z|[^"]+))|(?:\Wselect.+\W*from)|((?:select|create|rename|truncate|load|alter|delete|update|insert|desc)\s*\(\s*space\s*\()'; //	Detects MySQL comment-/space-obfuscated injections and backtick termination	Array
        $this->patterns[56] = '(?:@[\w-]+\s*\()|(?:]\s*\(\s*["!]\s*\w)|(?:<[?%](?:php)?.*(?:[?%]>)?)|(?:;[\s\w|]*\$\w+\s*=)|(?:\$\w+\s*=(?:(?:\s*\$?\w+\s*[(;])|\s*".*"))|(?:;\s*\{\W*\w+\s*\()'; //	Detects code injection attempts 1/3	Array
        $this->patterns[57] = '(?:(?:[;]+|(<[?%](?:php)?)).*(?:define|eval|file_get_contents|include|require|require_once|set|shell_exec|phpinfo|system|passthru|preg_\w+|execute)\s*["(@])'; //	Detects code injection attempts 2/3	Array
        $this->patterns[58] = '(?:(?:[;]+|(<[?%](?:php)?)).*[^\w](?:echo|print|print_r|var_dump|[fp]open))|(?:;\s*rm\s+-\w+\s+)|(?:;.*{.*\$\w+\s*=)|(?:\$\w+\s*\[\]\s*=\s*)'; //	Detects code injection attempts 3/3	Array
        $this->patterns[59] = '(?:\w+]?(?<!href)(?<!src)(?<!longdesc)(?<!returnurl)=(?:https?|ftp):)|(?:\{\s*\$\s*\{)'; //	Detects url injections and RFE attempts	Array
        $this->patterns[60] = '(?:function[^(]*\([^)]*\))|(?:(?:delete|void|throw|instanceof|new|typeof)[^\w.]+\w+\s*[([])|([)\]]\s*\.\s*\w+\s*=)|(?:\(\s*new\s+\w+\s*\)\.)'; //	Detects common function declarations and special JS operators	Array
        $this->patterns[61] = '(?:[\w.-]+@[\w.-]+%(?:[01][\db-ce-f])+\w+:)'; //	Detects common mail header injections	Array
        $this->patterns[62] = '(?:\.pl\?\w+=\w?\|\w+;)|(?:\|\(\w+=\*)|(?:\*\s*\)+\s*;)'; //	Detects perl echo shellcode injection and LDAP vectors	Array
        $this->patterns[63] = '(?:(^|\W)const\s+[\w\-]+\s*=)|(?:(?:do|for|while)\s*\([^;]+;+\))|(?:(?:^|\W)on\w+\s*=[\w\W]*(?:on\w+|alert|eval|print|confirm|prompt))|(?:groups=\d+\(\w+\))|(?:(.)\1{128,})'; //	Detects basic XSS DoS attempts	Array
        $this->patterns[64] = '(?:\({2,}\+{2,}:{2,})|(?:\({2,}\+{2,}:+)|(?:\({3,}\++:{2,})|(?:\$\[!!!\])'; //	Detects unknown attack vectors based on PHPIDS Centrifuge detection	Array
        $this->patterns[65] = '(?:[\s\/"]+[-\w\/\\\*]+\s*=.+(?:\/\s*>))'; //	Finds attribute breaking injections including obfuscated attributes	Array
        $this->patterns[66] = '(?:(?:msgbox|eval)\s*\+|(?:language\s*=\*vbscript))'; //	Finds basic VBScript injection attempts	Array
        $this->patterns[67] = '(?:\[\$(?:ne|eq|lte?|gte?|n?in|mod|all|size|exists|type|slice|or)\])'; //	Finds basic MongoDB SQL injection attempts	Array
        $this->patterns[68] = '(?:[\s\d\/"]+(?:on\w+|style|poster|background)=[$"\w])|(?:-type\s*:\s*multipart)'; //	finds malicious attribute injection attempts and MHTML attacks	Array
        $this->patterns[69] = '(?:(sleep\((\s*)(\d*)(\s*)\)|benchmark\((.*)\,(.*)\)))'; //	Detects blind sqli tests using sleep() or benchmark().	Array
        $this->patterns[70] = '(?i:(\%SYSTEMROOT\%))'; //	An attacker is trying to locate a file to read or write.	Array
        $this->patterns[71] = '(?i:(ping(.*)[\-(.*)\w|\w(.*)\-]))'; //	Detects remote code exectuion tests. Will match "ping -n 3 localhost" and "ping localhost -n 3"	Array
        $this->patterns[72] = '(?:(((.*)\%[c|d|i|e|f|g|o|s|u|x|p|n]){8}))'; //	Looking for a format string attack	Array
        $this->patterns[73] = '(?:(union(.*)select(.*)from))'; //	Looking for basic sql injection. Common attack string for mysql, oracle and others.	Array
        $this->patterns[74] = '(?:^(-0000023456|4294967295|4294967296|2147483648|2147483647|0000012345|-2147483648|-2147483649|0000023456|2.2250738585072007e-308|1e309)$)'; //


        /* Set filter */
        $xss  = "javascript|vbscript|expression|applet|meta|xml|blink|";
        $xss .= "link|style|script|embed|object|iframe|frame|frameset|";
        $xss .= "ilayer|layer|bgsound|title|base|form|img|body|href|div|cdata";

        $ua  = "curl|wget|winhttp|HTTrack|clshttp|loader|email|harvest|extract|grab|miner|";
        $ua .= "libwww-perl|acunetix|sqlmap|python|nikto|scan";

        $sql  = "[\x22\x27](\s)*(or|and)(\s).*(\s)*\x3d|";
        $sql .= "cmd=ls|cmd%3Dls|";
        $sql .= "(drop|alter|create|truncate).*(index|table|database)|";
        $sql .= "insert(\s).*(into|member.|value.)|";
        $sql .= "(select|union|order).*(select|union|order)|";
        $sql .= "0x[0-9a-f][0-9a-f]|";
        $sql .= "benchmark\([0-9]+,[a-z]+|benchmark\%28+[0-9]+%2c[a-z]+|";
        $sql .= "eval\(.*\(.*|eval%28.*%28.*|";
        $sql .= "update.*set.*=|delete.*from";

        $traversal = "\.\.\/|\.\.\\|%2e%2e%2f|%2e%2e\/|\.\.%2f|%2e%2e%5c";

        $rfi  = "%00|";
        $rfi .= "(?:((?:ht|f)tp(?:s?)|file|webdav)\:\/\/|~\/|\/).*\.\w{2,3}|";
        $rfi .= "(?:((?:ht|f)tp(?:s?)|file|webdav)%3a%2f%2f|%7e%2f%2f).*\.\w{2,3}";
    }

    
    /**
     * Attack filter 
     * TODO: вынести формирование отчета 
     */
    private function filter() 
    {
        
	$req_method = $_SERVER['REQUEST_METHOD'];
	$req_referr = $_SERVER['HTTP_REFERER'];
	$req_uagent = $_SERVER['HTTP_USER_AGENT'];
	$req_query  = $_SERVER['QUERY_STRING'];
	$req_uri    = $_SERVER['REQUEST_URI'];
	$req_ip     = getenv( 'REMOTE_ADDR' );
        
	$url  = ( !empty( $_SERVER['HTTPS'] ) ) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
	$time = date( "M j G:i:s Y" );
	
	/* Информация для лога */
	$msg  = "\nDATE/TIME: ".$time;
	$msg .= "\n\nFROM IP: http://whois.domaintools.com/".$req_ip;
	$msg .= "\nURI: ".$req_uri;
	$msg .= "\nSTRING: ".$req_query;
	$msg .= "\nMETHOD: ".$req_method;
	$msg .= "\nUSERAGENT: ".$req_uagent;
	$msg .= "\nREFERRER: ".$req_referr;
	$msg .= "\n";
        
	//global $xss, $ua, $traversal, $sql, $rfi;
	//$settings = (array) get_option( 'waf_settings' );
        
	/* Method Blacklist*/
	if ( preg_match( "/^(TRACE|DELETE|TRACK)/i", $req_method, $matched ) ) {
		//wp_waf_email( 'Method Blacklist', $msg, $matched[1], 'waf_method' );
	}
        
	/* Referrer */
	elseif ( preg_match( "/<[^>]*(". $this->xss .")[^>]*>/i", $req_referr, $matched ) ) {
		//wp_waf_email( 'Referrer XSS', $msg, $matched[1], 'waf_referrer' );
	}
        
	/* User Agent Empty */
	elseif ( preg_match( "/(^$)/i", $req_uagent, $matched ) ) {
		//wp_waf_email( 'User Agent Empty', $msg, $matched[1], 'waf_useragent_blank' );
	}
        
	/* User Agent Blacklist */
	elseif ( preg_match( "/^(". $this->ua .").*/i", $req_uagent, $matched ) ) {
		//wp_waf_email( 'User Agent Blacklist', $msg, $matched[1], 'waf_useragent' );
	}
        
	/* Query - > 255 */
	elseif ( strlen( $req_query ) > 255 ) {
		if ( $settings['waf_query_too_long'] != "" ) {
			//wp_waf_email( 'Query Too Long', $msg, '> 255', 'waf_query_too_long' );
		}
	}
        
	/* Query - Cross Site Scripting */
	elseif ( preg_match( "/(<|<.)[^>]*(". $this->xss .")[^>]*>/i", $req_query, $matched ) ) {
		//wp_waf_email( 'Query XSS', $msg, $matched[1], 'waf_query' );
	}
	elseif ( preg_match( "/((\%3c)|(\%3c).)[^(\%3e)]*(". $this->xss .")[^(\%3e)]*(%3e)/i", $req_query, $matched ) ) {
		//wp_waf_email( 'Query XSS', $msg, $matched[1], 'waf_query' );
	}
        
	/* Query - traversal */
	elseif ( preg_match( "/^.*(". $this->traversal .").*/i", $req_query, $matched ) ) {
		//wp_waf_email( 'Query traversal', $msg, $matched[1], 'waf_query' );
	}
        
	/* Query - Remote File Inclusion */
	elseif ( preg_match( "/^.*(". $this->rfi .").*/i", $req_query, $matched ) ) {
		//wp_waf_email( 'Query RFI', $msg, $matched[1], 'waf_query' );
	}
        
	/* Query - Sql injection */
	elseif ( preg_match( "/^.*(". $this->sql .").*/i", $req_query, $matched ) ) {
		//wp_waf_email( 'Query SQL', $msg, $matched[1], 'waf_query' );
	}
        
        else {
            return 0;
        }
        
    }

    
   	
    /**
     * Обработка данных свойства request
     * @todo Возможно, в этом методе лучше в параметрах задавать, что именно проверять (если будет проверка по отдельности GET, POST и COOKIES).
     */
    private function detects() {
        
        /* Проверка по паттернам */

	if (empty($this->request)) {
            return 0; // свойство пусто, нечего проверять
	}
	
	if (!is_string($this->request)) {
            $this->request = implode($this->request);   // преобразуем в строку
        }
                
	$this->request = mb_strtolower($this->request); // преобразуем в нижний регистр
		
	
	foreach($this->patterns as $key => $pattern) 
	{
            echo  $key." || ";
            if (preg_match("/".$pattern."/i", $this->request)) 
            {
                return $key; // вхождение найдено
            }
			
	}
	
            return 0; // все чисто
	
	}

    
	/**
         * Обработка события обнаружения (реакция на WAF)
         * @param type $key
         */
	private function processing($key) {
		
		$config = new CPSS_Config();

		var_dump($config->offline);
		
		#header("HTTP/1.0 503 Service Temporarily Unavailable");
		#header("Connection: close");
		#header("Content-Type: text/html");
	
		$error_msg = "Request denied. ";
	
		if (SET_VIEWERR) $error_msg .= "#".$key; // включить в сообщение ключ массива
	

		$method=$_SERVER['REQUEST_METHOD'];	
		$host=$_SERVER['HTTP_HOST'];
		$URI=urldecode($_SERVER['REQUEST_URI']);
	
		$strC = '';
		foreach($_GET as $keys => $val) {
			$strC.=$keys.'='.$val."\n"; 
		}
	
		$log = $method."/ $host  $URI\n--\n$strC";	
		$l=fopen("Z:/home/joomla2/www/log.txt","a+");
		fwrite($l,"$log#".$key." => ".$patterns[$key]."\n--\n");
		fclose($l);
	

		die ($error_msg); // хакер, FUCK!
		
	}
        
    
    /**
     * Запуск фильтра WAF
     * 
     * @todo Возможно, здесь лучше разделить проверку по массивам и проверять по отдельности GET, POST, COOKIE.
     */
    public function run() {
        
        $this->request = $_REQUEST; // получаем данные переменных $_GET, $_POST и $_COOKIE
        
        /**
         * Первый этап проверки - прогоняем содержимое по списку паттернов
         * 
         * @result Возвращает либо false, либо ключ массива паттернов, на котором произошло срабатывание
         */
        $result = $this->detects();
        
        if ($result) 
        {
            die("DETECT"); // тут будет вызов свойства processing 
        }
        
        /**
         * Второй этап проверки - выполняем дополнительную фильтрацию входящих данных
         */
        $result = $this->filter();
        
        if ($result) 
        {
            die("DETECT filter"); // тут будет вызов свойства processing 
        }
        
	return true; // ничего не найдено
    }
    

} // End class CPSS_Filter
