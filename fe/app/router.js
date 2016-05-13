import Ember from 'ember';
import config from './config/environment';

const Router = Ember.Router.extend({
  location: config.locationType
});

Router.map(function() {

  this.route('projects', {path: '/projects'}, function () {
    this.route('project', {path: '/:project_id'}, function () {

      this.route('reports', {path: '/reports'}, function () {
        this.route('report', {path: '/:report_id'});
      });

      this.route('files', {path: '/files'}, function() {
        this.route('file', {path: '/:file_id'}, function () {
          this.route('reports', {path: '/reports'}, function () {
            this.route('report', {path: '/:report_id'});
          });
        });
      });

      this.route('rules', function() {
        this.route('rule', {path: '/:rule_id'});
      });

    });
  });

});

export default Router;
