<?php
  $output = [];
  foreach($items as $item)
  {
    $output_index = count($output);
    $output[$output_index]['item']['time_starred'] = $item->time_starred;
    $output[$output_index]['item']['is_starred'] = $item->time_starred > 0;
    $output[$output_index]['item']['time_read'] = $item->time_read;
    $output[$output_index]['item']['is_read'] = $item->time_read > 0;
    $output[$output_index]['item']['guid'] = $item->guid;
    $output[$output_index]['item']['title'] = $item->title;
    $output[$output_index]['item']['pub_date'] = $item->pub_date;
    $output[$output_index]['item']['link'] = $item->link;
    $output[$output_index]['feed']['id'] = $item->feed->id;
    $output[$output_index]['feed']['title'] = $item->feed->title;
  }
  echo json_encode($output);
?>
