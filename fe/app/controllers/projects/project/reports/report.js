import Ember from 'ember';

const {
  computed
} = Ember;

export default Ember.Controller.extend({

  chartOptions: computed('model.{errors,warnings}', function () {
    const model = this.get('model');
    return {
      chart: {
        type: 'column'
      },
      title: {
        text: 'Errors and Warnings'
      },
      xAxis: {
        categories: ['Issues']
      },
      yAxis: {
        title: {
          text: 'Count'
        }
      }
    };
  }),

  chartData: computed('model.{errors,warnings}', function () {
    const model = this.get('model');
    return [
      {name: 'Errors', data: [model.get('errors')]},
      {name: 'Warnings', data: [model.get('warnings')]}
    ];
  }),

  fileColumns: [
    {template: 'projects/project/files/to-file-with-report', title: 'Path', sortedBy: 'path', filteredBy: 'path'},
    {propertyName: 'warnings'},
    {propertyName: 'warnings_percents', title: 'Warnings, %'},
    {propertyName: 'errors'},
    {propertyName: 'errors_percents', title: 'Errors, %'}
  ],

  ruleColumns: [
    {template: 'projects/project/rules/to-rule', title: 'Name', sortedBy: 'name', filteredBy: 'name'},
    {propertyName: 'warnings'},
    {propertyName: 'warnings_percents', title: 'Warnings, %'},
    {propertyName: 'errors'},
    {propertyName: 'errors_percents', title: 'Errors, %'}
  ]

});
