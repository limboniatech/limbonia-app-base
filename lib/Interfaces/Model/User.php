<?php

namespace Limbonia\Interfaces\Model;

interface User
{
  /**
   * Generate and return a generic admin user object
   *
   * @return \Limbonia\Interfaces\Model\User User object
   * @throws \Limbonia\Exception\Auth
   */
  public static function getAdmin();

  /**
   * Generate and return a user object from the specified email
   *
   * @param string $sEmail
   * @param \Limbonia\Database $oDatabase (optional)
   * @return \Limbonia\Interfaces\Model\User User object
   * @throws \Limbonia\Exception\Auth
   */
  public static function getByEmail($sEmail, \Limbonia\Database $oDatabase = null);

  /**
   * Generate and return a user object from the specified auth_token
   *
   * @param string $sAuthToken
   * @param \Limbonia\Database $oDatabase (optional)
   * @return \Limbonia\Model\User
   * @throws \Limbonia\Exception
   */
  public static function getByAuthToken($sAuthToken, \Limbonia\Database $oDatabase = null);

  /**
   * Generate and return a user object from the specified api_key
   *
   * @param string $sApiKey
   * @param \Limbonia\Database $oDatabase (optional)
   * @return \Limbonia\Model\User
   * @throws \Limbonia\Exception\Web
   */
  public static function getByApiKey($sApiKey, \Limbonia\Database $oDatabase = null);

  /**
   * Generate and return and password of the specified length
   *
   * @param integer $iLength (optional)
   * @return string
   */
  public static function generatePassword($iLength = null);

  /**
   * Authenticate the current user using what ever method they require
   *
   * @param string $sPassword
   * @throws \Limbonia\Exception
   */
  public function authenticate(string $sPassword);
  
  /**
   * Generate an auth_token, add it to the database for this user and then return it
   *
   * @return string
   * @throws \Limbonia\Exception
   */
  public function generateAuthToken();

  /**
   * Delete the specified auth_token from this user
   *
   * @param type $sAuthToken
   * @return boolean
   * @throws \Limbonia\Exception
   */
  public function deleteAuthToken($sAuthToken);
  /**
   * Generate an auth_token, add it to the database for this user and then return it
   *
   * @return string
   * @throws \Limbonia\Exception
   */
  public function generateApiToken();

  /**
   * Delete the specified auth_token from this user
   *
   * @param string $sAuthToken
   * @return boolean
   * @throws \Limbonia\Exception
   */
  public function deleteApiToken($sAuthToken);

  /**
   * Reset this user's password to something random and return that password
   *
   * @return string
   */
  public function resetPassword();

  /**
   * Does this user have the specified resource?
   *
   * @param string $sResource
   * @param string $sComponent (optional)
   * @return boolean
   */
  public function hasResource($sResource, $sComponent = null);

  /**
   * Is this user an admin?
   *
   * @return boolean
   */
  public function isAdmin();

}