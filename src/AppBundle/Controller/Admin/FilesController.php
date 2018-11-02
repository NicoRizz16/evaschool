<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Category;
use AppBundle\Entity\FileUploaded;
use AppBundle\Form\CategoryType;
use AppBundle\Form\FileUploadedType;
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

        return $this->render('admin/files/list_by_section_category.html.twig', array(
            'section' => $section,
            'category' => $category_id,
            'parent' => $parent,
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

    /**
     * @Route("/categorie/modifier/{category_id}/{section}/{parent_category_id}", name="admin_files_category_edit", requirements={"section": "\d+", "category_id": "\d+"}, defaults={"section": 1, "parent_category_id": null})
     */
    public function editCategory(Request $request, $category_id, $section, $parent_category_id = null)
    {
        if(!($section == Category::CRPE_SECTION || $section == Category::ECOLE_SECTION)){
            throw $this->createNotFoundException('Cette section n\'existe pas.');
        }

        $category = $this->getDoctrine()->getManager()->getRepository('AppBundle:Category')->find($category_id);
        if(!isset($category)){
            throw $this->createNotFoundException('Cette catégorie n\'existe pas.');
        }

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            $this->addFlash('success', 'La modification de la catégorie a bien été enregistrée !');

            return $this->redirectToRoute('admin_files_list_by_section_category', array('section' => $section, 'category_id' => $parent_category_id));
        }

        return $this->render('admin/files/edit_category.html.twig', array(
            'section' => $section,
            'parent_category_id' => $parent_category_id,
            'sectionCRPE' => Category::CRPE_SECTION,
            'sectionEcole' => Category::ECOLE_SECTION,
            'form' => $form->createView(),
            'category' => $category
        ));
    }

    /**
     * @Route("/categorie/supprimer/{category_id}/{section}/{parent_category_id}", name="admin_files_category_delete", requirements={"section": "\d+", "category_id": "\d+"}, defaults={"section": 1, "parent_category_id": null})
     */
    public function deleteCategoryAction(Request $request, $category_id, $section, $parent_category_id = null)
    {
        if(!($section == Category::CRPE_SECTION || $section == Category::ECOLE_SECTION)){
            throw $this->createNotFoundException('Cette section n\'existe pas.');
        }

        $category = $this->getDoctrine()->getManager()->getRepository('AppBundle:Category')->find($category_id);
        if(!isset($category)){
            throw $this->createNotFoundException('Cette catégorie n\'existe pas.');
        }

        $form = $this->get('form.factory')->create();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->remove($category);
            $em->flush();

            $this->addFlash('success', 'La catégorie "'.$category->getName().'" a bien été supprimée.');
            return $this->redirectToRoute('admin_files_list_by_section_category', array('section' => $section, 'category_id' => $parent_category_id));
        }

        return $this->render('admin/files/delete_category.html.twig', array(
            'section' => $section,
            'parent_category_id' => $parent_category_id,
            'sectionCRPE' => Category::CRPE_SECTION,
            'sectionEcole' => Category::ECOLE_SECTION,
            'form' => $form->createView(),
            'category' => $category
        ));
    }

    /**
     * @Route("/fichier/ajouter/{category_id}", name="admin_files_category_file_add", requirements={"category_id": "\d+"})
     */
    public function addFileUploaded(Request $request, $category_id = null)
    {
        $fileUploaded = new FileUploaded();

        $category = $this->getDoctrine()->getManager()->getRepository('AppBundle:Category')->find($category_id);
        if(!isset($category)){
            throw $this->createNotFoundException('Cette catégorie n\'existe pas.');
        }

        $form = $this->createForm(FileUploadedType::class, $fileUploaded);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $fileUploaded->setCategory($category);
            $em->persist($fileUploaded);
            $em->flush();

            $this->addFlash('success', 'Le nouveau fichier a bien été enregistrée !');

            return $this->redirectToRoute('admin_files_list_by_section_category', array('section' => $category->getSection(), 'category_id' => $category->getId()));
        }

        return $this->render('admin/files/add_file.html.twig', array(
            'category' => $category,
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/fichier/supprimer/{file_id}/parent/{category_id}", name="admin_files_file_delete", requirements={"file_id": "\d+", "category_id": "\d+"})
     */
    public function deleteFileUploadedAction(Request $request, $file_id, $category_id)
    {
        $fileUploaded = $this->getDoctrine()->getManager()->getRepository('AppBundle:FileUploaded')->find($file_id);
        if(!isset($fileUploaded)){
            throw $this->createNotFoundException('Ce fichier n\'existe pas.');
        }
        $parentCategory = $this->getDoctrine()->getManager()->getRepository('AppBundle:Category')->find($category_id);
        if(!isset($parentCategory)){
            throw $this->createNotFoundException('Cette catégorie parente n\'existe pas.');
        }

        $form = $this->get('form.factory')->create();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();


            $em->remove($fileUploaded);
            $em->flush();

            $this->addFlash('success', 'Le fichier "'.$fileUploaded->getDescription().'" a bien été supprimé.');
            return $this->redirectToRoute('admin_files_list_by_section_category', array('section' => $parentCategory->getSection(), 'category_id' => $parentCategory->getId()));
        }

        return $this->render('admin/files/delete_file.html.twig', array(
            'fileUploaded' => $fileUploaded,
            'category' => $parentCategory,
            'form' => $form->createView(),
        ));
    }

}
