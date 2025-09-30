<?php

namespace Allen\Basic\Util\Dav;

use Sabre\DAV\Auth\Backend\BasicCallBack;
use Allen\Account\Login\Password;

class AllenIdAuth extends BasicCallBack
{
	public function __construct(array $allowed_users = [])
	{
		parent::__construct(function ($user, $pass) use ($allowed_users) {
			if (!empty($allowed_users) && !in_array($user, $allowed_users)) {
				return false;
			}
			return Password::Check($user, null, $pass) === true;
		});
	}
}
