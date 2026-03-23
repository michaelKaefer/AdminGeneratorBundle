<?php

namespace MichaelKaefer\AdminUiBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeleteType extends AbstractType
{
    public function getParent(): ?string
    {
        return ButtonType::class;
    }
//    public function buildForm(FormBuilderInterface $builder, array $options): void
//    {
//        $builder->add('delete', SubmitType::class, [
//            'label' => 'Delete',
//            'attr' => ['class' => 'delete'],
//        ]);
//    }


    public function getBlockPrefix(): string
    {
        return 'delete';
    }
}
