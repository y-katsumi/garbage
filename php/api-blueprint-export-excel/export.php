<?php
require __DIR__ . '/vendor/autoload.php';

$system_title = '';

$contents = fileRead('index.md');
$data = getDataSource($contents);
export($data);



function fileRead($file)
{
    $body = [];
    $i = -1;
    $fp = fopen($file, 'r');
    $is_comment_out = false;
    while (!feof($fp)) {
        $line = fgets($fp);
        if (preg_match('/^## (.*) [[](.*) (.*)[]]$/', $line, $match)) {
            $i++;
            $body[$i] = [
                'title' => $match[1]
                ,'method' => $match[2]
                ,'url' => $match[3]
                ,'line' => []
            ];
        } else {
            if (isset($body[$i]['line'])) {
                if (strpos($line, '<!') !== false) {
                    $is_comment_out = true;
                } else if (strpos($line, '->') !== false) {
                    $is_comment_out = false;
                } else if (!$is_comment_out) {
                    $line = preg_replace("/\r\n|\r|\n/", "", $line);
                    if (!empty($line)) {
                        $body[$i]['line'][] = $line;
                    }
                }
            }
        }
    }
    fclose($fp);
    return $body;
}
// データソース抜き出し
function getDataSource($contents)
{
    $data_source = [];
    $last_key = count($contents) - 1;
    $last = $contents[$last_key];
    $contents[$last_key]['line'] = [];
    $is_data_source = false;
    foreach($last['line'] as $line){
        if (strpos($line, '## Data Structure') !== false) {
            $is_data_source = true;
        } else if (!$is_data_source) {
            $contents[$last_key]['line'][] = $line;
        } else {
            if (preg_match('/^### (.*)$/', $line, $match)) {
                $data_source_key = sprintf('(%s)', $match[1]);
                $data_source[$data_source_key] = [];
            } else if (isset($data_source[$data_source_key])) {
                $data_source[$data_source_key][] = $line;
            }
        }
    }
    return compact('contents', 'data_source');
}




function export($data)
{
    $contents = addDataSource($data);

    $book = new PHPExcel();
    $book = apiListSheet($book, $contents);
    $book = apiSheet($book, $contents);

    $writer = PHPExcel_IOFactory::createWriter($book, 'Excel2007');
    $writer->save('export.xlsx');
}
// datasourceを入れ込む
function addDataSource($data)
{
    $new = [];
    $data_source = $data['data_source'];
    foreach($data['contents'] as $key1 => $content){
        $new[$key1] = $content;
        $key2 = 0;
        foreach($content['line'] as $line){
            $new[$key1]['line'][$key2] = $line;
            $key2++;
            if (preg_match('/(.*)\((.*)\)(.*)$/', $line, $match)) {
                $key = '(' . $match[2] . ')';
                if (isset($data_source[$key])) {
                    foreach($data_source[$key] as $add_data){
                        $tab = substr_count($line, '    ');
                        for($i = 0;$i <= $tab;$i++){
                            $add_data .= '    ';
                        }
                        $new[$key1]['line'][$key2] = $add_data;
                        $key2++;
                    }
                }
            }
        }
    }
    return $new;
}
function apiListSheet($book, $contents)
{
    $sheet = $book->getActiveSheet()->setTitle('一覧');
    $sheet = setDefault($sheet);

    $line_no = 1;
    $column = 0;
    $sheet->setCellValueByColumnAndRow($column++, $line_no, 'No');
    $sheet->setCellValueByColumnAndRow($column++, $line_no, 'METHOD');
    $sheet->setCellValueByColumnAndRow($column++, $line_no, 'API名');
    $sheet->setCellValueByColumnAndRow($column++, $line_no, 'URL');

    foreach($contents as $content){
        $line_no++;
        $column = 0;
        $sheet->setCellValueByColumnAndRow($column++, $line_no, $line_no - 1);
        $sheet->setCellValueByColumnAndRow($column++, $line_no, $content['method']);
        $sheet->setCellValueByColumnAndRow($column++, $line_no, $content['title']);
        $sheet->setCellValueByColumnAndRow($column++, $line_no, $content['url']);
    }
    
    return $book;
}
function apiSheet($book, $contents)
{
    foreach($contents as $content){
        $sheet = $book->createSheet()->setTitle($content['title']);
        $sheet = setDefault($sheet);

        $line_no = 1;
        $column = 0;
        $sheet->setCellValueByColumnAndRow($column++, $line_no, 'METHOD');
        $sheet->setCellValueByColumnAndRow($column++, $line_no, $content['method']);
        $sheet->setCellValueByColumnAndRow($column++, $line_no, 'API名');
        $sheet->setCellValueByColumnAndRow($column++, $line_no, $content['title']);
        $sheet->setCellValueByColumnAndRow($column++, $line_no, 'URL');
        $sheet->setCellValueByColumnAndRow($column++, $line_no, $content['url']);
        $line_no++;
        foreach($content['line'] as $line){
            $data = repTab($line);
            if (preg_match('/^\+(.*):(.*)\((.*)\)-(.*)$/', $data['line'], $match)) {
                if (preg_match('/^(.*),(.*)$/', $match[3], $type)) {
                    $sheet->setCellValueByColumnAndRow($data['tab'], ++$line_no, $match[1]);
                    $sheet->setCellValueByColumnAndRow(++$data['tab'], $line_no, $type[1]);
                    $sheet->setCellValueByColumnAndRow(++$data['tab'], $line_no, $type[2]);
                    $sheet->setCellValueByColumnAndRow(++$data['tab'], $line_no, $match[4]);
                } else {
                    $sheet->setCellValueByColumnAndRow($data['tab'], ++$line_no, $match[1]);
                    $sheet->setCellValueByColumnAndRow(++$data['tab'], $line_no, $match[3]);
                    $sheet->setCellValueByColumnAndRow(++$data['tab'], $line_no, $match[4]);
                }
            } else if (preg_match('/^\+(.*):\((.*)\)$/', $data['line'], $match)) {
                $sheet->setCellValueByColumnAndRow($data['tab'], ++$line_no, $match[1]);
                $sheet->setCellValueByColumnAndRow(++$data['tab'], $line_no, $match[2]);
            } else {
                if (preg_match('/^### (.*)$/', $line, $match)) {
                    $sheet->getStyleByColumnAndRow($data['tab'], ++$line_no)->getFont()->setSize(14);
                    $sheet->getStyleByColumnAndRow($data['tab'], $line_no)->getFont()->setBold(true);
                    $sheet->setCellValueByColumnAndRow($data['tab'], $line_no, $match[1]);
                } else {
                    $sheet->setCellValueByColumnAndRow($data['tab'], ++$line_no, $data['line']);
                }
            }
        }
    }
    
    return $book;
}
function repTab($line)
{
    $data['tab'] = 0;
    $data['line'] = str_replace(' ', '', $line);
    $data['line'] = str_replace('(number)', '(int)', $data['line']);
    if ($tab = substr_count($line, '    ')) {
        $data['tab'] = $tab;
    }
    return $data;
}
function setDefault($sheet)
{
    $sheet->getDefaultStyle()->getFont()->setName('ＭＳＰ ゴシック')->setSize(12);
    return $sheet;
}