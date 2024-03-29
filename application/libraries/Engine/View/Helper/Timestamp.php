﻿<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Qasedak Group - Socialtools.ir
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Timestamp.php 8090 2010-12-21 01:42:10Z john $
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Qasedak Group - Socialtools.ir
 * @license    http://www.socialengine.com/license/
 */
class Engine_View_Helper_Timestamp extends Zend_View_Helper_HtmlElement
{
  const MINUTE = 60;
  const HOUR = 3600;
  const DAY = 86400;
  const WEEK = 604800;
  const MONTH = 2419200; // 4 weeks approximation
  const YEAR = 31536000; // 365 days approximation
  
  protected $_tag = 'span';

  protected $_extraClass;

  static protected $_registeredInTranslate = false;

  public function timestamp($time = null, $attribs = array())
  {
    if( null === $time )
    {
      $time = time();
    }

    else if( is_string($time) && !is_numeric($time) )
    {
      $time = strtotime($time);
    }
    
    else if( $time instanceof Zend_Date )
    {
      $time = $time->toValue();
    }

    if( !is_numeric($time) ) {
      //Zend_Registry::get('Zend_Log')->log(sprintf('Unknown timestamp format: %s (%s) ', $time, gettype($time)), Zend_Log::WARN);
      return $this->view->translate('Invalid date');
      //throw new Zend_View_Exception(sprintf('Unknown timestamp format: %s (%s) ', $time, gettype($time)));
    }

    // Register in headTranslate helper
    if( !self::$_registeredInTranslate ) {
      $this->view->headTranslate(array(
        'now', 'in a few seconds', 'a few seconds ago', '%s minute ago',
        'in %s minute', '%s hour ago', 'in %s hour', '%s at %s',
      ));
      self::$_registeredInTranslate = true;
    }

    // Prepare content
    $this->_extraClass = null;
    $content = $this->calculateDefaultTimestamp($time);
    
    // Prepare attributes
    // Format: 'Tue, 08 Dec 2009 19:59:52 -0800'
    $tag = $this->_tag;
    if( isset($attribs['tag']) ) {
      $tag = $attribs['tag'];
      unset($attribs['tag']);
    }

    // Prepare data in locale timezone
    $timezone = null;
    if( Zend_Registry::isRegistered('timezone') ) {
      $timezone = Zend_Registry::get('timezone');
    }
    if( null !== $timezone ) {
      $prevTimezone = date_default_timezone_get();
      date_default_timezone_set($timezone);
    }
	$t = new Zend_Date();
    $attribs['title'] = $t->jdate("l، j F Y G:i:s", $time);
    
    if( null !== $timezone ) {
      date_default_timezone_set($prevTimezone);
    }
    
    if( empty($attribs['class']) ) {
      $attribs['class'] = '';
    } else {
      $attribs['class'] .= ' ';
    }
    $attribs['class'] .= 'timestamp';
    if( $this->_extraClass ) {
      $attribs['class'] .= ' ' . $this->_extraClass;
    }

    return '<'
      . $tag
      . $this->_htmlAttribs($attribs)
      . '>'
      . $content
      . '</'
      . $tag
      . '>'
      ;
  }

  public function setTag($tag)
  {
    $this->_tag = $tag;
    return $this;
  }

  public function calculateDefaultTimestamp($time)
  {
	$t = new Zend_Date();
    $now = time();
    $deltaNormal = $time - $now;
    //$deltaNormal = $now - $time;
    $delta = abs($deltaNormal);
    $isPlus = ($deltaNormal > 0);

    // Prepare data in locale timezone
    $timezone = null;
    if( Zend_Registry::isRegistered('timezone') ) {
      $timezone = Zend_Registry::get('timezone');
    }
    if( null !== $timezone ) {
      $prevTimezone = date_default_timezone_get();
      date_default_timezone_set($timezone);
    }
    
    $nowDay = date('d', $now);
	
    $tsDay = date('d', $time);
	
    $nowWeek = $t->jdate('W', $now);
	
    $tsWeek = $t->jdate('W', $time);
	
	
    $tsDayOfWeek = date('D', $time);
	$jtsDayOfWeek = $t->jdate('l', $time);

    if( null !== $timezone ) {
      date_default_timezone_set($prevTimezone);
    }

    // Right now
    if( $delta < 1 )
    {
      $val = null;
      if( $isPlus ) {
        $key = 'now';
      } else {
        $key = 'now';
      }
    }

    // less than a minute
    else if( $delta < 60 )
    {
      $val = null;
      if( $isPlus ) {
        $key = 'in a few seconds';
      } else {
        $key = 'a few seconds ago';
      }
    }

    // less than an hour ago
    else if( $delta < self::HOUR )
    {
      $val = floor($delta / 60);
      if( $isPlus ) {
        $key = array('in %s minute', 'in %s minutes', $t->tr_num($val, 'fa'));
      } else {
        $key = array('%s minute ago', '%s minutes ago', $t->tr_num($val, 'fa'));
      }
    }

    // less than 12 hours ago, or less than a day ago and same day
    else if( $delta < self::HOUR * 12 || ($delta < self::DAY && $tsDay == $nowDay) )
    {
      $val = floor($delta / (60 * 60));
      if( $isPlus ) {
        $key = array('in %s hour', 'in %s hours', $t->tr_num($val, 'fa'));
      } else {
        $key = array('%s hour ago', '%s hours ago', $t->tr_num($val, 'fa'));
      }
    }

    // less than a week ago and same week
    else if( $delta < self::WEEK && $tsWeek == $nowWeek )
    {
      return $this->view->translate(
        '%s - %s',
        $jtsDayOfWeek,
        $this->view->locale()->toTime($time, array('size' => 'short'))
      );
    }

    // less than a year and same year
    else if( $delta < self::YEAR && date('Y', $time) == date('Y', $now) )
    {
		return $t->jdate("j F", $time);
    }

    // Otherwise use the full date
    else
    {
      return $t->jdate("Y/m/d", $time);
    }

    //$this->_extraClass = 'timestamp-update';
    
    $translator = $this->view->getHelper('translate');
    if( $translator ) {
      return $translator->translate($key, $val);
    } else {
      $key = is_array($string) ? $string[0] : $key;
      return sprintf($string, $val);
    }
  }
}