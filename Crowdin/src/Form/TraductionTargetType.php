<?php

namespace App\Form;

use App\Entity\TraductionTarget;
use App\Entity\Language;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class TraductionTargetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('target')
            ->add('language', EntityType::class, [
				'class' => Language::class,
				'choice_label' => function(Language $language) {
                    return sprintf('%s', $language->getLanguageName());
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TraductionTarget::class,
        ]);
    }
}
