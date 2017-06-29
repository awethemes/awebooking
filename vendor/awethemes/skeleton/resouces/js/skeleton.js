//=require vendor/deps.js

const Emitter = require('component-emitter');

window.Skeleton = window.Skeleton || {};

(function($, S) {
  'use strict';

  S.emitter = new Emitter;

  /**
   * Extendable Skeleton.
   *
   * @param  {Object} args
   * @return {void}
   */
  S.extend = function(args) { _.extend(this, args); };

  /**
   * Core methods.
   */
  S.extend({
    services: {},
    initialized: false,

    bind: function(key, cb) {
      this.services[key] = cb;
    },

    /**
     * Listen on the given `event` with `fn`.
     *
     * @param  {String} event
     * @param  {Function} fn
     * @return {Emitter}
     */
    on: function(event, fn) {
      return this.emitter.on(event, fn);
    },

    /**
     * Init Skelton.
     *
     * @return {void}
     */
    init: function() {
      this.emitter.emit('initialize', this);

      this.triggerTabs();

      this.initialized = true;
      this.emitter.emit('initialized', this);
    },

    triggerTabs() {
      var getStorageCurrentActive = function() {
          var currentActive = sessionStorage.getItem('cmb-current-active');

          try {
            currentActive = JSON.parse(currentActive);
          } catch(e) {
            sessionStorage.removeItem('cmb-current-active');
          }

          return currentActive || {};
      };

      var setActiveTab = function($current, $metabox, metaboxID) {
        // Do nothing if invalid metabox ID.
        if (! metaboxID) return;

        var targetID = $current.data('target');

        // Remove prev active from tab-pane and tab-li.
        $metabox.find('.cmb2-tab').removeClass('active');
        $metabox.find('.cmb2-tab-pane').removeClass('active');

        // Add active class to current.
        $(targetID).addClass('active');
        $current.closest('.cmb2-tab').addClass('active');

        // Add current active to sessionStorage
        if (window.sessionStorage) {
          var currentActive = getStorageCurrentActive();

          currentActive[metaboxID] = targetID;
          sessionStorage.setItem('cmb-current-active', JSON.stringify(currentActive));
        }
      };

      $('.cmb2-metabox').each(function() {
        var $metabox = $(this);
        var metaboxID = $metabox.attr('id').replace('cmb2-metabox-', '');

        // First, active via sessionStorage.
        if (window.sessionStorage) {
          var currentActive = getStorageCurrentActive();

          if (typeof currentActive[metaboxID] !== 'undefined') {
            var activeTarget = currentActive[metaboxID];
            setActiveTab($('[data-target="'+activeTarget+'"]', $metabox), $metabox, metaboxID);
          }
        }

        // Active tab when click on nav-link.
        $metabox.find('.cmb2-tab-link').on('click', function(e) {
          e.preventDefault();
          setActiveTab($(this), $metabox, metaboxID);
        });
      });
    }
  });

  S.deps = function () {
    var $context = $('.cmb2-wrap > .cmb2-metabox', $(document));

    $context.each(function () {
      var $this = $(this);
      var ruleset = $.deps.createRuleset();

      // Build dependencies rule.
      $this.find('[data-deps]').each(function () {
        var $el = $(this);

        var controllers = $el.data('deps').split('|'),
            conditions = $el.data('depsCondition').split('|'),
            depsValues = $el.data('depsValue').toString().split('|');

        $.each(controllers, function (index, controller) {
          var depsValue = depsValues[index] || '',
              condition = conditions[index] || conditions[0];

          var rule = ruleset.createRule('[data-deps-id="' + controller + '"]', condition, depsValue);
          rule.include($el);
        });
      });

      // Enable dependency.
      $.deps.enable($this, ruleset, {
        log: false,
        checkTargets: false,
        show: function show(el) {
          el.slideDown(250, function () {
            el.removeClass('hidden');
          });
        },
        hide: function hide(el) {
          el.slideUp(250, function () {
            el.addClass('hidden');
          });
        }
      });
    });
  };

  /**
   * Let's start!
   */
  $(function() {
    window.Skeleton.init();
    window.Skeleton.deps();
  });

})(jQuery, window.Skeleton);
