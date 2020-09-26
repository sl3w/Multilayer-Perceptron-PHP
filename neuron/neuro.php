<?php
define("COUNT_ON_SLOY", 4);
define("COUNT_OUTPUTS", 3);
define("FUNC_KOEF", 0.3);

global $isTeach, $numPrimer, $teachKoef, $hideLayCount;
$isTeach = false;

global $answers, $ethalons, $funcsAct, $dopWeights;

//global $dopWeight;
//$dopWeight = array();

//$teachTest = false;

global $do;
$do = $_POST['do'];
$hideLayCount = $_POST['hideLayCount'];

//$weights = array(
//    "w111" => 0.13,
//    "w112" => -0.34,
//    "w113" => -0.42,
//    "w114" => 0.38,
//    "w211" => 0.25,
//    "w221" => 0.07,
//    "w231" => -0.20,
////    "w222" => 0.32,
////    "w113" => -0.41,
////    "w213" => 0.12,
////    "w123" => 0.41,
////    "w223" => -0.12,
//);
//global $res;
//global $do;
//$do = 'init';
if ($do == 'init') {
    $numPrimer = 1;
    goEpoch($weights);

    $er = calcNetworkError($answers, $ethalons);

    $res['error'] = $er;
    $res['weights'] = $weights;
    $res['dopWeights'] = $dopWeights;
    //$res['fs'] = $funcsAct;
    echo json_encode($res);

} elseif ($do == 'teach' /*|| $teachTest*/) {
    $isTeach = true;

    $weights = $_POST['weights'];
    $dopWeights = $_POST['dopWeights'];
    $countEpochs = $_POST['countEpochs'];
    $teachKoef = $_POST['teachKoef'];

    for ($i = 0; $i < $countEpochs; $i++) {
        $numPrimer = 1;
        goEpoch($weights);
    }

    $isTeach = false;

    $answers = array();
    $ethalons = array();

    $numPrimer = 1;
    goEpoch($weights);

    $er = calcNetworkError($answers, $ethalons);

    $res['error'] = $er;
    $res['weights'] = $weights;
    $res['dopWeights'] = $dopWeights;
    echo json_encode($res);

} elseif ($do == 'test') {
    $weights = $_POST['weights'];
    $dopWeights = $_POST['dopWeights'];

    $in1 = $_POST['in1'];
//    $in2 = $_POST['in2'];
//    $in3 = $_POST['in3'];
//    $in4 = $_POST['in4'];

    $ans = startExample($weights, $in1);

    $res['in'] = $in1;
    $res['answer'] = $ans;
    echo json_encode($res);
}

function startExample(&$weights, $x, $eth = false)
{
    global $isTeach, $answers, $ethalons, $numPrimer, $hideLayCount;
    generateX($x);
    $yOut = firstForward($weights, count($x));

    $answers[] = $yOut;
    $ethalons[] = $eth;

    if (!$isTeach || !$eth) {
        return $yOut;
    }

    foreach ($yOut as $k => $value) {
        $d = $value - $eth[$k];

        $ds["d" . ($hideLayCount + 1) . ($k + 1)] = $d * calcProizvod($value);
    }

    if ($isTeach) {
        calcBackPropErrors($ds, $weights);

        $newWeights = weightCorrection($weights, $x, $ds);

        $weights = $newWeights;

        dopWeightsCorrection($ds);
    }
    $numPrimer++;
}

function generateX($x)
{
    for ($i = 0; $i < count($x); $i++) {
        setValueFuncAct(0, $i + 1, $x[$i]);
    }
}

function firstForward(&$weights, $xCount)
{
    global $hideLayCount, $dopWeights;
    for ($i = 1; $i <= $hideLayCount + 1; $i++) {
        $jMax = $i == $hideLayCount + 1 ? COUNT_OUTPUTS : COUNT_ON_SLOY;
        for ($j = 1; $j <= $jMax; $j++) {

            $kMax = $i == 1 ? $xCount : COUNT_ON_SLOY;

            $smtr = 0;
            for ($k = 1; $k <= $kMax; $k++) {
                $weightKey = "w" . $i . $j . $k;

                if (!key_exists($weightKey, $weights)) {
                    $weights[$weightKey] = getRandWeight();
                }
                $smtr += getValueFuncAct($i - 1, $k) * $weights[$weightKey];
            }

            if (!key_exists($j, $dopWeights[$i])) {
                $dopWeights[$i][$j] = getRandWeight();
            }
            //$smtr += $dopWeights[$i][$j];

            setValueFuncAct($i, $j, calcFuncActivation($smtr));

            if ($i == $hideLayCount + 1)
                $y[] = calcFuncActivation($smtr);
        }
    }

    return $y;
}

function calcBackPropErrors(&$ds, $weights)
{
    global $hideLayCount;
    for ($i = $hideLayCount; $i > 0; $i--) {
        for ($j = 1; $j <= COUNT_ON_SLOY; $j++) {
            $kMax = $i == $hideLayCount ? COUNT_OUTPUTS : COUNT_ON_SLOY;

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
    global $teachKoef, $hideLayCount;
    $newWeights = array();

    for ($i = 1; $i <= $hideLayCount + 1; $i++) {
        $jMax = $i == $hideLayCount + 1 ? COUNT_OUTPUTS : COUNT_ON_SLOY;
        for ($j = 1; $j <= $jMax; $j++) {

            $multDsA = $ds["d" . $i . $j] * $teachKoef * (-1);
            $kMax = $i == 1 ? count($x) : COUNT_ON_SLOY;

            for ($k = 1; $k <= $kMax; $k++) {
                $newW = getValueFuncAct($i - 1, $k) * $multDsA;

                $w = $weights["w" . $i . $j . $k];

                $newWeights["w" . $i . $j . $k] = $w + $newW;
            }
        }
    }
    return $newWeights;
}

function dopWeightsCorrection($ds)
{
    global $dopWeights, $teachKoef;

    $faBy1 = calcFuncActivation(1);
    foreach ($dopWeights as $layNum => $layWeight) {
        foreach ($layWeight as $neurNum => $dopWeight)
            if ($layNum == "1")
                $dopWeights[$layNum][$neurNum] = $dopWeight - $ds["d" . $layNum . $neurNum] * $teachKoef;
            else
                $dopWeights[$layNum][$neurNum] = $dopWeight - $ds["d" . $layNum . $neurNum] * $teachKoef * $faBy1;
    }
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
    global $funcsAct, $numPrimer;
    return $funcsAct["f" . $i . $j . "-" . $numPrimer];
}

function setValueFuncAct($i, $j, $value)
{
    global $funcsAct, $numPrimer;
    $funcsAct["f" . $i . $j . "-" . $numPrimer] = $value;
}

function goEpoch(&$weights)
{
    $fileName = 'iris.data';

    $handle = fopen($fileName, "r");
    while (!feof($handle)) {
        $buffer = fgets($handle, 4096);
        $exam = explode(",", trim($buffer));


//        $eth = array(0,0,0,0,0,0,0);
//        if($et == 0 || $et == 1)
//            continue;
//        $eth[$et] = 1;


        if ($fileName == 'balance.data') {
            $et = $exam[0];
            switch ($et) {
                case "R":
                    $eth = array(0, 0, 1);
                    break;
                case "L":
                    $eth = array(1, 0, 0);
                    break;
                case "B":
                    $eth = array(0, 1, 0);
                    break;
            }
            $xs = array_slice($exam, 1);
        } elseif ($fileName == 'iris.data') {
            $et = $exam[count($exam) - 1];
            switch ($et) {
                case "Iris-setosa":
                    $eth = array(1, 0, 0);
                    break;
                case "Iris-versicolor":
                    $eth = array(0, 1, 0);
                    break;
                case "Iris-virginica":
                    $eth = array(0, 0, 1);
                    break;
            }

            $xs = array_slice($exam, 0, count($exam) - 1);
        }

        startExample($weights, $xs, $eth);
    }
    fclose($handle);
}

function pre($var, $die = false)
{
    echo '<pre>';
    print_r($var);
    echo '</pre>';
    if ($die)
        die('Debug in PRE');
}