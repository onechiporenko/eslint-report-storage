import Ember from 'ember';

const {
  computed
} = Ember;

export default Ember.Controller.extend({

  columns: [
    {
      title: 'ID',
      template: 'reports/to-report',
      sortedBy: 'id'
    },
    {propertyName: 'date'},
    {propertyName: 'hash'},
    {propertyName: 'errors'},
    {propertyName: 'warnings'}
  ],

  chartOptions: computed('model.@each.date', function () {
    const target = this.get('target');
    const model = this.get('model');
    return {
      chart: {
        type: 'line'
      },
      title: {
        text: 'Dynamic of the Problems'
      },
      xAxis: {
        categories: model.mapBy('id').sort(),
        labels: {
          formatter: function () {
            // click on x-axis navigates to the report details page
            var url = target.generate('reports.report', this.value);
            return `<a href="${url}">${this.value}</a>`;
          },
          useHTML: true
        }
      },
      yAxis: {
        title: {
          text: 'Count'
        }
      },
      plotOptions: {
        series: {
          cursor: 'pointer',
          point: {
            events: {
              click: function () {
                // click on the graph points navigates to the report details page
                location.href = target.generate('reports.report', this.category);
              }
            }
          }
        }
      }
    };
  }),
  chartData: computed('model.@each.{errors,warnings}', function () {
    const model = this.get('model').sortBy('id');
    return [
      {name: 'Errors', data: model.mapBy('errors')},
      {name: 'Warnings', data: model.mapBy('warnings')}
    ];
  })

});
