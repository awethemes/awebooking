class Range_Field extends Skeleton.Field {
  /**
   * Init the field.
   */
  fire() {
    var $ = jQuery;

    this.$el.find('.cmb2-ui-slider-input').each(function() {
        var $input = $(this);

        if ($input.closest('.empty-row').length) {
            return;
        }

        var $text = $input.parent().find('.cmb2-ui-slider-preview');
        var $range = $input.parent().find('.cmb2-ui-slider');

        // Setup jQuery UI Slider.
        var rangeSlider = $range.slider({
            range: 'min',
            min: $input.data('min'),
            max: $input.data('max'),
            step: $input.data('step'),
            value: $input.data('value'),
            animate: true,
            slide: function(e, ui) {
                syncInputValue(ui.value);
            }
        });

        // Enable pips ui float.
        if ($input.data('float') && _.isObject($input.data('float'))) {
            rangeSlider.slider('float', $input.data('float'));
        }

        // Enable pips ui.
        if ($input.data('pips') && _.isObject($input.data('pips'))) {
            rangeSlider.slider('pips', $input.data('pips'));
        }

        var syncInputValue = function(value) {
            $text.text(value);
            $input.val(value).trigger('change');
        };

        var syncInputRange = function() {
            const inputValue = parseInt($(this).val());
            rangeSlider.slider('value', inputValue);

            // Fallback invalid value.
            if (rangeSlider.slider('value') !== inputValue) {
                $(this).val(rangeSlider.slider('value'));
            }
        };

        // Initiate the display.
        syncInputValue(rangeSlider.slider('value'));
        $input.on('change blur', syncInputRange);
    });
  }
}

module.exports = Range_Field;
