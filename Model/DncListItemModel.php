<?php
/**
 * Created by PhpStorm.
 * User: scottshipman
 * Date: 2019-02-21
 * Time: 17:22
 */

use Mautic\CoreBundle\Model\FormModel;


class DncListItemModel extends FormModel
{

    /**
     * @return string
     */
    public function getPermissionBase()
    {
        return 'plugin:donotcontactextrras:items';
    }

    /**
     * @return string
     */
    public function getActionRouteBase()
    {
        return 'DncListItem';
    }

}