<?php
$handle = fopen("balance.data", "r");
while (!feof($handle)) {
    $buffer = fgets($handle, 4096);
    $exam = explode(",", trim($buffer));

    $et = $exam[count($exam) - 1];

    //под весы
        $et = $exam[0];
        //$eth = array(0,0,0);
        //$eth[$et] = 1;
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

//        $eth = array(0,0,0,0,0,0,0,0);
//        if($et == 0 || $et == 1)
//            continue;
//        $eth[$et - 2] = 1;

//    switch ($et) {
//        case "Iris-setosa":
//            $eth = array(1, 0, 0);
//            break;
//        case "Iris-versicolor":
//            $eth = array(0, 1, 0);
//            break;
//        case "Iris-virginica":
//            $eth = array(0, 0, 1);
//            break;
//    }

//    $xs = array_slice($exam, 0, count($exam) - 1);
        $xs = array_splice($exam, 1);
//        pre($xs);
//        pre($eth);
//        die();
    pre($et);
    pre($xs);
    pre($eth);
    pre("--------------",1);
//        break;
}
fclose($handle);

function pre($var, $die = false)
{
    echo '<pre>';
    print_r($var);
    echo '</pre>';
    if ($die)
        die('Debug in PRE');
}