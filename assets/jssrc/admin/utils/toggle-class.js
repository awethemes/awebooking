var ToggleClass = function($el) {

  $(document)
  .on('click', $el, function(e) {
     e.preventDefault();
     $(this).parent().toggleClass('open');
  })
  .on('click', function() {

  });

};

module.exports = ToggleClass;
