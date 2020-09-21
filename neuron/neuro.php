<?php

define("COUNT_SLOY", 2);
define("COUNT_ON_SLOY", 2);
define("COUNT_OUTPUTS", 2);

define("FUNC_KOEF", 0.3);
define("TEACH_KOEF2", 0.01);

global $isTeach;
$isTeach = false;

global $answers, $ethalons, $fs, $numPrimer, $yOuts;
$answers = array();
$ethalons = array();


global $dopWeight;

$dopWeight = array();

//$weights = array(
//    "w111" => 0.13,
//    "w121" => -0.34,
//    "w211" => -0.42,
//    "w221" => 0.38,
//    "w112" => 0.25,
//    "w122" => 0.07,
//    "w212" => -0.20,
//    "w222" => 0.32,
//    "w113" => -0.41,
//    "w213" => 0.12,
////    "w123" => 0.41,
////    "w223" => -0.12,
//);


$weights = array(
    "w111" => 0.5,
    "w112" => -0.1,
    "w113" => 0.2,
    "w121" => 0.3,
    "w122" => -0.23,
    "w123" => 0.43,
    "w211" => 0.33,
    "w212" => -0.4,
    "w221" => -0.5,
    "w222" => 0.2,
    "w311" => -0.49,
    "w312" => 0.39,
);

$weights = array();

$teachTest = false;
if ($_POST['do'] == 'init') {
    $isTeach = false;

//    startExample($weights, array(2, 1, 3), array(1));
//    startExample($weights, array(1, 2, 1), array(0.5));
//    startExample($weights, array(3, 2, 1), array(1));
//    startExample($weights, array(1, 3, 2), array(0.5));

//    startExample($weights, array(1, 2, 3), array(0));
//    startExample($weights, array(1, 3, 2), array(0));
//    startExample($weights, array(2, 1, 3), array(0.5));
//    startExample($weights, array(2, 3, 1), array(0.5));
//    startExample($weights, array(3, 2, 1), array(1));
//    startExample($weights, array(3, 1, 2), array(1));


    $numPrimer = 1;
    startExample($weights, array(0, 0, 0), array(1,0));
    $numPrimer++;
    startExample($weights, array(2, 0, 0), array(1,0));
    $numPrimer++;
    startExample($weights, array(3, 0, 0), array(1,0));
    $numPrimer++;
    startExample($weights, array(0, 0, 1), array(1,0));
    $numPrimer++;
    startExample($weights, array(0, 0, 2), array(1,0));
    $numPrimer++;
    startExample($weights, array(0, 0, 3), array(1,0));
    $numPrimer++;
    startExample($weights, array(0, 1, 0), array(1,0));
    $numPrimer++;
    startExample($weights, array(0, 1, 1), array(0,1));
    $numPrimer++;
    startExample($weights, array(2, 1, 1), array(0,1));
    $numPrimer++;
    startExample($weights, array(3, 1, 1), array(0,1));
    $numPrimer++;
    startExample($weights, array(1, 0, 0), array(1,0));
    $numPrimer++;
    startExample($weights, array(2, 0, 0), array(1,0));
    $numPrimer++;
    startExample($weights, array(3, 0, 0), array(1,0));
    $numPrimer++;
    startExample($weights, array(1, 0, 1), array(0,1));
    $numPrimer++;
    startExample($weights, array(1, 2, 1), array(0,1));
    $numPrimer++;
    startExample($weights, array(1, 3, 1), array(0,1));
    $numPrimer++;
    startExample($weights, array(1, 1, 0), array(0,1));
    $numPrimer++;
    startExample($weights, array(1, 1, 2), array(0,1));
    $numPrimer++;
    startExample($weights, array(1, 1, 3), array(0,1));
    $numPrimer++;
    startExample($weights, array(1, 1, 1), array(0,1));


    $er = calcNetworkError($answers, $ethalons);

    $res['error'] = $er;
    $res['weights'] = $weights;
    $res['funcAct'] = $fs;
//    $res['answers'] = $yOuts;
    echo json_encode($res);

} elseif ($_POST['do'] == 'teach' || $teachTest) {
    $isTeach = true;

    $weights = $_POST['weights'];
//    $fs = $_POST['funcAct'];
//    $yOuts = $_POST['answers'];
    $countProhodov = $_POST['countProhodov'];

    for ($i = 0; $i < $countProhodov; $i++) {
//        $numPrimer = 1;
//        startExample($weights, array(2, 1, 3), array(1));
//        startExample($weights, array(1, 2, 1), array(0.5));
//        startExample($weights, array(3, 2, 1), array(1));
//        startExample($weights, array(1, 3, 2), array(0.5));

//        startExample($weights, array(1, 2, 3), array(0));
//        startExample($weights, array(1, 3, 2), array(0));
//        startExample($weights, array(2, 1, 3), array(0.5));
//        startExample($weights, array(2, 3, 1), array(0.5));
//        startExample($weights, array(3, 2, 1), array(1));
//        startExample($weights, array(3, 1, 2), array(1));


        $numPrimer = 1;
        startExample($weights, array(0, 0, 0), array(1,0));
        $numPrimer++;
        startExample($weights, array(2, 0, 0), array(1,0));
        $numPrimer++;
        startExample($weights, array(3, 0, 0), array(1,0));
        $numPrimer++;
        startExample($weights, array(0, 0, 1), array(1,0));
        $numPrimer++;
        startExample($weights, array(0, 0, 2), array(1,0));
        $numPrimer++;
        startExample($weights, array(0, 0, 3), array(1,0));
        $numPrimer++;
        startExample($weights, array(0, 1, 0), array(1,0));
        $numPrimer++;
        startExample($weights, array(0, 1, 1), array(0,1));
        $numPrimer++;
        startExample($weights, array(2, 1, 1), array(0,1));
        $numPrimer++;
        startExample($weights, array(3, 1, 1), array(0,1));
        $numPrimer++;
        startExample($weights, array(1, 0, 0), array(1,0));
        $numPrimer++;
        startExample($weights, array(2, 0, 0), array(1,0));
        $numPrimer++;
        startExample($weights, array(3, 0, 0), array(1,0));
        $numPrimer++;
        startExample($weights, array(1, 0, 1), array(0,1));
        $numPrimer++;
        startExample($weights, array(1, 2, 1), array(0,1));
        $numPrimer++;
        startExample($weights, array(1, 3, 1), array(0,1));
        $numPrimer++;
        startExample($weights, array(1, 1, 0), array(0,1));
        $numPrimer++;
        startExample($weights, array(1, 1, 2), array(0,1));
        $numPrimer++;
        startExample($weights, array(1, 1, 3), array(0,1));
        $numPrimer++;
        startExample($weights, array(1, 1, 1), array(0,1));
    }

    $isTeach = false;

    $answers = array();
    $ethalons = array();

    $numPrimer = 1;
//    startExample($weights, array(2, 1, 3), array(1));
//    startExample($weights, array(1, 2, 1), array(0.5));
//    startExample($weights, array(3, 2, 1), array(1));
//    startExample($weights, array(1, 3, 2), array(0.5));

//    startExample($weights, array(1, 2, 3), array(0));
//    startExample($weights, array(1, 3, 2), array(0));
//    startExample($weights, array(2, 1, 3), array(0.5));
//    startExample($weights, array(2, 3, 1), array(0.5));
//    startExample($weights, array(3, 2, 1), array(1));
//    startExample($weights, array(3, 1, 2), array(1));

    $numPrimer = 1;
    startExample($weights, array(0, 0, 0), array(1,0));
    $numPrimer++;
    startExample($weights, array(2, 0, 0), array(1,0));
    $numPrimer++;
    startExample($weights, array(3, 0, 0), array(1,0));
    $numPrimer++;
    startExample($weights, array(0, 0, 1), array(1,0));
    $numPrimer++;
    startExample($weights, array(0, 0, 2), array(1,0));
    $numPrimer++;
    startExample($weights, array(0, 0, 3), array(1,0));
    $numPrimer++;
    startExample($weights, array(0, 1, 0), array(1,0));
    $numPrimer++;
    startExample($weights, array(0, 1, 1), array(0,1));
    $numPrimer++;
    startExample($weights, array(2, 1, 1), array(0,1));
    $numPrimer++;
    startExample($weights, array(3, 1, 1), array(0,1));
    $numPrimer++;
    startExample($weights, array(1, 0, 0), array(1,0));
    $numPrimer++;
    startExample($weights, array(2, 0, 0), array(1,0));
    $numPrimer++;
    startExample($weights, array(3, 0, 0), array(1,0));
    $numPrimer++;
    startExample($weights, array(1, 0, 1), array(0,1));
    $numPrimer++;
    startExample($weights, array(1, 2, 1), array(0,1));
    $numPrimer++;
    startExample($weights, array(1, 3, 1), array(0,1));
    $numPrimer++;
    startExample($weights, array(1, 1, 0), array(0,1));
    $numPrimer++;
    startExample($weights, array(1, 1, 2), array(0,1));
    $numPrimer++;
    startExample($weights, array(1, 1, 3), array(0,1));
    $numPrimer++;
    startExample($weights, array(1, 1, 1), array(0,1));


    $er = calcNetworkError($answers, $ethalons);

    $res['error'] = $er;
    $res['weights'] = $weights;
    echo json_encode($res);

} elseif ($_POST['do'] == 'test') {
    $weights = $_POST['weights'];

    $in1 = $_POST['in1'];
    $in2 = $_POST['in2'];
    $in3 = $_POST['in3'];

    $ans = startExample($weights, array($in1, $in2, $in3));

    $res['answer'] = $ans;
//    $res['weights'] = $weights;
    echo json_encode($res);
}

function startExample(&$weights, $x, $eth = false, $skipForward = false)
{
    global $isTeach, $fs, $answers, $ethalons;
    generateX($x);
    $yOut = firstForward($weights, $x /*, $bigSs*/);

    $answers[] = $yOut;
    $ethalons[] = $eth;

    if (!$isTeach || !$eth) {
        return $yOut;
    }

    foreach ($yOut as $k => $value) {
        $proiz = calcProizvod($value);

        $d = $value - $eth[$k];

        $ds["d" . (COUNT_SLOY + 1) . ($k + 1)] = $d * $proiz;
    }

    if ($isTeach) {
        calcBackPropErrors($ds, $weights);
        $newWeights = weightCorrection($weights, $x, $ds);

        $weights = $newWeights;
    }
}

function generateX($x)
{
    for ($i = 0; $i < count($x); $i++) {
        setValueFuncAct(0, $i + 1, $x[$i]);
    }
}

function firstForward(&$weights, $x)
{
    for ($i = 1; $i <= COUNT_SLOY + 1; $i++) {
        $jMax = $i == COUNT_SLOY + 1 ? COUNT_OUTPUTS : COUNT_ON_SLOY;
        for ($j = 1; $j <= $jMax; $j++) {

            $kMax = $i == 1 ? count($x) : COUNT_ON_SLOY;

            $S = 0;
            for ($k = 1; $k <= $kMax; $k++) {
                $weightKey = "w" . $i . $j . $k;

                if (!key_exists($weightKey, $weights)) {
                    $weights[$weightKey] = getRandWeight();
                }
//                if ($i == COUNT_SLOY + 1) {
//                    pre(getValueFuncAct($i - 1, $k));
//                    pre($weights[$weightKey]);
//                }
                $S += getValueFuncAct($i - 1, $k) * $weights[$weightKey];
//                $S += 1 * getRandDopWeight($weightKey);
            }

//            if ($i == COUNT_SLOY + 1)
//                pre($S, 1);
            setValueFuncAct($i, $j, calcFuncActivation($S));

            if ($i == COUNT_SLOY + 1)
                $y[] = calcFuncActivation($S);
        }
    }

//    for ($j = 1; $j <= COUNT_OUTPUTS; $j++) {
//        $S = 0;
//        for ($i = 1; $i <= COUNT_ON_SLOY; $i++) {
//            $weightKey = "w" . (COUNT_SLOY + 1) . $j . $i;
//            if (!key_exists($weightKey, $weights)) {
//                $weights[$weightKey] = randWeight();
//            }
//
//            $S += $weights[$weightKey] * getValueFuncAct(COUNT_SLOY, $i);
////            $S += 1 * getRandDopWeight($weightKey);
//        }
//
//        $bigSs["s" . $i . $j] = $S;
//
//        $funAct = calcFa($S);
//
//        setValueFuncAct(COUNT_SLOY + 1, $j, $funAct);
//        $y[] = $funAct;
//    }

    return $y;
}

function getRandDopWeight($k)
{
    global $dopWeight;
    if (!key_exists($k, $dopWeight)) {
        $dopWeight["d" . $k] = getRandWeight();
    }
    pre($dopWeight);
    return $dopWeight["d" . $k];
}

function calcBackPropErrors(&$ds, $weights)
{
//    for ($i = 1; $i <= COUNT_ON_SLOY; $i++) {
//        $curD = 0;
//        $fPr = calcProizvod(getValueFuncAct(COUNT_SLOY, $i));
//        for ($j = 1; $j <= COUNT_OUTPUTS; $j++) {
//            $d = $ds["d" . (COUNT_SLOY + 1) . $j];
//            $w = $weights["w" . (COUNT_SLOY + 1) . $j . $i];
//
//            $curD += $d * $w * $fPr;
//            $ds["d" . COUNT_SLOY . $i] = $curD;
//        }
//    }

    for ($i = COUNT_SLOY; $i > 0; $i--) {
        for ($j = 1; $j <= COUNT_ON_SLOY; $j++) {
            $kMax = $i == COUNT_SLOY ? COUNT_OUTPUTS : COUNT_ON_SLOY;

            $curD = 0;
            for ($k = 1; $k <= $kMax; $k++) {
                $d = $ds["d" . ($i + 1) . $k];
                $w = $weights["w" . ($i + 1) . $k . $j];

                $curD += $d * $w;
            }

            $curD *= calcProizvod(getValueFuncAct($i, $j));
            $ds["d" . $i . $j] = $curD;
        }
    }
}

function weightCorrection($weights, $x, $ds)
{
    $newWeights = array();

    for ($i = 1; $i <= COUNT_SLOY + 1; $i++) {
        $jMax = $i == COUNT_SLOY + 1 ? COUNT_OUTPUTS : COUNT_ON_SLOY;
        for ($j = 1; $j <= $jMax; $j++) {

            $multDsA = $ds["d" . $i . $j] * TEACH_KOEF2 * (-1);

            $kMax = $i == 1 ? count($x) : COUNT_ON_SLOY;

            for ($k = 1; $k <= $kMax; $k++) {

                $newW = getValueFuncAct($i - 1, $k) * $multDsA;

                $w = $weights["w" . $i . $j . $k];
                $newWeights["w" . $i . $j . $k] = $w + $newW;
            }
        }
    }

//    for ($j = 1; $j <= COUNT_OUTPUTS; $j++) {
//        $multDsA = $ds["d" . (COUNT_SLOY + 1) . $j] * TEACH_KOEF2 * (-1);
//
//        for ($i = 1; $i <= COUNT_ON_SLOY; $i++) {
//            $newW = getValueFuncAct(COUNT_SLOY, $i) * $multDsA;
//            $w = $weights["w" . (COUNT_SLOY + 1) . $j . $i];
//
//            $newWeights["w" . (COUNT_SLOY + 1) . $j . $i] = $w + $newW;
//        }
//    }
    return $newWeights;
}


function getRandWeight()
{
    return mt_rand(0, mt_getrandmax() - 1) / mt_getrandmax() - 0.5;
}

function calcFuncActivation($x)
{
    return 1 / (1 + exp(-$x * FUNC_KOEF));
}

function calcProizvod($x)
{
    return $x * (1 - $x);
}

function calcNetworkError($answers, $ethalons)
{
    $sum = 0;
    foreach ($answers as $k => $answerVector) {
        $ethalonVector = $ethalons[$k];
        foreach ($answerVector as $l => $answer) {
            $sum += pow($answer - $ethalonVector[$l], 2);
        }
    }
    return $sum / 2;
}

function getValueFuncAct($i, $j)
{
    global $fs, $numPrimer;
    return $fs["f" . $i . $j . "-" . $numPrimer];
}

function setValueFuncAct($i, $j, $value)
{
    global $fs, $numPrimer;
    $fs["f" . $i . $j . "-" . $numPrimer] = $value;
}

function pre($var, $die = false)
{
    echo '<pre>';
    print_r($var);
    echo '</pre>';
    if ($die)
        die('Debug in PRE');
}

//function ulogging($input, $logname = 'debug', $dt = false)
//{
//    $endLine = "\r\n"; #PHP_EOL не используется, т.к. иногда это нужно конфигурировать это
//
//    $fp = fopen('' . $logname . '.txt', "a+");
//
//    if (is_string($input)) {
//        $writeStr = $input;
//    } else {
//        $writeStr = print_r($input, true);
//    }
//
//    if ($dt) {
//        fwrite($fp, date('d.m.Y H:i:s') . $endLine);
//    }
//
//    fwrite($fp, $writeStr . $endLine);
//
//    fclose($fp);
//    return true;
//}