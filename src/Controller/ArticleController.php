<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\UsersRepository;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



class ArticleController extends AbstractController
{
    /**
     * @Route("/articles/{id}", name="articleDetails" , requirements={"id"="\d+"})
     */
    public function details(ArticleRepository $repo, $id)
    {   
        $article=$repo->findOneById($id);
        return $this->render('Article/articleDetails.html.twig', [
            'controller_name' => 'ArticleController',
            'article' => $article
        ]);
    }
    
    
     /**
     * @Route("/articles/list", name="articleList")
     * @Route("/article", name="article")
     */
    public function list(ArticleRepository $repo){
        
        $articles=$repo->findAll();

        return $this->render('article/articleList.html.twig', ["articles" => $articles]);      

    }

     /**
     * @Route("/articles/add", name="articleAdd")
     * @Route("/articles/edit/{id}", name="articleEdit" , requirements={"id"="\d+"})
     */
    public function add(Article $article=null, Request $request, ObjectManager $manager)
    {
        // creates a article if on articleAdd
        if(is_null($article)) $article = new Article();

            //We use the form article
            $form = $this->createForm(ArticleType::class, $article);
            
            //the form handle the request
            $form->handleRequest($request);
           
            //is form is submitted and valid
            if ($form->isSubmitted() && $form->isValid()) {
                
                $article->setCreatedAt(new \DateTime());
                
                //we make the recording of an article and flush the manager to do it
                $manager->persist($article);
                $manager->flush();
                
                return $this->redirectToRoute('articleList');
            }
               
        return $this->render('article/articleAdd.html.twig', [
            'controller_name' => 'ArticleController',
            'form' => $form->createView(),
            'action' => is_null($article->getId())
            ]
        );
    }

    /**
     * 
     * @Route("/articles/delete/{id}", name="articleDelete" , requirements={"id"="\d+"})
     */
    public function delete(Article $article=null, Request $request, ObjectManager $manager)
    {
        //vérification à faire sur le user avec id
        if(!is_null($article)){
            $manager->remove($article);
            $manager->flush();
        }

        return $this->redirectToRoute('articleList');
    }

}
