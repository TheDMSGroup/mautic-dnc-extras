<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
$view->extend('MauticCoreBundle:Default:content.html.php');
$view['slots']->set('mauticContent', 'dncListItem');
$view['slots']->set('headerTitle', $view['translator']->trans('mautic.donotcontactextras.form.headerTitle').'s');

$view['slots']->set(
    'actions',
    $view->render(
        'MauticCoreBundle:Helper:page_actions.html.php',
        [
            'templateButtons' => [
                'new'    => true,
                'import' => true,
            ],
            'routeBase'     => 'donotcontactextras',
            'langVar'       => 'dncListItem',
            'customButtons' => [
                'imports' => [
                     'attr' => [
                            'class'       => 'btn btn-default',
                            'href'        => $view['router']->path('mautic_dnc.import_index'),
                            'data-toggle' => 'ajax',
                        ],
                    'iconClass' => 'fa fa-upload',
                    'btnText'   => $view['translator']->trans('mautic.dnc.form.import.button'),
                    'primary'   => true,
                    'tooltip'   => 'View existing imports or Upload items using a new csv file.',
                ],
            ],
        ]
    )
);
?>

<div class="panel panel-default bdr-t-wdh-0">
    <?php echo $view->render(
        'MauticCoreBundle:Helper:list_toolbar.html.php',
        [
            'searchValue' => $searchValue,
            'action'      => $currentRoute,
        ]
    ); ?>

    <div class="page-list">
        <?php $view['slots']->output('_content'); ?>
    </div>
</div>