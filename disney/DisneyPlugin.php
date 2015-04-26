<?php

/*
Title: Disney Plugin Main File
File: disney/DisneyPlugin.php
Description: Main file for plugin used in 2015 Craft CMS Summit
Author: Aaron Berkowitz, github.com/aberkie/, @asberk
*/

namespace Craft;

class DisneyPlugin extends BasePlugin
{
	function getName()
	{
		return Craft::t('Disney Plugin');
	}
	
	function getVersion()
	{
		return '1.0';
	}
	
	function getDeveloper()
	{
		return 'Aaron Berkowitz';
	}
	
	function getDeveloperUrl()
	{
		return 'https://github.com/aberkie';
	}

	public function hasCpSection()
	{
		return false;
	}
}