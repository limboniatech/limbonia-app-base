<?php
namespace Limbonia\Traits\Model;

trait User
{
  use \Limbonia\Traits\Model\HasResources;

  /**
   * The default password length
   *
   * @return integer
   */
  public static function passwordDefaultLength()
  {
    return 16;
  }

  /**
   * The default password length
   *
   * @return integer
   */
  public static function passwordEncryptionCost()
  {
    return 10;
  }

  /**
   * The default password length
   *
   * @return string
   */
  public static function passwordEncryptionAlgo()
  {
    return PASSWORD_BCRYPT;
  }

  /**
   * Generate and return a generic admin user object
   *
   * @return \Limbonia\Interfaces\Model\User User object
   * @throws \Limbonia\Exception\Auth
   */
  public static function getAdmin()
  {
    $oAdmin = parent::factory('User');
    $oAdmin->hData['FirstName'] = 'Master';
    $oAdmin->hData['LastName'] = 'Admin';
    $oAdmin->bAdmin = true;
    return $oAdmin;
  }

  /**
   * Generate and return a user object from the specified email
   *
   * @param string $sEmail
   * @param \Limbonia\Database $oDatabase (optional)
   * @return \Limbonia\Interfaces\Model\User User object
   * @throws \Limbonia\Exception\Auth
   */
  public static function getByEmail($sEmail, \Limbonia\Database $oDatabase = null)
  {
    if (empty($sEmail))
    {
      throw new \Limbonia\Exception\Auth('Authentication failed');
    }

    $oUserList = \Limbonia\Model::search('User', ['Email' => $sEmail], null, $oDatabase);

    if (count($oUserList) == 0)
    {
      throw new \Limbonia\Exception\Auth('Authentication failed');
    }

    return $oUserList[0];
  }

  /**
   * Generate and return a user object from the specified auth_token
   *
   * @param string $sAuthToken
   * @param \Limbonia\Database $oDatabase (optional)
   * @return \Limbonia\Interfaces\Model\User
   * @throws \Limbonia\Exception\Auth
   */
  public static function getByAuthToken($sAuthToken, \Limbonia\Database $oDatabase = null)
  {
    $oDatabase = $oDatabase instanceof \Limbonia\Database ? $oDatabase : \Limbonia\App::getDefault()->getDB();
    $oDatabase->query("DELETE FROM UserAuth WHERE TIMEDIFF(NOW(), LastUseTime) > '01:00:00'");
    $oResult = $oDatabase->query("SELECT * FROM UserAuth WHERE AuthToken = :AuthToken AND TIMEDIFF(NOW(), LastUseTime) < '00:20:00'", ['AuthToken' => $sAuthToken]);
    $hRow = $oResult->fetchOne();

    if (empty($hRow))
    {
      throw new \Limbonia\Exception\Auth('Authentication failed');
    }

    $oUser = \Limbonia\Model::fromId('User', $hRow['UserID'], $oDatabase);

    if (!$oUser->active)
    {
      throw new \Limbonia\Exception\Auth('Authentication failed');
    }

    $oDatabase->query("UPDATE UserAuth SET LastUseTime = NOW() WHERE AuthToken = '{$hRow['AuthToken']}' AND UserID = {$hRow['UserID']}");
    return $oUser;
  }

  /**
   * Generate and return a user object from the specified api_key
   *
   * @param string $sApiKey
   * @param \Limbonia\Database $oDatabase (optional)
   * @return \Limbonia\Interfaces\Model\User
   * @throws \Limbonia\Exception\Auth
   */
  public static function getByApiKey($sApiKey, \Limbonia\Database $oDatabase = null)
  {
    $oDatabase = $oDatabase instanceof \Limbonia\Database ? $oDatabase : \Limbonia\App::getDefault()->getDB();
    $oUserList = \Limbonia\Model::search('User', ['ApiKey' => $sApiKey], null, $oDatabase);

    if (count($oUserList) == 0)
    {
      throw new \Limbonia\Exception\Auth('Authentication failed');
    }

    return $oUserList[0];
  }

  /**
   * Generate and return and password of the specified length
   *
   * @param integer $iLength (optional)
   * @return string
   */
  public static function generatePassword($iLength = null)
  {
    $iLength = empty($iLength) ? static::passwordDefaultLength() : (integer)$iLength;
    $sLetters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $sPassword = '';

    for ($i = 0; $i < $iLength; $i++)
    {
      $sPassword .= $sLetters[(rand(1, strlen($sLetters) - 1))];
    }

    return $sPassword;
  }

  /**
   * Authenticate the current user using what ever method they require
   *
   * @param string $sPassword
   * @throws \Limbonia\Exception
   */
  public function authenticate(string $sPassword)
  {
    if (empty($sPassword))
    {
      throw new \Limbonia\Exception\Auth('Password not given');
    }

    if (!$this->active)
    {
      throw new \Limbonia\Exception\Auth('Authentication failed');
    }

    if (!password_verify($sPassword, $this->password))
    {
      throw new \Limbonia\Exception\Auth('Authentication failed');
    }
  }

  /**
   * Generate an auth_token, add it to the database for this user and then return it
   *
   * @return string
   * @throws \Limbonia\Exception
   */
  public function generateAuthToken()
  {
    $sAuthToken = sha1(static::generatePassword());
    $oResult = $this->getDatabase()->prepare("INSERT INTO UserAuth (UserID, AuthToken, LastUseTime) VALUES (:UserID, :AuthToken, NOW())");

    if (!$oResult->execute(['UserID' => $this->id, 'AuthToken' => $sAuthToken]))
    {
      throw new \Limbonia\Exception('Failed to store auth_token');
    }

    return $sAuthToken;
  }

  /**
   * Delete the specified auth_token from this user
   *
   * @param string $sAuthToken
   * @return boolean
   * @throws \Limbonia\Exception
   */
  public function deleteAuthToken($sAuthToken)
  {
    $oResult = $this->getDatabase()->prepare("DELETE FROM UserAuth WHERE UserID = :UserID AND AuthToken = :AuthToken");

    if (!$oResult->execute(['UserID' => $this->id, 'AuthToken' => $sAuthToken]))
    {
      throw new \Limbonia\Exception('Failed to delete auth_token');
    }

    return true;
  }

  /**
   * Generate an auth_token, add it to the database for this user and then return it
   *
   * @return string
   * @throws \Limbonia\Exception
   */
  public function generateApiToken()
  {
    $sApiToken = sha1(static::generatePassword());
    $oResult = $this->getDatabase()->prepare("INSERT INTO ApiAuth (UserID, ApiToken) VALUES (:UserID, :ApiToken, NOW())");

    if (!$oResult->execute(['UserID' => $this->id, 'ApiToken' => $sApiToken]))
    {
      throw new \Limbonia\Exception('Failed to store api_token');
    }

    return $sApiToken;
  }

  /**
   * Delete the specified auth_token from this user
   *
   * @param string $sAuthToken
   * @return boolean
   * @throws \Limbonia\Exception
   */
  public function deleteApiToken($sAuthToken)
  {
    $oResult = $this->getDatabase()->prepare("DELETE FROM UserAuth WHERE UserID = :UserID AND ApiToken = :ApiToken");

    if (!$oResult->execute(['UserID' => $this->id, 'ApiToken' => $sApiToken]))
    {
      throw new \Limbonia\Exception('Failed to delete api_token');
    }

    return true;
  }

  /**
   * Reset this user's password to something random and return that password
   *
   * @return string
   */
  public function resetPassword()
  {
    $sPassword = static::generatePassword();
    $this->password = $sPassword;
    $this->save();
    return $sPassword;
  }

  /**
   * Format the specified value to valid input using type data from the specified column
   *
   * @param string $sName
   * @param mixed $xValue
   * @return mixed
   */
  protected function formatInput($sName, $xValue)
  {
    if ($sName == 'Password')
    {
      return password_hash($xValue, static::passwordEncryptionAlgo(), ['cost' => static::passwordEncryptionCost()]);
    }

    if ($sName == 'Email')
    {
      $xValue = trim($xValue);
      //if it validates successfully then let the normal value be returned...
      \Limbonia\Email\Util::validate($xValue);
    }

    return parent::formatInput($sName, $xValue);
  }

  /**
   * Get the specified data
   *
   * @param string $sName
   * @return mixed
   */
  public function __get($sName)
  {
    if (strtolower($sName) == 'name')
    {
      return trim("$this->firstName $this->lastName");
    }

    return parent::__get($sName);
  }

  /**
   * Determine if the specified value is set (exists) or not...
   *
   * @param string $sName
   * @return boolean
   */
  public function __isset($sName)
  {
    if (strtolower($sName) == 'name')
    {
      return true;
    }

    return parent::__isset($sName);
  }
}