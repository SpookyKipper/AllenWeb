<?php

namespace Allen\Basic\Util\Request;

use Allen\Basic\Util\Request;
use CurlHandle;

class RequestStream extends Request
{
	protected function _CurlStart(): CurlHandle
	{
		$ch = parent::_CurlStart();
		curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($ch, $data) {
			echo $data;
			ob_flush();
			flush();
			return strlen($data);
		});
		return $ch;
	}
}
