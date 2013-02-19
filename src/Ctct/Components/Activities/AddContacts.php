<?php
namespace Ctct\Components\Activities;

use Ctct\Components\Component;
use Ctct\Util\Config;
use Ctct\Components\Activities\ActivityError;
use Ctct\Components\Activities\AddContactsImportData;
use Ctct\Exceptions\IllegalArgumentException;

/**
 * Represents an AddContact activity
 *
 * @package     Components
 * @subpackage     Activities
 * @author         Constant Contact
 */
class AddContacts extends Component
{
    public $import_data = array();
    public $lists = array();
    public $column_names = array();

    public function __construct(Array $contacts, Array $lists, Array $columnNames = array())
    {
      if (!empty($contacts)) {
              if ($contacts[0] instanceof AddContactsImportData) {
                  $this->import_data = $contacts;
              } else {
                throw new IllegalArgumentException(sprintf(Config::get('errors.id_or_object'), "AddContactsImportData"));
              }
          }

          $this->lists = $lists;
          $this->column_names = $columnNames;

          // attempt to determine the column names being used if they are not provided
          if (empty($columnNames)) {
              $usedColumns = array(Config::get('activities_columns.email'));

              $contact = $contacts[0];
              
              if (isset($contact->first_name)) {
                  $usedColumns[] = Config::get('activities_columns.first_name');
              }
              if (isset($contact->middle_name)) {
                  $usedColumns[] = Config::get('activities_columns.middle_name');
              }
              if (isset($contact->last_name)) {
                  $usedColumns[] = Config::get('activities_columns.last_name');
              }
              if (isset($contact->job_title)) {
                  $usedColumns[] = Config::get('activities_columns.job_title');
              }
              if (isset($contact->company_name)) {
                  $usedColumns[] = Config::get('activities_columns.company_name');
              }
              if (isset($contact->work_phone)) {
                  $usedColumns[] = Config::get('activities_columns.work_phone');
              }
              if (isset($contact->home_phone)) {
                  $usedColumns[] = Config::get('activities_columns.home_phone');
              }

              // Addresses
              $address = $contact->addresses[0];
              if (isset($address->line1)) {
                  $usedColumns[] = Config::get('activities_columns.address1');
              }
              if (isset($address->line2)) {
                  $usedColumns[] = Config::get('activities_columns.address2');
              }
              if (isset($address->line3)) {
                  $usedColumns[] = Config::get('activities_columns.address3');
              }
              if (isset($address->city)) {
                  $usedColumns[] = Config::get('activities_columns.city');
              }
              if (isset($address->state_code)) {
                  $usedColumns[] = Config::get('activities_columns.state');
              }
              if (isset($address->state_province)) {
                  $usedColumns[] = Config::get('activities_columns.state_province');
              }
              if (isset($address->country)) {
                  $usedColumns[] = Config::get('activities_columns.country');
              }
              if (isset($address->postal_code)) {
                  $usedColumns[] = Config::get('activities_columns.postal_code');
              }
              if (isset($address->sub_postal_code)) {
                  $usedColumns[] = Config::get('activities_columns.sub_postal_code');
              }

              // Custom Fields
              foreach ($contact->custom_fields as $customField) {
                  if (strpos($customField->name, 'custom_field_') !== false) {
                    $customFieldNumber = substr($customField->name, 13);
                     $usedColumns[] = Config::get('activities_columns.custom_field_'.$customFieldNumber);   
                }
              }
              $this->column_names = $usedColumns;
          }

      }

  public function toJson() 
    {
        foreach ($this->import_data as $contact) {
            foreach ($contact as $key => $value) {
                if ($value == null) {
                    unset($contact->$key);
                }
            }
        }
        return json_encode($this);
    }
}