<?php

namespace MD\SocomBundle\Form\Type;

use MD\SocomBundle\Model\OTagCommand;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OTagType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('clientReference', TextType::class, array(
                'label' => "Référence commande interne :",
                'required' => false,
                'attr' => array(
                    'placeholder' => "ex: Fi 360"
                )
            ))
            ->add('quantity',  IntegerType::class, array(
                //'label' => "Quantité de sachets (80 € HT / sachet)",
                'label'    => 'Commande minimum : 2 sachets',
                'required' => true,
                'attr' => array(
                    'step' => 1,
                    'min'  => 2
                )
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => OTagCommand::class,
        ));
    }
}
