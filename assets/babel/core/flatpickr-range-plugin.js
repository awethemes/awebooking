module.exports = function rangePlugin(config = {}) {
  return function (fp) {
    let dateFormat = '', secondInput, _firstInputFocused, _secondInputFocused, _prevDates;

    /**
     * Create the secondary input picker.
     */
    const createSecondInput = () => {
      // Create the second input.
      secondInput = config.input instanceof Element
        ? config.input
        : window.document.querySelector(config.input);

      // Set the "end-date" if second input have any value.
      if (secondInput.value) {
        const parsedDate = fp.parseDate(secondInput.value);

        if (parsedDate) {
          fp.selectedDates.push(parsedDate);
        }
      }
    };

    const dateAddDays = (inputDate, days = 1) => {
      const date = new Date(inputDate.getTime());

      date.setDate(date.getDate() + days);

      return date;
    };

    const plugin = {
      onParseConfig() {
        fp.config.mode = 'range';

        dateFormat = fp.config.altInput
          ? fp.config.altFormat
          : fp.config.dateFormat;
      },

      /**
       * On flatpickr ready.
       */
      onReady() {
        createSecondInput();
      }
    };

    return plugin;
  };
}
