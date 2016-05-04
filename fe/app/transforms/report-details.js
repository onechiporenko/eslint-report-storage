import Transform from 'ember-data/transform';

export default Transform.extend({
  deserialize(serialized) {
    ['files', 'rules'].forEach(type => {
      serialized[type].forEach(file => {
        file.warnings = parseInt(file.warnings, 10);
        file.errors = parseInt(file.errors, 10);
      });
    });
    return serialized;
  },

  serialize(deserialized) {
    return deserialized;
  }
});
