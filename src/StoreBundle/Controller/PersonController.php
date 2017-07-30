<?php

namespace StoreBundle\Controller;

use StoreBundle\Entity\Person;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
/**
 * @Route("/person")
 */
class PersonController extends Controller
{


    public function provideForm($person)
    {
        return $this->createFormBuilder($person)
            ->setAction($this->generateUrl('createPerson'))
            ->setMethod("POST")
            ->add('name', TextType::class)
            ->add('surname', TextType::class)
            ->add('description', TextType::class)
            ->getForm();

    }

    public function provideFormEdit($person)
    {
        return $this->createFormBuilder($person)
            ->setAction($this->generateUrl('edit'))
            ->setMethod("POST")
            ->add('name', TextType::class)
            ->add('surname', TextType::class)
            ->add('description', TextType::class)
            ->getForm();

    }

    /**
     * @Route("/new", name="newPerson")
     * @Method("GET")
     */
    public function newPersonAction()
    {
        $person = new Person();
        $form = $this->provideForm($person);

        return $this->render('StoreBundle:Person:newPerson.html.twig', ['form'=>$form->createView()]);

    }

    /**
     * @Route("/create", name="createPerson")
     * @Method({"POST"})
     */

    public function createPersonAction(Request $request)
    {
        $person = new Person();
        $form = $this->provideForm($person);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $person = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($person);
            $em->flush();
            return $this->redirectToRoute('newPerson');
        }
        return $this->render('StoreBundle:Person:newPerson.html.twig', ['form'=>$form->createView()]);
    }

//    /**
//     * @Route("/{id}/modify", name="modify")
//     * @Method("GET")
//     */
//    public function editPersonFormAction($id)
//    {
//        $person = new Person();
//        $form = $this->provideForm($person);
//
//        return $this->render('StoreBundle:Person:modifyPerson.html.twig', ['id'=>$id, 'form'=>$form->createView()]);
//
//    }

    /**
     * @Route("/{id}/modify", name="edit")
     * @Method({POST")
     */
    public function editPersonAction(Request $request, $id)
    {
        $repository = $this->getDoctrine()->getRepository('StoreBundle:Person');
        $person = $repository->find($id);

        $form = $this->createFormBuilder($person)
            ->setAction($this->generateUrl('modify' ))
            ->add('name', TextType::class)
            ->add('surname', TextType::class)
            ->add('description', TextType::class)
            ->add('submit', SubmitType::class, ['label'=>'Send'])
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $person = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($person);
            $em->flush();
        }
        return $this->render(':default:form.html.twig', ['form'=>$form->createView(), 'id'=>$id, 'person'=>$person]);
    }
}
