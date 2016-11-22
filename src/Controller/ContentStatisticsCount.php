<?php

/**
 * Contains \Drupal\content_statistics\Controller\ContentStatisticsCount.
 */ 

namespace Drupal\content_statistics\Controller;

use Drupal\Core\Controller\ControllerBase;

class ContentStatisticsCount extends ControllerBase {
  
  public function contentStatisticsCountInsert($nid, $body_value) {
    $counts = self::contentStatisticsCalculate($body_value);
    \Drupal::database()
      ->insert('content_statistics')
      ->fields(array(
        'letter' => $counts['#letter'],
        'word' => $counts['#word'],
        'sentence' => $counts['#sentence'],
        'nid' => $nid,
      ))
      ->execute();
  }
  
  public function contentStatisticsCountUpdate($nid, $body_value) {
    $counts = self::contentStatisticsCalculate($body_value);
    $exists = self::contentStatisticsCheckCount($nid);
    if (!empty($exists)) {
      \Drupal::database()
        ->update('content_statistics')
        ->condition('nid', $nid)
        ->fields(array(
          'letter' => $counts['#letter'],
          'word' => $counts['#word'],
          'sentence' => $counts['#sentence'],
        ))
        ->execute();
    }
    else {
      self::contentStatisticsCountInsert($nid, $body_value);
    }
  }
  
  public function contentStatisticsCalculate($body_value) {
    $body_value = strip_tags($body_value);
    if (!empty($body_value)) {
	  $letter = strlen(preg_replace('/\s|&nbsp;/', '', $body_value));
	  $word = str_word_count($body_value);
      $sentence = preg_match_all('/[^\s](\.|\!|\?)(?!\w)/', $body_value, $match);
    }
    $letter_count = !empty($letter) ? $letter : 0;
    $word_count = !empty($word) ? $word : 0;
    $sentence_count = !empty($sentence) ? $sentence : (!empty($letter) ? '1' : 0);
    $counts = array(
      '#letter' => $letter_count,
      '#word' => $word_count,
      '#sentence' => $sentence_count,
    );
    return $counts;
  }
  
  public function contentStatisticsCountDelete($nid) {
    \Drupal::database()
      ->delete('content_statistics')
      ->condition('nid', $nid)
      ->execute();
  }
  
  public function contentStatisticsCheckType($type) {
    $selected_types = \Drupal::config('content_statistics.settings')->get('content_statistics.content_types');
    if (!empty($selected_types[$type])) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }
  
  public function contentStatisticsCheckCount($nid) {
    $exists = \Drupal::database()
      ->select('content_statistics', 'content_statistics')
      ->fields('content_statistics')
      ->condition('content_statistics.nid', $nid)
      ->execute()
      ->fetchAll();
    return $exists;
  }
}
