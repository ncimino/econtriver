<?php
class SiteInfo {
	private $ini_file = "vars/site.ini";
	private $ini_arr;


	public function __construct($parse_now = true) {
		if ($parse_now) {
			$this->parseINIFile();
			$this->setPhpIniErrors();
		}
	}

	public function setINIFile($file) {
		$this->ini_file = $file;
	}

	public function parseINIFile() {
		if (! $ini_arr = parse_ini_file($this->ini_file)) {
			throw new Exception("ERROR:SiteInfo:parseINIFile - parse_ini_file - Failed to parse '{$this->ini_file}' <br />\n");
		} else {
			foreach ($ini_arr as $key => $value) {
				$this->setINIValue($key,$value);
			}
			return true;
		}
	}

	public function setINIValue($key,$value) {
		$this->ini_arr[$key] = $value;
	}

	public function getINIValue($key) {
		return $this->ini_arr['siteinfo.'.$key];
	}

	function getSalt() { return $this->getINIValue('salt'); }
	function getName() { return $this->getINIValue('site_name'); }
	function getSubName() { return $this->getINIValue('site_subname'); }
	function getFromEmail() { return $this->getINIValue('from_email'); }
	function getKeywords() { return $this->getINIValue('keywords'); }
	function getDescription() { return $this->getINIValue('description'); }
	function getImageDir() { return $this->getINIValue('image_dir'); }

	function getIconFile() { return $this->getINIValue('icon_file'); }
	function getIcon() { return $this->getSiteHTTP().$this->getImageDir().$this->getIconFile(); }
	function getIconType() { return $this->getINIValue('icon_type'); }

	function getLogoFile() { return $this->getINIValue('logo_file'); }
	function getLogoHeight() { return $this->getINIValue('logo_height'); }
	function getLogoWidth() { return $this->getINIValue('logo_width'); }
	function getLogo() { return $this->getSiteHTTP().$this->getImageDir().$this->getLogoFile(); }

	function getCssDir() { return $this->getINIValue('css_dir'); }
	function getCssFiles() { return $this->getINIValue('css_file'); }
	function getCss() {
		foreach ($this->getCssFiles() as $file_name) {
			$files[] = $this->getSiteHTTP().$this->getCssDir().$file_name;
		}
		return $files;
	}

	function getJsDir() { return $this->getINIValue('js_dir'); }
	function getJsFiles() { return $this->getINIValue('js_file'); }
	function getJs() {
		foreach ($this->getJsFiles() as $file_name) {
			$files[] = $this->getSiteHTTP().$this->getJsDir().$file_name;
		}
		return $files;
	}

	function getDomain() { return $_SERVER['HTTP_HOST']; }
	function getSiteHTTP() { return "http://".$this->getDomain().$this->getServerDir(); }
	function getServerRootDir() { return $_SERVER['DOCUMENT_ROOT']; }
	function getServerDir() { return $this->getINIValue('root_dir'); }
	function getSelf() { return $_SERVER['PHP_SELF']; }
	function getSelfFileName() { return basename($_SERVER['PHP_SELF']); }

	function getDisplayErrors() { return $this->getINIValue('display_errors'); }
	function getErrorReporting() { return $this->getINIValue('error_reporting'); }

	function setPhpIniErrors() {
		ini_set('display_errors', self::getDisplayErrors());
		error_reporting(self::getErrorReporting());
	}

	function verifyReferer() { return preg_match('/^'.Normalize::encodeFs($this->getSiteHTTP()).'/',$_SERVER['HTTP_REFERER']); }

	public function __toString() {
		$ini_str = '';
		foreach ($this->ini_arr as $key => $value) {
			if (is_array($value)) {
				foreach ($value as $array_key => $array_value) {
					$ini_str .= "ini_arr[".$key."][".$array_key."]=".$array_value."\n";
				}
			} else {
				$ini_str .= "ini_arr[".$key."]=".$value."\n";
			}
		}
		return $ini_str;
	}
}