<?php

/*
Title: Disney Controller
File: disney/controllers/DisneyController.php
Description: Controller to route API Requests
Author: Aaron Berkowitz, github.com/aberkie/, @asberk
*/

namespace Craft;

class DisneyController extends BaseController
{

    // This is needed to allow unauthenticated access
    // http://buildwithcraft.com/docs/plugins/controllers#allowing-anonymous-access-to-actions
	protected $allowAnonymous = true;

    // Action method for attractions
    // http://buildwithcraft.com/docs/plugins/controllers
    public function actionAttractions()
    {
        //Start the clock! 
        $start = microtime(true);

        //Grab these to create cache key
        $title = craft()->request->getParam('title');
        $location = craft()->request->getParam('location');
        $thrill_level = craft()->request->getParam('thrill_level');
        $interest = craft()->request->getParam('interest');
        $testimonial_state = craft()->request->getParam('testimonial_state');
        $age = craft()->request->getParam('age');

        //Build cache key
        $cache_key = "disney_actionAttractions_".$title.$location.$thrill_level.$interest.$testimonial_state.$age;
        $return_data = "";

        //Get cache, if it exists
        //http://buildwithcraft.com/classreference/services/CacheService#get-detail
        $cache = craft()->cache->get($cache_key);

        //If cache does not exist, run the process
        if( ! $cache )
        {
            $status_code = "";
            $status_text = "";

            //Go fetch attraction entries from service
            $entries = craft()->disney_attraction->getAttractions();

            //Check status code
            if($entries['status_code'] != 200)
            {
                //If it is no good, show errors and stop
                $this->returnJson($entries);
            } 

            //Set up return array
            $return = array();

            //Set up data array
            $data = array();

            //Loop through entries to get testimonials
            foreach($entries['entries'] as $entry)
            {
                //Set up testimonial relation
                $relations = array('and');
                $relations[] = array(
                    'field' => 'attraction',
                    'targetElement' => $entry
                );

                //Get testimonials from testimonial service
                $testimonial_entries = craft()->disney_testimonial->getTestimonials(array('relatedTo' => $relations));
                $testionials = array();

                //Grab the data we want to return to the user
                foreach($testimonial_entries as $testimonial)
                {
                    $testionials[] = array(
                        'guest_name' => $testimonial->guestName,
                        'guest_home_state' => $testimonial->homeState,
                        'content' => $testimonial->testimonialContent
                    );
                }

                //Set up thrill levels array, choosing what data we want to return to users
                $thirll_levels = array();
                foreach($entry->thrillLevel as $level)
                {
                    $thirll_levels[] = array(
                        $level->title
                    );
                }

                //Set up interests array, choosing what data we want to return to users
                $interests = array();
                foreach($entry->interest as $interest)
                {
                    $interests[] = array(
                        $interest->title
                    );
                }

                //Set up ages array, choosing what data we want to return to users
                $ages = array();
                foreach($entry->ages as $age)
                {
                    $ages[] = array(
                        $age->title
                    );
                }

                //Get location from attraction entry. All we want to return is the title.
                if($entry->location[0])
                {
                    $location = $entry->location[0]->title;
                }
                
                $data[] = array(
                    'title' => $entry->title,
                    'location' => $location,
                    'thrill_levels' => $thirll_levels,
                    'interests' => $interests,
                    'ages' => $ages,
                    'testimonials' => $testionials
                );
            }  

            //Set return data as array
            $return_data = array('data' => $data, 'entries' => $entries); 

            //Cache for one hour.
            //http://buildwithcraft.com/classreference/services/CacheService#set-detail
            craft()->cache->set($cache_key, $return_data, 3600);    

            
        } else {
            //If cache does exists, return it and skip the above process
            $return_data = $cache;
        }

        //Stop the clock the clock!
        $end = microtime(true);
        $measurements = array(
            'start' => $start,
            'end' => $end,
            'execution_time' => $end - $start
        );

        //Build return array to include status, measurements, and data
        $return = array(
            'status_code' => $return_data['entries']['status_code'],
            'measurements' => $measurements,
            'status_text' => $return_data['entries']['status_text'],
            'data' => $return_data['data']
        );

        //Return as JSON document
        //http://buildwithcraft.com/docs/plugins/controllers#this-gt-returnJson
        $this->returnJson($return);
    }

}