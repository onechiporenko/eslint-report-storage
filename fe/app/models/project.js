import Model from 'ember-data/model';
import attr from 'ember-data/attr';
import {hasMany} from 'ember-data/relationships';

export default Model.extend({

  name: attr('string'),

  path: attr('string'),

  subpath: attr('string'),

  description: attr('string'),

  reports: hasMany('report'),

  files: hasMany('file'),

  repo: attr('string')

});
