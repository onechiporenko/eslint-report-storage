import Ember from 'ember';

const {
  set,
  get
} = Ember;

export default Ember.Route.extend({

  breadCrumb: {},

  afterModel(model) {
    const path = get(model, 'path');
    const projectPath = get(model, 'project.path');
    set(this, 'breadCrumb', {title: path.replace(projectPath, '')});
    return model.reload();
  },

  model(params) {
    return this.store.findRecord('file', params.file_id);
  }
});
