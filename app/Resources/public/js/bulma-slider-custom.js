(function () {
    'use strict';

// Find output DOM associated to the DOM element passed as parameter
    function findOutputForSlider(element) {
        var idVal = element.id;
        var outputs = document.getElementsByTagName('output');
        for (var i = 0; i < outputs.length; i++) {
            if (outputs[i].htmlFor == idVal)
                return outputs[i];
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Get all document sliders
        var sliders = document.querySelectorAll('input[type="range"].slider');
        [].forEach.call(sliders, function (slider) {
            var output = findOutputForSlider(slider);
            var choicenames = jQuery.parseJSON(slider.getAttribute('data-choicenames'));
            if (output) {
                if (slider.classList.contains('has-output')) {
                    output.value = choicenames[slider.value];
                }

                // Add event listener to update output when slider value change
                slider.addEventListener('input', function (event) {

                    // Update output with slider choicename
                    output.value = choicenames[event.target.value];
                });
            }
        });
    });

}());
