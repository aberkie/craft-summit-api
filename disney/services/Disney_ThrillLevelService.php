<?php

/*
Title: Disney Thrill Level Service
File: disney/servies/Disney_ThrillLevelService.php
Description: Service to get thrill level categories by title
Author: Aaron Berkowitz, github.com/aberkie/, @asberk
*/

namespace Craft;

class Disney_ThrillLevelService extends BaseApplicationComponent
{

	public function getThrillLevel($title)
	{
		//Build cache key
		$cache_key = "disney_thrill_".urlencode($title);
		$return = "";

		//Get cache, if it exists
        //http://buildwithcraft.com/classreference/services/CacheService#get-detail
		$cache = craft()->cache->get($cache_key);

		//If cache does not exist, run the process
		if(! $cache )
		{
			$criteria = craft()->elements->getCriteria(ElementType::Category);
			$criteria->title = $title;
			$criteria->group = 'thrillLevel';
			$criteria->limit = 500;
			$interests = $criteria->find();

			$return = $interests;

			//Cache for one hour.
            //http://buildwithcraft.com/classreference/services/CacheService#set-detail
			craft()->cache->set($cache_key, $return, 3600);

		} else {
			//If cache does exists, return it and skip the above process
			$return = $cache;
		}
		
		return $return;
	}
}