$(function() {

    // only load the content if we are currently on .createPage
    if ($('body').is('.createPage')) {

        $('.add-another-collection-widget').click(function (e) {
            e.preventDefault();
            var list = $($(this).attr('data-list'));
            // Try to find the counter of the list
            // var counter = list.attr('data-widget-counter');
            var counter = list.children().length;
            // If the counter does not exist, use the length of the list
            // if (!counter) { counter = list.children().length; }

            var number = $( "input[id^='form_answer_']" ).length;

            // grab the prototype template
            var newWidget = list.attr('data-prototype');
            // replace the "__name__" used in the id and name of the prototype
            // with a number that's unique to your emails
            // end name attribute looks like name="contact[emails][2]"
            newWidget = newWidget.replace(/__name__/g, counter);
            newWidget = newWidget.replace(/__number__/g, (number+1));

            // Increase the counter
            counter++;
            // And store it, the length cannot be used if deleting widgets is allowed
            list.attr('data-widget-counter', counter);

            // create a new list element and add it to the list
            var newElem = $(list.attr('data-widget-tags')).html(newWidget);
            newElem.appendTo(list);

            // setDisabledSubmit();

            // create a new variable for the deletebutton
            var deleteButtonId = ('#delete-'+(counter-1));
            // add a listener to the deletebutton
            addDeleteListener($(deleteButtonId));

        });


        $('.triggers-visibility').on('click', function() {
            var triggerSelector = $(this).attr('data-trigger-selector');
            if ( $(this).is(':checked') ) {
                $(triggerSelector).removeClass('is-hidden');
            } else {
                $(triggerSelector).addClass('is-hidden');
            }
        });

        $('#next-steps a.button').on('click touchstart', function(e) {
            // e.preventDefault();
            $('#next-steps a.button').addClass('is-hidden');
            $('#next-steps button').removeClass('is-hidden');
        });

        $('#back-steps a.button').on('click touchstart', function() {
            $('#next-steps a.button').removeClass('is-hidden');
            $('#next-steps button').addClass('is-hidden');
        });

        function addDeleteListener(x) {
            $(x).click(function(e) {
                e.preventDefault();
                var deleteId = $(x).attr('data-deleteid');
                $(deleteId).parent().remove();
                var formAnswers = $( "input[id^='form_answer_']" );
                formAnswers.each(function(i) {
                    $(this).attr('placeholder', 'Answer ' + (parseInt(i)+1));
                });
                if (!formAnswers.length) {
                    $('.add-another-collection-widget').click();
                }
            });
        }

        function init() {
            if ($( "input[id^='form_answer_']" ).length < 1) {
                $('.add-another-collection-widget').click();
            }
            $('#next-steps button').addClass('is-hidden');
            if ( !$('#form_allowMultipleAnswers').is(':checked') ) {
                $('#allowed-answer-count').addClass('is-hidden');
            }

            $( "a[id^='delete-']" ).each(function(e) {
                addDeleteListener(this);
            });

            return true;
        }
        init();


        // $('#question-form button[type=submit]').click(function() {
        //     if ( $('#form_question').val().replace(/^\s+|\s+$/g, "").length === 0) {
        //         $('#form_question').addClass('is-danger');
        //     }
        //
        //     var answerInputs = $( "input[id^='form_answer_']" );
        //     var emptyAnswers = answerInputs.filter(function() {
        //         return this.value === "";
        //     });
        //
        //     if ((answerInputs.length - emptyAnswers.length) < 1) {
        //         emptyAnswers.each(function(i) {
        //             $(this).addClass('is-danger');
        //         });
        //     } else {
        //         emptyAnswers.each(function(i) {
        //             $(this).removeClass('is-danger');
        //         });
        //     }
        //
        //     $('#back-steps .button').click();
        // });

    }
});
