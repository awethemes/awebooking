(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
'use strict';

var $ = jQuery;
var plugin = window.awebooking;

var ajaxSearch = function ajaxSearch(query, callback) {
  $.ajax({
    type: 'GET',
    url: plugin.route('/search/customers'),
    data: { term: encodeURIComponent(query) },
    error: function error() {
      callback();
    },
    success: function success(res) {
      callback(res);
    }
  });
};

var initSelectize = function initSelectize(select) {
  $(select).selectize({
    valueField: 'id',
    labelField: 'display',
    searchField: 'display',
    dropdownParent: 'body',
    placeholder: $(this).data('placeholder'),
    load: function load(query, callback) {
      if (!query.length) {
        return callback();
      } else {
        ajaxSearch(query, callback);
      }
    }
  });
};

module.exports = function () {
  $('select.awebooking-search-customer, .selectize-search-customer .cmb2_select').each(function () {
    initSelectize(this);
  });
};

},{}]},{},[1]);

//# sourceMappingURL=search-customer.js.map
