<?php
namespace Limbonia\Model\Base;

/**
 * Limbonia User Model Class
 *
 * Model based wrapper around the User table
 *
 * @author Lonnie Blansett <lonnie@limbonia.tech>
 * @package Limbonia
 */
class User extends \Limbonia\Model implements \Limbonia\Interfaces\Model\User
{
  use \Limbonia\Traits\Model\User
  {
    __get as userTraitMagicGet;
    __isset as userTraitMagicIsset;
  }

  /**
   * The database schema for creating this model's table in the database
   *
   * @var string
   */
  protected static $sSchema = "UserID INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
Email VARCHAR(255) NOT NULL,
FirstName VARCHAR(50) NULL,
LastName VARCHAR(50) NULL,
Active TINYINT(1) NOT NULL DEFAULT 1,
Visible TINYINT(1) NOT NULL DEFAULT 1,
Password VARCHAR(255) BINARY NOT NULL DEFAULT '',
PRIMARY KEY (UserID),
UNIQUE INDEX Unique_Email (Email)";

  /**
   * The columns for this model's tables
   *
   * @var array
   */
  protected static $hColumns =
  [
    'UserID' =>
    [
      'Type' => 'int(10) unsigned',
      'Key' => 'Primary',
      'Default' => null,
      'Extra' => 'auto_increment',
    ],
    'Email' =>
    [
      'Type' => 'varchar(255)',
      'Key' => 'UNI',
      'Default' => null
    ],
    'FirstName' =>
    [
      'Type' => 'varchar(50)',
      'Default' => null
    ],
    'LastName' =>
    [
      'Type' => 'varchar(50)',
      'Default' => null
    ],
    'Active' =>
    [
      'Type' => 'tinyint(1)',
      'Default' => 1
    ],
    'Visible' =>
    [
      'Type' => 'tinyint(1)',
      'Default' => 1
    ],
    'Password' =>
    [
      'Type' => 'varchar(255)',
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
    'id' => 'UserID',
    'userid' => 'UserID',
    'email' => 'Email',
    'firstname' => 'FirstName',
    'lastname' => 'LastName',
    'active' => 'Active',
    'visible' => 'Visible',
    'password' => 'Password'
  ];

  /**
   * The default data used for "blank" or "empty" models
   *
   * @var array
   */
  protected static $hDefaultData =
  [
    'UserID' => '',
    'Email' => '',
    'FirstName' => '',
    'LastName' => '',
    'Active' => 1,
    'Visible' => 1,
    'Password' => ''
  ];

  /**
   * This object's data
   *
   * @var array
   */
  protected $hData =
  [
    'UserID' => '',
    'Email' => '',
    'FirstName' => '',
    'LastName' => '',
    'Position' => '',
    'Active' => 1,
    'Visible' => 1,
    'Password' => ''
  ];

  /**
   * List of columns that shouldn't be updated after the data has been created
   *
   * @var array
   */
  protected $aNoUpdate = ['UserID'];

  /**
   * The table that this object is referencing
   *
   * @var string
   */
  protected $sTable = 'User';

  /**
   * The name of the "ID" column associated with this object's table
   *
   * @var string
   */
  protected $sIdColumn = 'UserID';
}