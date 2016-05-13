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
    return model.reload();
  },

  model(params) {
    return this.store.findRecord('rule', params.rule_id, {reload: true});
  }

});
