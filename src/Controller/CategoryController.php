<?php

namespace App\Controller;
use App\Entity\Category;
use App\Form\CategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
class CategoryController extends AbstractController
{
    /**
     * @Route("/category", name="category_list")
     */
    public function listAction()
    {
        $category = $this->getDoctrine()
            ->getRepository('App\Entity\Category')
            ->findAll();
        return $this->render('category/index.html.twig', [
            'category' => $category
        ]);
    }
    /**
    * @Route("/category/create", name="category_create", methods={"GET","POST"})
    */
    public function createAction(Request $request)
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        
        if ($this->saveChanges($form, $request, $category)) {
            $this->addFlash(
                'notice',
                'Category Added'
            );
            
            return $this->redirectToRoute('category_list');
        }
        
        return $this->render('category/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function saveChanges($form, $request, $category)
    {
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $category->settype($request->request->get('category')['type']);
            $category->setbookId($request->request->get('category')['bookid']);
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();
            
            return true;
        }
        return false;
    }

    /**
     * @Route("/views/{id}", name="views_category")
     */
    public function detailsAction($id)
    {
        $category = $this->getDoctrine()
            ->getRepository('App\Entity\Category')
            ->find($id);

        return $this->render('category/views.html.twig', [
            'category' => $category
        ]);
    }

    /**
     * @Route("/category/delete/{id}", name="delete_category")
     */
    public function deleteAction($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $category = $entityManager->getRepository('App\Entity\Category')->find($id);
        $entityManager->remove($category);
        $entityManager->flush();

        return $this->redirectToRoute('category_list');
    }

    /**
    * @Route("/category/edit/{id}", name="edit_category")
    */
    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository('App\Entity\Category')->find($id);
        
        $form = $this->createForm(CategoryType::class, $category);
        
        if ($this->saveChanges($form, $request, $category)) {
            $this->addFlash(
                'notice',
                'category Edited'
            );
            return $this->redirectToRoute('category_list');
        }
        
        return $this->render('category/edit.html.twig', [
            'form' => $form->createView()
        ]);

    }
}

