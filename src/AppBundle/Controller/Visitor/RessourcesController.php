<?php

namespace AppBundle\Controller\Visitor;

use AppBundle\Entity\Category;
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
        $sectionCRPE = Category::CRPE_SECTION;
        $sectionEcole = Category::ECOLE_SECTION;

        return $this->render('visitor/ressources/index.html.twig', array(
            'sectionCRPE' => $sectionCRPE,
            'sectionEcole' => $sectionEcole
        ));
    }
}
