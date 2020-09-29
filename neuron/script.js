var currentWeightsValues;
var pastWeightsValues;
var currentDopWeightsValues;
var counterIteration = 0;
var counterEpochs = 0;
var hideLayCount = 0;
var hideLayNeuronCount = 0;
var outLayNeuronCount = 0;
var teachKoefStandart = 0;
var inputData;
var testData;

var startedStopCountStandart = 0;

var errorsStorage = [['Итерация', 'Значение ошибки'], [0, 0]];

$(function () {
    $("#init").click(function () {
        startedStopCountStandart = +$("#maxCountEpochs").val();

        $("#teach").attr("disabled", true);
        $("#test").attr("disabled", true);
        $("#testing").attr("disabled", true);
        counterIteration = 0;
        counterEpochs = 0;
        errorsStorage = [['Итерация', 'Значение ошибки']];

        hideLayCount = $("#hideLayCount").val();
        hideLayNeuronCount = $("#hideLayNeuronCount").val();
        outLayNeuronCount = $("#outLayNeuronCount").val();
        if (teachKoefStandart !== 0)
            $("#teachKoef").val(teachKoefStandart);
        teachKoefStandart = $("#teachKoef").val();

        $.ajax({
            url: "neuro.php",
            type: 'POST',
            dataType: 'json',
            data: {
                do: 'init',
                hideLayCount: hideLayCount,
                hideLayNeuronCount: hideLayNeuronCount,
                outLayNeuronCount: outLayNeuronCount
            },
            success: function (result) {
                $("#errorsTable td").remove();
                $("#errorsTable tbody tr").remove();
                $(".answer pre").text("");

                let er = result['error'];

                let weights = result['weights'];

                currentWeightsValues = weights;
                pastWeightsValues = weights;
                currentDopWeightsValues = result['dopWeights'];
                inputData = result['inputData'];
                testData = result['testData'];
                console.log(inputData);
                console.log(testData);

                console.log(result);
                let weightsStr = arrayToStr(weights);
                $("#weights").text("Текущее значение весов: \n\n" + weightsStr);

                $("#errorsTable tbody").html("<tr><td>" + counterIteration + "</td><td>" + counterEpochs + "</td><td>1</td><td>–</td><td>" + er + "</td></tr>");

                errorsStorage.push([counterIteration, er]);
                drawChart();

                $("#teach").attr("disabled", false);
                $("#test").attr("disabled", false);
                $("#testing").attr("disabled", false);
            }
        });
    });

    function arrayToStr(arr) {
        let blkstr = [];
        $.each(arr, function (index, value) {
            blkstr.push(index + ": " + value);
        });
        return blkstr.join("\n");
    }

    $("#teach").click(function () {
        toggleDisabled(true);


        counterIteration++;

        let countEpochs = +$("#countEpochs").val();
        let teachKoef = +$("#teachKoef").val();
        let dynamicTeachKoef = ($("#dynamicTeachKoef").is(':checked')) ? 1 : 0;
        let momentsKoef = ($("#stopTeach").is(':checked')) ? +$("#momentsKoef").val() : 0;

        $.ajax({
            url: "neuro.php",
            type: 'POST',
            dataType: 'json',
            data: {
                do: 'teach',
                weights: currentWeightsValues,
                dopWeights: currentDopWeightsValues,
                countEpochs: countEpochs,
                teachKoef: teachKoef,
                teachKoefStandart: teachKoefStandart,
                dynamicTeachKoef: dynamicTeachKoef,
                hideLayCount: hideLayCount,
                hideLayNeuronCount: hideLayNeuronCount,
                outLayNeuronCount: outLayNeuronCount,
                counterEpochs: counterEpochs,
                weightsPast: pastWeightsValues,
                momentsKoef: momentsKoef,
                startedStopCountStandart: startedStopCountStandart,
                inputData: inputData,
            },
            success: function (result) {
                counterEpochs += +countEpochs;

                // console.log(result);
                let er = result['error'];
                // console.log("Ошибка сети: " + er);

                let weights = result['weights'];
                currentWeightsValues = weights;
                pastWeightsValues = result['weightsPast'];

                let weightsStr = arrayToStr(weights);
                $("#weights").text("Текущее значение весов: \n\n" + weightsStr);

                $("#errorsTable tbody tr:first").before("<tr><td>" + counterIteration + "</td><td>" + counterEpochs + "</td><td>" + countEpochs + "</td><td>" + teachKoef + "</td><td>" + er + "</td></tr>");

                errorsStorage.push([counterEpochs, er]);
                drawChart();

                toggleDisabled(false);

                if (dynamicTeachKoef === 1)
                    $("#teachKoef").val(result['teachKoef']);


                let maxEpochs = $("#maxCountEpochs").val();
                let stopErrorParam = $("#stopErrorParam").val();

                if (er > stopErrorParam && counterEpochs < maxEpochs && !($("#stopTeach").is(':checked'))) {
                    $("#teach").click();
                } else {
                    if (countEpochs > maxEpochs)
                        $("#maxCountEpochs").val(+$("#maxCountEpochs").val() + startedStopCountStandart);
                    //$("#stopTeach").prop('checked', false);
                }
            }
        });
    });

    $("#test").click(function () {
        let inputTest = $("#inputTest").val();
        let inputAr = inputTest.split(',');

        $("#teach").attr("disabled", true);
        $("#test").attr("disabled", true);
        $("#testing").attr("disabled", true);

        $.ajax({
            url: "neuro.php",
            type: 'POST',
            dataType: 'json',
            data: {
                do: 'test',
                weights: currentWeightsValues,
                dopWeights: currentDopWeightsValues,
                hideLayCount: hideLayCount,
                hideLayNeuronCount: hideLayNeuronCount,
                outLayNeuronCount: outLayNeuronCount,
                in1: inputAr,
            },
            success: function (result) {
                console.log(result);

                $(".answer pre").text("Ответ сети: \n\n" + arrayToStr(result['answer']));
                $("#teach").attr("disabled", false);
                $("#test").attr("disabled", false);
                $("#testing").attr("disabled", false);
            }
        });
    });

    $("#testing").click(function () {
        // let inputTest = $("#inputTest").val();
        // let inputAr = inputTest.split(',');

        $("#teach").attr("disabled", true);
        $("#test").attr("disabled", true);
        $("#testing").attr("disabled", true);

        $.ajax({
            url: "neuro.php",
            type: 'POST',
            dataType: 'json',
            data: {
                do: 'testing',
                weights: currentWeightsValues,
                dopWeights: currentDopWeightsValues,
                hideLayCount: hideLayCount,
                hideLayNeuronCount: hideLayNeuronCount,
                outLayNeuronCount: outLayNeuronCount,
                testData: testData,
            },
            success: function (result) {
                // console.log(result);

                $(".answer2 pre").text("Процент правильных ответов: \n\n" + (result['answer'] * 100) + "\n\n" + result['answerText']);
                $("#teach").attr("disabled", false);
                $("#test").attr("disabled", false);
                $("#testing").attr("disabled", false);
            }
        });
    });

    google.charts.load('current', {packages: ['corechart', 'line']})

    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        let hideIterat = $("#hideIterat").val();
        let showStorage = errorsStorage;
        if (hideIterat < errorsStorage.length - 1) {
            let a = showStorage.slice(0, 1);
            let b = showStorage.slice(-(showStorage.length - hideIterat - 1));
            showStorage = a.concat(b);
        }
        var data = google.visualization.arrayToDataTable(showStorage);

        var options = {
            title: 'График ошибки',
            hAxis: {
                title: 'Номер итерации'
            },
            vAxis: {
                title: 'Значение ошибки'
            },
            backgroundColor: '#f1f8e9',
            legend: {position: 'top'}
        };

        var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

        chart.draw(data, options);
    }

    $("#hideIterat").on("change paste keyup", function () {
        drawChart();
    });

    function toggleDisabled(toog) {
        $("#init").attr("disabled", toog);
        $("#teach").attr("disabled", toog);
        $("#test").attr("disabled", toog);
        $("#testing").attr("disabled", toog);
        // $("#teachKoef").attr("disabled", toog);
        $("#useMoments").attr("disabled", toog);
        $("#momentsKoef").attr("disabled", toog);
    }
});