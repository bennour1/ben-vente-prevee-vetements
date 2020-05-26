<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\InscriptionType;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Response;


class SecurityController extends AbstractController
{
    /**
     * cette fonction permet de créer et modifier un compte utilisateur
     * @Route("/security/inscription", name="security_registration")
     * @Route("/security/{id}/modification", name="security_update")
     */
    //public function inscription(Request $request, ObjectManager $manager){
    //public function inscription(Request $request, EntityManagerInterface $manager){
    //public function inscriptionEtModification(Request $request, ManagerRegistry $managerRegistry){
    public function inscriptionEtModification(Utilisateur $utilisateur = null,Request $request, ManagerRegistry $managerRegistry, UserPasswordEncoderInterface $encoder){

        // Instansiation d'un utilisateur
        if(!$utilisateur){
            $utilisateur = new Utilisateur();
        }

       // Création un formulaire
       $form = $this->createForm(InscriptionType::class, $utilisateur);

       // Annalyser la requette http
       $form->handleRequest($request);

       if($form->isSubmitted() && $form->isValid()){
           // pour encoder le 2eme mot de passe
           $hash = $encoder->encodepassword($utilisateur, $utilisateur->getMdp());
           $utilisateur->setMdp($hash);

           // s'il n y pas un compte utilisateur j'ajoute la date de création
           if(!$utilisateur->getId()){
               $utilisateur ->setActivation('ok')
                            ->setDateAjout(new \DateTime())
               ;
           }

           dump($utilisateur);
           /*
           $manager->persist($utilisateur);
           $manager->flush();
            */
           $em = $managerRegistry->getManager();
           $em->persist($utilisateur);
           $em->flush();

           // Après une inscription je me dérige vers la route login
           return $this->redirectToRoute('security_login');
       }


       return $this->render('security/inscription.html.twig', [
           'form' => $form->createView(),
           // utilisateur existe updateMode = true
           'updateMode' => $utilisateur->getId() !== null
       ]);
   }


    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/security/connexion", name="security_login")
     */
    public function login(Request $request,AuthenticationUtils $authenticationUtils): Response{
        dump($request);
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }
}
