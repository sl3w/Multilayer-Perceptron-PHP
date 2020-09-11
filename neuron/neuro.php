<?php

define("COUNT_SLOY", 2);
define("COUNT_ON_SLOY", 2);
define("COUNT_OUTPUTS", 1);

define("TEACH_KOEF", 0.3);
define("TEACH_KOEF2", 0.65);

global $isTeach;
$isTeach = false;

global $answers, $ethalons;
$answers = array();
$ethalons = array();

//$x = array(1, 0);

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

//$weights = array();
//ksort($weights);
//pre($weights);

if($_POST['do'] == 'init') {
    startEpoch($weights, array(2, 1, 3), array(1));
    startEpoch($weights, array(1, 2, 1), array(0.5));
    startEpoch($weights, array(3, 2, 1), array(1));
    startEpoch($weights, array(1, 3, 2), array(0.5));


//    pre($answers);
//    pre($ethalons);

    $er = calcError($answers, $ethalons);

//    pre($er);


    $res['error'] = $er;
    $res['weights'] = $weights;
    echo json_encode($res);
}
else {
    $isTeach = true;


//pre($weights);

    startEpoch($weights, array(2, 1, 3), array(1));
    startEpoch($weights, array(1, 2, 1), array(0.5));
    startEpoch($weights, array(3, 2, 1), array(1));
    startEpoch($weights, array(1, 3, 2), array(0.5));

//pre($weights, 1);

    $isTeach = false;

    $answers = array();
    $ethalons = array();


    startEpoch($weights, array(2, 1, 3), array(1));
    startEpoch($weights, array(1, 2, 1), array(0.5));
    startEpoch($weights, array(3, 2, 1), array(1));
    startEpoch($weights, array(1, 3, 2), array(0.5));


    pre($answers);
    pre($ethalons);

    $er = calcError($answers, $ethalons);

    pre($er);


//$isTeach = false;
//pre(startEpoch($weights, array(0, 0, 1)));


}


function startEpoch(&$weights, $x, $eth = 0)
{
    global $isTeach;
//    $bigSs = array();
    $fs = array();
    generateX($x, $fs);
    $yOut = firstForward($weights, $x, $fs/*, $bigSs*/);

    global $answers, $ethalons;
//    $answers[] = number_format($sOut, 9);
    $answers[] = $yOut;
    $ethalons[] = $eth;

    if(!$isTeach) {
        return $yOut;
    }

//    pre($sOut, 1);

//    ksort($weights);
//    pre($weights);

    foreach ($yOut as $k => $value) {
        $proiz = calcProizvod($value);

//        pre($value);

//        pre($p, 1);

//        $y["y" . $k] = $p;

//        if ($isTeach) {
//            $fs["f" . (COUNT_SLOY + 1) . $k] = $proiz;
//            pre($eth[$k - 1]);
//            pre($p);

            $d = $value - $eth[$k];

//            pre($d, 1);

            $ds["d" . (COUNT_SLOY + 1) . ($k + 1)] = $d * $proiz;
//            pre($ds["d" . (COUNT_SLOY + 1) . ($k)]);
//        }
    }

//    pre($ds, 1);

//    pre($fs, 1);

    if ($isTeach) {
        calcErrors($ds, $weights, $fs);
//
//        pre($weights);
        $newWeights = weightCorrection($weights, $x, $fs, $ds);
//
//        ksort($newWeights);
////        pre($weights);
        $weights = $newWeights;
//        unset($newWeights);
//        pre($weights);
////        pre("--------------------------------");
//        return $y;
    }
//    else
//        return $y;

//    pre($ds, 1);
}

function generateX(&$x, &$fs)
{
    for ($i = 0; $i < count($x); $i++) {
        $fs["f0" . ($i + 1)] = $x[$i];
    }
}

function firstForward(&$weights, $x, &$fs, &$bigSs = array())
{
    for ($i = 1; $i <= COUNT_SLOY; $i++) {
        for ($j = 1; $j <= COUNT_ON_SLOY; $j++) {

            if ($i == 1)
                $kMax = count($x);
            else
                $kMax = COUNT_ON_SLOY;

            $S = 0;
            for ($k = 1; $k <= $kMax; $k++) {
                $weightKey = "w". $i . $j  . $k;

                if (!key_exists($weightKey, $weights)) {
                    $weights[$weightKey] = randWeight();
                }
//                if($i != 1) {
//                    pre("f" . ($i - 1) . $k . ": " . $fs["f" . ($i - 1) . $k]);
//                    pre($weightKey . ": " . $weights[$weightKey]);
//                    pre("____________________________________");

//                }
                $S += $fs["f" . ($i - 1) . $k] * $weights[$weightKey];
//                $S += 1 * getRandDopWeight($weightKey);
            }

            //die();

            $bigSs["s" . $i . $j] = $S;

            $fs["f" . $i . $j] = calcFa($S);
        }
        //die();

    }

//    pre($bigSs);
//    pre($fs, 1);

    for ($j = 1; $j <= COUNT_OUTPUTS; $j++) {
        $S = 0;
        for ($i = 1; $i <= COUNT_ON_SLOY; $i++) {
            $weightKey = "w" . (COUNT_SLOY + 1) . $j . $i;
            if (!key_exists($weightKey, $weights)) {
                $weights[$weightKey] = randWeight();
            }

//            pre("f" . COUNT_SLOY . $i . ": " . $fs["f" . COUNT_SLOY . $i]);
//            pre($weightKey . ": " . $weights[$weightKey]);
//            pre("____________________________________");

            $S += $weights[$weightKey] * $fs["f" . COUNT_SLOY . $i];
//            $S += 1 * getRandDopWeight($weightKey);
        }

        $bigSs["s" . $i . $j] = $S;

        $funAct = calcFa($S);

        $fs["f". (COUNT_SLOY + 1). $j] = $funAct;
//        $y[$j] = $funAct;
        $y[] = $funAct;
    }

    return $y;
}

function getRandDopWeight($k) {
    global $dopWeight;
    if (!key_exists($k, $dopWeight)) {
        $dopWeight["d". $k] = randWeight();
    }
    pre($dopWeight);
    return $dopWeight["d". $k];
}

function calcErrors(&$ds, $weights, $fs)
{
//    pre($ds);
    for ($i = 1; $i <= COUNT_ON_SLOY; $i++) {
        $curD = 0;
        $fPr = calcProizvod($fs["f" . (COUNT_SLOY) . $i]);
//        pre("f" . (COUNT_SLOY) . $i . ":" . $fs["f" . (COUNT_SLOY) . $i]);
        for ($j = 1; $j <= COUNT_OUTPUTS; $j++) {
//            for ($k = 1; $k <= COUNT_OUTPUTS; $k++) {
                $d = $ds["d" . (COUNT_SLOY + 1) . $j];
                $w = $weights["w" . (COUNT_SLOY + 1) . $j . $i];

//                pre("d" . (COUNT_SLOY + 1) . $j . ":" . $d);
//                pre("w" . (COUNT_SLOY + 1) . $j . $i . ":" . $w);
//                pre("f" . (COUNT_SLOY) . $i . ":" . $fs["f" . (COUNT_SLOY) . $i]);
//                pre("________________");

                $curD += $d * $w * $fPr;
//            }
            $ds["d" . COUNT_SLOY . $i] = $curD;
        }
    }
//    pre($ds);

    for ($i = COUNT_SLOY - 1; $i > 0; $i--) {
        for ($j = 1; $j <= COUNT_ON_SLOY; $j++) {
            $curD = 0;



//            $w = $weights["w" . $k . $i . $j];
//            pre("w" . (COUNT_SLOY + 1) . $j . $i . ":" . $w);


//            pre($weights);
            for ($k = 1; $k <= COUNT_ON_SLOY; $k++) {

                $d = $ds["d" . ($i + 1) . $k];
//                pre("d" . ($i + 1) . $k . ":" . $d);

                $w = $weights["w" . ($i + 1) . $k . $j];
//                pre("w" . ($i + 1) . $k . $j . ":" . $w);


                $curD += $d * $w;
            }
//            pre($fs);

//            pre("f". $i. $j . ":" . $fs["f". $i. $j]);
//            pre($curD);
            $curD *= calcProizvod($fs["f". $i. $j]);
//            die();
            $ds["d" . $i . $j] = $curD;
        }
//        pre($ds);
    }
//    pre($ds);
}

function weightCorrection($weights, $x, &$fs, $ds)
{
    $newWeights = array();

//    pre($ds);
    for ($i = 1; $i <= COUNT_SLOY; $i++) {
        for ($j = 1; $j <= COUNT_ON_SLOY; $j++) {

//            pre($ds["d" . $i . $j]);
            $multDsA = $ds["d" . $i . $j] * TEACH_KOEF2 * (-1);

            if ($i == 1)
                $kMax = count($x);
            else
                $kMax = COUNT_ON_SLOY;


            for ($k = 1; $k <= $kMax; $k++) {

//                pre($fs["f" . ($i - 1) . $k]);
                $newW = $fs["f" . ($i - 1) . $k] * $multDsA;

                $w = $weights["w" . $i . $j . $k];
//                pre($w);
                $newWeights["w" . $i . $j . $k] = $w + $newW;
            }

//            die();
        }

    }
//pre($weights);
//pre($newWeights);

    for ($j = 1; $j <= COUNT_OUTPUTS; $j++) {
//        $fPr = calcProizvod($fs["f" . (COUNT_SLOY + 1) . $j]);
//        $multDsFPrA = $ds["d" . (COUNT_SLOY + 1) . $j] * $fPr * TEACH_KOEF;

//        pre($ds["d" . (COUNT_SLOY + 1) . $j]);
        $multDsA = $ds["d" . (COUNT_SLOY + 1) . $j] * TEACH_KOEF2 * (-1);

        for ($i = 1; $i <= COUNT_ON_SLOY; $i++) {
//            pre($fs["f" . COUNT_SLOY . $i]);
            $newW = $fs["f" . COUNT_SLOY . $i] * $multDsA;
            $w = $weights["w"  . (COUNT_SLOY + 1) . $j . $i];
//            pre($w);
            $newWeights["w"  . (COUNT_SLOY + 1) . $j . $i] = $w + $newW;
        }
    }
//    pre($newWeights);
    return $newWeights;
}


function randWeight()
{
    return mt_rand(0, mt_getrandmax() - 1) / mt_getrandmax() - 0.5;
}

function calcFa($x)
{
    return 1 / (1 + exp(-$x * TEACH_KOEF));
}

function calcProizvod($x)
{
    return $x * (1 - $x);
}

function calcError($answers, $ethalons) {
    $sum = 0;
    foreach ($answers as $k => $answerVector) {
        //pre($answerVector);
        //pre($ethalons[$k]);
        $ethalonVector = $ethalons[$k];
        foreach ($answerVector as $l => $answer) {
            $sum += pow($answer - $ethalonVector[$l], 2);
        }
    }
//    pre($sum, 1);
    return $sum / 2;
}


function pre($var, $die = false)
{
    echo '<pre>';
    print_r($var);
    echo '</pre>';
    if ($die)
        die('Debug in PRE');
}