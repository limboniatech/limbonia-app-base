<?php
namespace Limbonia;

class Controller implements \Limbonia\Interfaces\Controller
{
  use \Limbonia\Traits\Controller
  {
    init as baseInit;
  }
  use \Limbonia\Traits\HasApp;

  /**
   * Instantiate a controller
   *
   * @param \Limbonia\App $oApp
   * @param \Limbonia\Router $oRouter (optional)
   */
  protected function __construct(\Limbonia\App $oApp, \Limbonia\Router $oRouter = null)
  {
      $this->baseConstruct($oApp, $oRouter);
  }

  /**
   * Initialize this controller's custom data, if there is any
   *
   * @throws \Limbonia\Exception
   */
  protected function init()
  {
    $this->baseInit();

    if (\method_exists($this, 'modelInit'))
    {
      $this->modelInit();
    }
  }
}