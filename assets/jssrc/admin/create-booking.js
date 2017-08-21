(function($, Vue, awebooking) {

  $(function() {

    new Vue({
      el: '#booking-rooms',

      data: {
        doingAjax: false,

        booking_id: 0,
        booking_status: '',

        availability_result: [],
        booked_rooms: {},
      },

      created: function() {
        this.booking_id = $('#post_ID').val();
        this.booking_status = $('#post_status').val();

        this.booked_rooms = BOOKING_ROOMS;
      },

      methods: {
        ajaxRequest: function() {
          var self = this;

          var exclude_rooms;
          if (this.booked_rooms && this.booked_rooms.room) {
            exclude_rooms = this.booked_rooms.room.id;
          }

          var ajaxData = {
            start_date: $('#booking_check_in').val(),
            end_date: $('#booking_check_out').val(),
            adults: $('#booking_adults').val(),
            children: $('#booking_children').val(),
          };

          ajaxData.exclude_rooms = exclude_rooms;

          self.doingAjax = true;
          awebooking.ajax('check_availability', ajaxData)
            .done(function(response) {
              if (response && response != "0" && response.success == true ) {
                self.availability_result = response.data;
              } else if (response.success == false && response.data.message) {
                self.availability_result = [];
                alert(response.data.message);
              } else {
                self.availability_result = [];
                alert('Something is wrong!');
              }
            })
            .always(function() {
              self.doingAjax = false;
            });
        },

        addRoom: function(index) {
          var self = this;
          var avai = this.availability_result[index];

          var ajaxData = {
            start_date: $('#booking_check_in').val(),
            end_date: $('#booking_check_out').val(),
            adults: $('#booking_adults').val(),
            children: $('#booking_children').val(),
            room_type: avai.room_type.id,
            booking_id: this.booking_id,
          };

          self.doingAjax = true;
          awebooking.ajax('add_booking_item', ajaxData)
            .done(function(response) {

              if (response && response != "0" && response.success == true ) {
                self.booked_rooms = response.data;
                self.availability_result = [];
              } else if (response.success == false && response.data.message) {
                self.booked_rooms = {};
                alert(response.data.message);
              } else {
                self.booked_rooms = {};
                alert('Something is wrong!');
              }

            })
            .always(function() {
              self.doingAjax = false;
            });
        }
      }
    });

  });

})(jQuery, Vue, ABKNG)
