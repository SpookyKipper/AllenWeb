<?php

namespace Allen\Basic\Element\Enum;

enum Allow: string
{
	case Accelerometer = 'accelerometer';
	case Autoplay = 'autoplay';
	case ClipboardWrite = 'clipboard-write';
	case EncryptedMedia = 'encrypted-media';
	case Gyroscope = 'gyroscope';
	case PictureInPicture = 'picture-in-picture';
	case WebShare = 'web-share';
}
