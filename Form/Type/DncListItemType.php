<?php

/*
 * @copyright   2018 Mautic Contributors. All rights reserved
 * @author      Digital Media Solutions, LLC
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticDoNotContactExtrasBundle\Form\Type;

use Mautic\CoreBundle\Security\Permissions\CorePermissions;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Mautic\CoreBundle\Form\EventListener\FormExitSubscriber;

/**
 * Class DncListItemType.
 */
class DncListItemType extends AbstractType
{
    /**
     * @var CorePermissions
     */
    private $security;

    /**
     * MediaAccountType constructor.
     *
     * @param CorePermissions $security
     */
    public function __construct(CorePermissions $security)
    {
        $this->security = $security;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        if (!empty($options['data']) && $options['data']->getId()) {
            $readonly = !$this->security->isGranted('plugin:donotcontactextras:items:publish');
            $data     = $options['data']->isPublished(true);
        } elseif (!$this->security->isGranted('plugin:donotcontactextras:items:publish')) {
            $readonly = true;
            $data     = false;
        } else {
            $readonly = false;
            $data     = false;
        }
        $builder->addEventSubscriber(new FormExitSubscriber('donotcontactextras', $options));

        $builder->add(
            'isPublished',
            'yesno_button_group',
            [
                'read_only' => $readonly,
                'empty_data'      => true,
                'data'      => isset($options['data']) ? $options['data']->getIsPublished() : true,
            ]
        );

        $builder->add(
            'channel',
            'choice',
            [
                'label'             => 'mautic.donotcontactextras.form.channel',
                'label_attr'        => ['class' => 'control-label'],
                'choices'           => ['email'=>'email', 'phone'=>'phone'],
                'choices_as_values' => false,
                'required'          => true,
                'attr'              => [
                    'class'   => 'form-control',
                    'tooltip' => 'mautic.donotcontactextras.form.channel.tooltip',
                ],
            ]
        );

        $builder->add(
            'name',
            'text',
            [
                'label'      => 'mautic.donotcontactextras.form.name',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => ['class' => 'form-control'],
                'required'   => true,
            ]
        );

        $builder->add(
            'reason',
            'choice',
            [
                'label'             => 'mautic.donotcontactextras.form.reason',
                'label_attr'        => ['class' => 'control-label'],
                'choices'           => [1 => "Unsubscribed", 2 => "Bounced", 3 => "Manually Added"],
                'choices_as_values' => false,
                'required'          => true,
                'empty_data'              => 3,
                'data'      => !empty($options['data']->getReason()) ? $options['data']->getReason() : 3,
                'attr'              => [
                    'class'   => 'form-control',
                    'tooltip' => 'mautic.donotcontactextras.form.reason.tooltip',
                ],
            ]
        );

        $builder->add(
            'description',
            'text',
            [
                'label'      => 'mautic.donotcontactextras.form.description',
                'label_attr' => ['class' => 'control-label'],
                'attr'              => [
                    'class'   => 'form-control',
                    'tooltip' => 'mautic.donotcontactextras.form.description.tooltip',
                ],
                'required'   => false,
            ]
        );

        if (!empty($options['action'])) {
            $builder->setAction($options['action']);
        }

        $customButtons = [];

        if (!empty($options['update_select'])) {
            $builder->add(
                'buttons',
                'form_buttons',
                [
                    'apply_text'        => false,
                    'pre_extra_buttons' => $customButtons,
                ]
            );
            $builder->add(
                'updateSelect',
                'hidden',
                [
                    'data'   => $options['update_select'],
                    'mapped' => false,
                ]
            );
        } else {
            $builder->add(
                'buttons',
                'form_buttons',
                [
                    'pre_extra_buttons' => $customButtons,
                ]
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'MauticPlugin\MauticDoNotContactExtrasBundle\Entity\DncListItem',
            ]
        );
        $resolver->setDefined(['update_select']);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'dnclistitem';
    }
}
