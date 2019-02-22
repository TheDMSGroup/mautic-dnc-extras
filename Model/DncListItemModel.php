<?php
/**
 * Created by PhpStorm.
 * User: scottshipman
 * Date: 2019-02-21
 * Time: 17:22
 */

namespace MauticPlugin\MauticDoNotContactExtrasBundle\Model;

use Mautic\CoreBundle\Model\FormModel;
use MauticPlugin\MauticDoNotContactExtrasBundle\Entity\DncListItemRepository;
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

}