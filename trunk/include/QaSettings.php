<?php

class QaSettings {
	static function setSetting($name,$userId,$value,$DBObj) {
		if (QaSettings::getSetting($name,$userId,$DBObj) === FALSE) {
			$sql = "INSERT INTO q_settings (user_id,name,value) VALUES ($userId,'$name',$value);";
		} else {
			$sql = "UPDATE q_settings SET value = $value WHERE user_id = $userId AND name = '$name';";
		}
		return $DBObj->query($sql);
	}
	static function getSetting($name,$userId,$DBObj) {
		$sql = "SELECT value FROM q_settings WHERE user_id = $userId AND name = '$name';";
		if ($resource = $DBObj->query($sql)) {
			if ($DBObj->num($resource)) {
				$result = $DBObj->fetch($resource);
				return $result['value'];
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}

?>