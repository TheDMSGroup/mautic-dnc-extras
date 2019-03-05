<?php
/**
 * Created by PhpStorm.
 * User: scottshipman
 * Date: 2019-02-21
 * Time: 17:22.
 */

namespace MauticPlugin\MauticDoNotContactExtrasBundle\Model;

use Mautic\CoreBundle\Model\FormModel;
use MauticPlugin\MauticDoNotContactExtrasBundle\Entity\DncListItem;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class DncListItemModel extends FormModel
{
    /**
     * @return string
     */
    public function getPermissionBase()
    {
        return 'donotcontactextras:items';
    }

    /**
     * @return string
     */
    public function getActionRouteBase()
    {
        return 'DoNotContactExtras';
    }

    /**
     * {@inheritdoc}
     *
     * @return \MauticPlugin\MauticDoNotContactExtrasBundle\Entity\DncListItemRepository
     */
    public function getRepository()
    {
        return $this->em->getRepository('MauticDoNotContactExtrasBundle:DncListItem');
    }

    /**
     * {@inheritdoc}
     *
     * @param object                              $entity
     * @param \Symfony\Component\Form\FormFactory $formFactory
     * @param string                              $action
     * @param array                               $options
     *
     * @throws NotFoundHttpException
     */
    public function createForm($entity, $formFactory, $action = null, $options = [])
    {
        if ($entity && !$entity instanceof DncListItem) {
            throw new MethodNotAllowedHttpException(['DncListItem']);
        }

        if (!empty($action)) {
            $options['action'] = $action;
        }

        // Prevent clone action from complaining about extra fields.
        $options['allow_extra_fields'] = true;

        return $formFactory->create('dnclistitem', $entity, $options);
    }

    /**
     * @param null $id
     *
     * @return DncListItem|null|object
     */
    public function getEntity($id = null)
    {
        if (null === $id) {
            return new DncListItem();
        }

        return parent::getEntity($id);
    }

    /**
     * @param      $fields
     * @param      $data
     * @param null $owner
     * @param null $list
     * @param null $tags
     * @param bool $persist
     * @param null $importId
     *
     * @return mixed
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function import($fields, $data, $owner = null, $list = null, $tags = null, $persist = true, $importId = null)
    {
        $fields    = array_flip($fields);
        $fieldData = [];

        foreach ($fields as $dncField => $importField) {
            // Prevent overwriting existing data with empty data
            if (array_key_exists($importField, $data) && !is_null($data[$importField]) && '' != $data[$importField]) {
                /* TODO UPDATE THE IMPUT HELPER FOR THIS */
                $fieldData[$dncField] = InputHelper::_($data[$importField], 'string');
            }
        }

        $dncListItem   = $this->checkForDuplicateDncValue($fieldData);
        $merged        = ($dncListItem->getId());

        if (!empty($fields['dateAdded']) && !empty($data[$fields['dateAdded']])) {
            $dateAdded = new DateTimeHelper($data[$fields['dateAdded']]);
            $dncListItem->setDateAdded($dateAdded->getUtcDateTime());
        }
        unset($fieldData['dateAdded']);

        if (!empty($fields['dateModified']) && !empty($data[$fields['dateModified']])) {
            $dateModified = new DateTimeHelper($data[$fields['dateModified']]);
            $dncListItem->setDateModified($dateModified->getUtcDateTime());
        }
        unset($fieldData['dateModified']);

        if (!empty($fields['lastActive']) && !empty($data[$fields['lastActive']])) {
            $lastActive = new DateTimeHelper($data[$fields['lastActive']]);
            $dncListItem->setLastActive($lastActive->getUtcDateTime());
        }
        unset($fieldData['lastActive']);

        if (!empty($fields['dateIdentified']) && !empty($data[$fields['dateIdentified']])) {
            $dateIdentified = new DateTimeHelper($data[$fields['dateIdentified']]);
            $dncListItem->setDateIdentified($dateIdentified->getUtcDateTime());
        }
        unset($fieldData['dateIdentified']);

        if (!empty($fields['createdByUser']) && !empty($data[$fields['createdByUser']])) {
            $userRepo      = $this->em->getRepository('MauticUserBundle:User');
            $createdByUser = $userRepo->findByIdentifier($data[$fields['createdByUser']]);
            if (null !== $createdByUser) {
                $dncListItem->setCreatedBy($createdByUser);
            }
        }
        unset($fieldData['createdByUser']);

        if (!empty($fields['modifiedByUser']) && !empty($data[$fields['modifiedByUser']])) {
            $userRepo       = $this->em->getRepository('MauticUserBundle:User');
            $modifiedByUser = $userRepo->findByIdentifier($data[$fields['modifiedByUser']]);
            if (null !== $modifiedByUser) {
                $dncListItem->setModifiedBy($modifiedByUser);
            }
        }
        unset($fieldData['modifiedByUser']);

        if (!empty($fields['ownerusername']) && !empty($data[$fields['ownerusername']])) {
            try {
                $newOwner = $this->userProvider->loadUserByUsername($data[$fields['ownerusername']]);
                $dncListItem->setOwner($newOwner);
                //reset default import owner if exists owner for contact
                $owner = null;
            } catch (NonUniqueResultException $exception) {
                // user not found
            }
        }
        unset($fieldData['ownerusername']);

        if (null !== $owner) {
            $dncListItem->setOwner($this->em->getReference('MauticUserBundle:User', $owner));
        }

        if ($persist) {
            $this->saveEntity($dncListItem);
        }

        return $merged;
    }

    /**
     * @param array            $queryFields
     * @param DncListItem|null $dncValue
     *
     * @return array|Lead
     */
    public function checkForDuplicateDncValue(array $fieldValue)
    {
        // look for existing record by $fieldValue
        /* TODO FIX THIS LOOKUP ADD CRITERIA */
        // $dncListItem = $this->getRepository()->findOneBy()

        if (empty($dncListItem)) {
            $dncListItem = new DncListItem();
        }

        return $dncListItem;
    }
}
