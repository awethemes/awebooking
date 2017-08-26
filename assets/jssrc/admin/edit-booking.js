const $ = window.jQuery;
const awebooking = window.TheAweBooking;

const AddLineItem = require('./booking/add-line-item.js');
const EditLineItem = require('./booking/edit-line-item.js');

$(function() {

  const $form = $('#awebooking-add-line-item-form');
  if ($form.length > 0) {
    new AddLineItem($form);
  }

  new EditLineItem;

  $('.js-delete-booking-item').on('click', function() {
    if (! confirm(awebooking.trans('warning'))) {
      return false;
    }
  });

});
