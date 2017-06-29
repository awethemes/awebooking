(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
'use strict';

(function ($) {
  'use strict';

  if (!window.Skeleton) {
    throw new Error('Skeleton not found!');
  }

  // var IconpackCollection = Backbone.Collection.extend({
  //   initialize: function( models ) {
  //     this.items = new Backbone.Collection( models );

  //     this.props = new Backbone.Model({
  //       group:  'all',
  //       search: ''
  //     });

  //     this.props.on( 'change', this.refresh, this );
  //   },

  //   /**
  //    * Refresh library when props is changed
  //    *
  //    * @param {Backbone.Model} props
  //    */
  //   refresh: function( props ) {
  //     var library = this,
  //         items   = this.items.toJSON();

  //     _.each( props.toJSON(), function( value, filter ) {
  //       if ( library.filters[ filter ] ) {
  //         items = _.filter( items, _.bind( library.filters[ filter ], this ), value );
  //       }
  //     }, this );

  //     this.reset( items );
  //   },
  //   filters: {
  //     /**
  //      * @static
  //      * @param {object} item
  //      *
  //      * @this wp.media.model.IconPickerFonts
  //      *
  //      * @returns {Boolean}
  //      */
  //     group: function( item ) {
  //       var groupId = this.props.get( 'group' );

  //       return ( 'all' === groupId || item.group === groupId || '' === item.group );
  //     },

  //     /**
  //      * @static
  //      * @param {object} item
  //      *
  //      * @this wp.media.model.IconPickerFonts
  //      *
  //      * @returns {Boolean}
  //      */
  //     search: function( item ) {
  //       var term = this.props.get( 'search' ),
  //           result;

  //       if ( '' === term ) {
  //         result = true;
  //       } else {
  //         result = _.any( [ 'id', 'name' ], function( key ) {
  //           var value = item[ key ];

  //           return value && -1 !== value.search( this );
  //         }, term );
  //       }

  //       return result;
  //     }
  //   }
  // });

  // var IconpackView = wp.Backbone.View.extend({
  //   template: wp.template( 'iconpack-template' ),
  // });

  // $(function() {
  //   var a = new IconpackView({
  //     el: '#skeleton-icons',
  //     collection: new IconpackCollection(window.skeletonIcons),
  //   });

  //   a.render();
  // });
})(jQuery);

},{}]},{},[1])

//# sourceMappingURL=skeleton-iconpack.js.map
