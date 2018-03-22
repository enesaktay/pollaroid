$(function() {

    // only load the content if we are currently on .votePage
    if ($('body').is('.votePage')) {

        // var
        if ($('.multiple-answer-subtitle').length) {

            var allowedAnswerCount = $('.multiple-answer-subtitle').attr('data-allowed-answer-count');
            $('.answerfield .is-checkradio').on('change', function() {
                if($('.answerfield .is-checkradio:checked').length > allowedAnswerCount) {
                    this.checked = false;
                }
            });
        }

    }
});
