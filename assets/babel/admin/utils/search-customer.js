const $ = jQuery;
const plugin = window.awebooking;

const ajaxSearch = function(query, callback) {
  $.ajax({
    type: 'GET',
    url: plugin.route( '/search/customers' ),
    data: { term: encodeURIComponent(query) },
    error: function() { callback(); },
    success: function(res) { callback(res); }
  });
};

const initSelectize = function(select) {
  $(select).selectize({
    valueField: 'id',
    labelField: 'display',
    searchField: 'display',
    dropdownParent: 'body',
    placeholder: $(this).data('placeholder'),
    load: function(query, callback) {
      if (! query.length) {
        return callback();
      } else {
        ajaxSearch(query, callback);
      }
    },
  });
}

module.exports = function() {
  $('select.awebooking-search-customer, .selectize-search-customer .cmb2_select').each(function() {
    initSelectize(this);
  });
};
