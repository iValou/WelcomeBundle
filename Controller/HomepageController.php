<?php

namespace tuto\WelcomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Eko\FeedBundle\Feed\Reader;
use tuto\WelcomeBundle\Entity\Article;

class HomepageController extends Controller
{
    public function indexAction()
    {        
    	$data = $this->feedAction();
    	$this->ajouterAction();
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
    
    public function ajouterAction()
	{
		// Création de l'entité
		$article = new Article();
		$article->setTitle('Mon dernier weekend');
		$article->setLink('Bibi');
/* 		$article->setPubDate($this->$data['entries'][0]['dateModified']); */		
		$article->setDescription('Bibi');

		
		// On récupère l'EntityManager
		$em = $this->getDoctrine()->getManager();
		
		// Étape 1 : On « persiste » l'entité
		$em->persist($article);
		
		// Étape 2 : On « flush » tout ce qui a été persisté avant
		$em->flush();
		
		// Reste de la méthode qu'on avait déjà écrit
		if ($this->getRequest()->getMethod() == 'POST')
		{
			$this->get('session')->getFlashBag()->add('info', 'Article bien enregistré');
			return $this->redirect( $this->generateUrl('sdzblog_voir', 
				array('id' => $article->getId())) );
		}
	}
}