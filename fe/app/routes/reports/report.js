import Ember from 'ember';

const {
  set,
  get
} = Ember;

export default Ember.Route.extend({

  breadCrumb: {},

  afterModel(model) {
    const id = get(model, 'id');
    set(this, 'breadCrumb', {title: id});
    model.reload();
  },

  model(params) {
    return this.store.findRecord('report', params.report_id, {reload: true});
  }

});
