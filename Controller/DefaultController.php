<?php

namespace Ioni\PayzenBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('IoniPayzenBundle:Default:index.html.twig');
    }
}
