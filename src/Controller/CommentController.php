<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentController extends AbstractController
{
      /**
     * @Route("profile/comments/{id}", name="commentDetails" , requirements={"id"="\d+"})
     */
    public function details(CommentRepository $repo, $id)
    {   
        $comment=$repo->findOneById($id);
        return $this->render('comments/commentDetails.html.twig', [
            'controller_name' => 'CommentController',
            'category' => $comment
        ]);
    }
    
    
     /**
     * @Route("profile/comments/list", name="commentList")
     * @Route("profile/comments", name="comment")
     */
    public function list(CommentRepository $repo){
        
        $comments=$repo->findAll();

        return $this->render('comment/commentList.html.twig', ["comments" => $comments]);      

    }

     /**
     * @Route("profile/comments/add", name="commentAdd")
     * @Route("admin/comments/edit/{id}", name="commentEdit" , requirements={"id"="\d+"})
     */
    public function add(Comment $comment=null, Request $request, ObjectManager $manager, ArticleRepository $repo, $id=null)
    {   

        if($request->get('_route') == "commentAddId" AND is_null($id)){}

        // creates a article if on articleAdd
        if(is_null($comment)) $comment = new Comment();

        //We use the form article
        $form = $this->createForm(CommentType::class, $comment);
    
        //the form handle the request
        $form->handleRequest($request);
        
        //is form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setCreatedAt(new \DateTime());        
            //we make the recording of an article and flush the manager to do it
            $manager->persist($comment);
            $manager->flush();
            
            return $this->redirectToRoute('commentList');
        }
            
        return $this->render('comment/commentAdd.html.twig', [
            'controller_name' => 'CommentController',
            'form' => $form->createView(),
            'action' => is_null($comment->getId())
            ]
        );
    }

     /**
     * 
     * @Route("profile/comments/add/{id}", name="commentAddId" ,  requirements={"id"="\d+"})
     * 
     */
    public function addFromArticle(Article $article, Request $request, ObjectManager $manager, ArticleRepository $repo, $id=null)
    {   

        $comment = new Comment();
        $comment->setArticle($article);
        
        //We use the form article
        $form = $this->createForm(CommentType::class, $comment);
        
        //the form handle the request
        $form->handleRequest($request);
        
        //is form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setCreatedAt(new \DateTime());        
            //we make the recording of an article and flush the manager to do it
            $manager->persist($comment);
            $manager->flush();
            
            return $this->redirectToRoute('commentList');
        }
            
        return $this->render('comment/commentAdd.html.twig', [
            'controller_name' => 'CommentController',
            'form' => $form->createView(),
            'action' => false
            ]
        );
    }



    /**
     * 
     * @Route("admin/comment/delete/{id}", name="commentDelete" , requirements={"id"="\d+"})
     */
    public function delete(Comment $comment=null, Request $request, ObjectManager $manager)
    {
        //vérification à faire sur le user avec id
        if(!is_null($comment)){
            $manager->remove($comment);
            $manager->flush();
        }

        return $this->redirectToRoute('commentList');
    }
}
