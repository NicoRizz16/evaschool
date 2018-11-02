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

    /**
     * @Route("/ressources/section/{section}/categorie/{category_id}", name="ressources_list", requirements={"section": "\d+"}, defaults={"section": 1, "category_id": null})
     */
    public function listBySectionAndCategory(Request $request, $section, $category_id = null)
    {
        $categoryTree = array(); // Récupérer l'arbre pour le breadcrumb null par defaut
        $parent = null;

        if(!($section == Category::CRPE_SECTION || $section == Category::ECOLE_SECTION)){
            throw $this->createNotFoundException('Cette section n\'existe pas.');
        }
        // Récupérer la liste des catégories ayant le même parent ou aucun
        $em = $this->getDoctrine()->getManager();
        if(!isset($category_id)) {
            // il n'a pas de categorie parente
            $parent = null;
        } else {
            // Récupérer la catégorie parente
            $parentRequestResponse = $em->getRepository('AppBundle:Category')->find($category_id);
            if(isset($parentRequestResponse)){
                $parent = $parentRequestResponse;

            } else {
                throw $this->createNotFoundException('Cette catégorie n\'existe pas.');
            }
        }

        $categoriesList = $em->getRepository('AppBundle:Category')->getCategoriesBySectionAndParent($section, $parent);

        if(isset($parent)){
            // Récupérer l'arbre pour le breadcrumb
            $categoryTree[] = $parent;

            $stopSignal = false;
            while($stopSignal == false){
                if($parent->getParent() != null){
                    $parent = $em->getRepository('AppBundle:Category')->find($parent->getParent());
                    $categoryTree[] = $parent;
                } else {
                    $stopSignal = true;
                }
            }
            $categoryTree = array_reverse($categoryTree);// inversion de l'ordre du tableau pour faciliter l'affichage
        }

        return $this->render('visitor/ressources/list_by_section_category.html.twig', array(
            'section' => $section,
            'category' => $category_id,
            'parent' => $parent,
            'sectionCRPE' => Category::CRPE_SECTION,
            'sectionEcole' => Category::ECOLE_SECTION,
            'categoriesList' => $categoriesList,
            'categoryTree' => $categoryTree
        ));
    }
}
