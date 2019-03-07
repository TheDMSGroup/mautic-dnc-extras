<?php

/*
     * @copyright   2019 Mautic Contributors. All rights reserved
     * @author      Digital Media Solutions, LLC
     *
     * @link        http://mautic.org
     *
     * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
     */

namespace MauticPlugin\MauticDoNotContactExtrasBundle\Security\Permissions;

use Mautic\CoreBundle\Security\Permissions\AbstractPermissions;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class DoNotContactExtrasPermissions.
 */
class DoNotContactExtrasPermissions extends AbstractPermissions
{
    /**
     * {@inheritdoc}
     */
    public function __construct($params)
    {
        parent::__construct($params);
        $this->addExtendedPermissions('items');
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getName()
    {
        return 'donotcontactextras';
    }

    /**
     * {@inheritdoc}
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface &$builder, array $options, array $data)
    {
        $this->addExtendedFormFields('donotcontactextras', 'items', $builder, $data);
    }
}
