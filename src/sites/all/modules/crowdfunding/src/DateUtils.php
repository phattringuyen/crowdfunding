<?php

namespace Drupal\crowdfunding;

/**
 * @file
 */
class DateUtils {

  /**
   *
   * @param int $timestamp_difference
   * @return array
   */
  static public function formatTimeRemainingString($timestamp_difference) {

    if ($timestamp_difference < 1) {
      return array(
        'amount' => 0,
        'text' => '',
      );
    }

    $a = array(
      12 * 30 * 24 * 60 * 60 => 'year',
      30 * 24 * 60 * 60 => 'month',
      24 * 60 * 60 => 'day',
      60 * 60 => 'hour',
      60 => 'minute',
      1 => 'second'
    );

    foreach ($a as $secs => $str) {
      $d = $timestamp_difference / $secs;
      if ($d >= 1) {
        $number = round($d);
        return array(
          'amount' => $number,
          'text' => $str . ($number > 1 ? 's' : '') . t(' to go'),
        );
      }
    }
  }
}