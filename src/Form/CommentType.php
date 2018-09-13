<?php

namespace App\Form;

use App\Entity\Users;
use App\Entity\Article;
use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content')
            ->add('author', EntityType::class, array(
                // looks for choices from this entity
                'class' => Users::class,
                'choice_label' => 'name'
                ))
            ->add('article', EntityType::class, array(
                // looks for choices from this entity
                'class' => Article::class,
                'choice_label' => 'title'
                ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
