<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Post;
use Symfony\Component\HttpFoundation\Response;

/**
 * Post controller.
 *
 * @Route("/post")
 */
class PostController extends Controller
{
    /**
     * Lists all Post entities.
     *
     * @Route("/", name="post_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $posts = $em->getRepository('AppBundle:Post')->findAll();

        return $this->render('post/index.html.twig', array(
            'posts' => $posts,
        ));
    }

    /**
     * Creates a new Post entity.
     *
     * @Route("/new", name="post_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @Security("has_role('ROLE_USER')")
     * @return RedirectResponse|Response
     */
    public function newAction(Request $request)
    {
        $post = new Post();
        $form = $this->createForm('AppBundle\Form\Type\PostType', $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('post_show', array('id' => $post->getId()));
        }

        return $this->render('post/new.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Post entity.
     *
     * @Route("/{id}", name="post_show")
     * @Method("GET")
     * @param Post $post
     * @return Response
     */
    public function showAction(Post $post)
    {
        $deleteForm = $this->createDeleteForm($post);

        return $this->render('post/show.html.twig', array(
            'post' => $post,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Post entity.
     *
     * @Route("/{id}/edit", name="post_edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param Post $post
     * @Security("has_role('ROLE_USER')")
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, Post $post)
    {
        $deleteForm = $this->createDeleteForm($post);
        $editForm = $this->createForm('AppBundle\Form\Type\PostType', $post);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('post_index');
        }

        return $this->render('post/edit.html.twig', array(
            'post' => $post,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Post entity.
     *
     * @Route("/{id}", name="post_delete")
     * @Method("DELETE")
     * @param Request $request
     * @param Post $post
     * @Security("has_role('ROLE_USER')")
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, Post $post)
    {
        $form = $this->createDeleteForm($post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($post);
            $em->flush();
        }

        return $this->redirectToRoute('post_index');
    }

    /**
     * Creates a form to delete a Post entity.
     *
     * @param Post $post The Post entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Post $post)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('post_delete', array('id' => $post->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
