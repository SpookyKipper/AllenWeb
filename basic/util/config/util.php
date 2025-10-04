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
Config::SetType('util.github.token', ConfigType::String);
Config::SetType('util.github.token_expire', ConfigType::Int);
Config::SetType('util.github.repos', ConfigType::Array);
