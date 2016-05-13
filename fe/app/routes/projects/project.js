import Ember from 'ember';

const {
  set,
  get
} = Ember;

export default Ember.Route.extend({

  breadCrumb: {},

  afterModel(model) {
    const name = get(model, 'name');
    set(this, 'breadCrumb', {title: name});
  },

  model(params) {
    return this.store.findRecord('project', params.project_id, {reload: true});
  },

  setupController(controller, model) {
    this._super(controller, model);
  }

});
