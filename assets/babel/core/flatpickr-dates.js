'use strict';

const $ = jQuery;
const debounce = require('debounce');

class SelectedDates {
  constructor(fp, input) {
    this.fp    = fp;
    this.input = input;
    this.dates = { startDate: null, endDate: null };
  }

  set(startDate, endDate) {
    this.setStartDate(startDate);
    this.setEndDate(endDate);
    this.update();
  }

  reset(update = false) {
    this.dates = { startDate: null, endDate: null };
    update && this.update();
  }

  update() {
    const input = this.input;
    const dateFormat = this.fp.config.dateFormat;

    this.fp.selectedDates = this.toArray();

    [input.start.value = '', input.end.value = ''] = this.fp.selectedDates.map(
      d => this.fp.formatDate(d, dateFormat)
    );
  }

  setStartDate(startDate) {
    const parsedDate = this.fp.parseDate(startDate);

    if (!parsedDate) {
      this.reset();
      return;
    }

    this.dates.startDate = parsedDate;

    if (this.dates.endDate && parsedDate >= this.dates.endDate) {
      this.dates.endDate = null;
    }
  }

  setEndDate(endDate) {
    const parsedDate = this.fp.parseDate(endDate);

    if (!parsedDate) {
      this.reset();
      return;
    }

    this.dates.endDate = parsedDate;
  }

  toArray() {
    let dates = Object.values(this.dates);

    return dates.filter((d) => { return d instanceof Date; });
  }
}

function RangeDatesPlugin(config) {
  const plugin = this;

  plugin.config = config;

  return function (fp) {
    const firstInput = config.firstInput;
    const secondInput = config.secondInput;

    let _minDate,
        _firstInputFocused,
        _secondInputFocused;

    const dates = new SelectedDates(fp, {
      start: firstInput,
      end: secondInput,
    });

    /**
     * Handle binding inputs.
     */
    function bindingInputs() {
      firstInput.setAttribute('data-fp-omit', '');
      secondInput.setAttribute('data-fp-omit', '');

      fp._bind(firstInput, ['focus', 'click'], debounce(function(e) {
        e.preventDefault();

        [_firstInputFocused, _secondInputFocused] = [true, false];

        fp.isOpen = false;
        fp.open(undefined, firstInput);

      }, 150, true));

      fp._bind(secondInput, ['focus', 'click'], debounce(function(e) {
        e.preventDefault();

        [_firstInputFocused, _secondInputFocused] = [false, true];

        fp.isOpen = false;
        fp.open(undefined, firstInput);

      }, 150, true));
    }

    return {
      onParseConfig() {
        fp.config.mode = 'range';
        fp.config.clickOpens = false;
        fp.config.closeOnSelect = false;

        _minDate = fp.config.minDate;
      },

      onReady() {
        bindingInputs();

        fp.config.ignoredFocusElements.push(firstInput);
        fp.config.ignoredFocusElements.push(secondInput);

        // dates.set(firstInput.value, secondInput.value);
      },

      onOpen() {
        $([firstInput, secondInput]).removeClass('selected');

        if (_firstInputFocused) {
          firstInput.classList.add('selected');
        } else {
          secondInput.classList.add('selected');
        }

        if (_secondInputFocused && fp.selectedDates[0]) {
          fp.config.minDate = fp.selectedDates[0];
        } else {
          fp.config.minDate = _minDate;
        }
      },

      onClose() {
        $([firstInput, secondInput]).removeClass('selected');
        [_firstInputFocused, _secondInputFocused] = [false, false];
      },

      onPreCalendarPosition() {
        fp._positionElement = firstInput;
        // fp._positionElement = _firstInputFocused ? firstInput : secondInput;
        setTimeout(() => { fp._positionElement = fp._input; }, 0);
      },

      onValueUpdate(selfDates) {
        if (_firstInputFocused) {
          dates.setStartDate(selfDates[0]);
        } else if (_secondInputFocused) {
          dates.setEndDate(selfDates[1] ? selfDates[1] : selfDates[0]);
        }

        dates.update();
        fp.setDate(fp.selectedDates, false);
      },

      onChange() {
        // if (_firstInputFocused) {
        //   setTimeout(() => { $(secondInput).trigger('focus'); }, 0);
        // } else if (_secondInputFocused && fp.selectedDates.length === 2) {
        //   setTimeout(fp.close, 0);
        // }
      },

    };
  };
}

module.exports = function FlatpickrDates(el, config = {}) {
  this.el = el;
  this.config = config;

  const $checkin = $(el).find('input[name="check_in"]');
  const $checkout = $(el).find('input[name="check_out"]');
  const $rangepicker = $(el).find('[data-hotel="rangepicker"]');

  const rangedates = new RangeDatesPlugin({
    firstInput: $checkin[0],
    secondInput: $checkout[0]
  });

  this.datepicker = awebooking.datepicker($rangepicker[0], {
    mode: 'range',
    inline: true,
    // static: true,
    // plugins: [ rangedates ],
  });
};
