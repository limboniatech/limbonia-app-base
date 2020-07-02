<?php

namespace Limbonia\Traits\Model;

trait HasResources
{
  /**
   * List of resources that this user has access to
   *
   * @var array
   */
  protected $hResource = null;

  /**
   * Is this user an admin?
   *
   * @var boolean
   */
  protected $bAdmin = null;

  /**
   * Is this user an admin?
   *
   * @return boolean
   * @throws \Limbonia\Exception
   */
  public function isAdmin()
  {
    if (is_null($this->bAdmin))
    {
      switch (strtolower(static::type()))
      {
        case "user":
          $oResult = $this->getDatabase()->prepare("SELECT COUNT(1) FROM User_Role u_r NATURAL JOIN Role_Key r_k NATURAL JOIN ResourceKey rk WHERE rk.Name='Admin' AND r_k.Level = 1000 AND u_r.UserID = :ID");
          break;

        case "role":
          $oResult = $this->getDatabase()->prepare("SELECT COUNT(1) FROM Role_Key gk NATURAL JOIN ResourceKey rk WHERE rk.Name='Admin' AND gk.Level = 1000 AND gk.RoleID = :ID");
          break;

        default:
          throw new \Limbonia\Exception("This type (" + static::type() + ") can not have resources!");
      }
    
      $oResult->execute([':ID' => $this->__get('id')]);
      $iAdminCount = $oResult->fetchColumn();
      $this->bAdmin = $iAdminCount > 0;
    }

    return $this->bAdmin;
  }

  /**
   * Does this user have the specified resource?
   *
   * @param string $sResource
   * @param string $sComponent (optional)
   * @return boolean
   * @throws \Limbonia\Exception
   */
  public function hasResource($sResource, $sComponent = null)
  {
    if ($this->isAdmin())
    {
      return true;
    }

    if (is_null($this->hResource))
    {
        switch (strtolower(static::type()))
        {
          case "user":
            $oResult = $this->getDatabase()->prepare("SELECT rl.Resource, rl.Component, rk.Name, r_k.Level FROM ResourceLock rl, Role_Key r_k, ResourceKey rk, User_Role u_r WHERE rk.KeyID = r_k.KeyID AND (rl.KeyID = r_k.KeyID OR rk.Name = 'Admin') AND rl.MinKey <= r_k.Level AND r_k.RoleID =u_r.RoleID AND  u_r.UserID = :ID");
            break;
  
          case "role":
            $oResult = $this->getDatabase()->prepare("SELECT rl.Resource, rl.Component, rk.Name, gk.Level FROM ResourceLock rl, Role_Key gk, ResourceKey rk WHERE rk.KeyID = gk.KeyID AND (rl.KeyID = gk.KeyID OR rk.Name = 'Admin') AND rl.MinKey <= gk.Level AND gk.RoleID = :ID");
            break;
  
          default:
            throw new \Limbonia\Exception("This type (" + static::type() + ") can not have resources!");
        }

        $oResult->execute([':ID' => $this->__get('id')]);
        $this->hResource = [];

        if ($bSuccess && count($oResult) > 0)
        {
          foreach ($oResult as $hResource)
          {
            $this->hResource[$hResource['Resource']][] = $hResource['Component'];
          }
        }
    
    }

    if (empty($sComponent))
    {
      return isset($this->hResource[$sResource]);
    }

    return isset($this->hResource[$sResource]) && in_array($sComponent, $this->hResource[$sResource]);
  }
}