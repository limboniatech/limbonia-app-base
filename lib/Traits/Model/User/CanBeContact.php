<?php
namespace Limbonia\Traits\Model\User;

trait CanBeContact
{
  /**
   *  Is this user a contact?
   *
   * @return boolean
   */
  public function isContact()
  {
    return 'contact' === $this->__get('type');
  }
}