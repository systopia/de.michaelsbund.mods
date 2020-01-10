<?php

use CRM_Mods_ExtensionUtil as E;

class CRM_Mods_Hooks {

  /**
   * Implements hook_civicrm_alterContent().
   *
   * @param $content
   * @param $context
   * @param $tplName
   * @param $object
   */
  public static function alterContent(&$content, $context, $tplName, &$object) {
    // Inject search columns template into contact search form templates.
    if (
      is_a($object, 'CRM_Contact_Form_Search')
      && in_array($tplName, array(
        'CRM/Contact/Form/Search/Basic.tpl',
        'CRM/Contact/Form/Search/Advanced.tpl',
      ))
    ) {
      // parse the template with smarty
      $smarty = CRM_Core_Smarty::singleton();
      $path = E::path('templates/CRM/Contact/SearchColumns.tpl');
      $html = $smarty->fetch($path);
      // append the html to the content
      $content .= $html;
    }
  }

}
