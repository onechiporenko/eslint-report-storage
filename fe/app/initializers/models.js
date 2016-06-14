import Ember from 'ember';
import Model from 'ember-data/model';

const {
  get,
  computed
  } = Ember;

export function initialize() {
  Model.reopen({
    numericId: computed('id', function () {
      return parseInt(get(this, 'id'), 10);
    })
  });
}

export default {
  name: 'models',
  initialize
};
