<?php

/*
Title: Disney Testimonial Service
File: disney/servies/Disney_TestimonialService.php
Description: Service to get testimonials by params
Author: Aaron Berkowitz, github.com/aberkie/, @asberk
*/

namespace Craft;

class Disney_TestimonialService extends BaseApplicationComponent
{

	public function getTestimonials($params)
	{

		//Build cache key
		$cache_key = "disney_testimonials_".urlencode(serialize($params));
		$return = "";

		//Get cache, if it exists
        //http://buildwithcraft.com/classreference/services/CacheService#get-detail		
		$cache = craft()->cache->get($cache_key);

		//If cache does not exist, run the process
		if(! $cache ) 
		{
			$criteria = craft()->elements->getCriteria(ElementType::Entry);
			$criteria->section = 'guestTestimonials';
			$criteria->limit = 500;

			foreach($params as $key=>$val)
			{
				$criteria->$key = $val;
			}

			$testimonials = $criteria->find();

			$return = $testimonials;

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