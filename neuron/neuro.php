<?php
define("COUNT_ON_SLOY", 4);
define("COUNT_OUTPUTS", 3);
define("FUNC_KOEF", 0.3);

global $isTeach, $answers, $ethalons, $funcsAct, $numPrimer, $teachKoef, $hideLayCount;
//$hideLayCount = 2;
$isTeach = false;

//global $dopWeight;
//$dopWeight = array();

//$teachTest = false;

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

global $do;
//$do = 'init';
if ($do == 'init') {
    $numPrimer = 1;
    goEpoch($weights);

    $er = calcNetworkError($answers, $ethalons);

    $res['error'] = $er;
    $res['weights'] = $weights;
    echo json_encode($res);

} elseif ($do == 'teach' /*|| $teachTest*/) {
    $isTeach = true;

    $weights = $_POST['weights'];
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
    echo json_encode($res);

} elseif ($do == 'test') {
    $weights = $_POST['weights'];

    $in1 = $_POST['in1'];
    $in2 = $_POST['in2'];
    $in3 = $_POST['in3'];
    $in4 = $_POST['in4'];

    $ans = startExample($weights, array($in1, $in2, $in3, $in4));

    $res['answer'] = $ans;
    echo json_encode($res);
}

function startExample(&$weights, $x, $eth = false)
{
    global $isTeach, $answers, $ethalons, $numPrimer, $hideLayCount;
    generateX($x);
    $yOut = firstForward($weights, $x);

    $answers[] = $yOut;
    $ethalons[] = $eth;

    if (!$isTeach || !$eth) {
        return $yOut;
    }

    foreach ($yOut as $k => $value) {
        $proiz = calcProizvod($value);

        $d = $value - $eth[$k];

        $ds["d" . ($hideLayCount + 1) . ($k + 1)] = $d * $proiz;
    }

    if ($isTeach) {
        calcBackPropErrors($ds, $weights);

        $newWeights = weightCorrection($weights, $x, $ds);

        $weights = $newWeights;
    }
    $numPrimer++;
}

function generateX($x)
{
    for ($i = 0; $i < count($x); $i++) {
        setValueFuncAct(0, $i + 1, $x[$i]);
    }
}

function firstForward(&$weights, $x)
{
    global $hideLayCount;
    for ($i = 1; $i <= $hideLayCount + 1; $i++) {
        $jMax = $i == $hideLayCount + 1 ? COUNT_OUTPUTS : COUNT_ON_SLOY;
        for ($j = 1; $j <= $jMax; $j++) {

            $kMax = $i == 1 ? count($x) : COUNT_ON_SLOY;

            $S = 0;
            for ($k = 1; $k <= $kMax; $k++) {
                $weightKey = "w" . $i . $j . $k;

                if (!key_exists($weightKey, $weights)) {
                    $weights[$weightKey] = getRandWeight();
                    global $do;
                    if ($do != 'init')
                    die();
                }
                $S += getValueFuncAct($i - 1, $k) * $weights[$weightKey];
            }

            setValueFuncAct($i, $j, calcFuncActivation($S));

            if ($i == $hideLayCount + 1)
                $y[] = calcFuncActivation($S);
        }
    }

    return $y;
}

//function getRandDopWeight($k)
//{
//    global $dopWeight;
//    if (!key_exists($k, $dopWeight)) {
//        $dopWeight["d" . $k] = getRandWeight();
//    }
//    pre($dopWeight);
//    return $dopWeight["d" . $k];
//}

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

$x1 = array(
  array(0.49703,0.499170,0.502369)
);

$x2 = array(
    array(1,0,0)
);

//pre($x1);
//pre($x2);
//
//pre(calcNetworkError($x1, $x2));
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
    $handle = fopen("iris.data", "r");
    while (!feof($handle)) {
        $buffer = fgets($handle, 4096);
        $exam = explode(",", trim($buffer));
//    pre($exam);
//    $eth = array(0,0,0,0,0,0,0,0);
        $et = $exam[count($exam) - 1];
//        if($et == 0 || $et == 1)
//            continue;
//        $eth[$et - 2] = 1;
        switch ($et) {
            case "Iris-setosa":
                $eth = array(1,0,0);
                break;
            case "Iris-versicolor":
                $eth = array(0,1,0);
                break;
            case "Iris-virginica":
                $eth = array(0,0,1);
                break;
        }

        $xs = array_splice($exam, 0, count($exam) - 1);
//        pre($xs);
//        pre($eth);
//        die();
        startExample($weights, $xs, $eth);
//        break;
    }
    fclose($handle);

//    startExample($weights, array(5.1,3.5,1.4,0.2), array(1));


//    startExample($weights, array(0, 0, 0), array(1, 0));
//    startExample($weights, array(2, 0, 0), array(1, 0));
//    startExample($weights, array(3, 0, 0), array(1, 0));
//    startExample($weights, array(0, 0, 1), array(1, 0));
//    startExample($weights, array(0, 0, 2), array(1, 0));
//    startExample($weights, array(0, 0, 3), array(1, 0));
//    startExample($weights, array(0, 1, 0), array(1, 0));
//    startExample($weights, array(0, 1, 1), array(0, 1));
//    startExample($weights, array(2, 1, 1), array(0, 1));
//    startExample($weights, array(3, 1, 1), array(0, 1));
//    startExample($weights, array(1, 0, 0), array(1, 0));
//    startExample($weights, array(2, 0, 0), array(1, 0));
//    startExample($weights, array(3, 0, 0), array(1, 0));
//    startExample($weights, array(1, 0, 1), array(0, 1));
//    startExample($weights, array(1, 2, 1), array(0, 1));
//    startExample($weights, array(1, 3, 1), array(0, 1));
//    startExample($weights, array(1, 1, 0), array(0, 1));
//    startExample($weights, array(1, 1, 2), array(0, 1));
//    startExample($weights, array(1, 1, 3), array(0, 1));
//    startExample($weights, array(1, 1, 1), array(0, 1));
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

//    $handle = fopen("iris.data", "r");
//    while (!feof($handle)) {
//        $buffer = fgets($handle, 4096);
//        $exam = explode(",", trim($buffer));
////    pre($exam);
////    $eth = array(0,0,0,0,0,0,0,0,0,0);
//        $et = $exam[count($exam) - 1];
//        switch ($et) {
//            case "Iris-setosa":
//                $eth = array(1,0,0);
//                break;
//            case "Iris-versicolor":
//                $eth = array(0,1,0);
//                break;
//            case "Iris-virginica":
//                $eth = array(0,0,1);
//                break;
//        }
//
//        $xs = array_splice($exam, 0, count($exam) - 1);
//        pre($xs);
//        pre($eth);
//        die();
////        startExample($weights, $xs, $eth);
//    }
//    fclose($handle);


//$hideLayCount = 1;
//$teachKoef = 0.2;
//
//$numPrimer = 1;
//goEpoch($weights);
//
//$er = calcNetworkError($answers, $ethalons);
//
//pre($er);
//pre($weights);
//
//$isTeach = true;
//
//for($i = 0; $i < 2000; $i++) {
//    $numPrimer = 1;
//    goEpoch($weights);
//}
//
//$isTeach = false;
//
//$answers=array();
//$ethalons=array();
//goEpoch($weights);
//
//$er = calcNetworkError($answers, $ethalons);
//pre($er);
//pre($weights);
//
//$isTeach = true;
//
//$numPrimer = 1;
//goEpoch($weights);
//pre($weights);