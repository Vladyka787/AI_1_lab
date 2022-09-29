<?php
echo "Начинается работа программы.\nВам будут заданы вопросы, отвечайте на них 1(ДА) или 0(НЕТ).\n";

$str_base = file_get_contents('base.txt');
$arr_base = explode("\n", $str_base);

$str_sootv = file_get_contents('sootv.txt');
$arr_sootv = explode("\n", $str_sootv);

$arr_buffer = 'ЕСЛИ №1=';
$result = null;
$chance = [];

while ($result == null) {

    if (preg_match("/(ДА|НЕТ)$/u", $arr_buffer)) {
        $arr_buffer_old = $arr_buffer;
        foreach ($arr_base as $item) {
            if ($arr_buffer != $arr_buffer_old) {
                break;
            }
            for ($i = 1, $iMax = count($arr_sootv); $i <= $iMax; $i++) {
                if ($arr_buffer != $arr_buffer_old) {
                    break;
                }
                if (preg_match("/" . $arr_buffer . " И №" . $i . "=" . "/u", $item)) {
                    $arr_buffer = $arr_buffer . " И №" . $i . "=";
                }
            }

            if (preg_match("/" . $arr_buffer . " ТО ИГРА=" . "/u", $item)) {
                $arr_buffer = $arr_buffer . "ТО ИГРА=";
                $result = trim($item, $arr_buffer);
            }
        }
    } else {
        if (preg_match_all("/№[0-9]=(?!(ДА|НЕТ))/u", $arr_buffer, $match) || preg_match_all("/№[1-9][0-9]=(?!(ДА|НЕТ))/u", $arr_buffer, $match)) {
            $number = $match[0][0];
            $question = '';
            foreach ($arr_sootv as $item) {
                if (preg_match("/" . $number . "/", $item)) {
                    $question = trim($item, $number);
                    echo $question . "\n";
                    echo "Введите свой ответ: ";
                    $answer = fgets(STDIN);
                    if ($answer == 1) {
                        $arr_buffer = $arr_buffer . 'ДА';
                        $chance[] = $number . 'ДА';
                    } else {
                        $arr_buffer = $arr_buffer . 'НЕТ';
                        $chance[] = $number . 'НЕТ';
                    }

                    break;
                }
            }
        }
    }

}

echo "\n";
echo "Вам подойдет игра: " . $result . "\n";
echo "\n";

$chance_result = [];

for ($i = 0, $iMax = count($arr_base); $i < $iMax; $i++) {
    $chance_result[$i] = 0;
}

for ($i = 0, $iMax = count($chance); $i < $iMax; $i++) {
    for ($j = 0, $jMax = count($arr_base); $j < $jMax; $j++) {
        if (preg_match("/" . $chance[$i] . "/u", $arr_base[$j])) {
            $chance_result[$j]++;
        }
    }
}

echo "Вероятность:\n";

for ($i = 0, $iMax = count($arr_base); $i < $iMax; $i++) {
    preg_match_all("/ИГРА=.+/u", $arr_base[$i], $match);
    $str = trim($match[0][0], "ИГРА=");

    echo $str . ": " . floor(($chance_result[$i] / $iMax * 1000)) / 1000 . "\n";
}