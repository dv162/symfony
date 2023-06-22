<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class AuthorController extends AbstractController
{
    /**
     * @Route("/author", name="author_list")
     */
    public function listAction()
    {
        $author = $this->getDoctrine()
            ->getRepository('App\Entity\Author')
            ->findAll();
        return $this->render('author/index.html.twig', [
            'author' => $author
        ]);
    }
/**
    * @Route("/author/create", name="author_create", methods={"GET","POST"})
    */
    public function createAction(Request $request)
    {
        $author = new Author();
        $form = $this->createForm(AuthorType::class, $author);
        
        if ($this->saveChanges($form, $request, $author)) {
            $this->addFlash(
                'notice',
                'Author Added'
            );
            
            return $this->redirectToRoute('author_list');
        }
        
        return $this->render('author/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function saveChanges($form, $request, $author)
    {
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $author->setName($request->request->get('author')['name']);
            $author->setAddress($request->request->get('author')['address']);
            $author->setBookId($request->request->get('author')['bookid']);
            $em = $this->getDoctrine()->getManager();
            $em->persist($author);
            $em->flush();
            
            return true;
        }
        return false;
    }

    /**
     * @Route("/views/{id}", name="views_author")
     */
    public function detailsAction($id)
    {
        $author = $this->getDoctrine()
            ->getRepository('App\Entity\Author')
            ->find($id);

        return $this->render('author/views.html.twig', [
            'author' => $author
        ]);
    }

    /**
     * @Route("/author/delete/{id}", name="delete_author")
     */
    public function deleteAction($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $author = $entityManager->getRepository('App\Entity\Author')->find($id);
        $entityManager->remove($author);
        $entityManager->flush();

        return $this->redirectToRoute('author_list');
    }

    /**
    * @Route("/author/edit/{id}", name="edit_author")
    */
    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $author = $em->getRepository('App\Entity\Author')->find($id);
        
        $form = $this->createForm(AuthorType::class, $author);
        
        if ($this->saveChanges($form, $request, $author)) {
            $this->addFlash(
                'notice',
                'author Edited'
            );
            return $this->redirectToRoute('author_list');
        }
        
        return $this->render('author/edit.html.twig', [
            'form' => $form->createView()
        ]);

    }
}
