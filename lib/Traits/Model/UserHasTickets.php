<?php

namespace Limbonia\Traits\Model;

trait UserHasTickets
{
  /**
   * Return the list of open tickets owned by this user
   *
   * @return \Limbonia\ModelList
   */
  public function getTickets()
  {
    return parent::search('Ticket', ['OwnerID' => $this->id, 'Status' => '!=:closed'], ['Priority', 'DueDate DESC'], $this->getDatabase());
  }

  /**
   * Is this user allowed to access the specified ticket?
   *
   * @param integer $iTicket - ID of the ticket to check
   * @return boolean
   * @throws \Limbonia\Exception
   */
  public function canAccessTicket($iTicket)
  {
    if (!$this->isContact())
    {
      return true;
    }

    $oResult = $this->getApp()->getDB()->prepare("SELECT COUNT(1) FROM Ticket WHERE TicketID = :TicketID AND (OwnerID = $this->id OR CreatorID = $this->id)");
    $oResult->bindValue(':TicketID', $iTicket, \PDO::PARAM_INT);

    if (!$oResult->execute())
    {
      $aError = $oResult->errorInfo();
      throw new \Limbonia\Exception("Failed to load data from $this->sTable: {$aError[2]}");
    }

    return $oResult->fetch() > 0;
  }
}