/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 14);
/******/ })
/************************************************************************/
/******/ ({

/***/ 14:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(15);


/***/ }),

/***/ 15:
/***/ (function(module, exports) {

var $ = window.jQuery;
var awebooking = window.TheAweBooking;

$(function () {

  var rangepicker = new awebooking.RangeDatepicker('input[name="datepicker-start"]', 'input[name="datepicker-end"]');

  rangepicker.init();

  var showComments = function showComments() {
    var $text = this.$el.parent().find('.datepicker-container').find('.write-here');
    var nights = this.endDay.diff(this.startDay, 'days');
    var text = '';

    var thatNight = this.endDay.clone().subtract(1, 'day');

    if (nights === 1) {
      text = 'One night: ' + thatNight.format(this.format);
    } else {
      text = nights + ' nights, from  ' + this.startDay.format(this.format) + ' to ' + thatNight.format(this.format) + ' night.';
    }

    $text.html(text);

    this.$el.parent().find('input[name="state"]').prop('disabled', false);
    this.$el.parent().find('button').prop('disabled', false);
  };

  var ajaxGetCalendar = function ajaxGetCalendar(calendar) {
    var $container = calendar.$el.parents('.abkngcal-container');
    $container.find('.abkngcal-ajax-loading').show();

    $.ajax({
      url: ajaxurl,
      type: 'POST',
      data: {
        action: 'get_awebooking_yearly_calendar',
        year: $container.find('.abkngcal select').val(),
        room: $container.data('room')
      }
    }).done(function (response) {
      calendar.destroy();
      calendar.$el.find('select').off();
      calendar.$el.parent().find('button').off();

      $container.html($(response).html());
      calendar.$el = $container.find('.abkngcal.abkngcal--yearly');

      calendar.initialize();
      calendar.on('apply', showComments);
      calendar.$el.parent().find('button').on('click', ajaxSaveState.bind(calendar));
      calendar.$el.find('select').on('change', function () {
        ajaxGetCalendar(calendar);
      });
    });
  };

  var ajaxSaveState = function ajaxSaveState(e) {
    e.preventDefault();
    var calendar = this;
    var $container = this.$el.parents('.abkngcal-container');

    $.ajax({
      url: ajaxurl,
      type: 'POST',
      data: {
        action: 'set_awebooking_availability',
        start: this.startDay.format(this.format),
        end: this.endDay.format(this.format),
        room_id: $container.data('room'),
        state: this.$el.parent().find('input[name="state"]:checked').val()
      }
    }).done(function () {
      ajaxGetCalendar(calendar);
    });
  };

  $('.abkngcal.abkngcal--yearly', document).each(function (index, el) {
    var calendar = new window.AweBookingYearlyCalendar(el);
    var $button = calendar.$el.parent().find('button');

    calendar.on('apply', showComments);
    $button.on('click', ajaxSaveState.bind(calendar));

    calendar.$el.find('select').on('change', function () {
      ajaxGetCalendar(calendar);
    });

    $button.prop('disabled', true);
  });
});

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAgYTdlOTZhNWJiMzdkY2MzZTkxNmMiLCJ3ZWJwYWNrOi8vLy4vYXNzZXRzL2pzc3JjL2FkbWluL21hbmFnZXItYXZhaWxhYmlsaXR5LmpzIl0sIm5hbWVzIjpbIiQiLCJ3aW5kb3ciLCJqUXVlcnkiLCJhd2Vib29raW5nIiwiVGhlQXdlQm9va2luZyIsInJhbmdlcGlja2VyIiwiUmFuZ2VEYXRlcGlja2VyIiwiaW5pdCIsInNob3dDb21tZW50cyIsIiR0ZXh0IiwiJGVsIiwicGFyZW50IiwiZmluZCIsIm5pZ2h0cyIsImVuZERheSIsImRpZmYiLCJzdGFydERheSIsInRleHQiLCJ0aGF0TmlnaHQiLCJjbG9uZSIsInN1YnRyYWN0IiwiZm9ybWF0IiwiaHRtbCIsInByb3AiLCJhamF4R2V0Q2FsZW5kYXIiLCJjYWxlbmRhciIsIiRjb250YWluZXIiLCJwYXJlbnRzIiwic2hvdyIsImFqYXgiLCJ1cmwiLCJhamF4dXJsIiwidHlwZSIsImRhdGEiLCJhY3Rpb24iLCJ5ZWFyIiwidmFsIiwicm9vbSIsImRvbmUiLCJyZXNwb25zZSIsImRlc3Ryb3kiLCJvZmYiLCJpbml0aWFsaXplIiwib24iLCJhamF4U2F2ZVN0YXRlIiwiYmluZCIsImUiLCJwcmV2ZW50RGVmYXVsdCIsInN0YXJ0IiwiZW5kIiwicm9vbV9pZCIsInN0YXRlIiwiZG9jdW1lbnQiLCJlYWNoIiwiaW5kZXgiLCJlbCIsIkF3ZUJvb2tpbmdZZWFybHlDYWxlbmRhciIsIiRidXR0b24iXSwibWFwcGluZ3MiOiI7QUFBQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTs7O0FBR0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsYUFBSztBQUNMO0FBQ0E7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RSxVQUFVaEMsU0FBU2YsR0FBVCxDQUFhQyxNQUFiLEdBQXNCQyxJQUF0QixDQUEyQixRQUEzQixDQUFkOztBQUVBYSxhQUFTa0IsRUFBVCxDQUFZLE9BQVosRUFBcUJuQyxZQUFyQjtBQUNBaUQsWUFBUWQsRUFBUixDQUFXLE9BQVgsRUFBb0JDLGNBQWNDLElBQWQsQ0FBbUJwQixRQUFuQixDQUFwQjs7QUFFQUEsYUFBU2YsR0FBVCxDQUFhRSxJQUFiLENBQWtCLFFBQWxCLEVBQTRCK0IsRUFBNUIsQ0FBK0IsUUFBL0IsRUFBeUMsWUFBVztBQUNsRG5CLHNCQUFnQkMsUUFBaEI7QUFDRCxLQUZEOztBQUlBZ0MsWUFBUWxDLElBQVIsQ0FBYSxVQUFiLEVBQXlCLElBQXpCO0FBQ0QsR0FaRDtBQWNELENBOUZELEUiLCJmaWxlIjoiL2pzL2FkbWluL21hbmFnZXItYXZhaWxhYmlsaXR5LmpzIiwic291cmNlc0NvbnRlbnQiOlsiIFx0Ly8gVGhlIG1vZHVsZSBjYWNoZVxuIFx0dmFyIGluc3RhbGxlZE1vZHVsZXMgPSB7fTtcblxuIFx0Ly8gVGhlIHJlcXVpcmUgZnVuY3Rpb25cbiBcdGZ1bmN0aW9uIF9fd2VicGFja19yZXF1aXJlX18obW9kdWxlSWQpIHtcblxuIFx0XHQvLyBDaGVjayBpZiBtb2R1bGUgaXMgaW4gY2FjaGVcbiBcdFx0aWYoaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0pIHtcbiBcdFx0XHRyZXR1cm4gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0uZXhwb3J0cztcbiBcdFx0fVxuIFx0XHQvLyBDcmVhdGUgYSBuZXcgbW9kdWxlIChhbmQgcHV0IGl0IGludG8gdGhlIGNhY2hlKVxuIFx0XHR2YXIgbW9kdWxlID0gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0gPSB7XG4gXHRcdFx0aTogbW9kdWxlSWQsXG4gXHRcdFx0bDogZmFsc2UsXG4gXHRcdFx0ZXhwb3J0czoge31cbiBcdFx0fTtcblxuIFx0XHQvLyBFeGVjdXRlIHRoZSBtb2R1bGUgZnVuY3Rpb25cbiBcdFx0bW9kdWxlc1ttb2R1bGVJZF0uY2FsbChtb2R1bGUuZXhwb3J0cywgbW9kdWxlLCBtb2R1bGUuZXhwb3J0cywgX193ZWJwYWNrX3JlcXVpcmVfXyk7XG5cbiBcdFx0Ly8gRmxhZyB0aGUgbW9kdWxlIGFzIGxvYWRlZFxuIFx0XHRtb2R1bGUubCA9IHRydWU7XG5cbiBcdFx0Ly8gUmV0dXJuIHRoZSBleHBvcnRzIG9mIHRoZSBtb2R1bGVcbiBcdFx0cmV0dXJuIG1vZHVsZS5leHBvcnRzO1xuIFx0fVxuXG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlcyBvYmplY3QgKF9fd2VicGFja19tb2R1bGVzX18pXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm0gPSBtb2R1bGVzO1xuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZSBjYWNoZVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5jID0gaW5zdGFsbGVkTW9kdWxlcztcblxuIFx0Ly8gZGVmaW5lIGdldHRlciBmdW5jdGlvbiBmb3IgaGFybW9ueSBleHBvcnRzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQgPSBmdW5jdGlvbihleHBvcnRzLCBuYW1lLCBnZXR0ZXIpIHtcbiBcdFx0aWYoIV9fd2VicGFja19yZXF1aXJlX18ubyhleHBvcnRzLCBuYW1lKSkge1xuIFx0XHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBuYW1lLCB7XG4gXHRcdFx0XHRjb25maWd1cmFibGU6IGZhbHNlLFxuIFx0XHRcdFx0ZW51bWVyYWJsZTogdHJ1ZSxcbiBcdFx0XHRcdGdldDogZ2V0dGVyXG4gXHRcdFx0fSk7XG4gXHRcdH1cbiBcdH07XG5cbiBcdC8vIGdldERlZmF1bHRFeHBvcnQgZnVuY3Rpb24gZm9yIGNvbXBhdGliaWxpdHkgd2l0aCBub24taGFybW9ueSBtb2R1bGVzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm4gPSBmdW5jdGlvbihtb2R1bGUpIHtcbiBcdFx0dmFyIGdldHRlciA9IG1vZHVsZSAmJiBtb2R1bGUuX19lc01vZHVsZSA/XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0RGVmYXVsdCgpIHsgcmV0dXJuIG1vZHVsZVsnZGVmYXVsdCddOyB9IDpcbiBcdFx0XHRmdW5jdGlvbiBnZXRNb2R1bGVFeHBvcnRzKCkgeyByZXR1cm4gbW9kdWxlOyB9O1xuIFx0XHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQoZ2V0dGVyLCAnYScsIGdldHRlcik7XG4gXHRcdHJldHVybiBnZXR0ZXI7XG4gXHR9O1xuXG4gXHQvLyBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGxcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubyA9IGZ1bmN0aW9uKG9iamVjdCwgcHJvcGVydHkpIHsgcmV0dXJuIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbChvYmplY3QsIHByb3BlcnR5KTsgfTtcblxuIFx0Ly8gX193ZWJwYWNrX3B1YmxpY19wYXRoX19cbiBcdF9fd2VicGFja19yZXF1aXJlX18ucCA9IFwiXCI7XG5cbiBcdC8vIExvYWQgZW50cnkgbW9kdWxlIGFuZCByZXR1cm4gZXhwb3J0c1xuIFx0cmV0dXJuIF9fd2VicGFja19yZXF1aXJlX18oX193ZWJwYWNrX3JlcXVpcmVfXy5zID0gMTQpO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIHdlYnBhY2svYm9vdHN0cmFwIGE3ZTk2YTViYjM3ZGNjM2U5MTZjIiwiY29uc3QgJCA9IHdpbmRvdy5qUXVlcnk7XG5jb25zdCBhd2Vib29raW5nID0gd2luZG93LlRoZUF3ZUJvb2tpbmc7XG5cbiQoZnVuY3Rpb24oKSB7XG5cbiAgY29uc3QgcmFuZ2VwaWNrZXIgPSBuZXcgYXdlYm9va2luZy5SYW5nZURhdGVwaWNrZXIoXG4gICAgJ2lucHV0W25hbWU9XCJkYXRlcGlja2VyLXN0YXJ0XCJdJyxcbiAgICAnaW5wdXRbbmFtZT1cImRhdGVwaWNrZXItZW5kXCJdJ1xuICApO1xuXG4gIHJhbmdlcGlja2VyLmluaXQoKTtcblxuICBjb25zdCBzaG93Q29tbWVudHMgPSBmdW5jdGlvbigpIHtcbiAgICB2YXIgJHRleHQgPSB0aGlzLiRlbC5wYXJlbnQoKS5maW5kKCcuZGF0ZXBpY2tlci1jb250YWluZXInKS5maW5kKCcud3JpdGUtaGVyZScpO1xuICAgIHZhciBuaWdodHMgPSB0aGlzLmVuZERheS5kaWZmKHRoaXMuc3RhcnREYXksICdkYXlzJyk7XG4gICAgdmFyIHRleHQgPSAnJztcblxuICAgIHZhciB0aGF0TmlnaHQgPSB0aGlzLmVuZERheS5jbG9uZSgpLnN1YnRyYWN0KDEsICdkYXknKTtcblxuICAgIGlmIChuaWdodHMgPT09IDEpIHtcbiAgICAgIHRleHQgPSAnT25lIG5pZ2h0OiAnICsgdGhhdE5pZ2h0LmZvcm1hdCh0aGlzLmZvcm1hdCk7XG4gICAgfSBlbHNlIHtcbiAgICAgIHRleHQgPSBuaWdodHMgKyAnIG5pZ2h0cywgZnJvbSAgJyArIHRoaXMuc3RhcnREYXkuZm9ybWF0KHRoaXMuZm9ybWF0KSArICcgdG8gJyArIHRoYXROaWdodC5mb3JtYXQodGhpcy5mb3JtYXQpICsgJyBuaWdodC4nO1xuICAgIH1cblxuICAgICR0ZXh0Lmh0bWwodGV4dCk7XG5cbiAgICB0aGlzLiRlbC5wYXJlbnQoKS5maW5kKCdpbnB1dFtuYW1lPVwic3RhdGVcIl0nKS5wcm9wKCdkaXNhYmxlZCcsIGZhbHNlKTtcbiAgICB0aGlzLiRlbC5wYXJlbnQoKS5maW5kKCdidXR0b24nKS5wcm9wKCdkaXNhYmxlZCcsIGZhbHNlKTtcbiAgfTtcblxuICBjb25zdCBhamF4R2V0Q2FsZW5kYXIgPSBmdW5jdGlvbihjYWxlbmRhcikge1xuICAgIHZhciAkY29udGFpbmVyID0gY2FsZW5kYXIuJGVsLnBhcmVudHMoJy5hYmtuZ2NhbC1jb250YWluZXInKTtcbiAgICAkY29udGFpbmVyLmZpbmQoJy5hYmtuZ2NhbC1hamF4LWxvYWRpbmcnKS5zaG93KCk7XG5cbiAgICAkLmFqYXgoe1xuICAgICAgdXJsOiBhamF4dXJsLFxuICAgICAgdHlwZTogJ1BPU1QnLFxuICAgICAgZGF0YToge1xuICAgICAgICBhY3Rpb246ICdnZXRfYXdlYm9va2luZ195ZWFybHlfY2FsZW5kYXInLFxuICAgICAgICB5ZWFyOiAkY29udGFpbmVyLmZpbmQoJy5hYmtuZ2NhbCBzZWxlY3QnKS52YWwoKSxcbiAgICAgICAgcm9vbTogJGNvbnRhaW5lci5kYXRhKCdyb29tJyksXG4gICAgICB9XG4gICAgfSlcbiAgICAuZG9uZShmdW5jdGlvbihyZXNwb25zZSkge1xuICAgICAgY2FsZW5kYXIuZGVzdHJveSgpO1xuICAgICAgY2FsZW5kYXIuJGVsLmZpbmQoJ3NlbGVjdCcpLm9mZigpO1xuICAgICAgY2FsZW5kYXIuJGVsLnBhcmVudCgpLmZpbmQoJ2J1dHRvbicpLm9mZigpO1xuXG4gICAgICAkY29udGFpbmVyLmh0bWwoJChyZXNwb25zZSkuaHRtbCgpKTtcbiAgICAgIGNhbGVuZGFyLiRlbCA9ICRjb250YWluZXIuZmluZCgnLmFia25nY2FsLmFia25nY2FsLS15ZWFybHknKTtcblxuICAgICAgY2FsZW5kYXIuaW5pdGlhbGl6ZSgpO1xuICAgICAgY2FsZW5kYXIub24oJ2FwcGx5Jywgc2hvd0NvbW1lbnRzKTtcbiAgICAgIGNhbGVuZGFyLiRlbC5wYXJlbnQoKS5maW5kKCdidXR0b24nKS5vbignY2xpY2snLCBhamF4U2F2ZVN0YXRlLmJpbmQoY2FsZW5kYXIpKTtcbiAgICAgIGNhbGVuZGFyLiRlbC5maW5kKCdzZWxlY3QnKS5vbignY2hhbmdlJywgZnVuY3Rpb24oKSB7XG4gICAgICAgIGFqYXhHZXRDYWxlbmRhcihjYWxlbmRhcik7XG4gICAgICB9KTtcblxuICAgIH0pO1xuICB9O1xuXG4gIGNvbnN0IGFqYXhTYXZlU3RhdGUgPSBmdW5jdGlvbihlKSB7XG4gICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgIHZhciBjYWxlbmRhciA9IHRoaXM7XG4gICAgdmFyICRjb250YWluZXIgPSB0aGlzLiRlbC5wYXJlbnRzKCcuYWJrbmdjYWwtY29udGFpbmVyJyk7XG5cbiAgICAkLmFqYXgoe1xuICAgICAgdXJsOiBhamF4dXJsLFxuICAgICAgdHlwZTogJ1BPU1QnLFxuICAgICAgZGF0YToge1xuICAgICAgICBhY3Rpb246ICdzZXRfYXdlYm9va2luZ19hdmFpbGFiaWxpdHknLFxuICAgICAgICBzdGFydDogdGhpcy5zdGFydERheS5mb3JtYXQodGhpcy5mb3JtYXQpLFxuICAgICAgICBlbmQ6IHRoaXMuZW5kRGF5LmZvcm1hdCh0aGlzLmZvcm1hdCksXG4gICAgICAgIHJvb21faWQ6ICRjb250YWluZXIuZGF0YSgncm9vbScpLFxuICAgICAgICBzdGF0ZTogdGhpcy4kZWwucGFyZW50KCkuZmluZCgnaW5wdXRbbmFtZT1cInN0YXRlXCJdOmNoZWNrZWQnKS52YWwoKSxcbiAgICAgIH0sXG4gICAgfSlcbiAgICAuZG9uZShmdW5jdGlvbigpIHtcbiAgICAgIGFqYXhHZXRDYWxlbmRhcihjYWxlbmRhcik7XG4gICAgfSk7XG4gIH07XG5cbiAgJCgnLmFia25nY2FsLmFia25nY2FsLS15ZWFybHknLCBkb2N1bWVudCkuZWFjaChmdW5jdGlvbihpbmRleCwgZWwpIHtcbiAgICBsZXQgY2FsZW5kYXIgPSBuZXcgd2luZG93LkF3ZUJvb2tpbmdZZWFybHlDYWxlbmRhcihlbCk7XG4gICAgbGV0ICRidXR0b24gPSBjYWxlbmRhci4kZWwucGFyZW50KCkuZmluZCgnYnV0dG9uJyk7XG5cbiAgICBjYWxlbmRhci5vbignYXBwbHknLCBzaG93Q29tbWVudHMpO1xuICAgICRidXR0b24ub24oJ2NsaWNrJywgYWpheFNhdmVTdGF0ZS5iaW5kKGNhbGVuZGFyKSk7XG5cbiAgICBjYWxlbmRhci4kZWwuZmluZCgnc2VsZWN0Jykub24oJ2NoYW5nZScsIGZ1bmN0aW9uKCkge1xuICAgICAgYWpheEdldENhbGVuZGFyKGNhbGVuZGFyKTtcbiAgICB9KTtcblxuICAgICRidXR0b24ucHJvcCgnZGlzYWJsZWQnLCB0cnVlKTtcbiAgfSk7XG5cbn0pO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIC4vYXNzZXRzL2pzc3JjL2FkbWluL21hbmFnZXItYXZhaWxhYmlsaXR5LmpzIl0sInNvdXJjZVJvb3QiOiIifQ==