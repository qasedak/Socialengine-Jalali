<?php

/**
 * SocialEngine
 *
 * @category   Engine
 * @package    Engine_View
 * @copyright  Copyright 2006-2010 Qasedak Group - Socialtools.ir
 * @license    http://www.socialengine.com/license/
 * @version    $Id: FormCalendarDateTime.php 8217 2011-01-14 22:58:59Z char $
 * @todo       documentation
 */

/**
 * @category   Engine
 * @package    Engine_View
 * @copyright  Copyright 2006-2010 Qasedak Group - Socialtools.ir
 * @license    http://www.socialengine.com/license/
 */
class Engine_View_Helper_FormCalendarDateTime extends Zend_View_Helper_FormElement
{
  public function formCalendarDateTime($name, $value = null, $attribs = null,
      $options = null, $listsep = "<br />\n")
  {
    $info = $this->_getInfo($name, $value, $attribs, $options, $listsep);
    extract($info); // name, value, attribs, options, listsep, disable

    // Get date format
    if( isset($attribs['dateFormat']) ) {
      $dateFormat = $attribs['dateFormat'];
      //unset($attribs['dateFormat']);
    } else {
      $dateFormat = 'ymd';
    }

    // Get use military time
    if( isset($attribs['useMilitaryTime']) ) {
      $useMilitaryTime = $attribs['useMilitaryTime'];
      //unset($attribs['useMilitaryTime']);
    } else {
      $useMilitaryTime = true;
    }

    // Check value type
    if( is_string($value) && preg_match('/^(\d{4})-(\d{2})-(\d{2})( (\d{2}):(\d{2})(:(\d{2}))?)?$/', $value, $m) ) {
		$stamp = mktime((int)$m[5], (int)$m[6], (int)$m[7], (int)$m[2], (int)$m[3], (int)$m[1]);
		$t = new Zend_Date();
		$shamsiDate = $t->tr_num($t->jdate("Y/m/j", $stamp));
      $tmpDateFormat = trim(str_replace(array('d', 'y', 'm'), array('/%3$d', '/%1$d', '/%2$d'), $dateFormat), '/');
      $value = array();

      // Get date
      $value['date'] = sprintf($tmpDateFormat, $m[1], $m[2], $m[3]);
      if( $value['date'] == '0/0/0' ) {
        unset($value['date']);
      }

      // Get time
      if( isset($m[6]) ) {
        $value['hour'] = $m[5];
        $value['minute'] = $m[6];
        if( !$useMilitaryTime ) {
          $value['ampm'] = ( $value['hour'] >= 12 ? 'PM' : 'AM' );
          if( 0 == (int) $value['hour'] ) {
            $value['hour'] = 12;
          } else if( $value['hour'] > 12 ) {
            $value['hour'] -= 12;
          }
        }
      }
    }

    if( !is_array($value) ) {
      $value = array();
    }


    // Prepare javascript
    
    // Prepare month and day names
    $localeObject = Zend_Registry::get('Locale');
    
    $months = Zend_Locale::getTranslationList('months', $localeObject);
    if($months['default'] == NULL) { $months['default'] = "wide"; }
    $months = $months['format'][$months['default']];

    $days = Zend_Locale::getTranslationList('days', $localeObject);
    if($days['default'] == NULL) { $days['default'] = "wide"; }
    $days = $days['format'][$days['default']];

    $calendarFormatString = trim(preg_replace('/\w/', '$0/', $dateFormat), '/');
    $calendarFormatString = str_replace('y', 'Y', $calendarFormatString);
    
    // Append files and script
    $this->view->headScript()->appendFile($this->view->baseUrl() . '/externals/calendar/js-persian-cal.min.js');
    $this->view->headLink()->appendStylesheet($this->view->baseUrl() . '/externals/calendar/js-persian-cal.css');
    /*$this->view->headScript()->appendScript("
  en4.core.runonce.add(function() {
    window.cal_{$name} = new Calendar({ '{$name}-date': '{$calendarFormatString}' }, {
      classes: ['event_calendar'],
      pad: 0,
      direction: 0,
      months : " . Zend_Json::encode(array_values($months)) . ",
      days : " . Zend_Json::encode(array_values($days)) . ",
      day_suffixes: ['', '', '', ''],
      onHideStart: function()   { if (typeof cal_{$name}_onHideStart    == 'function') cal_{$name}_onHideStart(); },
      onHideComplete: function(){ if (typeof cal_{$name}_onHideComplete == 'function') cal_{$name}_onHideComplete(); },
      onShowStart: function()   { if (typeof cal_{$name}_onShowStart    == 'function') cal_{$name}_onShowStart(); },
      onShowComplete: function(){ if (typeof cal_{$name}_onShowComplete == 'function') cal_{$name}_onShowComplete(); }
    });
  });
");
*/

    return
      '<div class="event_calendar_container" style="display:inline">' .
	  //'<input type="hidden" name="'.$name.'[date]" class="calendar" id="'.$name.'" />'.
      $this->view->formHidden($name . '[date]', @$value['date'], array_merge(array('class' => 'calendar', 'id' => $name . '-date'), (array) @$attribs['dateAttribs'])) .
        '<input type="text" value="'.( @$value['date'] ? @$shamsiDate: $this->view->translate('Select a date') ).'" class="pdate" id="calendar_output_span_' . $name . '-date" style="width: auto;" readonly />' .
      '</div>' ."&nbsp&nbsp;".'<script>var objCal_'.$name.' = new AMIB.persianCalendar( "calendar_output_span_'.$name.'-date", { extraInputID: "'.$name.'-date", extraInputFormat: "DD/MM/YYYY" } );</script>'.
      $this->view->formTime($name, $value, $attribs, $options)
      ;
  }
}