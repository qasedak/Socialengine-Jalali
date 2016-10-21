<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Event
 * @copyright  Copyright 2006-2010 Qasedak Group - Socialtools.ir
 * @license    http://www.socialengine.com/license/
 * @version    $Id: browse.tpl 9319 2011-09-23 20:48:26Z john $
 * @author     John Boehr <john@socialengine.com>
 */
/** Software Hijri_Shamsi , Solar(Jalali) Date and Time
Copyright(C)2011, Reza Gholampanahi , http://jdf.scr.ir
version 2.55 :: 1391/08/24 = 1433/12/18 = 2012/11/15 */

?>

<?php if( count($this->paginator) > 0 ): ?>
  <ul class='events_browse'>
    <?php foreach( $this->paginator as $event ): ?>
      <li>
        <div class="events_photo">
          <?php echo $this->htmlLink($event->getHref(), $this->itemPhoto($event, 'thumb.normal')) ?>
        </div>
        <div class="events_options">
        </div>
        <div class="events_info">
          <div class="events_title">
            <h3><?php echo $this->htmlLink($event->getHref(), $event->getTitle()) ?></h3>
          </div>
      <div class="events_members">
        <?php
			$value = $event->starttime;
			if( is_string($value) && preg_match('/^(\d{4})-(\d{2})-(\d{2})( (\d{2}):(\d{2})(:(\d{2}))?)?$/', $value, $m) )
			{
				$stamp = mktime((int)$m[5], (int)$m[6], (int)$m[7], (int)$m[2], (int)$m[3], (int)$m[1]);
				$t = new Zend_Date();
				$shamsiDate = $t->jdate("j F Y - G:i", $stamp);
				echo $shamsiDate;
			}
		?>
      </div>
          <div class="events_members">
            <?php echo $this->translate(array('%s guest', '%s guests', $event->membership()->getMemberCount()),$this->locale()->toNumber($event->membership()->getMemberCount())) ?>
            <?php echo $this->translate('led by') ?>
            <?php echo $this->htmlLink($event->getOwner()->getHref(), $event->getOwner()->getTitle()) ?>
          </div>
          <div class="events_desc">
            <?php echo $event->getDescription() ?>
          </div>
        </div>
      </li>
    <?php endforeach; ?>
  </ul>

  <?php if( $this->paginator->count() > 1 ): ?>
    <?php echo $this->paginationControl($this->paginator, null, null, array(
      'query' => $this->formValues,
    )); ?>
  <?php endif; ?>

<?php else: ?>

  <div class="tip">
    <span>
    <?php if( $this->filter != "past" ): ?>
      <?php echo $this->translate('Nobody has created an event yet.') ?>
      <?php if( $this->canCreate ): ?>
        <?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="'.$this->url(array('action'=>'create'), 'event_general').'">', '</a>'); ?>
      <?php endif; ?>
    <?php else: ?>
      <?php echo $this->translate('There are no past events yet.') ?>
    <?php endif; ?>
    </span>
  </div>

<?php endif; ?>
