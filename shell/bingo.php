<?php
define('HIT', '◯ ');
define('MATRIX', !empty($argv[1])? $argv[1]: 5);
define('DEBUG', !empty($argv[2])? true: false);

if ((MATRIX % 2) == 0) {
  echo '奇数じゃないと。。' . PHP_EOL;
  exit();
}

function drow_line(){
  echo "      ";
  for($i = 0;$i < MATRIX;$i++){
    echo "+-----";
  }
  echo "+" . PHP_EOL;
}
function show_bingo($bingo, $last_result = null){
  echo "      ";
  for($i = 0;$i < MATRIX;$i++){
    printf('%4d列', $i + 1);
  }
  echo PHP_EOL;

  drow_line();
  for($i = 0;$i < MATRIX * MATRIX;$i++){
    if (($i % MATRIX) == 0) {
      printf('%4d行', ($i + 1 + MATRIX) / MATRIX);
    }
    if (is_numeric($bingo[$i])) {
      printf('| %3d ', $bingo[$i]);
    } else {
      $color = 0;
      if (!empty($last_result['column']['bingo'][($i % MATRIX)])
        || !empty($last_result['line']['bingo'][(int)($i / MATRIX)])
        || (($last_result['aslant'][0] == 5) && ($i % (MATRIX + 1) == 0) )
        || (($last_result['aslant'][1] == 5) && (($i % (MATRIX - 1)) == 0) ) ) {
        $color = 1;
      } else if (!empty($last_result['column']['riti'][($i % MATRIX)])
        || !empty($last_result['line']['riti'][(int)($i / MATRIX)])
        || (($last_result['aslant'][0] == 4) && ($i % (MATRIX + 1) == 0) )
        || (($last_result['aslant'][1] == 4) && (($i % (MATRIX - 1)) == 0) ) ) {
        $color = 2;
      }
      printf("|\e[4%sm     \e[49m", $color);
    }
    if (($i % MATRIX) == (MATRIX - 1)) {
      echo '|' . PHP_EOL;
      drow_line();
    }
  }
}

function show_display($bingo, $bingo_init, $select = null, $last_result = null){
  echo "\033[;H\033[2J";
  if (!empty($select)) {
    echo '抽選済み' . implode(',', $select) . PHP_EOL;
  } else {
    echo PHP_EOL;
  }

  echo PHP_EOL . "初期配置" . PHP_EOL;
  show_bingo($bingo_init);
  echo PHP_EOL . "現在" . PHP_EOL;
  show_bingo($bingo, $last_result);
}

function get_num(){
  for($i = 1;$i <= MATRIX * MATRIX * 3;$i++){
    $num[$i - 1] = $i;
  }
  return $num;
}
// 添字削って削ったやつ返す
function random_key_delete($array){
  $key = rand(0, count($array) - 1);
  $value = $array[$key];
  unset($array[$key]);
  $array = array_merge($array);
  return array($value, $array);
}

function init(){
  $num = get_num();
  for($i = 0;$i < MATRIX * MATRIX;$i++){
    list($value, $num) = random_key_delete($num);
    $bingo[$i] = $value;
  }
  return $bingo;
}
function my_wait(){
  while(!DEBUG){
    if (fgets(STDIN)) {
      return;
    }
  }
}
function check($bingo, $bingo_init, $select){
  $aslant = array(0 => 0, 1 => 0);
  $line = array(
    'bingo' => array()
    ,'riti' => array()       //リーチ英語？
  );
  $column = $line;
  for($i = 0;$i < MATRIX;$i++){
    // 行チェック
    $count = 0;
    for($j = 0;$j < MATRIX;$j++){
      $key = ($i * MATRIX) + $j;
      $flag = true;
      if (!is_numeric($bingo[$key])) {
        $count++;
      }
    }
    switch ($count) {
      case 4:
        $line['riti'][$i] = $i + 1;
        break;
      case 5:
        $line['bingo'][$i] = $i + 1;
        break;
      default:
        break;
    }

    // 列チェック
    $count = 0;
    for($j = 0;$j < MATRIX;$j++){
      $key = $i + ($j * MATRIX);
      $flag = true;
      if (!is_numeric($bingo[$key])) {
        $count++;
      }
    }
    switch ($count) {
      case 4:
        $column['riti'][$i] = $i + 1;
        break;
      case 5:
        $column['bingo'][$i] = $i + 1;
        break;
      default:
        break;
    }

    // 左上から右下
    if (!is_numeric($bingo[(MATRIX + 1) * $i])) {
      $aslant[0]++;
    }

    // 右上から左下
    if (!is_numeric($bingo[(MATRIX - 1) * ($i + 1)])) {
      $aslant[1]++;
    }
  }
  $result = '';
  if (!empty($line['riti'])) {
    $result .= sprintf("\e[32m%s行目がリーチでした。。\e[39m" . PHP_EOL, implode(',', $line['riti']));
  }
  if (!empty($line['bingo'])) {
    $result .= sprintf("\e[31m%s行目がビンゴ！\e[39m" . PHP_EOL, implode(',', $line['bingo']));
  }
  if (!empty($column['riti'])) {
    $result .= sprintf("\e[32m%s列目がリーチでした。。\e[39m" . PHP_EOL, implode(',', $column['riti']));
  }
  if (!empty($column['bingo'])) {
    $result .= sprintf("\e[31m%s列目がビンゴ！\e[39m" . PHP_EOL, implode(',', $column['bingo']));
  }
  switch ($aslant[0]) {
    case 4:
      $result .= "\e[32m左上から右下がリーチでした。。\e[39m" . PHP_EOL;
      break;
    case 5:
      $result .= "\e[31m左上から右下がビンゴ!\e[39m" . PHP_EOL;
      break;
    default:
      break;
  }
  switch ($aslant[1]) {
    case 4:
      $result .= "\e[32m右上から左下がリーチでした。。\e[39m" . PHP_EOL;
      break;
    case 5:
      $result .= "\e[31m右上から左下がビンゴ!\e[39m" . PHP_EOL;
      break;
    default:
      break;
  }
  show_display($bingo, $bingo_init, $select, compact('line', 'column', 'aslant'));

  if (empty($result)) {
    echo '(´・ω・`)ビンゴなし' . PHP_EOL;
  } else {
    echo $result;
  }
}

function main(){
  $bingo = init();
  $bingo_init = $bingo;
  $bingo[(int)(MATRIX * MATRIX / 2)] = HIT;
  show_display($bingo, $bingo_init);
  echo '最初は真ん中開けます' . PHP_EOL;
  echo '後はエンター押すだけ' . PHP_EOL;

  $lottery = get_num();      // 抽選用
  $select = array();         // 抽選された数字

  for($i = 0;$i < 50;$i++){
    my_wait();
    $result_txt = '';
    list($value, $lottery) = random_key_delete($lottery);
    $key = array_search($value, $bingo, true);
    if ($key !== false) {
      $bingo[$key] = HIT;
      $result_txt = "ヒット" . PHP_EOL;
    }
    show_display($bingo, $bingo_init, $select);
    echo PHP_EOL;
    printf("！！\e[31m%2d\e[39m！！" . PHP_EOL, $value);
    echo $result_txt;

    $select[] = $value;
  }
  check($bingo, $bingo_init, $select);
}

main();