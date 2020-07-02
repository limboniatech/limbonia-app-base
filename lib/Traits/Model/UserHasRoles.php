<?php

namespace Limbonia\Traits\Model;

trait UserHasRoles
{
  /**
   * Return the list of resource keys and their levels that this role has
   *
   * @return array
   */
  public function getRoles()
  {
    return parent::getList('Role', "SELECT r.* FROM Role r NATURAL JOIN User_Role u_r WHERE u_r.UserID = $this->id ORDER BY NAME", $this->getDatabase());
  }

  /**
   * Return the list of resource key objects
   *
   * @return \Limbonia\ModelList
   */
  public function getRoleList()
  {
    return parent::search('Role', null, 'Name', $this->getDatabase());
  }

  /**
   * Set the specified list of resource keys for this role
   *
   * @param array $aRole
   */
  public function setRoles($aRole)
  {
    $this->getDatabase()->exec('DELETE FROM User_Role WHERE UserID = ' . $this->id);

    if (count($aRole) > 0)
    {
      $oResult = $this->getDatabase()->prepare("INSERT INTO User_Role VALUES ($this->id, :Role)");

      foreach ($aRole as $iRole)
      {
        $oResult->execute([':Role' => $iRole]);
      }
    }
  }
}