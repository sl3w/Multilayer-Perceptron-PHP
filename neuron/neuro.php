<?php
//define("COUNT_ON_SLOY", 4);
//define("COUNT_OUTPUTS", 3);
define("FUNC_KOEF", 0.3);

global $isTeach, $numPrimer, $teachKoef, $teachKoefStandart, $momentsKoef;
global $hideLayCount, $hideLayNeuronCount, $outLayNeuronCount;
global $testData;

$isTeach = false;

global $answers, $ethalons, $funcsAct, $dopWeights, $weightsPast;

//global $dopWeight;
//$dopWeight = array();

//$teachTest = false;

global $do;
$do = $_POST['do'];
$hideLayCount = $_POST['hideLayCount'];
$hideLayNeuronCount = $_POST['hideLayNeuronCount'];
$outLayNeuronCount = $_POST['outLayNeuronCount'];

//$res['do'] = $do1;
//$res['hlc'] = $hideLayCount;
//$res['hlnc'] = $hideLayNeuronCount;
//$res['olnc'] = $outLayNeuronCount;
//echo json_encode($res);

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
    $inputData = goEpoch($weights, -1);

    $er = calcNetworkError($answers, $ethalons);

    $res['error'] = $er;
    $res['weights'] = $weights;
    $res['dopWeights'] = $dopWeights;
    $res['inputData'] = $inputData;
    $res['testData'] = $testData;
    //$res['fs'] = $funcsAct;
    echo json_encode($res);

} elseif ($do == 'teach' /*|| $teachTest*/) {
    $isTeach = true;

    $weights = $_POST['weights'];
    $dopWeights = $_POST['dopWeights'];
    $countEpochs = $_POST['countEpochs'];
    $counterEpochs = $_POST['counterEpochs'];
    $teachKoef = $_POST['teachKoef'];
    $teachKoefStandart = $_POST['teachKoefStandart'];
    $momentsKoef = $_POST['momentsKoef'];

//    if ($counterEpochs == 0) {
//        $weightsPast = $weights;
//    } else {
    $weightsPast = $_POST['weightsPast'];
//    }

    for ($i = 0; $i < $countEpochs; $i++) {
        $numPrimer = 1;
        goEpoch($weights, $i);
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
    $res['weightsPast'] = $weightsPast;
    $res['teachKoef'] = $teachKoef;
    $res['momentsKoef'] = $momentsKoef;
    echo json_encode($res);

} elseif ($do == 'test') {
    $weights = $_POST['weights'];
    $dopWeights = $_POST['dopWeights'];

    $in1 = $_POST['in1'];

    $ans = startExample($weights, $in1);

    $res['in'] = $in1;
    $res['answer'] = $ans;
    $res['widths'] = $weights;
    $res['dopWidth'] = $dopWeights;
    echo json_encode($res);
} elseif ($do == 'testing') {
    $weights = $_POST['weights'];
    $dopWeights = $_POST['dopWeights'];

    $testData = $_POST['testData'];
    $countRight = 0;
    foreach ($testData as $testDat) {
        $ans = startExample($weights, $testDat[0]);

        $ansMaxInd = 0;
        $ansMax = $ans[0];
        for ($i = 1; $i < count($ans); $i++) {
            if ($ans[$i] > $ansMax) {
                $ansMax = $ans[$i];
                $ansMaxInd = $i;
            }
        }

        if ($testDat[1][$ansMaxInd] == 1)
            $countRight++;
    }
    $res['answer'] = $countRight / count($_POST['testData']);
    $res['answerText'] = $countRight . " из " . count($_POST['testData']);
    echo json_encode($res);
}

function startExample(&$weights, $x, $eth = false)
{
    global $isTeach, $answers, $ethalons, $numPrimer, $hideLayCount, $weightsPast, $momentsKoef;
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

//        $weightsPastTemp = $weights;

        $newWeights = weightCorrection($weights, $x, $ds, $momentsKoef);

//        $weightsPast = $weightsPastTemp;

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
    global $hideLayCount, $hideLayNeuronCount, $outLayNeuronCount, $dopWeights;
    for ($i = 1; $i <= $hideLayCount + 1; $i++) {
        $jMax = $i == $hideLayCount + 1 ? $outLayNeuronCount : $hideLayNeuronCount;
        for ($j = 1; $j <= $jMax; $j++) {

            $kMax = $i == 1 ? $xCount : $hideLayNeuronCount;

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
            $smtr += $dopWeights[$i][$j];

            setValueFuncAct($i, $j, calcFuncActivation($smtr));

            if ($i == $hideLayCount + 1)
                $y[] = calcFuncActivation($smtr);
        }
    }

    return $y;
}

function calcBackPropErrors(&$ds, $weights)
{
    global $hideLayCount, $hideLayNeuronCount, $outLayNeuronCount;
    for ($i = $hideLayCount; $i > 0; $i--) {
        for ($j = 1; $j <= $hideLayNeuronCount; $j++) {
            $kMax = $i == $hideLayCount ? $outLayNeuronCount : $hideLayNeuronCount;

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

function weightCorrection($weights, $x, $ds, $momentsKoef)
{
    global $teachKoef, $hideLayCount, $hideLayNeuronCount, $outLayNeuronCount, $weightsPast;
    $newWeights = array();

    for ($i = 1; $i <= $hideLayCount + 1; $i++) {
        $jMax = $i == $hideLayCount + 1 ? $outLayNeuronCount : $hideLayNeuronCount;
        for ($j = 1; $j <= $jMax; $j++) {

            $multDsA = $ds["d" . $i . $j] * $teachKoef * (-1);
            $kMax = $i == 1 ? count($x) : $hideLayNeuronCount;

            for ($k = 1; $k <= $kMax; $k++) {
                $newW = getValueFuncAct($i - 1, $k) * $multDsA;

                $wKey = "w" . $i . $j . $k;
                $w = $weights[$wKey];

                $newW += $w;

//                $momentsKoef *= 1.0;
//                if ($momentsKoef > 0) {
////                    die();
//                    $newW += $momentsKoef * ($w - $weightsPast[$wKey]);
//                }

                $newWeights[$wKey] = $newW;
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
//    global $do, $momentsKoef;
//    if ($do != 'init')
//        $sko = sqrt($sum / (count($answers) - 1)) * $_POST['startedStopCountStandart'] / ($_POST['counterEpochs'] + $_POST['startedStopCountStandart']);
//    else
    $sko = sqrt($sum / (count($answers) - 1));
//    if ($momentsKoef > 0)
//        $sko *= 0.7;
    return $sko;
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

function goEpoch(&$weights, $numEpochOnIter = false)
{
    if ($numEpochOnIter == -1) {
        $fileName = 'zoo.data';

        $inputData = array();

        $handle = fopen($fileName, "r");

        while (!feof($handle)) {
            $buffer = fgets($handle, 1000);
            $exam = explode(",", trim($buffer));

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
            } elseif ($fileName == 'balance2.data') {
                $et = $exam[0];
                switch ($et) {
                    case "R":
                        $eth = array(0, 1);
                        break;
                    case "L":
                        $eth = array(1, 0);
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
            } elseif ($fileName == 'bupa.data') {
                //6 входов
                $et = $exam[count($exam) - 1];
                switch ($et) {
                    case "1":
                        $eth = array(1, 0);
                        break;
                    case "2":
                        $eth = array(0, 1);
                        break;
                }

                $xs = array_slice($exam, 0, count($exam) - 1);
            } elseif ($fileName == 'dermatology.data') {
                //34 входа, 6 выходов
                $et = $exam[count($exam) - 1];

                $eth = array(0, 0, 0, 0, 0, 0);
                $eth[$et - 1] = 1;

                $xs = array_slice($exam, 0, count($exam) - 1);
            } elseif ($fileName == 'glass.data') {
                //9 входов
                $et = $exam[count($exam) - 1];
                $eth = false;
//                if ($et >= 4)
//                    continue;
//                if ($et == 1 || $et == 3)
//                    $eth = array(1, 0);
//                elseif ($et == 2)
//                    $eth = array(0, 1);
                $eth = array(0, 0, 0, 0, 0, 0, 0);
                $eth[$et - 1] = 1;

                $xs = array_slice($exam, 1, count($exam) - 2);
            } elseif ($fileName == 'soybean-small.data') {
                $et = $exam[count($exam) - 1];
                switch ($et) {
                    case "D1":
                        $eth = array(1, 0, 0, 0);
                        break;
                    case "D2":
                        $eth = array(0, 1, 0, 0);
                        break;
                    case "D3":
                        $eth = array(0, 0, 1, 0);
                        break;
                    case "D4":
                        $eth = array(0, 0, 0, 1);
                        break;
                }

                $xs = array_slice($exam, 0, count($exam) - 1);
            } elseif ($fileName == 'tic-tac-toe.data') {
                //9 входов
                $et = $exam[count($exam) - 1];
                switch ($et) {
                    case "1":
                        $eth = array(1, 0);
                        break;
                    case "2":
                        $eth = array(0, 1);
                        break;
                }
                $xs = array_slice($exam, 0, count($exam) - 1);
            } elseif ($fileName == 'tae.data') {
                $et = $exam[count($exam) - 1];
                switch ($et) {
                    case "1":
                        $eth = array(1, 0, 0);
                        break;
                    case "2":
                        $eth = array(0, 1, 0);
                        break;
                    case "3":
                        $eth = array(0, 0, 1);
                        break;
                }
                $xs = array_slice($exam, 0, count($exam) - 1);
            } elseif ($fileName == 'winequality-red.csv') {
                $et = $exam[count($exam) - 1];
                switch ($et) {
                    case "5":
                        $eth = array(1, 0, 0, 0);
                        break;
                    case "6":
                        $eth = array(0, 1, 0, 0);
                        break;
                    case "7":
                        $eth = array(0, 0, 1, 0);
                        break;
                    case "8":
                        $eth = array(0, 0, 0, 1);
                        break;
                }
                $xs = array_slice($exam, 0, count($exam) - 1);
            }elseif ($fileName == 'nursery.data') {
                $et = $exam[count($exam) - 1];
                $eth = false;
                switch ($et) {
                    case "12":
                    case "15":
//                        $eth = array(1, 0, 0, 0);
//                        continue;
                        break;
                    case "11":
                        $eth = array(0, 1, 0);
                        break;
                    case "13":
                        $eth = array(0, 0, 1);
                        break;
                    case "22":
                        $eth = array(1, 0, 0);
                        break;
                }
                $xs = array_slice($exam, 0, count($exam) - 1);
            } elseif ($fileName == 'zoo.data') {
                $et = $exam[count($exam) - 1];
                $eth = array(0,0,0,0,0,0,0);
                $eth[$et - 1] = 1;

                $xs = array_slice($exam, 1, count($exam) - 2);
            } elseif ($fileName == 'poker-hand-training-true.data') {
                $et = $exam[count($exam) - 1];
                $eth = array(0,0,0,0,0,0,0,0,0);
                $eth[$et - 1] = 1;

                $xs = array_slice($exam, 1, count($exam) - 2);
            }

//            startExample($weights, $xs, $eth);

            if ($eth)
                $inputData[] = array($xs, $eth);
        }
        fclose($handle);
//        return $inputData;
        shuffle($inputData);
        $count = round(0.7 * count($inputData));
        global $testData;
        $testData = array_slice($inputData, $count);
        $inputData = array_slice($inputData, 0, $count);
    } else
        $inputData = $_POST['inputData'];
    foreach ($inputData as $i => $example) {
        startExample($weights, $example[0], $example[1]);
    }

    if ($numEpochOnIter > 0 && $_POST['dynamicTeachKoef'])
        recalcTeachKoef($numEpochOnIter);

    return $inputData;
}

function recalcTeachKoef($numEpochOnIter)
{
    global $teachKoef, $teachKoefStandart;

    $counterEpochs = $_POST['counterEpochs'] + $numEpochOnIter;
    $teachKoef = $teachKoefStandart / (1 + $counterEpochs / $_POST['startedStopCountStandart']);
}

function pre($var, $die = false)
{
    echo '<pre>';
    print_r($var);
    echo '</pre>';
    if ($die)
        die('Debug in PRE');
}