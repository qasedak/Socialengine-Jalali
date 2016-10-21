<?php
/**
 * SocialEngine
 *
 * @category   Engine
 * @package    Engine_Form
 * @copyright  Copyright 2006-2010 Qasedak Group - Socialtools.ir
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Birthdate.php 7371 2010-09-14 03:33:35Z john $
 */

/**
 * @category   Engine
 * @package    Engine_Form
 * @copyright  Copyright 2006-2010 Qasedak Group - Socialtools.ir
 * @license    http://www.socialengine.com/license/
 */
class Engine_Form_Element_Birthdate extends Engine_Form_Element_Date
{
  public function isValid($value, $context = null)
  {
    if ((empty($value['day']) || empty($value['month'])) && $this->isRequired())
    {
      $this->_messages[] = "Birthdays must include a month and a date.";
      return false;
    }
    return parent::isValid($value, $context);
  }
  
  public function getYearMax()
  {
	
    // Default is this year
    if( is_null($this->_yearMax) )
    {
		$t = new Zend_Date();
      $date = $t->jdate("Y", '', '', '', 'en');
      $this->_yearMax = (int) ($date - 12);
    }
    return $this->_yearMax;
  }
}