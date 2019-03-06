<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
if ($tmpl == 'index') {
    $view->extend('MauticDoNotContactExtrasBundle:DncListItem:index.html.php');
}
?>
<?php if (count($items)): ?>
    <div class="table-responsive">
        <table class="table table-hover table-striped table-bordered dnclistitem-list" id="dncListItemTable"
               class="overflow:auto">
            <thead>
            <tr>
                <?php
                echo $view->render(
                    'MauticCoreBundle:Helper:tableheader.html.php',
                    [
                        'checkall'        => 'true',
                        'target'          => '#dncListItemTable',
                        'langVar'         => 'dncListItem',
                        'routeBase'       => 'donotcontactextras',
                        'templateButtons' => [
                            'delete' => $permissions['plugin:donotcontactextras:items:delete'],
                        ],
                    ]
                );
                ?>
                <?php
                    echo $view->render('MauticCoreBundle:Helper:tableheader.html.php', [
                        'sessionVar' => $sessionVar,
                        'orderBy'    => 'dli.name',
                        'text'       => $view['translator']->trans('mautic.donotcontactextras.form.name'),
                        'class'      => 'visible-md visible-lg col-dnclistitem-name',
                    ]);

                    echo $view->render('MauticCoreBundle:Helper:tableheader.html.php', [
                        'sessionVar' => $sessionVar,
                        'orderBy'    => 'dli.channel',
                        'text'       => $view['translator']->trans('mautic.donotcontactextras.form.channel'),
                        'class'      => 'visible-md visible-lg col-dnclistitem-channel',
                    ]);
                     echo $view->render('MauticCoreBundle:Helper:tableheader.html.php', [
                        'sessionVar' => $sessionVar,
                        'orderBy'    => 'dli.reason',
                        'text'       => $view['translator']->trans('mautic.donotcontactextras.form.reason'),
                        'class'      => 'visible-md visible-lg col-dnclistitem-reason',
                    ]);
                     echo $view->render('MauticCoreBundle:Helper:tableheader.html.php', [
                        'sessionVar' => $sessionVar,
                        'orderBy'    => 'dli.dateAdded',
                        'text'       => $view['translator']->trans('mautic.donotcontactextras.form.dateadded'),
                        'class'      => 'visible-md visible-lg col-dnclistitem-dateadded',
                    ]);
                     echo $view->render('MauticCoreBundle:Helper:tableheader.html.php', [
                        'sessionVar' => $sessionVar,
                        'orderBy'    => 'dli.id',
                        'text'       => $view['translator']->trans('mautic.core.id'),
                        'class'      => 'visible-md visible-lg col-dnclistitem-id',
                    ]);
                ?>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($items as $item): ?>
                <tr id="field_<?php echo $item->getId(); ?>">
                    <td>
                        <?php
                        echo $view->render(
                            'MauticCoreBundle:Helper:list_actions.html.php',
                            [
                                'item'            => $item,
                                'templateButtons' => [
                                    'view'   => true,
                                    'edit'   => true,
                                    'delete' => true,
                                ],
                                'routeBase'       => 'donotcontactextras',
                                'langVar'         => 'dncListItem',
                                'pull'            => 'left',
                            ]
                        );
                        ?>
                    </td>
                    <td>
                    <span class="ellipsis">
                        <?php echo $view->render(
                            'MauticCoreBundle:Helper:publishstatus_icon.html.php',
                            ['item' => $item, 'model' => 'donotcontactextras.dnclistitem', 'disableToggle' => false]
                        ); ?>
                        <a href="<?php echo $view['router']->path(
                            'mautic_contactfield_action',
                            ['objectAction' => 'edit', 'objectId' => $item->getId()]
                        ); ?>"><?php echo $item->getName(); ?></a>
                    </span>
                    </td>
                    <td class="visible-md visible-lg"><?php echo $item->getChannel(); ?></td>
                    <td class="visible-md visible-lg"><?php echo $view['translator']->trans('mautic.dnc.reason.'.$item->getReason()); ?></td>
                    <td class="visible-md visible-lg"><?php echo $item->getDateAdded()->format("Y-m-d H:i:s"); ?></td>
                    <td class="visible-md visible-lg"><?php echo $item->getId(); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="panel-footer">
        <?php echo $view->render(
            'MauticCoreBundle:Helper:pagination.html.php',
            [
                'totalItems' => $totalItems,
                'page'       => $page,
                'limit'      => $limit,
                'baseUrl'    => $view['router']->path('mautic_donotcontactextras_index'),
                'sessionVar' => 'leadfield',
            ]
        ); ?>
    </div>
<?php else: ?>
    <?php echo $view->render('MauticCoreBundle:Helper:noresults.html.php'); ?>
<?php endif; ?>
