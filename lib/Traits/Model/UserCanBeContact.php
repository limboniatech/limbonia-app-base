<?php

namespace Limbonia\Traits\Model;

trait UserCanBeContact
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