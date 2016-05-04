import Ember from 'ember';

const {
  observer,
  $
} = Ember;

export default Ember.Controller.extend({

  /**
   *
   *
   * @type {File}
   */
  file: null,

  /**
   * @type {string}
   */
  fileContent: [],

  /**
   * @type {object[]}
   */
  reportByFile: [],

  /**
   * @type {boolean}
   */
  reportByFileIsLoaded: false,

  /**
   * Load file source and report for this file
   */
  loadFileContent: observer('file.id', 'model.id', 'reportByFileIsLoaded', function () {
    const fileId = this.get('file.id');
    const reportId = this.get('model.id');
    const reportByFileIsLoaded = this.get('reportByFileIsLoaded');
    var self = this;
    if(reportId && fileId && !reportByFileIsLoaded) {
      $.get(`/files/${fileId}/${reportId}`).then(function (fileContent) {
        self.set('fileContent', fileContent.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').split('\n'));
        $.getJSON(`/files/${fileId}/${reportId}/results`).then(function (results) {
          self.set('reportByFile', results.data);
          self.set('reportByFileIsLoaded', true);
        });
      });
    }
  })

});
