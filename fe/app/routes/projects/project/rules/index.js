import Ember from 'ember';

export default Ember.Route.extend({

  model() {
    var projectId = this.modelFor('projects.project').get('id');
    return this.store.query('rule', {project_id: projectId});
  }

});
