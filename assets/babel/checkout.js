import $ from 'jquery';

$('.payment-method').on('click', function () {
  $('.payment-method').removeClass('selected')
  $(this).addClass('selected')
  $(this).find('input').prop('checked', true)
})
