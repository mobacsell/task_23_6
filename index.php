<?php

include 'example_persons_array.php';

//echo getFullnameFromParts('Алексеев', 'Александр', 'Владимирович');
//print_r(getPartsFromFullname('Алексеева Диана Владимировна'));
//echo getShortName('Митрофанов Олег Николаевич');
//echo getGenderFromName('Суворова Анастасия Михайловна');
//getGenderDescription($example_persons_array);
//getPerfectPartner('Митрофанова', 'Наталия', 'Юрьевна', $example_persons_array);

function getFullnameFromParts($surname, $name, $patronomyc) {
//Функция принимает в качестве аргументов строки <Фамилия>, <Имя>, <Отчество> и возвращает склеенную строку <Фамилия Имя Отчество>.

    return "$surname $name $patronomyc";  
}

function getPartsFromFullname($fullname) {
//Функция принимает в качестве аргумента склеенную строку ФИО и преобразует ее в массив с ключами name, surname, patronomyc.
    
    $arr = explode(' ', $fullname);
    $arr = array_combine(['surname', 'name', 'patronomyc'], $arr);
    return $arr;
}

function getShortName($fullname) {
//Функция принимает в качестве аргумента строку <Фамилия Имя Отчество> и возвращает строку вида <Фамилия И.>
        
    $name = getPartsFromFullName($fullname)['name'];
    $surname = getPartsFromFullName($fullname)['surname'];

    return $surname . ' ' . mb_substr($name, 0, 1) . '.';
}

function getGenderFromName($fullname) {
//Функция определяет пол по ФИО. Если возвращает 1, то муж. пол; если -1, то жен. пол; если 0, то пол не определен.
    
    $sexNum = 0;
    $name = getPartsFromFullName($fullname)['name'];
    $surname = getPartsFromFullName($fullname)['surname'];
    $patronomyc = getPartsFromFullName($fullname)['patronomyc'];
    
    if (mb_substr($patronomyc, -3) === 'вна') {
        $sexNum -= 1;
    } elseif (mb_substr($patronomyc, -2) === 'ич') {
        $sexNum += 1;
    }

    if (mb_substr($name, -1) === 'а') {
        $sexNum -= 1;
    } elseif (mb_substr($name, -1) === 'й' || mb_substr($name, -1) === 'н') {
        $sexNum += 1;
    }

    if (mb_substr($surname, -2) === 'ва') {
        $sexNum -= 1;
    } elseif (mb_substr($surname, -1) === 'в') {
        $sexNum += 1;
    }

    return $sexNum <=> 0;
}

function getGenderDescription($array) {
//Функция определяет гендерный состав аудитории.

    $maleArray = array_filter($array, function($value) {
        return getGenderFromName($value['fullname']) === 1;
    });

    $femaleArray = array_filter($array, function($value) {
        return getGenderFromName($value['fullname']) === -1;
    });

    $undefSexArray = array_filter($array, function($value) {
        return getGenderFromName($value['fullname']) === 0;
    });

    $genderDescArray =  array('male' => round( count($maleArray) / count($array) * 100, 1 ), 
                            'female' => round( count($femaleArray) / count($array) * 100, 1 ), 
                            'undefinedSex' => round( count($undefSexArray) / count($array) * 100, 1 )
                            );

    echo <<<HEREDOC
    Гендерный состав аудитории: <br>
    --------------------------- <br>
    Мужчины - {$genderDescArray['male']}% <br>
    Женщины - {$genderDescArray['female']}% <br>
    Не удалось определить - {$genderDescArray['undefinedSex']}%
    HEREDOC;
}

function getPerfectPartner($surname, $name, $patronomyc, $arrPersons)    {
//Функция принимает в качестве аргумента Фамилию, Имя и Отчество и отбирает из массива персон идеальную пару для указанной.

    $surname = mb_convert_case($surname, MB_CASE_TITLE_SIMPLE);
    $name = mb_convert_case($name, MB_CASE_TITLE_SIMPLE);
    $patronomyc = mb_convert_case($patronomyc, MB_CASE_TITLE_SIMPLE);

    $fullnameFirst = getFullnameFromParts($surname, $name, $patronomyc);
    $sexFirst = getGenderFromName($fullnameFirst);

    $getShortName = 'getShortName';
    $randomPercent = rand(5000, 10000) / 100;
    $heart = mb_chr(9825, 'UTF-8');
    
    foreach($arrPersons as $value)  {
        $random = rand(1, count($arrPersons) - 1);
        $fullnameSecond = $arrPersons[$random]['fullname'];
        $sexSecond = getGenderFromName($fullnameSecond);
        if($sexSecond * (-1) === $sexFirst)   {
            break;
        }
    } 

    echo <<<HEREDOC
    {$getShortName($fullnameFirst)} + {$getShortName($fullnameSecond)} = <br>
    $heart Идеально на {$randomPercent}% $heart
    HEREDOC;
}