<?php

use Allen\Basic\Util\{Config, Language};
?>
<!DOCTYPE html>
<html lang="<?= Language::Get() ?>">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
	<header>
		<h1>
			<img src="<?= Config::Get('web.start.web_logo', 'https://cdn.asallenshih.tw/image/Allen.png') ?>" alt="Logo" width="32px" height="32px">
			<?= Config::Get('web.start.site_name', 'AS_Allen_Shih') ?>
			<?= Language::Output([
				'zh-Hant-TW' => '電子郵件通知',
				'en-US' => ' Email Notification',
			]) ?>
		</h1>
	</header>
	<hr>
	<main>
		<h2><?= $title ?></h2>
		<?= $message ?>
	</main>
	<hr>
	<footer>
		<p><?= Language::Output([
				'zh-Hant-TW' => '收到電子郵件時，請注意「寄件人」、「超連結開啟後所屬網域拼字」，以免受騙。',
				'en-US' => 'When receiving an email, please pay attention to the "sender" and the "domain spelling after opening the hyperlink" to avoid being deceived.',
			]) ?><br><?= Language::Output([
							'zh-Hant-TW' => '超連結的網域可能因為電子郵件追蹤而改變，請以網頁載入後畫面為準。',
							'en-US' => 'The domain of the hyperlink may change due to email tracking, please refer to the page after loading.',
						]) ?></p>
		<p>&copy; <?= date('Y') + Language::YearOffset() ?> <?= Config::Get('web.end.author', 'AS_Allen_Shih') ?></p>
	</footer>
	<style>
		h1 {
			font-size: 32px;
		}

		h2 {
			font-size: 24px;
		}

		p {
			font-size: 16px;
		}
	</style>
</body>

</html>