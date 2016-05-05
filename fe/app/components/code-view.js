import Ember from 'ember';

const {
  computed,
  observer,
  isArray,
  isEmpty,
  run,
  on,
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
   * Lines of the code with inserted tooltips
   *
   * @type {string[]}
   */
  codeWithTooltips: [],

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

  formatCodeWithTooltips: on('init', function () {
    const details = this.get('details') || [];
    var code = this.get('code');
    if (!isArray(code)) {
      this.set('codeWithTooltips', []);
      return;
    }
    code = code.slice();
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
    this.set('codeWithTooltips', code);
    this.trigger('tooltipsAdded');
  }),

  willDestroyElement () {
    this.turnOffTooltips();
  },

  showTooltipsAfterCodeIsFormatted: on('tooltipsAdded', function () {
    const codeWithTooltips = this.get('codeWithTooltips');
    const self = this;
    if (!isEmpty(codeWithTooltips)) {
      run.next(self, self.turnOnTooltips);
    }
  }),

  turnOnTooltips() {
    $('[rel="tooltip"]').tooltip({trigger: 'manual', html: true}).tooltip('show');
    this.set('tooltipsAreVisible', true);
  },

  turnOffTooltips() {
    $('[rel="tooltip"]').tooltip('destroy');
    this.set('tooltipsAreVisible', false);
  },

  actions: {
    toggleTooltips() {
      const tooltipsAreVisible = this.get('tooltipsAreVisible');
      if (tooltipsAreVisible) {
        this.turnOffTooltips();
      }
      else {
        this.turnOnTooltips();
      }
    }
  }

});
