const $ = jQuery;
const plugin = window.awebooking;

const ajaxSearch = function(type = 'customers', query, callback) {
  $.ajax({
    type: 'GET',
    url: plugin.route( `/search/${type}` ),
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
        ajaxSearch('customers', query, callback);
      }
    },
  });
}

const initSelectizeServices = function(select) {
  $(select).selectize({
    valueField: 'id',
    labelField: 'name',
    searchField: 'name',
    dropdownParent: 'body',
    placeholder: $(this).data('placeholder'),
    load: function(query, callback) {
      if (! query.length) {
        return callback();
      } else {
        ajaxSearch('services', query, callback);
      }
    },
    // render: {
    //   item: function(item, escape) {
    //     return '<div>' +
    //         (item.name ? '<span class="name">' + escape(item.name) + '</span>' : '') +
    //     '</div>';
    //   },
    //   option: function(item, escape) {
    //     var label = item.label ? item.label : '';
    //     return '<div>' + label + '</div>';
    //   }
    // },
  });
}

module.exports = function() {
  $('select.awebooking-search-customer, .selectize-search-customer .cmb2_select' ).each(function() {
    initSelectize(this);
  });

  $('.selectize-search-services' ).each(function() {
    initSelectizeServices(this);
  });
};
