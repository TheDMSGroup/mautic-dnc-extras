<?php

/*
 * @copyright   2016 Mautic Contributors. All rights reserved
 * @author      Mautic, Inc.
 *
 * @link        https://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

if (!isset($entity)) {
    $entity = $form->vars['data'];
}

$extendTemplate = (!empty($useSlim)) ? 'slim' : 'content';
$view->extend('MauticCoreBundle:Default:'.$extendTemplate.'.html.php');
if (isset($mauticContent)) {
    $view['slots']->set('mauticContent', $mauticContent);
}

if (!isset($headerTitle)) {
    if (!empty($entity->getId())) {
        $headerTitle = $view['translator']->trans('mautic.donotcontactextras.form.headerTitle').' - '.$entity->getChannel().' : '.$entity->getName(
            );
    } else {
        $headerTitle = 'Create '.$view['translator']->trans('mautic.donotcontactextras.form.headerTitle');
    }
}
$view['slots']->set('headerTitle', $headerTitle);

$attr = $form->vars['attr'];
if ($view['slots']->has('formAttr')) {
    $attr = array_merge($attr, $view['slots']->get('formAttr'));
}

echo $view['form']->start($form);
?>

    <div class="col-md-9 bg-white height-auto bdr-l">
        <div class="pa-md">
            <div class="form-group mb-0">
                <div class="row">
                    <div class="col-md-4">
                        <?php echo $view['form']->row($form['isPublished']); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        <?php echo $view['form']->row($form['channel']); ?>
                    </div>
                    <div class="col-md-4">
                        <?php echo $view['form']->row($form['name']); ?>
                    </div>
                    <div class="col-md-3">
                        <?php echo $view['form']->row($form['reason']); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-9">
                        <?php echo $view['form']->row($form['description']); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php echo $view['form']->end($form); ?>

