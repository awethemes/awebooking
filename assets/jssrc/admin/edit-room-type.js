'use strict';

const $ = window.jQuery;
const AweBooking = window.TheAweBooking;

const RoomsCreator = {
  el: '#abkng-rooms',
  template: '#awebooking-rooms-manager-template',

  data: {
    rooms: [],
    totalRooms: 0,
    roomTypeTitle: '',
  },

  created: function() {
    this.rooms = window.ABKNG_ROOMS || [];
    this.totalRooms = this.rooms.length;
    this.roomTypeTitle = $('#title').val();

    if (this.totalRooms == 0) {
      this.totalRooms = 1;
      this.regenerateRooms();
    }
  },

  methods: {
    regenerateRooms: function() {
      var roomsLength = this.rooms.length;
      var numberOfRooms = parseInt(this.totalRooms, 10);

      // Re-update title.
      this.roomTypeTitle = $('#title').val();

      if (numberOfRooms <= 0) {
        this.totalRooms = 1;
      }

      if (numberOfRooms > roomsLength) {
        for (var i = roomsLength; i < numberOfRooms; i++) {
          this.rooms.push({ id: -1, name: this.roomTypeTitle + ' - ' +  (i+1) });
        }
      } else if (numberOfRooms < roomsLength) {
        if (confirm(AweBooking.trans('ask_reduce_the_rooms'))) {
          for (var i = roomsLength - 1; i >= numberOfRooms; i--) {
            this.rooms.splice(i, 1);
          }
        }
      }
    },

    deleteRoomByIndex: function(index) {
      if (! confirm(AweBooking.trans('warning'))) {
        return;
      }

      if (this.rooms.length > 1) {
        this.rooms.splice(index, 1);
        this.totalRooms = this.rooms.length;
      }
    }
  },
};

$(function() {

  const roomsCreator = new AweBooking.Vue(RoomsCreator);
  $('#title').on('change', function() {
    var title = $(this).val();

    if (roomsCreator.$data.rooms.length === 1 && roomsCreator.$data.rooms[0].name == ' - 1') {
      roomsCreator.$data.rooms[0].name = title + ' - 1';
    }
  });

});
