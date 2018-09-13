<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\UsersRepository;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



class ArticleController extends AbstractController
{
    /**
     * @Route("articles/{id}", name="articleDetails" , requirements={"id"="\d+"})
     */
    public function details(Article $article, CommentRepository $repoComment, Request $request)
    {   
        $comment=new Comment();
         //We use the form article
         $form = $this->createForm(CommentType::class, $comment);
         $form->remove('article');
         $form->remove('author');
         //the form handle the request
         $form->handleRequest($request);
         
         //is form is submitted and valid
         if ($form->isSubmitted() && $form->isValid()) {
             $comment->setCreatedAt(new \DateTime());   
             $comment->setArticle($article);
             $comment->setAuthor($article->getAuthor());

             //we make the recording of an article and flush the manager to do it
             $manager->persist($comment);
             $manager->flush();
             
             return $this->redirectToRoute('commentList');
         }

        $comments=$repoComment->findByArticle($article->getId());
        return $this->render('Article/articleDetails.html.twig', [
            'controller_name' => 'ArticleController',
            'form' => $form->createView(),
            'article' => $article,
            'comments' => $comments
            
        ]);
    }
    
    
     /**
     * @Route("articles/list", name="articleList")
     * @Route("article", name="article")
     * @Route("/", name="home")
     * @Route("/logout", name="logout")
     *
     */
    public function list(ArticleRepository $repoArticle, CommentRepository $repoComment){
        
        $articles=$repoArticle->findAll();
        $comments=$repoComment->findAll();

        return $this->render('article/articleList.html.twig', ["articles" => $articles, "comments" => $comments]);      

    }

     /**
     * @Route("admin/articles/add", name="articleAdd")
     * @Route("admin/articles/edit/{id}", name="articleEdit" , requirements={"id"="\d+"})
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
     * @Route("admin/articles/delete/{id}", name="articleDelete" , requirements={"id"="\d+"})
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
