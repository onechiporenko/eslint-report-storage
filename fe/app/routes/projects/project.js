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
    this.transitionTo('projects.project.reports.index', model);
  },

  model(params) {
    return this.store.findRecord('project', params.project_id);
  }

});
