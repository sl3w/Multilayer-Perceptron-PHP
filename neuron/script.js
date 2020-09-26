var currentWeightsValues;
var currentDopWeightsValues;
var counterIteration = 0;
var hideLayCount = 0;

var errorsStorage = [['Итерация', 'Значение ошибки'], [0, 0]];

$(function () {
    $("#init").click(function () {
        $("#teach").attr("disabled", true);
        $("#test").attr("disabled", true);
        counterIteration = 0;
        errorsStorage = [['Итерация', 'Значение ошибки']];

        hideLayCount = $("#hideLayCount").val();

        $.ajax({
            url: "neuro.php",
            type: 'POST',
            dataType: 'json',
            data: {do: 'init', hideLayCount: hideLayCount},
            success: function (result) {
                $("#errorsTable td").remove();
                $("#errorsTable tbody tr").remove();
                $(".answer pre").text("");

                let er = result['error'];

                let weights = result['weights'];

                currentWeightsValues = weights;
                currentDopWeightsValues = result['dopWeights'];

                console.log(result);
                let weightsStr = arrayToStr(weights);
                $("#weights").text("Текущее значение весов: \n\n" + weightsStr);

                $("#errorsTable tbody").html("<tr><td>" + counterIteration + "</td><td>1</td><td>–</td><td>" + er + "</td></tr>");

                errorsStorage.push([counterIteration, er]);
                drawChart();

                $("#teach").attr("disabled", false);
                $("#test").attr("disabled", false);
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
        $("#teach").attr("disabled", true);
        $("#test").attr("disabled", true);
        counterIteration++;

        let countEpochs = $("#countEpochs").val();
        let teachKoef = $("#teachKoef").val();

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
                hideLayCount: hideLayCount
            },
            success: function (result) {
                console.log(result);
                let er = result['error'];
                console.log("Ошибка сети: " + er);

                let weights = result['weights'];
                currentWeightsValues = weights;

                let weightsStr = arrayToStr(result['weights']);
                $("#weights").text("Текущее значение весов: \n\n" + weightsStr);

                $("#errorsTable tbody tr:first").before("<tr><td>" + counterIteration + "</td><td>" + countEpochs + "</td><td>" + teachKoef + "</td><td>" + er + "</td></tr>");

                errorsStorage.push([counterIteration, er]);
                drawChart();

                $("#teach").attr("disabled", false);
                $("#test").attr("disabled", false);

                if (er > 1 && !($("#stopTeach").is(':checked'))) {
                    $("#teach").click();
                } else {
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

        $.ajax({
            url: "neuro.php",
            type: 'POST',
            dataType: 'json',
            data: {
                do: 'test',
                weights: currentWeightsValues,
                dopWeights: currentDopWeightsValues,
                hideLayCount: hideLayCount,
                in1: inputAr,
            },
            success: function (result) {
                console.log(result);

                $(".answer pre").text("Ответ сети: \n\n" + arrayToStr(result['answer']));
                $("#teach").attr("disabled", false);
                $("#test").attr("disabled", false);
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
});