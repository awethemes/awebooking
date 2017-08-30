webpackJsonp([4],{

/***/ 21:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(22);


/***/ }),

/***/ 22:
/***/ (function(module, exports) {

var $ = window.jQuery;
var AweBooking = window.TheAweBooking;

$(function () {

  var $dialog = AweBooking.Popup.setup('#awebooking-set-availability-popup');

  var rangepicker = new AweBooking.RangeDatepicker('input[name="datepicker-start"]', 'input[name="datepicker-end"]');

  var showComments = function showComments(calendar) {
    var nights = calendar.getNights();
    var toNight = calendar.endDay;

    var text = '';
    if (nights === 1) {
      text = 'One night: ' + toNight.format(calendar.format);
    } else {
      text = '<b>' + nights + '</b> nights' + ' nights, from <b>' + calendar.startDay.format(calendar.format) + '</b> to <b>' + toNight.format(calendar.format) + '</b>';
    }

    return text;
  };

  var onApplyCalendar = function onApplyCalendar() {
    var calendar = this;

    calendar.room_name = this.$el.closest('.abkngcal-container').find('h2').text();
    calendar.data_id = this.$el.closest('[data-room]').data('room');
    calendar.comments = showComments(calendar);

    var formTemplate = wp.template('availability-calendar-form');
    $dialog.find('.awebooking-dialog-contents').html(formTemplate(calendar));

    $dialog.find('form').data('calendar', calendar);
    $dialog.dialog('open');
  };

  var ajaxGetCalendar = function ajaxGetCalendar(calendar) {
    var $container = calendar.$el.parents('.abkngcal-container');
    $container.find('.abkngcal-ajax-loading').show();

    wp.ajax.post('get_awebooking_yearly_calendar', {
      year: $container.find('.abkngcal select').val(),
      room: $container.data('room')
    }).done(function (data) {
      calendar.destroy();
      calendar.$el.find('select').off();

      $container.html($(data.html).html());
      calendar.$el = $container.find('.abkngcal.abkngcal--yearly');

      calendar.initialize();
      calendar.on('apply', onApplyCalendar);
      calendar.$el.find('select').on('change', ajaxGetCalendar.bind(null, calendar));
    });
  };

  $('.abkngcal.abkngcal--yearly', document).each(function (index, el) {
    var calendar = new window.AweBookingYearlyCalendar(el);
    $(el).data('availability-calendar', calendar);

    calendar.on('apply', onApplyCalendar);
    calendar.$el.find('select').on('change', ajaxGetCalendar.bind(null, calendar));
  });

  $dialog.find('form').on('submit', function (e) {
    e.preventDefault();

    var $el = $(this);
    var calendar = $el.data('calendar');

    $el.addClass('ajax-loading');
    AweBooking.ajaxSubmit(this, 'set_awebooking_availability').always(function () {
      $el.removeClass('ajax-loading');
    }).done(function (data) {
      ajaxGetCalendar(calendar);
      $dialog.dialog('close');
    }).fail(function (e) {
      if (e.error && typeof e.error === 'string') {
        alert(e.error);
      }
    });
  });

  rangepicker.init();
});

/***/ })

},[21]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9hc3NldHMvanNzcmMvYWRtaW4vbWFuYWdlci1hdmFpbGFiaWxpdHkuanMiXSwibmFtZXMiOlsiJCIsIndpbmRvdyIsImpRdWVyeSIsIkF3ZUJvb2tpbmciLCJUaGVBd2VCb29raW5nIiwiJGRpYWxvZyIsIlBvcHVwIiwic2V0dXAiLCJyYW5nZXBpY2tlciIsIlJhbmdlRGF0ZXBpY2tlciIsInNob3dDb21tZW50cyIsImNhbGVuZGFyIiwibmlnaHRzIiwiZ2V0TmlnaHRzIiwidG9OaWdodCIsImVuZERheSIsInRleHQiLCJmb3JtYXQiLCJzdGFydERheSIsIm9uQXBwbHlDYWxlbmRhciIsInJvb21fbmFtZSIsIiRlbCIsImNsb3Nlc3QiLCJmaW5kIiwiZGF0YV9pZCIsImRhdGEiLCJjb21tZW50cyIsImZvcm1UZW1wbGF0ZSIsIndwIiwidGVtcGxhdGUiLCJodG1sIiwiZGlhbG9nIiwiYWpheEdldENhbGVuZGFyIiwiJGNvbnRhaW5lciIsInBhcmVudHMiLCJzaG93IiwiYWpheCIsInBvc3QiLCJ5ZWFyIiwidmFsIiwicm9vbSIsImRvbmUiLCJkZXN0cm95Iiwib2ZmIiwiaW5pdGlhbGl6ZSIsIm9uIiwiYmluZCIsImRvY3VtZW50IiwiZWFjaCIsImluZGV4IiwiZWwiLCJBd2VCb29raW5nWWVhcmx5Q2FsZW5kYXIiLCJlIiwicHJldmVudERlZmF1bHQiLCJhZGRDbGFzcyIsImFqYXhTdWJtaXQiLCJhbHdheXMiLCJyZW1vdmVDbGFzcyIsImZhaWwiLCJlcnJvciIsImFsZXJ0IiwiaW5pdCJdLCJtYXBwaW5ncyI6Ijs7Ozs7Ozs7Ozs7OztBQUFBLElBQU1BLElBQUlDLE9BQU9DLE1BQWpCO0FBQ0EsSUFBTUMsYUFBYUYsT0FBT0csYUFBMUI7O0FBRUFKLEVBQUUsWUFBVzs7QUFFWCxNQUFNSyxVQUFVRixXQUFXRyxLQUFYLENBQWlCQyxLQUFqQixDQUF1QixvQ0FBdkIsQ0FBaEI7O0FBRUEsTUFBTUMsY0FBYyxJQUFJTCxXQUFXTSxl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bnQiOlsiY29uc3QgJCA9IHdpbmRvdy5qUXVlcnk7XG5jb25zdCBBd2VCb29raW5nID0gd2luZG93LlRoZUF3ZUJvb2tpbmc7XG5cbiQoZnVuY3Rpb24oKSB7XG5cbiAgY29uc3QgJGRpYWxvZyA9IEF3ZUJvb2tpbmcuUG9wdXAuc2V0dXAoJyNhd2Vib29raW5nLXNldC1hdmFpbGFiaWxpdHktcG9wdXAnKTtcblxuICBjb25zdCByYW5nZXBpY2tlciA9IG5ldyBBd2VCb29raW5nLlJhbmdlRGF0ZXBpY2tlcihcbiAgICAnaW5wdXRbbmFtZT1cImRhdGVwaWNrZXItc3RhcnRcIl0nLFxuICAgICdpbnB1dFtuYW1lPVwiZGF0ZXBpY2tlci1lbmRcIl0nXG4gICk7XG5cbiAgY29uc3Qgc2hvd0NvbW1lbnRzID0gZnVuY3Rpb24oY2FsZW5kYXIpIHtcbiAgICBjb25zdCBuaWdodHMgPSBjYWxlbmRhci5nZXROaWdodHMoKTtcbiAgICBjb25zdCB0b05pZ2h0ID0gY2FsZW5kYXIuZW5kRGF5O1xuXG4gICAgbGV0IHRleHQgPSAnJztcbiAgICBpZiAobmlnaHRzID09PSAxKSB7XG4gICAgICB0ZXh0ID0gJ09uZSBuaWdodDogJyArIHRvTmlnaHQuZm9ybWF0KGNhbGVuZGFyLmZvcm1hdCk7XG4gICAgfSBlbHNlIHtcbiAgICAgIHRleHQgPSBgPGI+JHtuaWdodHN9PC9iPiBuaWdodHNgICsgJyBuaWdodHMsIGZyb20gPGI+JyArIGNhbGVuZGFyLnN0YXJ0RGF5LmZvcm1hdChjYWxlbmRhci5mb3JtYXQpICsgJzwvYj4gdG8gPGI+JyArIHRvTmlnaHQuZm9ybWF0KGNhbGVuZGFyLmZvcm1hdCkgKyAnPC9iPic7XG4gICAgfVxuXG4gICAgcmV0dXJuIHRleHQ7XG4gIH07XG5cbiAgY29uc3Qgb25BcHBseUNhbGVuZGFyID0gZnVuY3Rpb24oKSB7XG4gICAgbGV0IGNhbGVuZGFyID0gdGhpcztcblxuICAgIGNhbGVuZGFyLnJvb21fbmFtZSA9IHRoaXMuJGVsLmNsb3Nlc3QoJy5hYmtuZ2NhbC1jb250YWluZXInKS5maW5kKCdoMicpLnRleHQoKTtcbiAgICBjYWxlbmRhci5kYXRhX2lkICAgPSB0aGlzLiRlbC5jbG9zZXN0KCdbZGF0YS1yb29tXScpLmRhdGEoJ3Jvb20nKTtcbiAgICBjYWxlbmRhci5jb21tZW50cyAgPSBzaG93Q29tbWVudHMoY2FsZW5kYXIpO1xuXG4gICAgY29uc3QgZm9ybVRlbXBsYXRlID0gd3AudGVtcGxhdGUoJ2F2YWlsYWJpbGl0eS1jYWxlbmRhci1mb3JtJyk7XG4gICAgJGRpYWxvZy5maW5kKCcuYXdlYm9va2luZy1kaWFsb2ctY29udGVudHMnKS5odG1sKGZvcm1UZW1wbGF0ZShjYWxlbmRhcikpO1xuXG4gICAgJGRpYWxvZy5maW5kKCdmb3JtJykuZGF0YSgnY2FsZW5kYXInLCBjYWxlbmRhcik7XG4gICAgJGRpYWxvZy5kaWFsb2coJ29wZW4nKTtcbiAgfTtcblxuICBjb25zdCBhamF4R2V0Q2FsZW5kYXIgPSBmdW5jdGlvbihjYWxlbmRhcikge1xuICAgIGNvbnN0ICRjb250YWluZXIgPSBjYWxlbmRhci4kZWwucGFyZW50cygnLmFia25nY2FsLWNvbnRhaW5lcicpO1xuICAgICRjb250YWluZXIuZmluZCgnLmFia25nY2FsLWFqYXgtbG9hZGluZycpLnNob3coKTtcblxuICAgIHdwLmFqYXgucG9zdCggJ2dldF9hd2Vib29raW5nX3llYXJseV9jYWxlbmRhcicsIHtcbiAgICAgIHllYXI6ICRjb250YWluZXIuZmluZCgnLmFia25nY2FsIHNlbGVjdCcpLnZhbCgpLFxuICAgICAgcm9vbTogJGNvbnRhaW5lci5kYXRhKCdyb29tJyksXG4gICAgfSlcbiAgICAuZG9uZShmdW5jdGlvbihkYXRhKSB7XG4gICAgICBjYWxlbmRhci5kZXN0cm95KCk7XG4gICAgICBjYWxlbmRhci4kZWwuZmluZCgnc2VsZWN0Jykub2ZmKCk7XG5cbiAgICAgICRjb250YWluZXIuaHRtbCgkKGRhdGEuaHRtbCkuaHRtbCgpKTtcbiAgICAgIGNhbGVuZGFyLiRlbCA9ICRjb250YWluZXIuZmluZCgnLmFia25nY2FsLmFia25nY2FsLS15ZWFybHknKTtcblxuICAgICAgY2FsZW5kYXIuaW5pdGlhbGl6ZSgpO1xuICAgICAgY2FsZW5kYXIub24oJ2FwcGx5Jywgb25BcHBseUNhbGVuZGFyKTtcbiAgICAgIGNhbGVuZGFyLiRlbC5maW5kKCdzZWxlY3QnKS5vbignY2hhbmdlJywgYWpheEdldENhbGVuZGFyLmJpbmQobnVsbCwgY2FsZW5kYXIpKTtcbiAgICB9KTtcbiAgfTtcblxuICAkKCcuYWJrbmdjYWwuYWJrbmdjYWwtLXllYXJseScsIGRvY3VtZW50KS5lYWNoKGZ1bmN0aW9uKGluZGV4LCBlbCkge1xuICAgIGxldCBjYWxlbmRhciA9IG5ldyB3aW5kb3cuQXdlQm9va2luZ1llYXJseUNhbGVuZGFyKGVsKTtcbiAgICAkKGVsKS5kYXRhKCdhdmFpbGFiaWxpdHktY2FsZW5kYXInLCBjYWxlbmRhcik7XG5cbiAgICBjYWxlbmRhci5vbignYXBwbHknLCBvbkFwcGx5Q2FsZW5kYXIpO1xuICAgIGNhbGVuZGFyLiRlbC5maW5kKCdzZWxlY3QnKS5vbignY2hhbmdlJywgYWpheEdldENhbGVuZGFyLmJpbmQobnVsbCwgY2FsZW5kYXIpKTtcbiAgfSk7XG5cbiAgJGRpYWxvZy5maW5kKCdmb3JtJykub24oJ3N1Ym1pdCcsIGZ1bmN0aW9uKGUpIHtcbiAgICBlLnByZXZlbnREZWZhdWx0KCk7XG5cbiAgICBjb25zdCAkZWwgPSAkKHRoaXMpO1xuICAgIGNvbnN0IGNhbGVuZGFyID0gJGVsLmRhdGEoJ2NhbGVuZGFyJyk7XG5cbiAgICAkZWwuYWRkQ2xhc3MoJ2FqYXgtbG9hZGluZycpO1xuICAgIEF3ZUJvb2tpbmcuYWpheFN1Ym1pdCh0aGlzLCAnc2V0X2F3ZWJvb2tpbmdfYXZhaWxhYmlsaXR5JylcbiAgICAgIC5hbHdheXMoZnVuY3Rpb24oKSB7XG4gICAgICAgICRlbC5yZW1vdmVDbGFzcygnYWpheC1sb2FkaW5nJyk7XG4gICAgICB9KVxuICAgICAgLmRvbmUoZnVuY3Rpb24oZGF0YSkge1xuICAgICAgICBhamF4R2V0Q2FsZW5kYXIoY2FsZW5kYXIpO1xuICAgICAgICAkZGlhbG9nLmRpYWxvZygnY2xvc2UnKTtcbiAgICAgIH0pXG4gICAgICAuZmFpbChmdW5jdGlvbihlKSB7XG4gICAgICAgIGlmIChlLmVycm9yICYmIHR5cGVvZiBlLmVycm9yID09PSAnc3RyaW5nJykge1xuICAgICAgICAgIGFsZXJ0KGUuZXJyb3IpO1xuICAgICAgICB9XG4gICAgICB9KTtcbiAgfSk7XG5cbiAgcmFuZ2VwaWNrZXIuaW5pdCgpO1xufSk7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gLi9hc3NldHMvanNzcmMvYWRtaW4vbWFuYWdlci1hdmFpbGFiaWxpdHkuanMiXSwic291cmNlUm9vdCI6IiJ9