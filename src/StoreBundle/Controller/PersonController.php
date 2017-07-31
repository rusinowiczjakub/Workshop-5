<?php

namespace StoreBundle\Controller;

use StoreBundle\Entity\Person;
use StoreBundle\Form\AddressType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use StoreBundle\Entity\Address;
/**
 * @Route("/person")
 */
class PersonController extends Controller
{


    public function provideForm($person)
    {
        return $this->createFormBuilder($person)
            ->setAction($this->generateUrl('newPerson'))
            ->setMethod("POST")
            ->add('name', TextType::class)
            ->add('surname', TextType::class)
            ->add('description', TextType::class)
            ->getForm();

    }

    public function provideFormEdit($person)
    {
        return $this->createFormBuilder($person)
            ->add('name', TextType::class)
            ->add('surname', TextType::class)
            ->add('description', TextType::class)
            ->getForm();

    }

    /**
     * @Route("/new", name="newPerson")
     *
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

    /**
     * @Route("/{id}/modify", name="edit")
     *
     */
    public function editPersonAction(Request $request, $id)
    {
        $repository = $this->getDoctrine()->getRepository('StoreBundle:Person');
        $person = $repository->find($id);
        $address = $this->getDoctrine()->getRepository('StoreBundle:Address')->find($id);

//        $person->setAddress($address->getId());

        $formAddress = $this->createForm(AddressType::class, $address);
        $form = $this->provideFormEdit($person);
        $form->handleRequest($request);
        $formAddress->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $person = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($person);
            $em->flush();

            return $this->redirectToRoute('show_person', ['id'=>$id]);
        }

        if ($formAddress->isValid() && $formAddress->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($address);
            $em->flush();
            return $this->redirectToRoute('show_person', ['id' => $id]);
        }

        return $this->render('StoreBundle:Person:modifyPerson.html.twig', ['form'=>$form->createView(), 'id'=>$id, 'person'=>$person, 'formAddress'=>$formAddress->createView()]);
    }
    /**
     * @Route("/", name="allPeople")
     */
    public function showAllPeopleAction(){
        $repository = $this->getDoctrine()->getRepository('StoreBundle:Person');
        $allPeople = $repository->findAll();

        return $this->render('StoreBundle:Person:allPersons.html.twig', ['allPeople' => $allPeople]);
    }

    /**
     * @Route("/{id}/delete", name="delete")
     */
    public function deletePersonAction($id)
    {
        $repository = $this->getDoctrine()->getRepository('StoreBundle:Person');
        $person = $repository->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($person);
        $em->flush();

        return $this->redirectToRoute('allPeople');
    }

    /**
     * @Route("/{id}", name="show_person")
     */
    public function showPersonAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $person = $this->getDoctrine()->getRepository('StoreBundle:Person')->find($id);
//        var_dump($person);

        $address = $em->getRepository('StoreBundle:Address')->getPersonAddress($person->getAddress());

        return $this->render('StoreBundle:Person:onePerson.html.twig', array('person' => $person, 'address'=>$address));
    }


}
