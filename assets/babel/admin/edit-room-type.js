(function($) {
  'use strict';

  const awebooking = window.awebooking || {};

  /**
   * Scroll to first checked category.
   *
   * @link https://github.com/scribu/wp-category-checklist-tree/blob/master/category-checklist-tree.php
   */
  $(function() {
    $('[id$="-all"] > ul.categorychecklist').each(function() {
      const $list = $(this);
      const $firstChecked = $list.find(':checked').first();

      if (! $firstChecked.length) {
        return;
      }

      const posFirst = $list.find('input').position().top;
      const posChecked = $firstChecked.position().top;

      $list.closest('.tabs-panel').scrollTop(posChecked - posFirst + 5);
    });
  });

  $(function() {
    const $metabox = $('#awebooking-room-type-data');

    // Handle tabs.
    $metabox.on('click', '.awebooking-tabs a', function(e) {
      e.preventDefault();

      $('.awebooking-tabs > li.active', $metabox).removeClass('active');
      $(this).parent().addClass('active');

      $('.awebooking-tabs-panels > div', $metabox).hide();
      $($(this).attr('href'), $metabox).show();
    });

    $('.awebooking-tabs > li:first > a', $metabox).trigger('click');
  });

})(jQuery);
