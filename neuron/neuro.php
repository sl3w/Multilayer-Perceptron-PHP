<?php

define("COUNT_SLOY", 2);
define("COUNT_ON_SLOY", 2);
define("COUNT_OUTPUTS", 2);

define("TEACH_KOEF", 0.1);

global $isTeach;
$isTeach= true;

//$x = array(1, 0);

global $dopWeight;

$dopWeight = array();

$weights = array(
    "w111" => 0.13,
    "w121" => -0.34,
    "w211" => -0.42,
    "w221" => 0.38,
    "w112" => 0.25,
    "w122" => 0.07,
    "w212" => -0.20,
    "w222" => 0.32,
    "w113" => -0.41,
    "w213" => 0.12,
    "w123" => 0.41,
    "w223" => -0.12,
);
//$weights = array();
ksort($weights);
//pre($weights);

pre(startEpoch($weights, array(1, 0), array(1, 1)));
//pre(startEpoch($weights, array(0, 0, 0), array(1, 0)));
//pre(startEpoch($weights, array(0, 0, 1), array(1, 0)));
//pre(startEpoch($weights, array(0, 1, 0), array(1, 0)));
//pre(startEpoch($weights, array(0, 1, 1), array(0, 1)));
//pre(startEpoch($weights, array(1, 0, 0), array(1, 0)));
//pre(startEpoch($weights, array(1, 0, 1), array(0, 1)));
//pre(startEpoch($weights, array(1, 1, 0), array(0, 1)));
//pre(startEpoch($weights, array(1, 1, 1), array(0, 1)));

//$isTeach = false;
//pre(startEpoch($weights, array(1, 1, 1)));




function startEpoch(&$weights, $x, $e = 0)
{
    global $isTeach;
    $bigSs = array();
    $fs = array();
    generateX($x, $fs);
    $sOut = firstForward($weights, $x, $bigSs, $fs);
//    ksort($weights);
//    pre($weights);

    foreach ($sOut as $k => $value) {
        $p = calcFa($value);
        $y["y" . $k] = $p;

        if ($isTeach) {
            $fs["f" . (COUNT_SLOY + 1) . $k] = $p;
//            pre($e[$k - 1]);
//            pre($p);
            $ds["d" . (COUNT_SLOY + 1) . ($k)] = $e[$k - 1] - $p;
//            pre($ds["d" . (COUNT_SLOY + 1) . ($k)]);
        }
    }


    if ($isTeach) {
        calcErrors($ds, $weights);

        $newWeights = weightCorrection($weights, $x, $fs, $ds);
        ksort($newWeights);
//        pre($weights);
        $weights = $newWeights;
        unset($newWeights);
//        pre($weights);

//        pre($y, 1);
        return $y;
    } else
        return $y;
}

function generateX(&$x, &$fs)
{
    for ($i = 0; $i < count($x); $i++) {
        $fs["f0" . ($i + 1)] = $x[$i];
    }
}

function firstForward(&$weights, $x, &$bigSs, &$fs)
{
    for ($i = 1; $i <= COUNT_SLOY; $i++) {
        for ($j = 1; $j <= COUNT_ON_SLOY; $j++) {

            if ($i == 1)
                $kMax = count($x);
            else
                $kMax = COUNT_ON_SLOY;

            $S = 0;
            for ($k = 1; $k <= $kMax; $k++) {
                $weightKey = "w" . $k . $j . $i;
                if (!key_exists($weightKey, $weights)) {
                    $weights[$weightKey] = randWeight();
                }
                $S += $fs["f" . ($i - 1) . $k] * $weights[$weightKey];
//                $S += 1 * getRandDopWeight($weightKey);
            }

            $bigSs["s" . $i . $j] = $S;

            $fs["f" . $i . $j] = calcFa($S);
        }
    }


    for ($j = 1; $j <= COUNT_OUTPUTS; $j++) {
        $S = 0;
        for ($i = 1; $i <= COUNT_ON_SLOY; $i++) {
            $weightKey = "w" . $i . $j . (COUNT_SLOY + 1);
            if (!key_exists($weightKey, $weights)) {
                $weights[$weightKey] = randWeight();
            }
            $S += $weights[$weightKey] * $fs["f" . COUNT_SLOY . $i];
//            $S += 1 * getRandDopWeight($weightKey);
        }
        $y[$j] = $S;
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

function calcErrors(&$ds, $weights)
{
    for ($i = 1; $i <= COUNT_ON_SLOY; $i++) {
        for ($j = 1; $j <= COUNT_OUTPUTS; $j++) {
//            pre($weights["w" . $i . $j . (COUNT_SLOY + 1)]);
            $ds["d" . COUNT_SLOY . $i] = $ds["d" . (COUNT_SLOY + 1) . $j] * $weights["w" . $i . $j . (COUNT_SLOY + 1)];
//            pre($ds["d" . COUNT_SLOY . $i]);
        }
    }

//    die();

    for ($i = COUNT_SLOY - 1; $i > 0; $i--) {
        for ($j = 1; $j <= COUNT_ON_SLOY; $j++) {
            $curD = 0;
            for ($k = 1; $k <= COUNT_ON_SLOY; $k++) {
                $curD += $ds["d" . ($i + 1) . $k] * $weights["w" . $j . $k . ($i + 1)];
            }
            $ds["d" . $i . $j] = $curD;
        }
    }
}

function weightCorrection($weights, $x, &$fs, $ds)
{
    $newWeights = array();

    for ($i = 1; $i <= COUNT_SLOY; $i++) {
        for ($j = 1; $j <= COUNT_ON_SLOY; $j++) {

            $fPr = calcProizvod($fs["f" . $i . $j]);

            //перемножено здесь, а не в цикле, для ускорения
            $multDsFPrA = $ds["d" . $i . $j] * $fPr * TEACH_KOEF;

            if ($i == 1)
                $kMax = count($x);
            else
                $kMax = COUNT_ON_SLOY;

            for ($k = 1; $k <= $kMax; $k++) {
                $w = $weights["w" . $k . $j . $i];
                $newWeights["w" . $k . $j . $i] = $w + $multDsFPrA * $fs["f" . ($i - 1) . $k];
            }
        }
    }

    for ($j = 1; $j <= COUNT_OUTPUTS; $j++) {
        $fPr = calcProizvod($fs["f" . (COUNT_SLOY + 1) . $j]);
        pre($fPr);
        $multDsFPrA = $ds["d" . (COUNT_SLOY + 1) . $j] * $fPr * TEACH_KOEF;

        for ($i = 1; $i <= COUNT_ON_SLOY; $i++) {
            $w = $weights["w" . $i . $j . (COUNT_SLOY + 1)];
            pre($w);
            $newWeights["w" . $i . $j . (COUNT_SLOY + 1)] = $w + $multDsFPrA * $fs["f" . COUNT_SLOY . $i];
        }
    }

    return $newWeights;
}

function pre($var, $die = false)
{
    echo '<pre>';
    print_r($var);
    echo '</pre>';
    if ($die)
        die('Debug in PRE');
}

function randWeight()
{
    return mt_rand(0, mt_getrandmax() - 1) / mt_getrandmax() - 0.5;
}

function calcFa($x)
{
    return 1 / (1 + exp(-$x));
}

function calcProizvod($x)
{
    return $x * (1 - $x);
}