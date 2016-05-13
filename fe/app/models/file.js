import Model from 'ember-data/model';
import attr from 'ember-data/attr';
import {belongsTo} from 'ember-data/relationships';

export default Model.extend({
  path: attr('string'),
  reports: attr(),
  project: belongsTo('project')
});
