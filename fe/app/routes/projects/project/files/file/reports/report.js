import Ember from 'ember';

const {
  set,
  get
} = Ember;

export default Ember.Route.extend({

  breadCrumb: {},

  model(params) {
    return this.store.findRecord('report', params.report_id);
  },

  afterModel(model) {
    const id = get(model, 'id');
    set(this, 'breadCrumb', {title: id});
  },

  setupController(controller, model) {
    this._super(controller, model);
    const file = this.controllerFor('projects.project.files.file').get('model');
    controller.set('file', file);
  },

  resetController (controller) {
    controller.setProperties({
      file: null,
      model: null,
      fileContent: [],
      reportByFile: [],
      reportByFileIsLoaded: false
    });
  }

});
