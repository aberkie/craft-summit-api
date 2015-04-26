<?php

namespace Craft;

class DisneyVariable
{

	//Events
	public function doImport()
	{
		$return = craft()->disney_import->doImport();
		//$url = UrlHelper::getActionUrl('disney/api/attraction');
		return $return;
	}
}