<?php

use Allen\Basic\Util\{Config, ConfigType};

Config::SetType('util.language.default', ConfigType::String);
Config::SetType('util.app.name', ConfigType::String);
Config::SetType('util.db.default.type', ConfigType::String);
Config::SetType('util.db.default.host', ConfigType::String);
Config::SetType('util.db.default.name', ConfigType::String);
Config::SetType('util.db.default.user', ConfigType::String, ConfigType::Null);
Config::SetType('util.db.default.pass', ConfigType::String, ConfigType::Null);
Config::SetType('util.db.default.options', ConfigType::Array);
Config::SetType('util.email.from', ConfigType::String);
Config::SetType('util.email.from_name', ConfigType::String);
Config::SetType('util.email.host', ConfigType::String);
Config::SetType('util.email.username', ConfigType::String, ConfigType::Null);
Config::SetType('util.email.password', ConfigType::String, ConfigType::Null);
Config::SetType('util.email.smtp_secure', ConfigType::Bool, ConfigType::Null);
Config::SetType('util.email.port', ConfigType::Int, ConfigType::Null);
Config::SetType('util.email.template_path', ConfigType::String);
Config::SetType('util.github.token', ConfigType::String);
Config::SetType('util.github.token_expire', ConfigType::Int);
Config::SetType('util.github.repos', ConfigType::Array);
Config::SetType('util.shlink.host', ConfigType::String);
Config::SetType('util.shlink.api_key', ConfigType::String);
