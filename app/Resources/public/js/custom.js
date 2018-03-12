$(function() {
    // globally custom js, should load on all pages
    console.log('custom loaded');

    if ($('body').is('.votePage') || $('body').is('.resultPage')) {

        // Load easytimer to show how much time is left to vote
        var Timer = require('easytimer.js');

        var copyBtn = $('.copy-btn');

        copyBtn.click(function() {
            var copySelector = $(this).data('copyselector');
            $(copySelector).select();
            document.execCommand("copy");
            var temp = copyBtn.data('tooltip');
            copyBtn.attr('data-tooltip', 'Copied!');
            $('.copy-btn svg').removeClass('fa-copy').addClass('fa-check');
            setTimeout(function(){
                $('.copy-btn svg').addClass('fa-copy').removeClass('fa-check');
                copyBtn.attr('data-tooltip', temp);
            }, 1000);
        });

        var timeLeft = $("#timeLeft");
        var timeLeftData = timeLeft.data("timelefttovote");

        if (typeof timeLeftData !== 'undefined') {
            var timer = new Timer();
            timer.start({
                countdown:true,
                precision: 'seconds',
                startValues: {
                    days : timeLeftData[0],
                    hours: timeLeftData[1],
                    minutes: timeLeftData[2],
                    seconds: timeLeftData[3]
                },
            });

            timer.addEventListener('targetAchieved', function (e) {

                if ( $("#navigationBar .has-text-right .button").length ) {
                    $("#navigationBar .has-text-right .button").click();
                } else {
                    $('#navigationBar').remove();
                }
                // if ($('body').is('.resultPage')) {
                // } else {
                //     $('#timeLeft').remove();
                // }
            });

            timer.addEventListener('secondsUpdated', function (e) {
                $('#timeLeft .days').html(timer.getTimeValues().days);
                $('#timeLeft .hours').html(timer.getTimeValues().hours);
                $('#timeLeft .minutes').html(timer.getTimeValues().minutes);
                $('#timeLeft .seconds').html(timer.getTimeValues().seconds);
            });
        }
    }


});
