import Ember from 'ember';

const {computed} = Ember;

export default Ember.Controller.extend({

  customClasses: {
    table: 'table table-striped'
  },

  columns: [
    {title: 'Name', template: 'rules/to-rule', sortedBy: 'name', filteredBy: 'name'}
  ]

});
