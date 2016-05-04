import Ember from 'ember';

const {
  set,
  get
} = Ember;

export default Ember.Route.extend({

  breadCrumb: {},

  afterModel(model) {
    const path = get(model, 'path');
    set(this, 'breadCrumb', {title: path.replace('/home/on/git/clean-ambari/ambari-web/', '')});
  },

  model(params) {
    return this.store.findRecord('file', params.file_id, {reload: true});
  }

});
