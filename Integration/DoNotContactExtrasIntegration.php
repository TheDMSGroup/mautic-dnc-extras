<?php

/*
 * @copyright   2018 Mautic Contributors. All rights reserved
 * @author      Digital Media Solutions, LLC
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticDoNotContactExtrasBundle\Integration;

use Mautic\CoreBundle\Form\Type\YesNoButtonGroupType;
use Mautic\PluginBundle\Integration\AbstractIntegration;

/**
 * Class CampaignWatchIntegration.
 */
class DoNotContactExtrasIntegration extends AbstractIntegration
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'DoNotContactExtras';
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        return 'Do Not Contact Extras';
    }

    /**
     * @return string
     */
    public function getAuthenticationType()
    {
        return 'none';
    }

    public function appendToForm(&$builder, $data, $formArea)
    {
        if ('features' === $formArea) {
            // $builder->add(
            //     'dnc_extras_sample_thing',
            //     YesNoButtonGroupType::class,
            //     [
            //         'label'       => 'mautic.dnc_extras.sample_thing.label',
            //         'label_attr'  => [
            //             'class' => 'control-label',
            //         ],
            //         'data'        => isset($data['dnc_extras_sample_thing']) ? (bool) $data['dnc_extras_sample_thing'] : false,
            //         'attr'        => [
            //             'class'   => 'form-control',
            //             'tooltip' => $this->translator->trans('mautic.dnc_extras.sample_thing.tooltip'),
            //         ],
            //     ]
            // );
        }
    }
}
