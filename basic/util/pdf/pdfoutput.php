<?php

namespace Allen\Basic\Util\Pdf;

enum PdfOutput
{
	case ToString;
	case ToFile;
	case ToBrowser;
}
