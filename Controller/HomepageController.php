<?php

namespace tuto\WelcomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Eko\FeedBundle\Feed\Reader;

class HomepageController extends Controller
{
    public function indexAction()
    {        
    	$data = $this->feedAction();
        return $this->render('tutoWelcomeBundle:Homepage:index.html.twig', array('desc' => $data['entries'][0]['dateModified']->format('Y-m-d H:i:s')));
    }
    
    public function feedAction()
    {
    	$reader = new Reader;
    	$feed = $reader->load('http://www.mein-elektroauto.com/feed/')->get();
    	
    	$data = array
    	(
    		'title'			=> $feed->getTitle(),
    		'link'			=> $feed->getLink(),
		    'dateModified'	=> $feed->getDateModified(),
		    'description'	=> $feed->getDescription(),
		    'language'		=> $feed->getLanguage(),
		    'entries'		=> array(),
    	);
    	
    	foreach ($feed as $entry)
    	{
		    $edata = array(
		        'title'			=> $entry->getTitle(),
		        'description'	=> $entry->getDescription(),
		        'dateModified'	=> $entry->getDateModified(),
		        'authors'		=> $entry->getAuthors(),
		        'link'			=> $entry->getLink(),
		        'content'		=> $entry->getContent()
		    );
		    $data['entries'][] = $edata;
		}
    	
    	return $data;
    }
}