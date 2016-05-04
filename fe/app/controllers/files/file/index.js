import Ember from 'ember';

const {
  computed,
  isEmpty
  } = Ember;

export default Ember.Controller.extend({

  chartOptions: computed('model.reports.[]', function () {
    const id = this.get('model.id');
    const reports = this.get('model.reports');
    const target = this.get('target');
    return {
      chart: {
        type: 'line'
      },
      title: {
        text: 'Dynamic of the Problems'
      },
      xAxis: {
        categories: isEmpty(reports) ? [] : reports.mapBy('report_id'),
        labels: {
          formatter: function () {
            // click on x-axis navigates to the file/report details
            var url = target.generate('files.file.reports.report', id, this.value);
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
                // click on the graph points navigates to the file/report details
                location.href = target.generate('files.file.reports.report', id, this.category);
              }
            }
          }
        }
      }
    };
  }),

  chartData: computed('model.reports.@each.{errors,warnings}', function () {
    const reports = this.get('model.reports');
    return isEmpty(reports) ? [] : [
      {name: 'Errors', data: reports.mapBy('errors')},
      {name: 'Warnings', data: reports.mapBy('warnings')}
    ];
  })

});
