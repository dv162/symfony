<?php

namespace App\Controller;
use App\Entity\Book;
use App\Form\BookType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class BookController extends AbstractController
{
    /**
     * @Route("/book", name="book_list")
     */
    public function listAction()
    {
        $books = $this->getDoctrine()
            ->getRepository('App\Entity\Book')
            ->findAll();
        return $this->render('book/index.html.twig', [
            'books' => $books
        ]);
    }
    /**
     * @Route("/book/views/{id}", name="book_views")
     */
    public function detailsAction($id)
    {
        $book = $this->getDoctrine()
            ->getRepository('App\Entity\Book')
            ->find($id);

        return $this->render('book/views.html.twig', [
            'book' => $book
        ]);
    }

    /**
     * @Route("/book/delete/{id}", name="book_delete")
     */
    public function deleteAction($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $book = $entityManager->getRepository('App\Entity\Book')->find($id);
        $entityManager->remove($book);
        $entityManager->flush();

        return $this->redirectToRoute('book_list');
    }
    /**
    * @Route("/book/create", name="book_create", methods={"GET","POST"})
    */
    public function createAction(Request $request)
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        
        if ($this->saveChanges($form, $request, $book)) {
            $this->addFlash(
                'notice',
                'Book Added'
            );
            
            return $this->redirectToRoute('book_list');
        }
        
        return $this->render('book/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function saveChanges($form, $request, $book)
    {
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $book->setName($request->request->get('book')['name']);
            $book->setAuthorId($request->request->get('book')['authorid']);
            $book->setPrice($request->request->get('book')['price']);
            $book->setQuantity($request->request->get('book')['quantity']);
            $em = $this->getDoctrine()->getManager();
            $em->persist($book);
            $em->flush();
            
            return true;
        }
        return false;
    }
    

    /**
    * @Route("/book/edit/{id}", name="book_edit")
    */
    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $book = $em->getRepository('App\Entity\Book')->find($id);
        
        $form = $this->createForm(BookType::class, $book);
        
        if ($this->saveChanges($form, $request, $book)) {
            $this->addFlash(
                'notice',
                'Book Edited'
            );
            return $this->redirectToRoute('book_list');
        }
        
        return $this->render('book/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
