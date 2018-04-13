(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
'use strict';

(function ($) {
  'use strict';

  var awebooking = window.awebooking || {};

  /**
   * Scroll to first checked category.
   *
   * @link https://github.com/scribu/wp-category-checklist-tree/blob/master/category-checklist-tree.php
   */
  $(function () {
    $('[id$="-all"] > ul.categorychecklist').each(function () {
      var $list = $(this);
      var $firstChecked = $list.find(':checked').first();

      if (!$firstChecked.length) {
        return;
      }

      var posFirst = $list.find('input').position().top;
      var posChecked = $firstChecked.position().top;

      $list.closest('.tabs-panel').scrollTop(posChecked - posFirst + 5);
    });
  });

  $(function () {
    var $metabox = $('#awebooking-room-type-data');

    // Handle tabs.
    $metabox.on('click', '.awebooking-tabs a', function (e) {
      e.preventDefault();

      $('.awebooking-tabs > li.active', $metabox).removeClass('active');
      $(this).parent().addClass('active');

      $('.awebooking-tabs-panels > div', $metabox).hide();
      $($(this).attr('href'), $metabox).show();
    });

    $('.awebooking-tabs > li:first > a', $metabox).trigger('click');
  });
})(jQuery);

},{}]},{},[1]);

//# sourceMappingURL=edit-room-type.js.map
