<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CategoryType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, array(
            'label' => 'Nom de la catégorie',
            'required' => true,
            'constraints' => array(
                new Length(array(
                    'min' => 2,
                    'max' => 50,
                    'minMessage' => 'Le nom de la catégorie doit faire plus de {{ limit }} caractères',
                    'maxMessage' => 'Le nom de la catégorie doit faire moins de {{ limit }} caractères'
                )),
                new NotBlank(array(
                    'message' => 'Vous devez indiquer un nom de catégorie'
                ))
            )))
            ->add('color', TextType::class, array(
                'label' => 'Code hexadécimal de la couleur',
                'required' => true,
                'constraints' => array(
                    new Length(array(
                        'min' => 2,
                        'max' => 50,
                        'minMessage' => 'Le nom de la couleur doit faire plus de {{ limit }} caractères',
                        'maxMessage' => 'Le nom de la couleur doit faire moins de {{ limit }} caractères'
                    )),
                    new NotBlank(array(
                        'message' => 'Vous devez indiquer une couleur'
                    ))
            )));
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Category'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_category';
    }


}
