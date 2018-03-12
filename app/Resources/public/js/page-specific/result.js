$(function() {

    // only load the content if we are currently on .resultPage
    if ($('body').is('.resultPage')) {

        // Load nice color palettes into the chart
        var colors = require('nice-color-palettes');

        // find the chart
        var doughNutChart = $("#doughnut-chart");

        // get all the data needed for the chart
        var votes = doughNutChart.data('votes');

        var answers = doughNutChart.data('answers');

        var chartLabel = [];

        function setChartLabel(voteArray) {
            $.each( answers, function( key, answer ) {
                if (typeof voteArray[key] === 'undefined') {
                    voteArray[key] = 0;
                }
                chartLabel[key] = answer + " - " + voteArray[key] + ((voteArray[key]==1)?" Vote":" Votes");
            });
        }


        if (typeof votes !== 'undefined') {

            setChartLabel(votes);

            // initialize the chart with the data from twig
            var myChart = new Chart(doughNutChart, {
                type: 'doughnut',
                data: {
                    labels: chartLabel,
                    datasets: [{
                        label: '# of Votes',
                        backgroundColor: colors[3].concat(colors[1]),
                        data: votes
                    }]
                },
                options: {
                    title: {
                        display: true,
                        text: 'Poll Result'
                    },
                    tooltips: {
                        callbacks: {
                            label: function (tooltipItem, data) {
                                var dataset = data.datasets[tooltipItem.datasetIndex];
                                var total = dataset.data.reduce(function (previousValue, currentValue, currentIndex, array) {
                                    return previousValue + currentValue;
                                });
                                var currentValue = dataset.data[tooltipItem.index];
                                var percentage = Math.floor(((currentValue / total) * 100) + 0.5);
                                return currentValue + " Votes (" +percentage + "%)";
                            }
                        }
                    }
                }
            });
        }


        // function to change the data from the chart
        function addData(chart, data, datasetIndex) {
            chart.data.datasets[datasetIndex].data = data;

            setChartLabel(data);

            chart.update();
        }
        // function addData(chart, label, data) {
        //     chart.data.labels.push(label);
        //     chart.data.datasets.forEach((dataset) => {
        //         dataset.data.push(data);
        //     });
        //     chart.update();
        // }

        // get the ajax call url
        var refreshUrl = $('#resultHeroBody').data('refreshurl');

        // function to wrap the ajax call
        function ajaxUpdate(_callback, loop) {
            $.ajax({
                type: "GET",
                url: refreshUrl,
                dataType: "json",
                success: function(response) {
                    // callback to the caller, to remove the loading from the button
                    _callback();
                    // update the chart with the new data, calls declared function
                    addData(myChart, response.votes, 0);
                    // if to filter the constant updated button
                    if (loop) {
                        // if to filter when the user wants to stop keep updating
                        if (stopKeepRefresh) {
                            stopKeepRefreshBtn.addClass('is-hidden');
                            onceRefreshBtn.removeClass('is-hidden');
                            keepRefreshBtn.removeClass('is-loading');
                            stopKeepRefreshBtn.attr('disabled', false);
                            stopKeepRefresh = false;
                            return true;
                        }
                        // timeout of 3s in order to not flood the server with requests, calls the function itself
                        setTimeout(function(){
                            ajaxUpdate(function () {
                                console.log('refreshed chart');
                            }, true);
                        }, 3000);
                    }
                }
            });
        }

        // declare all the refresh buttons
        var onceRefreshBtn = $('#onceRefresh');
        var keepRefreshBtn = $('#keepRefresh');
        var stopKeepRefreshBtn = $('#stopKeepRefresh');

        // set a boolean to to filter out the stop refreshing
        var stopKeepRefresh = false;

        // calls ajax update function from above, with loop = false which means the function wont keep calling itself
        onceRefreshBtn.click(function() {
            onceRefreshBtn.addClass('is-loading');
            ajaxUpdate(function () {
                console.log('refreshed chart');
                // show the loading bar for 1s to filter some spam click
                setTimeout(function(){
                    onceRefreshBtn.removeClass('is-loading');
                }, 1000);
            }, false);
        });

        // calls ajax update function from above, with loop = false which means the function will keep calling itself
        keepRefreshBtn.click(function() {
            onceRefreshBtn.addClass('is-hidden');
            stopKeepRefreshBtn.removeClass('is-hidden');
            keepRefreshBtn.addClass('is-loading');
            ajaxUpdate(function () {
                console.log('refreshed chart');
            }, true);
        });

        // sets the boolean to true to make the ajaxUpdate function stop after finishing the current request
        stopKeepRefreshBtn.click(function() {
            stopKeepRefreshBtn.attr('disabled', true);
            stopKeepRefresh = true;
        });

    }
});
