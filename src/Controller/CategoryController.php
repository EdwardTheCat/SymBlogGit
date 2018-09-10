<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryController extends AbstractController
{

     /**
     * @Route("/categories/{id}", name="categoryDetails" , requirements={"id"="\d+"})
     */
    public function details(CategoryRepository $repo, $id)
    {   
        $category=$repo->findOneById($id);
        return $this->render('category/categoryDetails.html.twig', [
            'controller_name' => 'CategoryController',
            'category' => $category
        ]);
    }
    
    
     /**
     * @Route("/categories/list", name="categoryList")
     * @Route("/category", name="category")
     */
    public function list(CategoryRepository $repo){
        
        $categories=$repo->findAll();

        return $this->render('category/categoryList.html.twig', ["categories" => $categories]);      

    }

     /**
     * @Route("/categories/add", name="categoryAdd")
     * @Route("/categories/edit/{id}", name="categoryEdit" , requirements={"id"="\d+"})
     */
    public function add(Category $category=null, Request $request, ObjectManager $manager)
    {
        // creates a article if on articleAdd
        if(is_null($category)) $category = new Category();

            //We use the form article
            $form = $this->createForm(CategoryType::class, $category);
            
            //the form handle the request
            $form->handleRequest($request);
           
            //is form is submitted and valid
            if ($form->isSubmitted() && $form->isValid()) {
                            
                //we make the recording of an article and flush the manager to do it
                $manager->persist($category);
                $manager->flush();
                
                return $this->redirectToRoute('categoryList');
            }
               
        return $this->render('category/categoryAdd.html.twig', [
            'controller_name' => 'CategoryController',
            'form' => $form->createView(),
            'action' => is_null($category->getId())
            ]
        );
    }

    /**
     * 
     * @Route("/categories/delete/{id}", name="categoryDelete" , requirements={"id"="\d+"})
     */
    public function delete(Category $category=null, Request $request, ObjectManager $manager)
    {
        //vérification à faire sur le user avec id
        if(!is_null($category)){
            $manager->remove($category);
            $manager->flush();
        }

        return $this->redirectToRoute('categoryList');
    }
}
