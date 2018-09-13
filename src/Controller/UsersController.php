<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\UsersType;
use App\Repository\UsersRepository;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class UsersController extends AbstractController
{

    /**
     * @Route("admin/users/{id}", name="detailsUser" , requirements={"id"="\d+"})
     */
    public function details(UsersRepository $repo, $id)
    {   
        $user=$repo->findOneById($id);
        return $this->render('Users/usersDetails.html.twig', [
            'controller_name' => 'UsersController',
            'user' => $user
        ]);
    }


     /**
      * 
     * @Route("admin/users/list", name="usersList")
     * @Route("admin/users", name="users")
     */
    public function list(UsersRepository $repo){
        //pour insérer les fitures
        // $user=new Users();
        // $entityManager = $this->getDoctrine()->getManager();
        // $user->load($entityManager);

        //$entityManager = $this->getDoctrine()->getManager();

        //$repo= $entityManager->getRepository(Users::class);//'Users::class ->\App\Entity\Users

        $users=$repo->findAll();

        return $this->render('users/UsersList.html.twig', ["users" => $users]);
        
        // return $this->render('users/listUsers.html.twig', ["test1" => $test1, "test2" => $test2]);

    }

    /**
     * @Route("adminroot/users/add", name="userAdd")
     * @Route("adminroot/users/edit/{id}", name="userEdit" , requirements={"id"="\d+"})
     */
    public function add(Users $user=null, Request $request, ObjectManager $manager, UserPasswordEncoderInterface $passwordEncoder)
    {
        // creates a task and gives it some dummy data for this example
        if(is_null($user)) $user = new Users();
        
        /*$user->setPassword("123");
        $user->setDateCreate(new \DateTime());
        $user->setDateLastLogin(new \DateTime());*/

        
        // $user->setDateCreate(new \DateTime(time()));


        // $form = $this->createFormBuilder($user)
        //     ->add('name')
        //     ->add('lastName')
        //     ->add('email')
        //     ->add('password', RepeatedType::class, array(
        //         'type' => PasswordType::class,
        //         'first_options'  => array('label' => 'Password'),
        //         'second_options' => array('label' => 'Repeat Password'),
        //     ))
            
        //     ->getForm();

            $form = $this->createForm(UsersType::class, $user);
        
            
            $user=$this->getUser();

            dump($user);

            if(in_array("ROLE_USER", $user->getRoles())){
                $form->add('roles', ChoiceType::class, array('choices' => array( 'ROLE_USER' => 0) ) );
            }
            if(in_array("ROLE_AUTHOR", $user->getRoles())){
                $form->add('roles', ChoiceType::class, array('choices' => array( 'ROLE_USER' => 0, 'ROLE_AUTHOR' => 1) ) );
            }
            if(in_array("ROLE_ADMIN", $user->getRoles())){
                $form->add('roles', ChoiceType::class, array('choices' => array( 'ROLE_USER' => 0, 'ROLE_AUTHOR' => 1, 'ROLE_ADMIN' => 2) ) );
            }

            $form->handleRequest($request);
           
            if ($form->isSubmitted() && $form->isValid()) {
                
                $user->setDateCreate(new \DateTime());
                
                //we encode the password
                $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
                if($user->getPassword()!=$password) $user->setPassword($password);

                //set user active
                $user->setIsActive(true);
                //$user->eraseCredentials();
                
                dump($user);

                //save user
                $manager->persist($user);
                $manager->flush();
                
                $this->addFlash('success', 'Your account has been added !');

                //return $this->redirectToRoute('detailsUser',array('id' => $user->getId()));
                return $this->redirectToRoute('usersList');
                
                //$response = new RedirectResponse('usersList');
                //$response->prepare($request);
            
                // return $response->send();
            }
            
         
        
        return $this->render('Users/usersAdd.html.twig', [
            'controller_name' => 'UsersController',
            'form' => $form->createView(),
            'action' => is_null($user->getId())
            ]
        );
    }

     /**
     * 
     * @Route("adminroot/users/delete/{id}", name="userDelete" , requirements={"id"="\d+"})
     */
    public function delete(Users $user=null, Request $request, ObjectManager $manager, UserPasswordEncoderInterface $passwordEncoder)
    {
        //vérification à faire sur le user avec id
        if(!is_null($user)){
            $manager->remove($user);
            $manager->flush();
        }

        return $this->redirectToRoute('usersList');
    }

}
