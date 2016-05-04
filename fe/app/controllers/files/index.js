import Ember from 'ember';

const {computed} = Ember;

export default Ember.Controller.extend({

  customClasses: {
    table: 'table table-striped'
  },

  paths: computed('model.@each.path', function () {
    return this.get('model').mapBy('path');
  }),

  modelsCount: computed.alias('model.length'),

  columns: [
    {title:'Path', template: 'files/to-file', sortedBy: 'path', filteredBy: 'path'}
  ]

});
