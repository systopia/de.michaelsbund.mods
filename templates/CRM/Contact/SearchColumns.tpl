{* create a temporary table with the data *}
<table id="contact_search_data">
  <thead>
    <tr>
      <th scope="col">Arbeitgeber</th>
      <th scope="col">Funktion</th>
      <th scope="col">Ansprechpartner</th>
    </tr>
  </thead>
  <tbody>
  {foreach from=$rows item=row}
    <tr id="rowid{$row.contact_id}" class="{cycle values="odd-row,even-row"} crm-contact_{$row.contact_id}">
      <td class="crm-contact-arbeitgeber">{$row.arbeitgeber}</td>
      <td class="crm-contact-job_title">{$row.job_title}</td>
      <td class="crm-contact-ansprechpartner">{$row.ansprechpartner}</td>
    </tr>
  {/foreach}
  </tbody>
</table>

{* then move the column from the temporary table into the original one *}
{literal}
  <script type="text/javascript">
    (function($) {
      var $selectorTable = $('form.crm-search-form table.selector:first');
      var $headerRow = $selectorTable.find('thead tr');

      // get the penultimate column index
      var columnNr = $headerRow.find('th:last').prev('th').index() + 1;

      // Add header column.
      $('#contact_search_data thead tr').find('th').insertAfter($selectorTable.find('thead tr th:nth-child(' + columnNr + ')'));

      // iterate over all items
      $('#contact_search_data tbody tr').each(function(rowIndex) {
        $(this).find('td').insertAfter($selectorTable.find('tbody tr:nth-child(' + (rowIndex+1) + ') td:nth-child(' + columnNr + ')'));
      });

      // finally delete the temp table
      $('#contact_search_data').remove();

    })(cj || jQuery);
  </script>
{/literal}
