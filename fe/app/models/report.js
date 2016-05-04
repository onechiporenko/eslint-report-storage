import Model from 'ember-data/model';
import attr from 'ember-data/attr';

export default Model.extend({

  date: attr('string'),
  hash: attr('string'),
  errors: attr('number'),
  warnings: attr('number'),
  details: attr('report-details')

});
