$(function() {

    // only load the content if we are currently on .createPage
    if ($('body').is('.createPage')) {

        $('.add-another-collection-widget').click(function (e) {
            e.preventDefault();
            var list = $($(this).attr('data-list'));
            // Try to find the counter of the list
            var counter = list.attr('data-widget-counter');
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

            setDisabledSubmit();

            // create a new variable for the deletebutton
            var deleteButtonId = ('#delete-'+(counter-1));
            // add a listener to the deletebutton
            $(deleteButtonId).click(function (e) {
                e.preventDefault();
                var deleteId = $(this).attr('data-deleteid');
                $(deleteId).parent().remove();
                // $(this).remove();
                var formAnswers = $( "input[id^='form_answer_']" );
                formAnswers.each(function(i) {
                    $(this).attr('placeholder', 'Answer ' + (parseInt(i)+1));
                });
                if (!formAnswers.length) {
                    $('.add-another-collection-widget').click();
                }
                setDisabledSubmit();
            });

        });


        $('.triggers-visibility').on('click', function(){
            var triggerSelector = $(this).attr('data-trigger-selector');
            if ( $(this).is(':checked') ) {
                // console.log('checked');
                $(triggerSelector).removeClass('is-hidden');
            } else {
                // console.log('not checked');
                $(triggerSelector).addClass('is-hidden');
            }
        });

        $('#next-steps a.button').on('click touchstart', function() {
            $('#next-steps a.button').addClass('is-hidden');
            $('#next-steps button').removeClass('is-hidden');
        });

        $('#back-steps a.button').on('click touchstart', function() {
            $('#next-steps a.button').removeClass('is-hidden');
            $('#next-steps button').addClass('is-hidden');
        });

        function areAllInputsFilled() {
            var inputs = $('form').find("input[type=text]");
            var full = inputs.filter(function() {
                return this.value !== "";
            });
            console.log(full.length);
            if(full.length >= 2) {
                return true;
            }
            return false;
        }

        function setDisabledSubmit() {
            setInputListener();
            var disableButton = true;
            areAllInputsFilled()?disableButton=false:disableButton=true;
            $('form button[type=submit]').prop('disabled', disableButton);
        }

        function init() {
            $('.add-another-collection-widget').click();
            $('#next-steps button').addClass('is-hidden');
            $('#allowed-answer-count').addClass('is-hidden');
            setDisabledSubmit();
            return true;
        }
        init();

        function setInputListener() {
            $('input[type=text]').off('input');
            $('input[type=text]').on('input', function() {
                setDisabledSubmit()
            });
        }

    }
});
