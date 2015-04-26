<?php

/*
Title: Disney Location Service
File: disney/servies/Disney_LocationService.php
Description: Service to get age locations by title
Author: Aaron Berkowitz, github.com/aberkie/, @asberk
*/

namespace Craft;

class Disney_LocationService extends BaseApplicationComponent
{
	public function getLocation($title)
	{
		//Build cache key
		$cache_key = "disney_locations_".urlencode($title);
		$return = "";

		//Get cache, if it exists
        //http://buildwithcraft.com/classreference/services/CacheService#get-detail
		$cache = craft()->cache->get($cache_key);

		//If cache does not exist, run the process
		if(! $cache )
		{
			$criteria = craft()->elements->getCriteria(ElementType::Entry);
			$criteria->title = $title;
			$criteria->section = 'locations';
			$criteria->limit = 500;
			$locations = $criteria->find();

			$all_locations = $locations;

			//Since locations are in a structure, return all children locations as well
			foreach($locations as $location)
			{
				$criteria = craft()->elements->getCriteria(ElementType::Entry);
				$criteria->descendantOf = $location;
				$criteria->section = 'locations';
				$criteria->limit = 100;
				$descendants = $criteria->find();

				$all_locations = array_merge($all_locations, $descendants);

			}

			$return = $all_locations;

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
