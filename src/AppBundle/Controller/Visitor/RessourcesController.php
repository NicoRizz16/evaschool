<?php

namespace AppBundle\Controller\Visitor;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RessourcesController extends Controller
{
    /**
     * @Route("/ressources", name="ressources_index")
     */
    public function indexAction(Request $request)
    {
        return $this->render('visitor/ressources/index.html.twig');
    }
}
