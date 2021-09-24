<?php

namespace App\Controller\Admin;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// cette annotation me permet de securiser toutes les routes du controller qui prendront pour prefixe
//  /admin/categorie. Seul l'admin y aura accés
#[Route('/admin/categorie')]
class CategorieController extends AbstractController
{
    // j'ajoute admin devant categorie_index, pour pouvoir les visualiser
    #[Route('/', name: 'admin_categorie_index', methods: ['GET'])]
    public function index(CategorieRepository $categorieRepository): Response
    {
        return $this->render('administrateur/admin_categorie/index.html.twig', [
            'categories' => $categorieRepository->findAll(),
        ]);
    }
    // j'ajoute admin devant categorie_new, pour pouvoir les créer
    #[Route('/new', name: 'admin_categorie_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($categorie);
            $entityManager->flush();

            return $this->redirectToRoute('admin_categorie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('administrateur/admin_categorie/new.html.twig', [
            'categorie' => $categorie,
            'form' => $form,
        ]);
    }
    // j'ajoute admin devant categorie_show, pour pouvoir les visualiser et ensuite les modifier
    #[Route('/{id}', name: 'admin_categorie_show', methods: ['GET'])]
    public function show(Categorie $categorie): Response
    {
        return $this->render('administrateur/admin_categorie/show.html.twig', [
            'categorie' => $categorie,
        ]);
    }
    // j'ajoute admin devant categorie_edit, pour pouvoir les modfier
    #[Route('/{id}/edit', name: 'admin_categorie_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Categorie $categorie): Response
    {
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_categorie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('administrateur/admin_categorie/edit.html.twig', [
            'categorie' => $categorie,
            'form' => $form,
        ]);
    }
    // j'ajoute admin devant categorie_delete, pour pouvoir les supprimer
    #[Route('/{id}', name: 'admin_categorie_delete', methods: ['POST'])]
    public function delete(Request $request, Categorie $categorie): Response
    {
        if ($this->isCsrfTokenValid('delete' . $categorie->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($categorie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_categorie_index', [], Response::HTTP_SEE_OTHER);
    }
}
