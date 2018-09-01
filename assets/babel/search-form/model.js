import plugin from 'awebooking'

const SearchModel = (($) => {
  'use strict';

  function formatDate(date, format) {
    const _date = plugin.utils.dates.parse(date, 'Y-m-d');

    if (!_date) {
      return '';
    }

    return plugin.utils.dates.format(_date, format || plugin.i18n.dateFormat);
  }

  class Model {
    constructor(data = {}) {
      this.adults = ko.observable(data.adults || 1)
      this.children = ko.observable(data.children || 0)
      this.infants = ko.observable(data.infants || 0)
      this.checkIn = ko.observable(data.checkIn || '')
      this.checkOut = ko.observable(data.checkOut || '')

      this.checkInDate = ko.computed(() => {
        return formatDate(this.checkIn(), 'Y-m-d')
      })

      this.checkOutDate = ko.computed(() => {
        return formatDate(this.checkOut(), 'Y-m-d')
      })
    }

    checkInFormatted(format) {
      return formatDate(this.checkIn(), format)
    }

    checkOutFormatted(format) {
      return formatDate(this.checkOut(), format)
    }
  }

  return Model;
})(jQuery);

export default SearchModel
