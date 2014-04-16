<?php

// src/tuto/WelcomeBundle/Command/GreetCommand.php
namespace tuto\WelcomeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Eko\FeedBundle\Feed\Reader;
use tuto\WelcomeBundle\Entity\Article;

class GreetCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('demo:greet')
            ->setDescription('get RSS feed')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {    
        $reader = new Reader;
    	$feed = $reader->load('http://www.mein-elektroauto.com/feed/')->get();
    	
    	$date = new \DateTime('now');
    	$date=$date->sub(new \DateInterval('PT1H'));
    	
    	$em = $this->getContainer()->get('doctrine.orm.entity_manager');
		$query = $em->createQuery(
		    ' SELECT a FROM tutoWelcomeBundle:Article a WHERE a.pubDate =
		    	( SELECT MAX(b.pubDate) FROM tutoWelcomeBundle:Article b ) '
		)->setMaxResults(1);
		$last_date = $query->getSingleResult()->getPubDate();
		
    	foreach ($feed as $item)
    	{
    		// Création de l'entité
			$article = new Article();
			
	    	// On récupère l'EntityManager
			$em = $this->getContainer()->get('doctrine.orm.entity_manager');
			$article->setTitle($item->getTitle());
			$article->setLink($item->getLink());
			$article->setPubDate($item->getDateModified());		
			$article->setDescription($item->getDescription());
			if (($date->diff($article->getPubDate())->format('%R') == '+') &&
				($last_date->diff($article->getPubDate())->format('%R') == '+'))
			{				
				// Étape 1 : On « persiste » l'entité
				$em->persist($article);
	
				// Étape 2 : On « flush » tout ce qui a été persisté avant
				$em->flush();
			}
		}
    }
}