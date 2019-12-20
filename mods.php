<?php

require_once 'mods.civix.php';
use CRM_Mods_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function mods_civicrm_config(&$config) {
  _mods_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function mods_civicrm_xmlMenu(&$files) {
  _mods_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function mods_civicrm_install() {
  _mods_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function mods_civicrm_postInstall() {
  _mods_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function mods_civicrm_uninstall() {
  _mods_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function mods_civicrm_enable() {
  _mods_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function mods_civicrm_disable() {
  _mods_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function mods_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _mods_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function mods_civicrm_managed(&$entities) {
  _mods_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function mods_civicrm_caseTypes(&$caseTypes) {
  _mods_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function mods_civicrm_angularModules(&$angularModules) {
  _mods_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function mods_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _mods_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_entityTypes
 */
function mods_civicrm_entityTypes(&$entityTypes) {
  _mods_civix_civicrm_entityTypes($entityTypes);
}

/**
 * Implements hook_civicrm_searchColumns().
 *
 * @param $objectName
 * @param $headers
 * @param $rows
 * @param CRM_Core_Selector_Controller $selector
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_searchColumns
 */
function mods_civicrm_searchColumns($objectName, &$headers, &$rows, &$selector) {
  if ($objectName == 'contact') {
    $additional_headers = array(
      array(
        'name' => 'Ansprechpartner',
        'field_name' => 'ansprechpartner',
        'weight' => 90,
        'display_callback' => function($contact_id) {
          $relationships = civicrm_api3('Relationship', 'get', array(
            'contact_id_a' => $contact_id,
            'relationship_type_id' => 13,
            'return' => array(
              'contact_id_b',
            ),
            'option.limit' => 0,
          ));
          if ($relationships['count']) {
            $contact_ids = array_map(function($relationship) {
              return $relationship['contact_id_b'];
            }, $relationships['values']);
            $ansprechpartner = CRM_Contact_BAO_Contact_Utils::formatContactIDSToLinks($contact_ids);
            $ansprechpartner = array_map(function($links) {
              return $links['view'];
            }, $ansprechpartner['rows']);
            $value = '<ul><li>' . implode('</li><li>', $ansprechpartner) . '</li></ul>';
          }
          else {
            $value = NULL;
          }
          return $value;
        },
      ),
    );

    foreach ($additional_headers as &$additional_header) {
      // Add header field.
      $headers[$additional_header['field_name']] = array_filter($additional_header, function($key) {
        return in_array($key, array(
          'name',
          'field_name',
          'weight',
        ));
      }, ARRAY_FILTER_USE_KEY);
    }

    // Add data.
    if (!empty($rows)) {
      foreach ($rows as $contact_id => $row) {
        // Unset the reference variable.
        unset($additional_header);
        foreach ($additional_headers as $additional_header) {
          $rows[$contact_id][$additional_header['field_name']] = $additional_header['display_callback']($contact_id);
        }
      }
    }

    $stop = 'here';
  }
}

/**
 * The extra search column (see above) does not alter the template,
 * so we inject javascript into the template-content.
 */
function mods_civicrm_alterContent(&$content, $context, $tplName, &$object) {
  // get page- resp. form-class of the object
  // TODO: Do this only once when rendering the contact selector table.
  if (is_a($object, 'CRM_Contact_Form_Search')) {
    // parse the template with smarty
    $smarty = CRM_Core_Smarty::singleton();
    $path = E::path('templates/CRM/Contact/SearchColumns.tpl');
    $html = $smarty->fetch($path);
    // append the html to the content
    $content .= $html;
  }
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function mods_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
function mods_civicrm_navigationMenu(&$menu) {
  _mods_civix_insert_navigation_menu($menu, 'Mailings', array(
    'label' => E::ts('New subliminal message'),
    'name' => 'mailing_subliminal_message',
    'url' => 'civicrm/mailing/subliminal',
    'permission' => 'access CiviMail',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _mods_civix_navigationMenu($menu);
} // */
