<?php

/*
 * @copyright   2019 Mautic Contributors. All rights reserved
 * @author      Digital Media Solutions, LLC
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticDoNotContactExtrasBundle;

use Doctrine\ORM\Mapping as ORM;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;
use Mautic\CoreBundle\Entity\FormEntity;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Mapping\ClassMetadata;
/**
 * Class DncListItem
 */
class DncListItem extends FormEntity
{
    /** @var int */
    private $id;

    /** @var \DateTime */
    private $dateAdded;

    /** @var */
    private $channel;

    /** @var string */
    private $value;

    /**
     * @param ClassMetadata $metadata
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint(
            'value',
            new NotBlank(
                ['message' => 'mautic.dnc_extras.value.required']
            )
        );

        $metadata->addPropertyConstraint(
            'channel',
            new NotBlank(
                ['message' => 'mautic.dnc_extras.channel.required']
            )
        );
    }

    /**
     * @param ORM\ClassMetadata $metadata
     */
    public static function loadMetadata(ORM\ClassMetadata $metadata)
    {
        $builder = new ClassMetadataBuilder($metadata);
        $builder->setTable('dnc_extras_list_items')
            ->setCustomRepositoryClass('MauticPlugin\MauticDoNotContactExtrasBundle\Entity\DncListItemRepository');

        $builder->addIdColumns();

        $builder->addNamedField('dateAdded', 'datetime', 'date_added');

        $builder->addNamedField('value', 'string', 'value', false);

        $builder->addNamedField('channel', 'string', 'channel', false);

    }

    /**
     * Prepares the metadata for API usage.
     *
     * @param $metadata
     */
    public static function loadApiMetadata(ApiMetadataDriver $metadata)
    {
        $metadata->setGroupPrefix('Account')
            ->addListProperties(
                [
                    'id',
                    'dateAdded',
                    'channel',
                    'value',
                ]
            )
            ->addProperties(
                [
                    'id',
                    'dateAdded',
                    'channel',
                    'value',
                ]
            )
            ->setGroupPrefix('AccountBasic')
            ->addListProperties(
                [
                    'id',
                    'dateAdded',
                    'channel',
                    'value',
                ]
            )
            ->build();
    }
}