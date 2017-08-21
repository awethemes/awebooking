;(function($, Vue, awebooking) {
  'use strict';

  var RoomsCreator = {
    el: '#abkng-rooms',

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
          if (confirm(awebooking.trans('ask_reduce_the_rooms'))) {
            for (var i = roomsLength - 1; i >= numberOfRooms; i--) {
              this.rooms.splice(i, 1);
            }
          }
        }
      },

      deleteRoomByIndex: function(index) {
        if (! confirm(awebooking.trans('warning'))) {
          return;
        }

        if (this.rooms.length > 1) {
          this.rooms.splice(index, 1);
          this.totalRooms = this.rooms.length;
        }
      }
    },
  };

  var ExtraServicesCreator = {
    el: '#extra-services',

    data: {
      services: [],
      all_services: [],

      serviceExist: 0,

      newService: {
        name: '',
        price: 0.00,
        operation: 'add',
        type: 'optional',
      },

      showAddNewContent: false,
    },

    created: function() {
      this.services = window.ABKNG_CURRENT_SERVICES || [];
      this.all_services = window.ABKNG_ALL_SERVICES || [];
    },

    methods: {
      isPricable: function() {
        return this.newService.operation == 'increase' || this.newService.operation == 'decrease';
      },
      addNewService: function() {
        var self = this;
        $.ajax({
            url: awebooking.ajax_url,
            type: 'post',
            data: _.extend( this.newService, {
                action: 'set_term_meta',
                post_id: $('#post_ID').val(),
            }),
            beforeSend: function() {
            },
            success: function( response ) {
              var service = {id: response.data, name: self.newService.name, price: self.newService.price, operation: self.newService.operation, type: self.newService.type };

              self.all_services.push(service);
              self.services.push(service);

              self.newService.name = '';
              self.newService.operation = 'add';
              self.newService.type = 'optional';
              self.newService.price = 0.00;
              self.showAddNewContent = false;
            }
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
        var name = service.name;
        var price = service.price;
        var operation = service.operation;
        var type = service.type;


        return name + ' ' + this.checkOperation(operation,price);
      },
    }
  };

  awebooking.on('ready', function() {
    awebooking.roomsCreator = new Vue(RoomsCreator);
    awebooking.extraServiceCreator = new Vue(ExtraServicesCreator);

    var roomCreator = awebooking.roomsCreator;
    $('#title').on('change', function() {
      var title = $(this).val();

      if (roomCreator.$data.rooms.length === 1 && roomCreator.$data.rooms[0].name == ' - 1') {
        roomCreator.$data.rooms[0].name = title + ' - 1';
      }
    });

  });

})(jQuery, Vue, ABKNG);
