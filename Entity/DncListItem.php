<?php

/*
 * @copyright   2019 Mautic Contributors. All rights reserved
 * @author      Digital Media Solutions, LLC
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticDoNotContactExtrasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Mautic\ApiBundle\Serializer\Driver\ApiMetadataDriver;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;
use Mautic\CoreBundle\Entity\FormEntity;
use MauticPlugin\MauticDoNotContactExtrasBundle\Constraints\PhoneEmail;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * Class DncListItem.
 */
class DncListItem extends FormEntity
{
    /** @var int */
    private $id;

    /** @var */
    private $channel;

    /** @var int
     * see LeadBundle/Entity/DoNotContact
     * must be 0:is_contactbale, 1:unsubscibed, 2:bounced, 3:manual
     */
    private $reason;

    /** @var string */
    private $description;

    /** @var string
     * used as the value - either email or phone
     */
    private $name;

    /**
     * @param ClassMetadata $metadata
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint(
            'name',
            new NotBlank(
                ['message' => 'mautic.dnc.name.required']
            )
        );

        $metadata->addPropertyConstraint(
            'name',
            new PhoneEmail(
                ['message' => 'mautic.dnc.name.phone_email']
            )
        );

        $metadata->addPropertyConstraint(
            'channel',
            new NotBlank(
                ['message' => 'mautic.dnc.channel.required']
            )
        );

        $metadata->addConstraint(
            new UniqueEntity(
                [
                    'fields'           => ['name', 'channel'],
                    'message'          => 'mautic.dnc.name.unique',
                    'repositoryMethod' => 'checkUniqueNameChannel',
                ]
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

        $builder->addNamedField('channel', 'string', 'channel', false);

        $builder->addNamedField('reason', 'integer', 'reason', false);

        $builder->addUniqueConstraint(['name', 'channel'], 'name_channel');
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
                    'name',
                    'reason',
                    'description',
                ]
            )
            ->addProperties(
                [
                    'id',
                    'dateAdded',
                    'channel',
                    'name',
                    'reason',
                    'description',
                ]
            )
            ->setGroupPrefix('AccountBasic')
            ->addListProperties(
                [
                    'id',
                    'dateAdded',
                    'channel',
                    'name',
                    'reason',
                    'description',
                ]
            )
            ->build();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return DncListItem
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @param int $id
     *
     * @return DncListItem
     */
    public function setChannel($channel)
    {
        $this->isChanged('channel', $channel);
        $this->channel = $channel;

        return $this;
    }

    /**
     * @return int
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * @param int $id
     *
     * @return DncListItem
     */
    public function setReason($reason)
    {
        $this->isChanged('reason', $reason);
        $this->reason = $reason;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return DncListItem
     */
    public function setDescription($description)
    {
        $this->isChanged('description', $description);

        $this->description = $description;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     *
     * @return DncListItem
     */
    public function setName($name)
    {
        $this->isChanged('name', $name);

        $this->name = $name;

        return $this;
    }
}
