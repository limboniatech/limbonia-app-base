<?php
namespace Limbonia\Model;

/**
 * Limbonia Role Model Class
 *
 * Model based wrapper around the Role table
 *
 * @author Lonnie Blansett <lonnie@limbonia.tech>
 * @package Limbonia
 */
class Role extends \Limbonia\Model
{
  use \Limbonia\Traits\Model\HasResources;

  /**
   * The database schema for creating this model's table in the database
   *
   * @var string
   */
  protected static $sSchema = "`RoleID` int(10) unsigned NOT NULL AUTO_INCREMENT,
`Name` varchar(255) NOT NULL,
`Description` text,
PRIMARY KEY (`RoleID`),
UNIQUE KEY `Unique_RoleName` (`Name`)";

  /**
   * The columns for this model's tables
   *
   * @var array
   */
  protected static $hColumns =
  [
    'RoleID' =>
    [
      'Type' => 'int(10) unsigned',
      'Key' => 'Primary',
      'Default' => 0,
      'Extra' => 'auto_increment'
    ],
    'Name' =>
    [
      'Type' => 'varchar(255)',
      'Key' => 'UNI',
      'Default' => ''
    ],
    'Description' =>
    [
      'Type' => 'text',
      'Default' => ''
    ]
  ];

  /**
   * The aliases for this model's columns
   *
   * @var array
   */
  protected static $hColumnAlias =
  [
    'roleid' => 'RoleID',
    'id' => 'RoleID',
    'name' => 'Name',
    'description' => 'Description'
  ];

  /**
   * The default data used for "blank" or "empty" models
   *
   * @var array
   */
  protected static $hDefaultData =
  [
    'RoleID' => 0,
    'Name' => '',
    'Description' => ''
  ];

  /**
   * This object's data
   *
   * @var array
   */
  protected $hData =
  [
    'RoleID' => 0,
    'Name' => '',
    'Description' => ''
  ];

  /**
   * List of columns that shouldn't be updated after the data has been created
   *
   * @var array
   */
  protected $aNoUpdate = ['RoleID'];

  /**
   * The table that this object is referencing
   *
   * @var string
   */
  protected $sTable = 'Role';

  /**
   * The name of the "ID" column associated with this object's table
   *
   * @var string
   */
  protected $sIdColumn = 'RoleID';

  /**
   * Return the list of resource keys and their levels that this user has
   *
   * @return array
   */
  public function getResourceKeys()
  {
    $oResult = $this->getDatabase()->query("SELECT KeyID, Level FROM Role_Key WHERE RoleID = $this->id");
    return $oResult->fetchAssoc();
  }

  /**
   * Return the list of resource key objects
   *
   * @return \Limbonia\ModelList
   */
  public function getResourceList()
  {
    return parent::search('ResourceKey', null, 'Name', $this->getDatabase());
  }

  /**
   * Set the specified list of resource keys for this user
   *
   * @param array $hResource
   */
  public function setResourceKeys($hResource)
  {
    $this->getDatabase()->exec('DELETE FROM Role_Key WHERE RoleID = ' . $this->id);

    if (count($hResource) > 0)
    {
      $oResult = $this->getDatabase()->prepare("INSERT INTO Role_Key VALUES ($this->id, :Key, :Level)");

      foreach ($hResource as $iKey => $iLevel)
      {
        $oResult->execute([':Key' => $iKey, ':Level' => (integer)$iLevel]);
      }
    }
  }
}