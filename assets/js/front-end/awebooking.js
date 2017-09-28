(function($) {
  "use strict";

  var Awebooking = {

    datePicker: function() {
      $('[data-init="datepicker"]').each(function () {
        var el = $(this);

        var datePickerDefault = {
          minDate: 0,
          altFormat: 'yy-mm-dd',
          monthNames: _awebookingDateSetting.i10n.months,
          monthNamesShort: _awebookingDateSetting.i10n.monthsShort,
          dayNames: _awebookingDateSetting.i10n.weekdays,
          dayNamesMin: _awebookingDateSetting.i10n.weekdaysMin,
          dayNamesShort: _awebookingDateSetting.i10n.weekdaysShort,
          beforeShow: function() {
            $('#ui-datepicker-div').addClass('awebooking-datepicker');
          }
        };

        if ( el.hasClass('awebooking-start-date') ) {
          var valid_date = function () {
            var minNights = el.data('minNights');
            var el_endDate = $('.awebooking-end-date');
            var startDate = $(this).datepicker('getDate');
            var minDate = $(this).datepicker('getDate');
            minDate.setDate(minDate.getDate() + minNights);
            el_endDate.datepicker('setDate', minDate);
            el_endDate.datepicker('option', 'minDate', minDate);
          };

          datePickerDefault.onSelect = valid_date;
        }

        // Merge settings.
        var settings = $.extend(datePickerDefault, el.data());
        delete settings.init;

        el.datepicker(settings);
      });
    },

    calendar: function() {
      $("#awebooking-check-available-calendar").datepicker({
        numberOfMonths: [1, 2],
        minDate : 0
      });
    },

    datePickerCallBack: function() {
        var start_date = $("#start-date");
        var start_date_val = start_date.val();
        var end_date = $("#end-date");
        var end_date_val = end_date.val();
        if ( start_date_val ) {
          var valid_start_date = new Date(start_date_val);
          $(".awebooking-start-date").datepicker('setDate', valid_start_date)
        }

        if ( end_date_val ) {
          var valid_end_date = new Date(end_date_val);
          $(".awebooking-end-date").datepicker('setDate', valid_end_date)
        }
    },

    tabsAweBooking: function() {
      $('.awebooking-tab').each(function() {
        var $el = $(this);
        var $control = $el.find('.awebooking-tab__controls');
        var $content = $el.find('.awebooking-tab__content');

        $control.find('li').eq(0).addClass('active');
        $content.eq(0).show();

        $control.find('li').each(function() {
          var $item = $(this);

          $item.find('a').on('click', function(e) {
            e.preventDefault();
            var id = $(this).attr('href');

            $control.find('li').removeClass('active');
            $(this).parent().addClass('active');

            $content.hide();
            $(id).show();
          });
        });
      });
    },

    accordion: function() {
      $('.awebooking-accordion').each(function () {
        $(this).accordion({
          heightStyle: "content"
        });
      });
    },

    load: function() {
      this.datePicker();
      this.calendar();
      this.datePickerCallBack();
      this.tabsAweBooking();
      this.accordion();
    }

  };

  $(document).ready(function() {
    Awebooking.load();
  });
})(jQuery);
