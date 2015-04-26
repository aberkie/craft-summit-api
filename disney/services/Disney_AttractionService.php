<?php

/*
Title: Disney Attraction Service
File: disney/servies/Disney_AttractionService.php
Description: Service to get attractions
Author: Aaron Berkowitz, github.com/aberkie/, @asberk
*/

namespace Craft;

class Disney_AttractionService extends BaseApplicationComponent
{
	public function getAttractions($params = null)
	{
		//Grab possible parameters from URL
		//http://buildwithcraft.com/classreference/services/HttpRequestService#getParam-detail

		$title = craft()->request->getParam('title');
		$location = craft()->request->getParam('location');
		$thrill_level = craft()->request->getParam('thrill_level');
		$interest = craft()->request->getParam('interest');
		$testimonial_state = craft()->request->getParam('testimonial_state');
		$age = craft()->request->getParam('age');

		//Build Cache Key
		$cache_key = "disney_attractions_".$title.$location.$thrill_level.$interest.$testimonial_state.$age;
		$return = "";

		//Get cache, if it exists
        //http://buildwithcraft.com/classreference/services/CacheService#get-detail
		$cache = craft()->cache->get($cache_key);

		//If cache does not exist, run the process
		if(! $cache )
		{

			$locations = null;
			//If location parameter is set, call location service to get locations
			if($location)
			{
				$locations = craft()->disney_location->getLocation($location);
			}

			$thrillLevels = null;
			//If thrill level parameter is set, call thrillLevel service to get thrill levels
			if($thrill_level)
			{
				$thrillLevels = craft()->disney_thrillLevel->getThrillLevel($thrill_level);
			}
			

			$interests = null;
			//If interest parameter is set, call interest service to get interests
			if($interest)
			{
				$interests = craft()->disney_interest->getInterest($interest);
			}

			$ages = null;
			//If age level parameter is set, call age service to get ages
			if($age)
			{
				$ages = craft()->disney_age->getAges($age);
			}

			$testimonials = null;
			//If testimonial parameter is set, call testimonial service to get testimonials
			if($testimonial_state)
			{
				$testimonials = craft()->disney_testimonial->getTestimonials(array('homeState' => $testimonial_state));
			}



			//Set up available relations
			//http://buildwithcraft.com/docs/relations
			$relations = array('and');

			//For each possible relation, add an array to our $relations array
			//specifying the field and either a target element or source element
			//http://buildwithcraft.com/docs/relations#the-relatedTo-param
			if(count( $locations ))
			{
				$relations[] = array(
					'field' => 'location',
					'targetElement' => $locations
				);
			}

			if(count( $thrillLevels ))
			{
				$relations[] = array(
					'field' => 'thrillLevel',
					'targetElement' => $thrillLevels
				);
			}

			if(count($interests))
			{
				$relations[] = array(
					'field' => 'interest',
					'targetElement' => $interests
				);
			}

			if(count($ages))
			{
				$relations[] = array(
					'field' => 'ages',
					'targetElement' => $ages
				);
			}

			if(count($testimonials))
			{
				//Since testimonials are linked to attractions from the testimonial entry, 
				//the testimonials are the source element
				$relations[] = array(
					'field' => 'attraction',
					'sourceElement' => $testimonials
				);
			}

			//Time to get Entries!
			$criteria = craft()->elements->getCriteria(ElementType::Entry);
			$criteria->section = 'attractions';
			//Check if there is a title parameter set
			if($title != '')
			{
				$criteria->title = $title;
			}
			
			//If there are relations is our relations array, set them to the relatedTo parameter
			if( count($relations) > 1 )
			{
				$criteria->relatedTo = $relations;
			}
				
			$entries = $criteria->find();

			//Set up return array
			//If there was an error, status_code should be set to something else
			$return = array(
				'status_code' => 200,
				'status_text' => 'OK',
				'entries' => $entries
			);
           
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