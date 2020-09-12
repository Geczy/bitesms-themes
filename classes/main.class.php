<?php
$screenshots = json_decode(file_get_contents('screens.json'), TRUE);
$results = $screenshots['results'];
$results['count'] = count($screenshots);

// use get variable to paging number
$page = !isset($_GET['page']) ? 1 : $_GET['page'];
$limit = 8; // five rows per page
$offset = ($page - 1) * $limit; // offset
$total_items = count($screenshots); // total items
$total_pages = ceil($total_items / $limit);
$final = array_splice($screenshots, $offset, $limit); // splice them according to offset and limit

if (empty($_GET['kimonoUpdate'])) {
  return;
}

require 'simple_html_dom.php';

function trailingslashit($string) {
  return untrailingslashit($string) . '/';
}

function untrailingslashit($string) {
  return rtrim($string, '/\\');
}

$request = "https://www.kimonolabs.com/api/9oalrm34?apikey=d9ffb71cb4e39796d6802b7260dae244";
$response = file_get_contents($request);
$results = json_decode($response, TRUE);

// Save 'results' from json request
$resultsCopy = $results;
unset($resultsCopy['results']);
$screenshots['results'] = $resultsCopy;

foreach ($results['results']['detail'] as $key => $value) {
  if (strstr($value['category'], 'Theme') === FALSE) {
    continue;
  }

  $id = explode('http://cydiaupdates.com/cydia://package/', $value['cydiaurl']);
  $id = end($id);

  if (!empty($screenshots[$id])) {
    continue;
  }

  $screenshots[$id] = $value;
  $isModMyi = strstr($value['depictionurl'], 'modmyi.com/') !== FALSE;
  $isMacCiti = strstr($value['depictionurl'], 'macciti.com/') !== FALSE;
  $isZodttd = strstr($value['depictionurl'], 'zodttd.com/') !== FALSE;
  $isTouchrevteam = strstr($value['depictionurl'], 'touchrevteam.com/') !== FALSE;
  $isBigBoss = strstr($value['depictionurl'], 'thebigboss.org/') !== FALSE;

  if ($isModMyi) {
    $value['screenshot_url'] = str_replace('.d.php', '.php', $value['depictionurl']);
  }

  if ($isBigBoss) {
    $value['screenshot_url'] = str_replace('Dp.php', '.php', $value['depictionurl']);
  }

  if ($isTouchrevteam) {
    $value['screenshot_url'] = str_replace('.php', '.html.php', $value['depictionurl']);
  }

  if ($isMacCiti) {
    $value['screenshot_url'] = str_replace('index.php', 'screenshots.php', $value['depictionurl']);
  }

  if ($isZodttd) {
    $value['screenshot_url'] = $value['depictionurl'];
  }

  if (empty($value['screenshot_url'])) {
    // var_dump($value);
    continue;
  }

  $html = file_get_html($value['screenshot_url']);

  if (empty($html)) {
    // var_dump($value);
    continue;
  }

  if ($isMacCiti) {
    foreach ($html->find('img') as $image) {
      $screenshots[$id]['pics'][] = trailingslashit(str_replace('screenshots.php', '', $value['screenshot_url'])) . $image->attr['src'];
    }
  }

  if ($isZodttd) {
    foreach ($html->find('img[src^=screenshots]') as $image) {
      $screenshots[$id]['pics'][] = trailingslashit(current(explode("depiction.php", $value['screenshot_url']))) . $image->attr['src'];
    }
  }

  if ($isTouchrevteam) {
    foreach ($html->find('img[src^=Images]') as $image) {
      $screenshots[$id]['pics'][] = trailingslashit(current(explode(".com/", $value['screenshot_url'])) . '.com') . $image->attr['src'];
    }
  }

  if ($isBigBoss) {
    foreach ($html->find('img[src]') as $image) {
      $screenshots[$id]['pics'][] = trailingslashit(current(explode("/moreinfo/", $value['screenshot_url'])) . '/moreinfo/') . $image->attr['src'];
    }
  }

  if ($isModMyi) {
    foreach ($html->find('img[src^=http]') as $image) {
      $screenshots[$id]['pics'][] = $image->attr['src'];
    }
  }

}

foreach ($screenshots as $key => $part) {
  $sort[$key] = !empty($part['date']) ? strtotime($part['date']) : 0;
}
array_multisort($sort, SORT_DESC, $screenshots);

$json = json_encode($screenshots);
$file = fopen('screens.json', 'w');
fwrite($file, $json);
fclose($file);
