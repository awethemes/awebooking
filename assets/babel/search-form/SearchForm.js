import $ from 'jquery';
import { applyFilters } from '@wordpress/hooks';
import isSameDay from 'react-dates/lib/utils/isSameDay';
import toMomentObject from 'react-dates/lib/utils/toMomentObject';

import Control from '../utils/control';
import { formatDateString } from '../utils/date-utils';

export default class SearchForm {
  constructor(root, instance) {
    this.root = $(root);
    this.instance = instance;

    // Store the input elements
    this.elements = {};
    this.linkElements();

    this._registerBindings();

    this.reactDatePicker = undefined;
    if (window.createReactDatePicker && this.root.find('.abrs-searchbox__dates').length > 0) {
      this._createDatePicker();
    }
  }

  getFormData() {
    const elements = this.elements;

    let data = {};

    Object.keys(elements).forEach(index => {
      data[index] = elements[index].get();
    });

    return data;
  }

  getRootElement() {
    return this.root[0];
  }

  _createDatePicker() {
    const config = window.awebooking.config.datepicker;
    let { disableDays, disableDates } = config;

    disableDates = !Array.isArray(disableDates)
      ? disableDates.split(/,\s?/).map(day => toMomentObject(day))
      : disableDates;

    const isDayBlocked = (day) => {
      let disabled = false;

      if (Array.isArray(disableDays) && disableDays.length > 0) {
        disabled = disableDays.includes(parseInt(day.format('d'), 10));
      }

      if (!disabled && disableDates.length > 0) {
        disabled = disableDates.some(test => isSameDay(day, test));
      }

      return disabled;
    };

    let options = applyFilters('awebookingCreateReactDatePickerArgs', {
      isRTL: 'rtl' === $('html').attr('dir'),
      isDayBlocked: isDayBlocked,
      minimumNights: config.minNights || 1,
      maximumNights: config.maxNights || 0,
      minimumDateRange: config.minDate || 0,
      // maximumDateRange: config.maxNights ? (config.maxNights + config.minDate + 1) : 0,
      numberOfMonths: config.showMonths || 1,
    }, this);

    this.reactDatePicker = window.createReactDatePicker(this, options);
  }

  _registerBindings() {
    const binding = (bind) => {
      return (value) => {
        this.elements[bind].set(value ? formatDateString(value) : '');
      };
    };

    if (this.elements.hasOwnProperty('check_in_alt')) {
      this.elements['check_in'].bind(binding('check_in_alt'));
    }

    if (this.elements.hasOwnProperty('check_out_alt')) {
      this.elements['check_out'].bind(binding('check_out_alt'));
    }
  }

  /**
   * Link elements between settings and inputs
   */
  linkElements() {
    const control = this;

    const nodes = control.root.find('select, input, textarea');
    let radios = {};

    nodes.each((index, element) => {
      let node = $(element);

      if (node.data('_elementLinked')) {
        return;
      }

      // Prevent re-linking element.
      node.data('_elementLinked', true);
      let name = node.prop('name');

      if (node.is(':radio')) {
        if (radios[name]) {
          return;
        }

        radios[name] = true;
        node = nodes.filter('[name="' + name + '"]');
      }

      index = name || index;

      if (node.data('element')) {
        index = node.data('element');
      }

      control.elements[index] = new Control(node);
    });
  }
}
