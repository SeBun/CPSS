<?php
/**
 * CONTROL AND PROTECTION OF THE SITE SYSTEM (CPSS)
 *
 * @copyright  Copyright (C) 2016 Sergey Bunin. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 * 
 * TEXTDB - класс для работы с текстовыми базами данных.
 * Класс описан тут: http://scharger.ru/post/klass_tekstovoi_bazy_dannyh_na_php
 */
 
defined('_CPSS') or die;
 


	//Функция для обработки ошибок
	function process_error ($msg, $file, $line) {
		$handler = fopen($GLOBALS['CONFIG_LOG_FILE'], "a");
		fputs($handler, " - [".time()."] PHP Error ($file, $line) : $msg\n");
		fclose($handler);
		return true;
	}
	
	//класс
	class fc_db {
            
		private $fc_db_p		= "./db";
		private $ext			= ".fdb";
		private $db_f 			= "_dat";
		private $delim 			= "/";
		private $database 		= "";
		private $mtime 			= 0;
		private $q_count 		= 0;
		private $file_read_c 	= 0;
		private $file_write_c 	= 0;
		private $inited			= false;
		private $last_table		= "";
		private $errors			= array();
                
		public function fc_db($db_name) {
			if (is_dir($this->fc_db_p.$this->delim.$db_name)) {
				$this->database = $db_name;
				$this->inited = true;
			} else {
					mkdir($this->fc_db_p.$this->delim.$db_name);
					$this->database = $db_name;
					$this->inited = true;
			}
		}
                
		public function create_table ($name) {
			if (!is_dir($this->fc_db_p.$this->delim.$this->database.$this->delim.$name)) {
				try {
					mkdir($this->fc_db_p.$this->delim.$this->database.$this->delim.$name);
				} catch(Exception $e) {process_error ($e->getMessage, $e->getFile(), $e->getLine); unset($e);}
				$this->last_table = $name;
				return true;
			} else {
					$this->_thr_err(4, "Table exist: $name");
			}
			return false;
		}
                
		public function check_table_exists($name) {
			return is_dir($this->fc_db_p.$this->delim.$this->database.$this->delim.$name);
		}
                
		public function select_table ($name) {
			if (is_dir($this->fc_db_p.$this->delim.$this->database.$this->delim.$name)) {
				$this->last_table = $name;
				return true;
			} else {
					$this->_thr_err(4, "Table not exists: $name");
			}
			return false;
		}
                
		public function insert($key, $value, $table = true) {
			$this->q_count++;
			if ($table) {
				$table = $this->last_table;
			} else {
					$this->last_table = $table;
			}
			$dest = $this->fc_db_p.$this->delim.$this->database.$this->delim.$table.$this->delim.$this->_sub_table($key).$this->db_f.$this->ext;
			$data = $this->_read($key, $dest);
			if ($data[1] > 0) {
				$this->_thr_err(3, "INSERT Error: dublicate key $key");
			} else {
					return $this->_write($key, $value, $dest);
			}
		}
                
		public function select($key, $scan_all = false, $table = true) {
			if ($table) {
				$table = $this->last_table;
			}
			$dest = $this->fc_db_p.$this->delim.$this->database.$this->delim.$table.$this->delim.$this->_sub_table($key).$this->db_f.$this->ext;
			return $this->_read($key, $dest, $scan_all);
		}
                
		private function _sub_table($key) {
			$tmp = "$key";
			return md5($tmp[1]);
		}
                
		private function _read($key, $file, $scan_all = false) {
			$start_time = microtime(true);
			$this->file_read_c++;
			try {
				$f = @fopen($file, "r");
				$ret = array();
				if ($f) {
					$i = 1;
					$fi = 0;
					while (($a = fgets($f)) !== false) {
						if (strpos($a, $key) !== false) {
							if ($scan_all) {
								$fi++;
								$ret[] = explode("|", $a);
							} else {
									$fi = 1;
									$ret[] = explode("|", $a);
									break;
							}
						}
						$i++;
					}
					fclose($f);
					$this->mtime += microtime(true) - $start_time;
					foreach ($ret as $key => $a) {
						$ret[$key][0] = $this->_text_re_prepare($a[0]);
						$ret[$key][1] = $this->_text_re_prepare($a[1]);
					}
					if ($fi < 1) {return false;}
					return array(1=>$fi, 2=>$i, 3=>$ret);
				}
			} catch(Exception $e) {process_error ($e->getMessage, $e->getFile(), $e->getLine); unset($e);}
			return false;
		}
                
		private function _write($key, $value, $file) {
			try {
				$key = $this->_text_prepare($key);
				$value = $this->_text_prepare($value);
				$start_time = microtime(true);
				if (is__writable($file)) {
					$this->file_write_c++;
					if (!$f = fopen($file, 'a')) {
						$this->_thr_err(2, "I/O Error: $file");
						return false;
					}
					if (fwrite($f, $key."|".$value."\n") === false) {
						$this->_thr_err(2, "I/O Error: $file");
						return false;
					}
					fclose($f);
				} else {
						$this->_thr_err(2, "I/O Error: $file");
				}
			} catch(Exception $e) {process_error ($e->getMessage, $e->getFile(), $e->getLine); unset($e);}
			$this->mtime += microtime(true) - $start_time;
			return true;
		}
                
		private function _text_prepare($text) {
			return trim(str_ireplace("\n", "{endl}", str_ireplace("|", "{\}", str_ireplace("
", "{endl}", str_ireplace("\t", "{endt}", $text)))));
		}
                
		private function _text_re_prepare($text) {
			return trim(str_ireplace("{endl}", "\n", str_ireplace("{endt}", "\t", str_ireplace("{\}", "|", $text))));
		}
                
		private function _thr_err($code, $msg) {
			$this->errors[] = array(1=>$code, 2=>$msg);
			return true;
		}
                
		public function get_mtime() {
			return $this->mtime;
		}
                
		public function get_errors() {
			return $this->errors;
		}
                
		public function get_file_read_c() {
			return $this->file_read_c;
		}
                
		public function get_file_write_c() {
			return $this->file_write_c;
		}
                
		public function get_q_count() {
			return $this->q_count;
		}
	}
	?>