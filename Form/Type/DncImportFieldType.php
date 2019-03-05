<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticDoNotContactExtrasBundle\Form\Type;

use Mautic\CoreBundle\Factory\MauticFactory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class DncImportFieldType.
 */
class DncImportFieldType extends AbstractType
{
    private $factory;

    /**
     * @param MauticFactory $factory
     */
    public function __construct(MauticFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($options['dnc_fields'] as $field => $label) {
            $builder->add(
                $field,
                'choice',
                [
                    'choices'    => $options['import_fields'],
                    'label'      => $label,
                    'required'   => false,
                    'label_attr' => ['class' => 'control-label'],
                    'attr'       => ['class' => 'form-control'],
                    'data'       => $this->getDefaultValue($field, $options['import_fields']),
                ]
            );
        }

        $buttons = ['cancel_icon' => 'fa fa-times'];

        if (empty($options['line_count_limit'])) {
            $buttons = array_merge(
                $buttons,
                [
                    'apply_text'  => 'mautic.lead.import.in.background',
                    'apply_class' => 'btn btn-success',
                    'apply_icon'  => 'fa fa-history',
                    'save_text'   => 'mautic.lead.import.start',
                    'save_class'  => 'btn btn-primary',
                    'save_icon'   => 'fa fa-upload',
                ]
            );
        } else {
            $buttons = array_merge(
                $buttons,
                [
                    'apply_text' => false,
                    'save_text'  => 'mautic.lead.import',
                    'save_class' => 'btn btn-primary',
                    'save_icon'  => 'fa fa-upload',
                ]
            );
        }

        $builder->add('buttons', 'form_buttons', $buttons);
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(['dnc_fields', 'import_fields', 'object']);
        $resolver->setDefaults(['line_count_limit' => 0]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'dnc_field_import';
    }

    /**
     * @param string $fieldName
     * @param array  $importFields
     *
     * @return string
     */
    public function getDefaultValue($fieldName, array $importFields)
    {
        if (isset($importFields[$fieldName])) {
            return $importFields[$fieldName];
        }

        return null;
    }
}
