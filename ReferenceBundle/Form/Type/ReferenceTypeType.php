<?php

namespace Itkg\ReferenceBundle\Form\Type;

use OpenOrchestra\BackofficeBundle\EventSubscriber\AddSubmitButtonSubscriber;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use OpenOrchestra\BackofficeBundle\EventListener\TranslateValueInitializerListener;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Form\AbstractType;

/**
 * Class ReferenceTypeType
 */

class ReferenceTypeType extends AbstractType
{
    protected $translateValueInitializer;
    protected $referenceTypeClass;
    protected $translator;

    /**
     * @param string                            $referenceTypeClass
     * @param TranslatorInterface               $translator
     * @param TranslateValueInitializerListener $translateValueInitializer
     */
    public function __construct(
        $referenceTypeClass,
        TranslatorInterface $translator,
        TranslateValueInitializerListener $translateValueInitializer
    )
    {
        $this->translateValueInitializer = $translateValueInitializer;
        $this->referenceTypeClass = $referenceTypeClass;
        $this->translator = $translator;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $referenceTypeIdName = "referenceTypeId";

        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this->translateValueInitializer, 'preSetData'));
        $builder
            ->add($referenceTypeIdName, 'text', array(
                'label' => 'itkg_reference_bundle.form.reference.reference_type.id',
                'attr' => array(
                    'class' => 'generate-id-dest',
                )
            ))
            ->add('names', 'translated_value_collection', array(
                'label' => 'itkg_reference_bundle.form.reference.reference_type.names'
            ))
            ->add('fields', 'collection', array(
                'type' => 'field_type',
                'allow_add' => true,
                'allow_delete' => true,
                'label' => 'itkg_reference_bundle.form.reference.reference_type.fields',
                'attr' => array(
                    'data-prototype-label-add' => $this->translator->trans('itkg_reference_bundle.form.reference.reference_type.add'),
                    'data-prototype-label-new' => $this->translator->trans('itkg_reference_bundle.form.reference.reference_type.new'),
                    'data-prototype-label-remove' => $this->translator->trans('itkg_reference_bundle.form.reference.reference_type.delete'),
                    'data-prototype-callback-add' => "checkReferenceTypeId('#". $this->getName() . "_". $referenceTypeIdName . "')",
                    'data-prototype-callback-error-message' => $this->translator->trans('itkg_reference_bundle.form.reference.reference_type.error_reference_id')
                )
            ));

        $builder->addEventSubscriber(new AddSubmitButtonSubscriber());
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => $this->referenceTypeClass,
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'itkg_reference_type';
    }
}
