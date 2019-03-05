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
$object = $view['translator']->trans('mautic.donotcontactextras.form.headerTitle');
$view['slots']->set('mauticContent', 'dncImport');
$view['slots']->set('headerTitle', $view['translator']->trans('mautic.dnc.import.dncs', ['%object%' => $object]));

?>
<?php if (isset($form['file'])): ?>
<?php echo $view->render('MauticDoNotContactExtrasBundle:Import:upload_form.html.php', ['form' => $form]); ?>
<?php else: ?>
<?php echo $view->render('MauticDoNotContactExtrasBundle:Import:mapping_form.html.php', ['form' => $form]); ?>
<?php endif; ?>
