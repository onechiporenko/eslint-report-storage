import Ember from 'ember';
import ModelsInitializer from 'fe/initializers/models';
import { module, test } from 'qunit';

let application;

module('Unit | Initializer | models', {
  beforeEach() {
    Ember.run(function() {
      application = Ember.Application.create();
      application.deferReadiness();
    });
  }
});

// Replace this with your real tests.
test('it works', function(assert) {
  ModelsInitializer.initialize(application);

  // you would normally confirm the results of the initializer here
  assert.ok(true);
});
