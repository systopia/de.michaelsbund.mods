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

  public static function searchColumns($objectName, &$headers, &$rows, &$selector) {
    if ($objectName == 'contact') {
      $additional_headers = array(
        array(
          'name' => 'Funktion',
          'field_name' => 'job_title',
          'weight' => 90,
          'display_callback' => function($contact_id) {
            $contact = civicrm_api3('Contact', 'getsingle', array(
              'id' => $contact_id,
              'return' => array(
                'job_title',
              ),
            ));
            return $contact['job_title'];
          },
        ),
        array(
          'name' => 'Arbeitgeber',
          'field_name' => 'arbeitgeber',
          'weight' => 90,
          'display_callback' => function($contact_id) {
            $relationships = civicrm_api3('Relationship', 'get', array(
              'contact_id_a' => $contact_id,
              'relationship_type_id' => 5,
              'is_active' => 1,
              'return' => array(
                'contact_id_b',
              ),
              'option.limit' => 0,
            ));
            if ($relationships['count']) {
              $contact_ids = array_map(function($relationship) {
                return $relationship['contact_id_b'];
              }, $relationships['values']);

              $contact_links = array_map(function($contact_id) {
                $contact = civicrm_api3('Contact', 'getsingle', array(
                  'id' => $contact_id,
                ));
                return '<a href="' . CRM_Utils_System::url('civicrm/contact/view', 'reset=1&cid=' . $contact_id) . '">'
                  . $contact['display_name']
                  . '</a>';
              }, $contact_ids);
              $value = '<ul><li>' . implode('</li><li>', $contact_links) . '</li></ul>';
            }
            else {
              $value = NULL;
            }
            return $value;
          },
        ),
        array(
          'name' => 'Ansprechpartner',
          'field_name' => 'ansprechpartner',
          'weight' => 90,
          'display_callback' => function($contact_id) {
            $relationships = civicrm_api3('Relationship', 'get', array(
              'contact_id_b' => $contact_id,
              'relationship_type_id' => 13,
              'is_active' => 1,
              'return' => array(
                'contact_id_a',
              ),
              'option.limit' => 0,
            ));
            if ($relationships['count']) {
              $contact_ids = array_map(function($relationship) {
                return $relationship['contact_id_a'];
              }, $relationships['values']);

              $contact_links = array_map(function($contact_id) {
                $contact = civicrm_api3('Contact', 'getsingle', array(
                  'id' => $contact_id,
                ));
                return '<a href="' . CRM_Utils_System::url('civicrm/contact/view', 'reset=1&cid=' . $contact_id) . '">'
                  . $contact['display_name']
                  . '</a>';
              }, $contact_ids);
              $value = '<ul><li>' . implode('</li><li>', $contact_links) . '</li></ul>';
            }
            else {
              $value = NULL;
            }
            return $value;
          },
        ),
      );

      foreach ($additional_headers as &$additional_header) {
        // TODO: Once the CRM/Contact/Form/Selector.tpl supports arbitrary columns,
        //       set the header column. In the meantime, we're using a separate
        //       template to inject the columns using JavaScript.
        //       @see mods_civicrm_alterContent().
        //      // Add header field.
        //      $headers[$additional_header['field_name']] = array_filter($additional_header, function($key) {
        //        return in_array($key, array(
        //          'name',
        //          'field_name',
        //          'weight',
        //        ));
        //      }, ARRAY_FILTER_USE_KEY);
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

      /**
       * Remove unnecessary columns.
       */
      unset($headers['country']);
      array_walk($rows, function(&$row) {
        unset($row['country']);
      });
    }
  }

}
