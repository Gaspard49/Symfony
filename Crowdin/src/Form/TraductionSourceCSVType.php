<?php

namespace App\Form;

use App\Entity\TraductionSource;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class TraductionSourceCSVType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        	->add('file', FileType::class, [
				'label' => 'Add Sources (CSV file)',

				// unmapped means that this field is not associated to any entity property
				'mapped' => false,
				'required' => false,

				// unmapped fields can't define their validation using annotations
				// in the associated entity, so you can use the PHP constraint classes
				'constraints' => [
					new File([
						'maxSize' => '1024k',
						'mimeTypes' => [
                            'text/*.csv',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid CSV document',
					])
				],
			]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TraductionSource::class,
        ]);
    }
}
