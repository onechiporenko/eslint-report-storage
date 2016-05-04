import Ember from 'ember';

const {
  computed,
  isArray,
  $
} = Ember;

export default Ember.Component.extend({

  /**
   * Lines of the code
   *
   * @type {string[]}
   */
  code: [],

  /**
   * List with errors and warnings
   *
   * @type {object[]}
   */
  details: [],

  /**
   * Determines if tooltips with code issues are visible
   *
   * @type {boolean}
   */
  tooltipsAreVisible: false,

  /**
   * Lines of the code with inserted tooltips
   *
   * @type {string[]}
   */
  codeWithTooltips: computed('code.[]', 'details.[]', function () {
    const details = this.get('details') || [];
    var code = this.get('code');
    if (!isArray(code)) {
      return [];
    }
    details.forEach(function (byLine) {
      const index = byLine.line - 1;
      var type = byLine.severity === 2 ? 'Error' : 'Warning';
      var rule = byLine.ruleId;
      var msg = byLine.message.replace(/"/g, '&quot;');
      msg = `${rule}. ${type}: ${msg}`;
      const tooltip = `<span rel="tooltip" data-original-title="${msg}"></span>`;
      const insertAt = byLine.column - 1;
      if (!code[index] || code[index].indexOf('<span') !== -1) {
        return;
      }
      code[index] = code[index].slice(0, insertAt) + tooltip + code[index].slice(insertAt);
    });
    return code;
  }),

  willDestroyElement () {
    $('[rel="tooltip"]').tooltip('destroy');
  },

  actions: {
    toggleTooltips() {
      const tooltipsAreVisible = this.get('tooltipsAreVisible');
      if (tooltipsAreVisible) {
        $('[rel="tooltip"]').tooltip('destroy');
      }
      else {
        $('[rel="tooltip"]').tooltip({trigger: 'manual', html: true}).tooltip('show');
      }
      this.toggleProperty('tooltipsAreVisible');
    }
  }

});
