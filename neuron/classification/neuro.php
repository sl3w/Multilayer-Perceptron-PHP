<?php
define("FUNC_KOEF", 0.3);

global $isTeach, $numPrimer, $teachKoef, $teachKoefStandart, $momentsKoef;
global $hideLayCount, $hideLayNeuronCount, $outLayNeuronCount;
global $testData;

$isTeach = false;

global $answers, $ethalons, $funcsAct, $dopWeights, $weightsPast;

global $do;
$do = $_POST['do'];
$hideLayCount = $_POST['hideLayCount'];
$hideLayNeuronCount = $_POST['hideLayNeuronCount'];
$outLayNeuronCount = $_POST['outLayNeuronCount'];

//$weights = array(
//    "w111" => -0.24099216505000004
//, "w112" => 0.41497906433184584
//, "w113" => -0.1533754200923142
//, "w114" => 0.12504433916185254
//, "w115" => 0.20457524513107506
//, "w116" => 0.0539199717128277
//, "w117" => -0.06993425757155486
//, "w118" => -0.3297643912163397
//, "w119" => 0.43162838832085415
//, "w1110" => 0.21463074568409046
//, "w1111" => 0.0019756152769436763
//, "w1112" => -0.38056760462027395
//, "w1113" => 0.2743548405237286
//, "w1114" => 0.00003823800945568223
//, "w1115" => 0.21433151593167876
//, "w1116" => 0.17030012359390967
//, "w121" => -0.43636202297004034
//, "w122" => 0.37178348464508704
//, "w123" => 0.26940027986159565
//, "w124" => 0.21457166164860675
//, "w125" => -0.031057229978524725
//, "w126" => -0.37229653861014944
//, "w127" => 0.27349410242098104
//, "w128" => -0.4346628677727016
//, "w129" => -0.26321435056776477
//, "w1210" => -0.07529485299032873
//, "w1211" => -0.28839732789825523
//, "w1212" => -0.18533153398210722
//, "w1213" => 0.4559070244226172
//, "w1214" => 0.37407994311027226
//, "w1215" => 0.18529349224888414
//, "w1216" => -0.2632073363117908
//, "w131" => 0.42135340018260914
//, "w132" => -0.32885968677180805
//, "w133" => -0.3741327118473745
//, "w134" => 0.3755161747688037
//, "w135" => -0.25641509041023214
//, "w136" => 0.353098648531874
//, "w137" => 0.014139157493663568
//, "w138" => 0.4105006046176425
//, "w139" => -0.32816085630476516
//, "w1310" => 0.0793415231534007
//, "w1311" => -0.08111670174687946
//, "w1312" => -0.38256578281641274
//, "w1313" => -0.025836661237215952
//, "w1314" => -0.2868109079016423
//, "w1315" => 0.4510157168614751
//, "w1316" => 0.4459036960945948
//, "w141" => -0.3636472801974263
//, "w142" => 0.04562332413421166
//, "w143" => -0.13781648764285093
//, "w144" => -0.06568837238740566
//, "w145" => -0.3040051021631831
//, "w146" => -0.1784209309510984
//, "w147" => -0.2942394920597968
//, "w148" => 0.11588253295788664
//, "w149" => -0.455937304979161
//, "w1410" => -0.4582860241449839
//, "w1411" => -0.298896314482622
//, "w1412" => -0.15403294640315368
//, "w1413" => 0.09376733684622096
//, "w1414" => -0.2659438521442673
//, "w1415" => -0.4940870171385291
//, "w1416" => -0.45998789135366114
//, "w151" => -0.10054554259429943
//, "w152" => 0.3015191908001523
//, "w153" => -0.28153263255093836
//, "w154" => 0.20279460386503234
//, "w155" => 0.3885775068255968
//, "w156" => -0.23971202910864353
//, "w157" => 0.08777717295464926
//, "w158" => -0.19493966861392353
//, "w159" => -0.1393062759373832
//, "w1510" => 0.013132057391634255
//, "w1511" => -0.37458970764399957
//, "w1512" => 0.42516562199460606
//, "w1513" => -0.2531315287356877
//, "w1514" => -0.1601874673088023
//, "w1515" => 0.4260390023356485
//, "w1516" => 0.03612976080557784
//, "w161" => 0.4142598397630546
//, "w162" => 0.2697092396066102
//, "w163" => 0.10290263435007196
//, "w164" => -0.028379351612357107
//, "w165" => -0.33054608983478795
//, "w166" => 0.2275530601514285
//, "w167" => 0.00936590671044113
//, "w168" => 0.11448981036175498
//, "w169" => -0.010465481090576156
//, "w1610" => 0.2705544958685313
//, "w1611" => 0.10631243354003528
//, "w1612" => -0.26594077552014067
//, "w1613" => -0.30683283498828895
//, "w1614" => 0.040113685904123675
//, "w1615" => -0.09528505317647246
//, "w1616" => 0.04771571259373597
//, "w171" => -0.2619081455105488
//, "w172" => -0.19345436603457405
//, "w173" => 0.141782146246071
//, "w174" => 0.23442200326101015
//, "w175" => 0.11871715105078984
//, "w176" => 0.31429755958462957
//, "w177" => 0.147878057625088
//, "w178" => -0.43891226730258776
//, "w179" => -0.23970756853917036
//, "w1710" => -0.2297312918723241
//, "w1711" => -0.47525508793781285
//, "w1712" => -0.1257525401309843
//, "w1713" => -0.42946671225571387
//, "w1714" => -0.06784636786572934
//, "w1715" => 0.04371552380906207
//, "w1716" => -0.46613749347912964
//, "w181" => 0.23972674260834548
//, "w182" => 0.28914878041909486
//, "w183" => 0.050978773530097055
//, "w184" => -0.2773875611728931
//, "w185" => -0.4337046262499432
//, "w186" => -0.22472521463629103
//, "w187" => -0.20692013143884025
//, "w188" => 0.47451629721304234
//, "w189" => 0.013334892929221964
//, "w1810" => 0.39727865527257256
//, "w1811" => 0.18430583909354448
//, "w1812" => -0.23742642474240921
//, "w1813" => 0.28444982030636157
//, "w1814" => -0.23958489845487518
//, "w1815" => -0.3957857004812852
//, "w1816" => -0.31108592534954005
//, "w191" => 0.41727961735673236
//, "w192" => -0.4055403903618177
//, "w193" => -0.23185618442104022
//, "w194" => 0.3902886472131538
//, "w195" => 0.3580473893592355
//, "w196" => 0.4100409387191948
//, "w197" => -0.3033507260509537
//, "w198" => -0.1501667623641746
//, "w199" => 0.35013200428808666
//, "w1910" => -0.3292451406965242
//, "w1911" => 0.4528830777634322
//, "w1912" => -0.2556588280739537
//, "w1913" => -0.0882610970122093
//, "w1914" => 0.17251269597211516
//, "w1915" => -0.4401147435140399
//, "w1916" => -0.4737722654704807
//, "w1101" => 0.13946129411433883
//, "w1102" => 0.4312840928003584
//, "w1103" => -0.302006116976033
//, "w1104" => -0.10189269557683389
//, "w1105" => -0.036314766638127494
//, "w1106" => -0.19199596889875642
//, "w1107" => 0.4032060177546023
//, "w1108" => -0.07573847499477604
//, "w1109" => -0.3550502307969379
//, "w11010" => -0.4799466677848001
//, "w11011" => -0.46306129776083926
//, "w11012" => -0.06110907791234044
//, "w11013" => 0.047361920376942424
//, "w11014" => 0.10306177642338987
//, "w11015" => 0.09003702485469967
//, "w11016" => 0.4437000541685615
//, "w1117" => 0.0730547544420952
//, "w1118" => -0.10591445006705563
//, "w1119" => -0.12017640965998938
//, "w11110" => -0.32314288654511
//, "w11111" => -0.35311024489491727
//, "w11112" => -0.3447076817251312
//, "w11113" => 0.4858284317822328
//, "w11114" => -0.07021765297754556
//, "w11115" => -0.10338379796751951
//, "w11116" => -0.36933349485943723
//, "w211" => 0.08578730355286379
//, "w212" => 0.2068392996242453
//, "w213" => 0.06154861513597365
//, "w214" => 0.01808661293149305
//, "w215" => -0.0042196435407826915
//, "w216" => 0.4791876873835864
//, "w217" => 0.23550242825201828
//, "w218" => 0.27895496495950733
//, "w219" => -0.2729418700434928
//, "w2110" => 0.17018018833835613
//, "w2111" => -0.35048260299977035
//, "w221" => -0.3870663404870156
//, "w222" => 0.18494053868806948
//, "w223" => 0.08072944245335156
//, "w224" => -0.4647821127273059
//, "w225" => -0.04288142246328358
//, "w226" => 0.06536009095858786
//, "w227" => -0.000680268043968979
//, "w228" => -0.31199779864959315
//, "w229" => 0.2103977458134283
//, "w2210" => 0.13010037766308546
//, "w2211" => 0.1332549846885982
//, "w231" => 0.20896147969596157
//, "w232" => -0.35058466943473776
//, "w233" => 0.07880783853065587
//, "w234" => -0.16430688820048556
//, "w235" => -0.2264175925992511
//, "w236" => 0.2872312147110846
//, "w237" => 0.413167034235395
//, "w238" => -0.03669449851694262
//, "w239" => -0.18434365125575275
//, "w2310" => 0.2215025330528163
//, "w2311" => 0.3626861958125076
//, "w241" => -0.3695082500900646
//, "w242" => -0.3214720016445368
//, "w243" => -0.13053262123397208
//, "w244" => 0.3950654328312099
//, "w245" => -0.078644338333348
//, "w246" => -0.003729305464648336
//, "w247" => 0.24148914205911065
//, "w248" => 0.1420617688643102
//, "w249" => 0.0027385272564079965
//, "w2410" => -0.2546650496100379
//, "w2411" => -0.22759789588283652
//, "w251" => -0.4503121068469771
//, "w252" => -0.3483857339473375
//, "w253" => 0.09379943301612481
//, "w254" => 0.016271010747305636
//, "w255" => 0.04343595567319358
//, "w256" => 0.3722002608106473
//, "w257" => -0.2888503874600168
//, "w258" => -0.4229103019102059
//, "w259" => -0.27973010706702717
//, "w2510" => -0.1551299885172071
//, "w2511" => -0.34987352036399466
//, "w261" => 0.4259337405329262
//, "w262" => 0.19406608291625327
//, "w263" => -0.27308127226917134
//, "w264" => 0.3351826997637668
//, "w265" => 0.29472226220868636
//, "w266" => 0.1520521913897489
//, "w267" => 0.27579267778237937
//, "w268" => -0.27067646559825004
//, "w269" => 0.27398820490296383
//, "w2610" => -0.03115536064428992
//, "w2611" => -0.3240584315844152
//, "w271" => -0.3581403102065158
//, "w272" => 0.38447680598333334
//, "w273" => 0.019142097103941325
//, "w274" => 0.11280081589371005
//, "w275" => -0.02615745622951421
//, "w276" => 0.35192852646667905
//, "w277" => -0.2655916725125125
//, "w278" => 0.04847957265958169
//, "w279" => 0.2751207997906584
//, "w2710" => -0.002720410238355564
//, "w2711" => 0.41656989507217423
//);

if ($do == 'init') {
    $numPrimer = 1;
    $inputData = goEpoch($weights, -1);

    $er = calcNetworkError($answers, $ethalons);

    $res['error'] = $er;
    $res['weights'] = $weights;
    $res['dopWeights'] = $dopWeights;
    $res['inputData'] = $inputData;
    $res['testData'] = $testData;
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
                $dopWeights[$i][$j] = getRandWeight();
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

function goEpoch(&$weights, $numEpochOnIter = false)
{
    if ($numEpochOnIter == -1) {
        $fileName = 'examples/zoo.data';

        $inputData = array();

        $handle = fopen($fileName, "r");

        while (!feof($handle)) {
            $buffer = fgets($handle, 1000);
            $exam = explode(",", trim($buffer));

            $et = $exam[count($exam) - 1];
            $eth = array(0, 0, 0, 0, 0, 0, 0);
            $eth[$et - 1] = 1;

            $xs = array_slice($exam, 1, count($exam) - 2);

            if ($eth)
                $inputData[] = array($xs, $eth);
        }
        fclose($handle);
//        return $inputData;
//        shuffle($inputData);
//        $count = round(0.7 * count($inputData));
        global $testData;
        $testData = array_slice($inputData, 80);
        $inputData = array_slice($inputData, 0, 80);
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