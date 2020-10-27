<?php
define("REAL_DATA", false);

global $isTeach, $numPrimer, $teachKoef, $teachKoefStandart, $momentsKoef;
global $hideLayCount, $hideLayNeuronCount, $outLayNeuronCount;
global $testData, $futureTestData;

$isTeach = false;

global $answers, $ethalons, $funcsAct, $dopWeights, $weightsPast;

global $do;
$do = $_POST['do'];

//$hideLayCount = $_POST['hideLayCount'];
$hideLayCount = 1;
$hideLayNeuronCount = $_POST['hideLayNeuronCount'];
$outLayNeuronCount = 1;

$weights = array(
    "w111" => 0.3362996079196686
, "w112" => 0.3377145118255701
, "w113" => -0.27497442335587663
, "w121" => -0.12365506897850664
, "w122" => 0.04498905807034537
, "w123" => 0.2965310717916726
, "w211" => 0.3956474828979222
, "w212" => -0.10275207487994437
,"w131" =>  -0.4040317274183183
,"w132" =>  0.40447580064855315
,"w133" =>  0.4402289278526925
,"w141" =>  0.25936680369049625
,"w142" =>  0.4355361642947123
,"w143" =>  0.014660713036852235
,"w151" =>  -0.35826606762514734
,"w152" =>  -0.05797684218640292
,"w153" =>  0.22948728116670958
,"w161" =>  0.13677817752434784
,"w162" =>  -0.12877430190740818
,"w163" =>  -0.05757485682031832
,"w171" =>  0.11027031932504394
,"w172" =>  0.27313604334981
,"w173" =>  0.1891178035592278
,"w213" =>  -0.1569255146463055
,"w214" =>  0.1572684318093902
,"w215" =>  0.2971906954409511
,"w216" =>  -0.36847335606276677
,"w217" =>  -0.12016667640775752
);

if ($do == 'init') {
    $numPrimer = 1;
    $inputData = array();

    if (REAL_DATA) {
        $fileName = 'data/tesla.txt';
        $countExamples = 200;
        $allMaxTeachCount = 240;

        $handle = fopen($fileName, "r");

        $sumx = 0;
        $ii = 0;
        while (!feof($handle)) {
            $buffer = fgets($handle, 100);
            $x = doubleval(trim($buffer));
            if ($ii >= $allMaxTeachCount - $countExamples) {
                if ($ii < $allMaxTeachCount) {
                    $xs[] = $x;
                    $sumx += pow($x, 2);
                } else {
                    $futureTestData[] = $x;
                }
            }
            $ii++;
        }
        fclose($handle);
        $sumx = sqrt($sumx);
//        $sumx = 1;

        foreach ($xs as $i => $x) {
            $xs[$i] = $x / $sumx;
        }

        $windowSize = $_POST['windowSize'];
        for ($i = 0; $i < count($xs) - $windowSize; $i++) {
            $inputData[] = array(array_slice($xs, $i, $windowSize), array($xs[$i + $windowSize]));
        }

        foreach ($inputData as $i => $example) {
            startExample($weights, $example[0], $example[1]);
        }

        $testData = array(array_slice($xs, count($xs) - $windowSize), $futureTestData[0] / $sumx, $futureTestData[0], -1);
        $futureTestData = array_slice($futureTestData, 1);
    } else {
        $sumx = 0;
        $countExamples = 100;

        for ($ii = 1; $ii <= $countExamples; $ii++) {
            $xs[] = $ii;

            $sumx += pow($ii, 2);
        }
        $sumx = sqrt($sumx);
//        $sumx = 1;

        foreach ($xs as $i => $x) {
            $xs[$i] = $x / $sumx;
        }

        $windowSize = $_POST['windowSize'];
        for ($i = 0; $i < count($xs) - $windowSize; $i++) {
            $inputData[] = array(array_slice($xs, $i, $windowSize), array($xs[$i + $windowSize]));
        }

        foreach ($inputData as $i => $example) {
            startExample($weights, $example[0], $example[1]);
        }

        $testData = array(array_slice($xs, count($xs) - $windowSize), ($countExamples + 1) / $sumx, $countExamples + 1, -1);

        $futureTestData = array();
        for ($jj = 0; $jj < 20; $jj++) {
            $futureTestData[$jj] = $countExamples + $jj + 2;
        }
    }

    $er = calcNetworkError($answers, $ethalons);

    $res['error'] = $er;
    $res['weights'] = $weights;
    $res['dopWeights'] = $dopWeights;
    $res['inputData'] = $inputData;
    $res['answers'] = $answers;
    $res['eths'] = $ethalons;
    $res['kfNorm'] = $sumx;
    $res['testData'] = $testData;
    $res['futureTestData'] = $futureTestData;
    echo json_encode($res);

} elseif ($do == 'teach') {
    $isTeach = true;

    $weights = $_POST['weights'];
    $dopWeights = $_POST['dopWeights'];

    $countEpochs = $_POST['countEpochs'];
    $counterEpochs = $_POST['counterEpochs'];

    $teachKoef = $_POST['teachKoef'];
    $teachKoefStandart = $_POST['teachKoefStandart'];
    $momentsKoef = $_POST['momentsKoef'];

    $weightsPast = $_POST['weightsPast'];
    $inputData = $_POST['inputData'];

    for ($j = 0; $j < $countEpochs; $j++) {
        $numPrimer = 1;

        foreach ($inputData as $i => $example) {
            startExample($weights, $example[0], $example[1]);
        }

        if ($_POST['dynamicTeachKoef'])
            recalcTeachKoef($j);
    }

    $isTeach = false;

    $answers = array();
    $ethalons = array();

    $numPrimer = 1;
    foreach ($inputData as $i => $example) {
        startExample($weights, $example[0], $example[1]);
    }

    $er = calcNetworkError($answers, $ethalons);

    $res['error'] = round($er, 7);
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

    $countTests = $_POST['countTests'];
    $kfNorm = $_POST['kfNorm'];
    $testData = $_POST['testData'];
    $futureTestData = $_POST['futureTestData'];

    $ansAr = array();
    $want = array();

    $skoSum = 0;
    for ($ii = 0; $ii < $countTests; $ii++) {
        $want[] = +$testData[2];
        $ans = startExample($weights, $testData[0]);
        $skoSum += pow($testData[1] - $ans[0], 2);

        $testData[0] = array_slice($testData[0], 1);
        $testData[0][count($testData[0])] = $ans[0];
        $testData[2] = $futureTestData[$testData[3] + 1];
        $testData[3] = $testData[3] + 1;
        $testData[1] = $testData[2] / $kfNorm;

        $ansAr[] = $ans[0] * $kfNorm;
    }

    $sum = 0;
    foreach ($ansAr as $k => $answer) {
        $sum += pow($answer / $kfNorm - $want[$k] / $kfNorm, 2);
    }

    $er = sqrt($sum / (count($ansAr) - 1));

    $res['testData'] = $testData;
    $res['ans'] = $ansAr;
    $res['want'] = $want;
    $res['er'] = round($er, 7);
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

//    foreach ($yOut as $k => $value) {
    $d = $yOut[0] - $eth[0];

    $ds["d" . ($hideLayCount + 1) . "1"] = $d * calcProizvod($yOut[0]);
//    }


    if ($isTeach) {
        calcBackPropErrors($ds, $weights);

        $weightsPastTemp = $weights;

        $newWeights = weightCorrection($weights, $x, $ds, $momentsKoef);

        $weightsPast = $weightsPastTemp;

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
//                $dopWeights[$i][$j] = getRandWeight();
                $dopWeights[$i][$j] = 0.5;
            }
//            $smtr += $dopWeights[$i][$j];

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

            global $numPrimer, $zhop;

            $zhop[$numPrimer]["curD"] = $curD;
            $zhop[$numPrimer]["Fa"] = getValueFuncAct($i, $j);
            $zhop[$numPrimer]["proizvod"] = calcProizvod(getValueFuncAct($i, $j));

            $curD *= calcProizvod(getValueFuncAct($i, $j));
            $zhop[$numPrimer]["curD*proizvod"] = $curD;
            $ds["d" . $i . $j] = $curD;
        }
    }
}

function weightCorrection($weights, $x, $ds, $momentsKoef)
{
    global $teachKoef, $hideLayCount, $hideLayNeuronCount, $outLayNeuronCount, $weightsPast, $zhop2;
    $newWeights = array();

    for ($i = 1; $i <= $hideLayCount + 1; $i++) {
        $jMax = $i == $hideLayCount + 1 ? $outLayNeuronCount : $hideLayNeuronCount;
        for ($j = 1; $j <= $jMax; $j++) {

            $multDsA = $ds["d" . $i . $j] * $teachKoef;

            $zhop2[$i . $j] = $multDsA;

            $kMax = $i == 1 ? count($x) : $hideLayNeuronCount;

            for ($k = 1; $k <= $kMax; $k++) {
                $newW = getValueFuncAct($i - 1, $k) * $multDsA;

                $wKey = "w" . $i . $j . $k;
                $w = $weights[$wKey];

                $newW = $w - $newW;

                $momentsKoef *= 1.0;
                if ($momentsKoef > 0) {
                    $newW += $momentsKoef * ($w - $weightsPast[$wKey]);
                }

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
            if ($layNum == 1)
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
    return 1 / (1 + exp(-$x));
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

    return sqrt($sum / (count($answers) - 1));
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

//function goEpoch(&$weights, $numEpochOnIter = false)
//{
//    if ($numEpochOnIter == -1) {
//
//        $inputData = array();
//
//        $sumx = 0;
//        for ($ii = 1; $ii <= 100; $ii++) {
//            $x = 0 + $ii;
//            $xs[] = $x;
//
//            $sumx += pow($x, 2);
//        }
//        $sumx = sqrt($sumx);
//        global $kfNorm;
//        $kfNorm = $sumx;
//
//        foreach ($xs as $i => $x) {
//            $xs[$i] = $x / $sumx;
//        }
//
//        for ($i = 0; $i < count($xs) - 5; $i++) {
//            $inputData[] = array(array_slice($xs, $i, 5), array($xs[$i + 5]));
//        }
//    } else
//        $inputData = $_POST['inputData'];
//
//    foreach ($inputData as $i => $example) {
//        startExample($weights, $example[0], $example[1]);
//    }
//
////    if ($numEpochOnIter > 0 && $_POST['dynamicTeachKoef'])
////        recalcTeachKoef($numEpochOnIter);
//
//
//    return $inputData;
//}

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