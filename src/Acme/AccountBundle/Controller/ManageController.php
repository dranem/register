<?php
// src/Acme/AccountBundle/Controller/ManageController.php
namespace Acme\AccountBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class ManageController
{
	private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function isloginAction()
    {
    	$user = $this->session->get('uid');
    	//$user = $this->container->get('session')->get('uid');
        if($user) 
            return true;
        else
            return false;
    }
}