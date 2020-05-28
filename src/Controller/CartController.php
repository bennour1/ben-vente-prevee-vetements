<?php

namespace App\Controller;

use App\Service\Cart\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * affichage de mon panier avec la récuperation de ma session
     * @Route("/cart", name="cart_index")
     */
    /*
    public function index(SessionInterface $session)
    {
        // récuperation de mon pannier
        $panier = $session->get('panier', []);
        // création un tableau qui contient plus d'information à partir de mon tableau panier
        $panierAvecInfo = [];
        $repo = $this->getDoctrine()->getRepository(Variante::class);
        foreach ( $panier as $id => $quantite ){
            $panierAvecInfo[] = [
                'produit' => $repo->find($id),
                'quantite' => $quantite,
            ];
        }
        //dd($panierAvecInfo);
        //dd($panierAvecInfo[0]['produit']);
        // Calcul du total globale
        $total = 0;
        foreach ($panierAvecInfo as $item){
            // calcul du total de chaque produit
            $totalItem = $item['produit']->getArticle()->getPrix() * $item['quantite'];
            // total globale
            $total += $totalItem;
        }
        return $this->render('cart/index.html.twig', [
            'items' => $panierAvecInfo,
            'total' => $total
        ]);
    }
    */
    public function index(CartService $cartService)
    {
        // Information pannier
        $panierAvecInfo = $cartService->recupereInformationPannier();
        // Calcul du total globale
        $total = $cartService->calculerTotalPannier();
        return $this->render('cart/index.html.twig', [
            'items' => $panierAvecInfo,
            'total' => $total
        ]);
    }
    /**
     * Ajouter un produit dans mon pannier
     * @Route("/cart/add/{id}", name="cart_add")
     */
    /*
    public function ajouter($id, SessionInterface $session){
    //public function ajouter($id, Request $request){
        // acceder à la session avec symfony avec via une requette (HttpFoundation)
        //$session = $request->getSession();
        // Definition de mon pannier. Si je n'ai pas de panier mon panier est un tableau vide
        $panier = $session->get('panier', []);
        // Ajouter le produit. Si le produit existe dans mon panier j'ajoute ++1
        if( !empty($panier[$id])){
            $panier[$id]++;
        } else{
            $panier[$id] = 1;
        }
        // remettre mon pannier dans ma session
        $session->set('panier', $panier);
        // dump and dai
        // dd($session->get('panier'));
        return $this->redirectToRoute("cart_index");
    }
    */
    public function add($id, CartService $cartService){
        // Ajouter un produit
        $cartService->ajouterUnProduitDansPannier($id);
        // Retoure à mon pannier
        return $this->redirectToRoute("cart_index");
    }
    /**
     * Supprimer un produit de mon panier
     * @Route("/cart/remove/{id}", name="cart_remove")
     */
    /*
    public function supprimer($id, SessionInterface $session){
        // récuperation panier
        $panier = $session->get('panier', []);
        // Je supprime le produit sélctionner s'il existe dans mon panier
        if ( !empty($panier[$id])){
            unset($panier[$id]);
        }
        // Génération de la nouvelle pannier
        $session->set('panier', $panier);
        // Retourner à la liste de mon panier
        return $this->redirectToRoute("cart_index");
    }
    */
    public function remove($id, CartService $cartService){
        // Supprimer un produit
        $cartService->supprimerUnProduitDeMonPannier($id);
        // Retourner à la liste de mon panier
        return $this->redirectToRoute("cart_index");
    }
}
