webpackJsonp([3],{

/***/ 19:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(20);


/***/ }),

/***/ 20:
/***/ (function(module, exports) {

var $ = window.jQuery;
var awebooking = window.TheAweBooking;

$(function () {
  'use strict';

  var $dialog = awebooking.Popup.setup('#awebooking-set-price-popup');

  var rangepicker = new awebooking.RangeDatepicker('input[name="datepicker-start"]', 'input[name="datepicker-end"]');

  var showComments = function showComments(calendar) {
    var nights = calendar.getNights();

    var text = '';
    var toNight = calendar.endDay;

    if (nights === 1) {
      text = 'One night: ' + toNight.format(calendar.format);
    } else {
      text = '<b>' + nights + '</b> nights' + ' nights, from <b>' + calendar.startDay.format(calendar.format) + '</b> to <b>' + toNight.format(calendar.format) + '</b>';
    }

    return text;
  };

  var onApplyCalendar = function onApplyCalendar() {
    var calendar = this;
    calendar.room_type = this.$el.closest('.abkngcal-container').find('h2').text();
    calendar.data_id = this.$el.closest('[data-unit]').data('unit');
    calendar.comments = showComments(calendar);

    var formTemplate = wp.template('pricing-calendar-form');
    $dialog.find('.awebooking-dialog-contents').html(formTemplate(calendar));

    $dialog.dialog('open');
  };

  $('.abkngcal--pricing-calendar', document).each(function (index, el) {
    var calendar = new PricingCalendar(el);
    $(el).data('pricing-calendar', calendar);

    calendar.on('apply', onApplyCalendar);
  });

  rangepicker.init();
});

/***/ })

},[19]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9hc3NldHMvanNzcmMvYWRtaW4vbWFuYWdlci1wcmljaW5nLmpzIl0sIm5hbWVzIjpbIiQiLCJ3aW5kb3ciLCJqUXVlcnkiLCJhd2Vib29raW5nIiwiVGhlQXdlQm9va2luZyIsIiRkaWFsb2ciLCJQb3B1cCIsInNldHVwIiwicmFuZ2VwaWNrZXIiLCJSYW5nZURhdGVwaWNrZXIiLCJzaG93Q29tbWVudHMiLCJjYWxlbmRhciIsIm5pZ2h0cyIsImdldE5pZ2h0cyIsInRleHQiLCJ0b05pZ2h0IiwiZW5kRGF5IiwiZm9ybWF0Iiwic3RhcnREYXkiLCJvbkFwcGx5Q2FsZW5kYXIiLCJyb29tX3R5cGUiLCIkZWwiLCJjbG9zZXN0IiwiZmluZCIsImRhdGFfaWQiLCJkYXRhIiwiY29tbWVudHMiLCJmb3JtVGVtcGxhdGUiLCJ3cCIsInRlbXBsYXRlIiwiaHRtbCIsImRpYWxvZyIsImRvY3VtZW50IiwiZWFjaCIsImluZGV4IiwiZWwiLCJQcmljaW5nQ2FsZW5kYXIiLCJvbiIsImluaXQiXSwibWFwcGluZ3MiOiI7Ozs7Ozs7Ozs7Ozs7QUFBQSxJQUFNQSxJQUFJQyxPQUFPQyxNQUFqQjtBQUNBLElBQU1DLGFBQWFGLE9BQU9HLGFBQTFCOztBQUVBSixFQUFFLFlBQVc7QUFDWDs7QUFFQSxNQUFNSyxVQUFVRixXQUFXRyxLQUFYLENBQWlCQyxLQUFqQixDQUF1Qiw2QkFBdkIsQ0FBaEI7O0FBRUEsTUFBTUMsY0FBYyxJQUFJTCxXQUFXTSxlQUFmLENBQ2xCLGdDQURrQixFQUVsQiw4QkFGa0IsQ0FBcEI7O0FBS0EsTUFBTUMsZUFBZSxTQUFmQSxZQUFlLENBQVNDLFFBQVQsRUFBbUI7QUFDdEMsUUFBSUMsU0FBU0QsU0FBU0UsU0FBVCxFQUFiOztBQUVBLFFBQUlDLE9BQU8sRUFBWDtBQUNBLFFBQUlDLFVBQVVKLFNBQVNLLE1BQXZCOztBQUVBLFFBQUlKLFdBQVcsQ0FBZixFQUFrQjtBQUNoQkUsYUFBTyxnQkFBZ0JDLFFBQVFFLE1BQVIsQ0FBZU4sU0FBU00sTUFBeEIsQ0FBdkI7QUFDRCxLQUZELE1BRU87QUFDTEgsYUFBTyxRQUFNRixNQUFOLG1CQUE0QixtQkFBNUIsR0FBa0RELFNBQVNPLFFBQVQsQ0FBa0JELE1BQWxCLENBQXlCTixTQUFTTSxNQUFsQyxDQUFsRCxHQUE4RixhQUE5RixHQUE4R0YsUUFBUUUsTUFBUixDQUFlTixTQUFTTSxNQUF4QixDQUE5RyxHQUFnSixNQUF2SjtBQUNEOztBQUVELFdBQU9ILElBQVA7QUFDRCxHQWJEOztBQWVBLE1BQU1LLGtCQUFrQixTQUFsQkEsZUFBa0IsR0FBVztBQUNqQyxRQUFNUixXQUFXLElBQWpCO0FBQ0FBLGFBQVNTLFNBQVQsR0FBcUIsS0FBS0MsR0FBTCxDQUFTQyxPQUFULENBQWlCLHFCQUFqQixFQUF3Q0MsSUFBeEMsQ0FBNkMsSUFBN0MsRUFBbURULElBQW5ELEVBQXJCO0FBQ0FILGFBQVNhLE9BQVQsR0FBcUIsS0FBS0gsR0FBTCxDQUFTQyxPQUFULENBQWlCLGFBQWpCLEVBQWdDRyxJQUFoQyxDQUFxQyxNQUFyQyxDQUFyQjtBQUNBZCxhQUFTZSxRQUFULEdBQXFCaEIsYUFBYUMsUUFBYixDQUFyQjs7QUFFQSxRQUFNZ0IsZUFBZUMsR0FBR0MsUUFBSCxDQUFZLHVCQUFaLENBQXJCO0FBQ0F4QixZQUFRa0IsSUFBUixDQUFhLDZCQUFiLEVBQTRDTyxJQUE1QyxDQUFpREgsYUFBYWhCLFFBQWIsQ0FBakQ7O0FBRUFOLFlBQVEwQixNQUFSLENBQWUsTUFBZjtBQUNELEdBVkQ7O0FBWUEvQixJQUFFLDZCQUFGLEVBQWlDZ0MsUUFBakMsRUFBMkNDLElBQTNDLENBQWdELFVBQVNDLEtBQVQsRUFBZ0JDLEVBQWhCLEVBQW9CO0FBQ2xFLFFBQUl4QixXQUFXLElBQUl5QixlQUFKLENBQW9CRCxFQUFwQixDQUFmO0FBQ0FuQyxNQUFFbUMsRUFBRixFQUFNVixJQUFOLENBQVcsa0JBQVgsRUFBK0JkLFFBQS9COztBQUVBQSxhQUFTMEIsRUFBVCxDQUFZLE9BQVosRUFBcUJsQixlQUFyQjtBQUNELEdBTEQ7O0FBT0FYLGNBQVk4QixJQUFaO0FBRUQsQ0E5Q0QsRSIsImZpbGUiOiIvanMvYWRtaW4vbWFuYWdlci1wcmljaW5nLmpzIiwic291cmNlc0NvbnRlbnQiOlsiY29uc3QgJCA9IHdpbmRvdy5qUXVlcnk7XG5jb25zdCBhd2Vib29raW5nID0gd2luZG93LlRoZUF3ZUJvb2tpbmc7XG5cbiQoZnVuY3Rpb24oKSB7XG4gICd1c2Ugc3RyaWN0JztcblxuICBjb25zdCAkZGlhbG9nID0gYXdlYm9va2luZy5Qb3B1cC5zZXR1cCgnI2F3ZWJvb2tpbmctc2V0LXByaWNlLXBvcHVwJyk7XG5cbiAgY29uc3QgcmFuZ2VwaWNrZXIgPSBuZXcgYXdlYm9va2luZy5SYW5nZURhdGVwaWNrZXIoXG4gICAgJ2lucHV0W25hbWU9XCJkYXRlcGlja2VyLXN0YXJ0XCJdJyxcbiAgICAnaW5wdXRbbmFtZT1cImRhdGVwaWNrZXItZW5kXCJdJ1xuICApO1xuXG4gIGNvbnN0IHNob3dDb21tZW50cyA9IGZ1bmN0aW9uKGNhbGVuZGFyKSB7XG4gICAgdmFyIG5pZ2h0cyA9IGNhbGVuZGFyLmdldE5pZ2h0cygpO1xuXG4gICAgdmFyIHRleHQgPSAnJztcbiAgICB2YXIgdG9OaWdodCA9IGNhbGVuZGFyLmVuZERheTtcblxuICAgIGlmIChuaWdodHMgPT09IDEpIHtcbiAgICAgIHRleHQgPSAnT25lIG5pZ2h0OiAnICsgdG9OaWdodC5mb3JtYXQoY2FsZW5kYXIuZm9ybWF0KTtcbiAgICB9IGVsc2Uge1xuICAgICAgdGV4dCA9IGA8Yj4ke25pZ2h0c308L2I+IG5pZ2h0c2AgKyAnIG5pZ2h0cywgZnJvbSA8Yj4nICsgY2FsZW5kYXIuc3RhcnREYXkuZm9ybWF0KGNhbGVuZGFyLmZvcm1hdCkgKyAnPC9iPiB0byA8Yj4nICsgdG9OaWdodC5mb3JtYXQoY2FsZW5kYXIuZm9ybWF0KSArICc8L2I+JztcbiAgICB9XG5cbiAgICByZXR1cm4gdGV4dDtcbiAgfTtcblxuICBjb25zdCBvbkFwcGx5Q2FsZW5kYXIgPSBmdW5jdGlvbigpIHtcbiAgICBjb25zdCBjYWxlbmRhciA9IHRoaXM7XG4gICAgY2FsZW5kYXIucm9vbV90eXBlID0gdGhpcy4kZWwuY2xvc2VzdCgnLmFia25nY2FsLWNvbnRhaW5lcicpLmZpbmQoJ2gyJykudGV4dCgpO1xuICAgIGNhbGVuZGFyLmRhdGFfaWQgICA9IHRoaXMuJGVsLmNsb3Nlc3QoJ1tkYXRhLXVuaXRdJykuZGF0YSgndW5pdCcpO1xuICAgIGNhbGVuZGFyLmNvbW1lbnRzICA9IHNob3dDb21tZW50cyhjYWxlbmRhcik7XG5cbiAgICBjb25zdCBmb3JtVGVtcGxhdGUgPSB3cC50ZW1wbGF0ZSgncHJpY2luZy1jYWxlbmRhci1mb3JtJyk7XG4gICAgJGRpYWxvZy5maW5kKCcuYXdlYm9va2luZy1kaWFsb2ctY29udGVudHMnKS5odG1sKGZvcm1UZW1wbGF0ZShjYWxlbmRhcikpO1xuXG4gICAgJGRpYWxvZy5kaWFsb2coJ29wZW4nKTtcbiAgfTtcblxuICAkKCcuYWJrbmdjYWwtLXByaWNpbmctY2FsZW5kYXInLCBkb2N1bWVudCkuZWFjaChmdW5jdGlvbihpbmRleCwgZWwpIHtcbiAgICBsZXQgY2FsZW5kYXIgPSBuZXcgUHJpY2luZ0NhbGVuZGFyKGVsKTtcbiAgICAkKGVsKS5kYXRhKCdwcmljaW5nLWNhbGVuZGFyJywgY2FsZW5kYXIpO1xuXG4gICAgY2FsZW5kYXIub24oJ2FwcGx5Jywgb25BcHBseUNhbGVuZGFyKTtcbiAgfSk7XG5cbiAgcmFuZ2VwaWNrZXIuaW5pdCgpO1xuXG59KTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAuL2Fzc2V0cy9qc3NyYy9hZG1pbi9tYW5hZ2VyLXByaWNpbmcuanMiXSwic291cmNlUm9vdCI6IiJ9