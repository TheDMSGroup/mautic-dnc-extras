<?php
/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticDoNotContactExtrasBundle\Model;

use Mautic\CoreBundle\Helper\InputHelper;
use Mautic\CoreBundle\Helper\PhoneNumberHelper;
use Mautic\CoreBundle\Model\FormModel;
use Mautic\UserBundle\Entity\UserRepository;
use MauticPlugin\MauticDoNotContactExtrasBundle\DncEvents;
use MauticPlugin\MauticDoNotContactExtrasBundle\Entity\DncListItem;
use MauticPlugin\MauticDoNotContactExtrasBundle\Event\DncEvent;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class DncListItemModel extends FormModel
{
    /** @var PhoneNumberHelper */
    protected $phoneHelper;

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
     * @return DncListItem|object|null
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

        foreach ($fields as $importField => $dncField) {
            // Prevent overwriting existing data with empty data
            if (array_key_exists($importField, $data) && !is_null($data[$importField]) && '' != $data[$importField]) {
                /* TODO UPDATE THE IMPUT HELPER FOR THIS */
                $fieldData[$dncField] = InputHelper::_($data[$importField], 'string');
            }
        }

        $dncListItem = $this->checkForDuplicateDncValue($fieldData);
        $merged      = $dncListItem->getId();

        // Not Merged
        if (empty($merged)) {
            if (filter_var($fieldData['name'], FILTER_VALIDATE_EMAIL)) {
                $dncListItem->setChannel('email');
            } else {
                $dncListItem->setChannel('sms');
            }
            $dncListItem->setName($fieldData['name']);

            // For now, the reason is always 3 - Manual / Manually Added
            $dncListItem->setReason(3);

            $dateAdded = new \DateTime('now');
            $dncListItem->setDateAdded($dateAdded);

            if (!empty($owner)) {
                $userRepo      = $this->em->getRepository('MauticUserBundle:User');
                $createdByUser = $userRepo->find($owner);
                if (null !== $createdByUser) {
                    $dncListItem->setCreatedBy($createdByUser);
                }
            }
        }

        $dateModified = new \DateTime('now');
        $dncListItem->setDateModified($dateModified);

        if (!empty($owner)) {
            /* @var UserRepository $userRepo */
            $userRepo       = $this->em->getRepository('MauticUserBundle:User');
            $modifiedByUser = $userRepo->find($owner);
            if (null !== $modifiedByUser) {
                $dncListItem->setModifiedBy($modifiedByUser);
            }
        }

        if ($persist) {
            $this->saveEntity($dncListItem);
        }

        return $merged;
    }

    /**
     * @param array $fieldValue
     *
     * @return DncListItem|object|null
     */
    public function checkForDuplicateDncValue(array $fieldValue)
    {
        // look for existing record by $fieldValue
        $dncListItem = $this->getRepository()->findOneBy(['name' => $fieldValue['name']]);

        if (empty($dncListItem)) {
            $dncListItem = new DncListItem();
        }

        return $dncListItem;
    }

    /**
     * @return \MauticPlugin\MauticDoNotContactExtrasBundle\Entity\DncListItemRepository
     */
    public function getRepository()
    {
        return $this->em->getRepository('MauticDoNotContactExtrasBundle:DncListItem');
    }

    /**
     * @param            $action
     * @param            $entity
     * @param bool       $isNew
     * @param Event|null $event
     *
     * @return DncEvent|Event|\Symfony\Component\EventDispatcher\Event|null
     */
    protected function dispatchEvent($action, &$entity, $isNew = false, Event $event = null)
    {
        if (!$entity instanceof DncListItem) {
            throw new MethodNotAllowedHttpException(['DncListItem']);
        }

        switch ($action) {
            case 'pre_save':

                // Normalize entries on pre-save, in case it is via API or import possibly skipping form validators.
                $name  = DncEvents::PRE_SAVE;
                $value = trim($entity->getName());
                if ('email' !== $entity->getChannel()) {
                    if (!$this->phoneHelper) {
                        $this->phoneHelper = new PhoneNumberHelper();
                    }
                    try {
                        $normalized = $this->phoneHelper->format($value);
                        if (!empty($normalized)) {
                            $entity->setName($normalized);
                        }
                    } catch (\Exception $e) {
                    }
                }

                break;
            case 'post_save':
                $name = DncEvents::POST_SAVE;
                break;
            case 'pre_delete':
                $name = DncEvents::PRE_DELETE;
                break;
            case 'post_delete':
                $name = DncEvents::POST_DELETE;
                break;
            default:
                return null;
        }

        if ($this->dispatcher->hasListeners($name)) {
            if (empty($event)) {
                $event = new DncEvent($entity, $isNew);
                $event->setEntityManager($this->em);
            }

            $this->dispatcher->dispatch($name, $event);

            return $event;
        } else {
            return null;
        }
    }
}
