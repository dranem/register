<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/app/", name="homepage")
     */
    public function indexAction()
    {
    	exit;
        return $this->render('default/index.html.twig');
    }
}
