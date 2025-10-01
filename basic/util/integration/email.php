<?php

namespace Allen\Basic\Util\Integration;

use Allen\Basic\Path;
use Exception;
use Allen\Basic\Util\Config;
use PHPMailer\PHPMailer\PHPMailer;

class Email extends PHPMailer
{
	public function __construct(
		?string $from = null,
		?string $from_name = null,
		?string $host = null,
		?string $username = null,
		?string $password = null,
		bool $smtp_secure = true,
		?int $port = null,
		bool $exceptions = true,
	) {
		parent::__construct($exceptions);
		if (!is_null($from)) $this->setFrom($from, $from_name ?? '');
		$this->isSMTP();
		$this->CharSet = 'UTF-8';
		if (!is_null($host)) $this->Host = $host;
		if (!is_null($username)) $this->Username = $username;
		if (!is_null($password)) $this->Password = $password;
		if (!is_null($username) && !is_null($password)) $this->SMTPAuth = true;
		if ($smtp_secure === true) {
			$this->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
			$this->Port = 587;
		} else if (is_null($smtp_secure)) {
			$this->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
			$this->Port = 465;
		}
		if (!is_null($port)) $this->Port = $port;
	}
	public function Html(string $html): self
	{
		$this->isHTML(true);
		$this->Body = $html;
		return $this;
	}
	public function HtmlTemplate(string $template, string $title, string $message): self
	{
		$path = Path::Setting(Config::Get('util.email.template_path', 'integration/email/')) . $template . '.php';
		if (!file_exists($path)) {
			throw new Exception('Email template not found: ' . $template);
		}
		$title = htmlspecialchars($title);
		$message = nl2br($message, false);
		ob_start();
		include $path;
		$content = ob_get_clean();
		$this->Html($content);
		$this->Subject = $title;
		$this->AltBody = strip_tags(preg_replace('/<br\s*\/?>/i', \PHP_EOL, $message));
		return $this;
	}
	static public function FromConfig(bool $exceptions = true): self
	{
		return new self(
			from: Config::Get('util.email.from', null),
			from_name: Config::Get('util.email.from_name', null),
			host: Config::Get('util.email.host', null),
			username: Config::Get('util.email.username', null),
			password: Config::Get('util.email.password', null),
			smtp_secure: Config::Get('util.email.smtp_secure', true),
			port: Config::Get('util.email.port', null),
			exceptions: $exceptions,
		);
	}
}
