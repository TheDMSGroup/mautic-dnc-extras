<?php

/*
 * @copyright   2018 Mautic Contributors. All rights reserved
 * @author      Digital Media Solutions, LLC
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
$view->extend('MauticCoreBundle:Default:content.html.php');
$view['slots']->set('mauticContent', 'donotcontactextras');
$view['slots']->set('headerTitle', 'Do Not Contact List Item '. ' - '.$item->getChannel() . ' : '. $item->getName());

// echo $view['assets']->includeScript(
//     'plugins/MauticDoNotContactExtrasBundle/Assets/build/donotcontactextras.min.js',
//     'donotcontactextrasOnLoad',
//     'donotcontactextrasOnLoad'
// );
// echo $view['assets']->includeStylesheet('plugins/MauticMediaBundle/Assets/build/donotcontactextras.min.css');

$view['slots']->set(
    'actions',
    $view->render(
        'MauticCoreBundle:Helper:page_actions.html.php',
        [
            'item'            => $item,
            'templateButtons' => [
                'edit'   => $view['security']->hasEntityAccess(
                    $permissions['plugin:donotcontactextras:items:editown'],
                    $permissions['plugin:donotcontactextras:items:editother'],
                    $item->getCreatedBy()
                ),
                'clone'  => $permissions['plugin:donotcontactextras:items:create'],
                'delete' => $view['security']->hasEntityAccess(
                    $permissions['plugin:donotcontactextras:items:deleteown'],
                    $permissions['plugin:donotcontactextras:items:deleteother'],
                    $item->getCreatedBy()
                ),
                'close'  => $view['security']->isGranted('plugin:donotcontactextras:items:view'),
            ],
            'routeBase'       => 'donotcontactextras',
            'langVar'         => 'mautic.donotcontactextras',
        ]
    )
);

?>
<!-- start: box layout -->
<div class="box-layout">
    <!-- left section -->
    <div class="col-md-9 bg-white height-auto">
        <div class="bg-auto">
            <!-- form detail header -->
            <div class="pr-md pl-md pt-lg pb-lg">
                <div class="box-layout">
                    <div class="col-xs-10">
                        <div class="text-muted"><?php echo $item->getDescription(); ?></div>
                    </div>
                    <div class="col-xs-2 text-right">
                        <?php echo $view->render(
                            'MauticCoreBundle:Helper:publishstatus_badge.html.php',
                            ['entity' => $item]
                        ); ?>
                    </div>
                </div>
            </div>
            <!--/ form detail header -->

            <!-- form detail collapseable -->
            <div class="collapse" id="media-details">
                <div class="pr-md pl-md pb-md">
                    <div class="panel shd-none mb-0">
                        <table class="table table-bordered table-striped mb-0">
                            <tbody>
                            <?php echo $view->render(
                                'MauticCoreBundle:Helper:details.html.php',
                                ['entity' => $item]
                            ); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!--/ form detail collapseable -->
        </div>

        <div class="bg-auto bg-dark-xs">
            <!-- form detail collapseable toggler -->
            <div class="hr-expand nm">
                <span data-toggle="tooltip" title="<?php echo $view['translator']->trans('mautic.core.details'); ?>">
                    <a href="javascript:void(0)" class="arrow text-muted collapsed" data-toggle="collapse"
                       data-target="#donotcontactextras-details"><span
                            class="caret"></span> <?php echo $view['translator']->trans(
                            'mautic.core.details'
                        ); ?></a>
                </span>
            </div>
            <!--/ form detail collapseable toggler -->

            <!-- tabs controls -->
            <ul class="nav nav-tabs pr-md pl-md mt-10 hide">
            </ul>
            <!--/ tabs controls -->

            <!-- start: tab-content -->
            <div class="tab-content pl-md pr-md pb-md hide">
            </div>
            <!--/ end: tab-content -->
        </div>
    </div>
    <!--/ left section -->

    <!-- right section -->
    <div class="col-md-3 bg-white bdr-l height-auto">

        <div class="panel bg-transparent shd-none bdr-rds-0 bdr-w-0 mb-0">

            <!-- recent activity -->
            <?php echo $view->render(
                'MauticCoreBundle:Helper:recentactivity.html.php',
                ['logs' => $auditlog['events']]
            ); ?>

        </div>
    </div>
    <!--/ right section -->
</div>
<!--/ end: box layout -->

<input type="hidden" name="entityId" id="entityId" value="<?php echo $item->getId(); ?>"/>
