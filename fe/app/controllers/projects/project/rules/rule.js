import Ember from 'ember';

const {computed} = Ember;

export default Ember.Controller.extend({

  chartOptions: computed('model.reports[]', function () {
    const reports = this.get('model.reports');
    const target = this.get('target');
    return {
      chart: {
        type: 'line'
      },
      title: {
        text: 'Dynamic of the Problem'
      },
      xAxis: {
        categories: reports.mapBy('report_id'),
        labels: {
          formatter: function () {
            // click on x-axis navigates to the report details page
            var url = target.generate('projects.project.reports.report', this.value);
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
                location.href = target.generate('projects.project.reports.report', this.category);
              }
            }
          }
        }
      }
    };
  }),

  chartData: computed('model.reports.@each.{errors,warnings}', function () {
    const reports = this.get('model.reports');
    return [
      {name: 'Errors', data: reports.mapBy('errors')},
      {name: 'Warnings', data: reports.mapBy('warnings')}
    ];
  })

});
