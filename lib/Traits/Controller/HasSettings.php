<?php

namespace Limbonia\Traits\Controller;

trait HasSettings
{
  /**
   * List of fields used by controller settings
   *
   * @var array
   */
  protected static $hSettingsFields = [];

  /**
   * A list of the actual controller settings
   *
   * @var array
   */
  protected $hSettings = [];

  /**
   * Have this controller's settings been changed since the last save?
   *
   * @var boolean
   */
  protected $bChangedSettings = false;

  protected function construct()
  {
      $this->hSettings = $this->oApp->getSettings($this->sType);

      if (empty($this->hSettings))
      {
        $this->hSettings = $this->defaultSettings();
        $this->bChangedSettings = true;
        $this->saveSettings();
      }
  }

  /**
   * Destructor
   */
  public function __destruct()
  {
    $this->saveSettings();
  }

  /**
   * Return the list of fields used by this controller's settings
   *
   * @return array
   */
  public function getSettingsFields()
  {
    return static::$hSettingsFields;
  }

  /**
   * Return the default settings
   *
   * @return array
   */
  protected function defaultSettings()
  {
    return [];
  }

  /**
   * Save the current settings, if any to the database
   *
   * @return boolean - True on success or false on failure
   */
  protected function saveSettings()
  {
    if (!$this->bChangedSettings)
    {
      return true;
    }

    if ($this->oApp->saveSettings($this->sType, $this->hSettings))
    {
      $this->bChangedSettings = false;
      return true;
    }

    return false;
  }

  /**
   * Return the specified setting, if it exists
   *
   * @param string $sName
   * @return mixed
   */
  public function getSetting($sName=null)
  {
    if (count($this->hSettings) == 0)
    {
      return null;
    }

    if (empty($sName))
    {
      return $this->hSettings;
    }

    return $this->hSettings[strtolower($sName)] ?? null;
  }

  /**
   * Set the specified setting to the specified value
   *
   * @param string $sName
   * @param mixed $xValue
   * @return boolean
   */
  protected function setSetting($sName, $xValue)
  {
    $sLowerName = strtolower($sName);

    if (!isset(static::$hSettingsFields[$sLowerName]))
    {
      return false;
    }

    $this->bChangedSettings = true;
    $this->hSettings[$sLowerName] = $xValue;
    return true;
  }
}