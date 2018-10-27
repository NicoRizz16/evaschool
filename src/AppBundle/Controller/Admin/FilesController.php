<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Category;
use AppBundle\Form\CategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route("/admin211195/fichiers")
 * @Security("has_role('ROLE_ADMIN')")
 */
class FilesController extends Controller
{
    /**
     * @Route("/index", name="admin_files_index")
     */
    public function filesIndexAction(Request $request)
    {
        $sectionCRPE = Category::CRPE_SECTION;
        $sectionEcole = Category::ECOLE_SECTION;

        // replace this example code with whatever you need
        return $this->render('admin/files/index.html.twig', array(
            'sectionCRPE' => $sectionCRPE,
            'sectionEcole' => $sectionEcole
        ));
    }

    /**
     * @Route("/liste/section/{section}/categorie/{category_id}", name="admin_files_list_by_section_category", requirements={"section": "\d+"}, defaults={"section": 1, "category_id": null})
     */
    public function listBySectionAndCategory(Request $request, $section, $category_id = null)
    {
        $categoryTree = array(); // Récupérer l'arbre pour le breadcrumb null par defaut

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

        return $this->render('admin/files/list_by_section_category.html.twig', array(
            'section' => $section,
            'category' => $category_id,
            'sectionCRPE' => Category::CRPE_SECTION,
            'sectionEcole' => Category::ECOLE_SECTION,
            'categoriesList' => $categoriesList,
            'categoryTree' => $categoryTree
        ));
    }

    /**
     * @Route("/categorie/ajouter/{section}/{parent_category_id}", name="admin_files_category_add", requirements={"section": "\d+"}, defaults={"section": 1, "parent_category_id": null})
     */
    public function addCategory(Request $request, $section, $parent_category_id = null)
    {
        $category = new Category();
        if($section == Category::CRPE_SECTION || $section == Category::ECOLE_SECTION){
            $category->setSection($section);
        } else {
            throw $this->createNotFoundException('Cette section n\'existe pas.');
        }

        if(isset($parent_category_id)){
            $parentCategory = $this->getDoctrine()->getManager()->getRepository('AppBundle:Category')->find($parent_category_id);
            if(isset($parentCategory)){
                $category->setParent($parentCategory);
            }
        }

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            $this->addFlash('success', 'La nouvelle catégorie a bien été enregistrée !');

            return $this->redirectToRoute('admin_files_list_by_section_category', array('section' => $section, 'category_id' => $parent_category_id));
        }

        return $this->render('admin/files/add_category.html.twig', array(
            'section' => $section,
            'parent_category_id' => $parent_category_id,
            'sectionCRPE' => Category::CRPE_SECTION,
            'sectionEcole' => Category::ECOLE_SECTION,
            'form' => $form->createView()
        ));
    }


}
