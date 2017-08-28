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

const ExtraServicesCreator = {
  el: '#extra-services',
  template: '#awebooking-manager-service-template',

  data: {
    services: [],
    all_services: [],
    serviceExist: 0,

    newService: {
      name: '',
      value: 0.00,
      operation: 'add',
      type: 'optional',
    },

    showAddNewContent: false,
  },

  created: function() {
    this.defaultService = this.newService;

    this.services = window.ABKNG_CURRENT_SERVICES || [];
    this.all_services = window.ABKNG_ALL_SERVICES || [];
  },

  methods: {
    isPricable: function() {
      return this.newService.operation == 'increase' || this.newService.operation == 'decrease';
    },
    addNewService: function() {
      const self = this;

      const data = this.newService;
      data.room_type = $('#post_ID').val();

      wp.ajax.post('add_awebooking_service', data)
        .done(function(service) {
          if (service.id) {
            self.all_services.push(service);
            self.services.push(service);

            self.newService = defaultService;
            self.showAddNewContent = false;
          }
        })
        .fail(function(error) {
          alert(error.message);
        });
    },

    deleteService: function(index) {
      this.services.splice(index, 1);
    },

    checkIncludeService: function(id) {
      var check = _.find(this.services, function (s) { return s.id == id; });
      return (typeof check !== 'undefined');
    },

    addNewServiceExist: function() {
      var id = this.serviceExist;
      if ((typeof(id) != "undefined") && (id != 0)) {
        var service = _.find(this.all_services, function (s) { return s.id == id; });
        this.services.push(service);
        this.serviceExist = 0;
      }
    },

    checkOperation: function(operation, price) {
      switch (operation) {
          case 'add':
            operation = '+ ' + price;
            break;
          case 'add-daily':
            operation = '+ ' + price + '/night';
            break;
          case 'add-person':
            operation = '+ ' + price + '/person';
            break;
          case 'add-person-daily':
            operation = '+ ' + price + '/person/night';
            break;
          case 'increase':
            operation = '+ ' + price + '%';
            break;
          case 'sub':
            operation = '- ' + price;
            break;
          case 'sub-daily':
            operation = '- ' + price + '/night';
            break;
          case 'decrease':
            operation = '- ' + price + '%';
            break;
      }

      return operation;
    },

    buildTitle: function( service ) {
      return service.name + ' ' + this.checkOperation(service.operation, service.value);
    },
  }
};

$(function() {

  const roomsCreator = new AweBooking.Vue(RoomsCreator);
  const extraServiceCreator = new AweBooking.Vue(ExtraServicesCreator);

  $('#title').on('change', function() {
    var title = $(this).val();

    if (roomsCreator.$data.rooms.length === 1 && roomsCreator.$data.rooms[0].name == ' - 1') {
      roomsCreator.$data.rooms[0].name = title + ' - 1';
    }
  });

});
