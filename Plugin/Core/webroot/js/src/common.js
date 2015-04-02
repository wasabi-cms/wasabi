require.config({
  paths: {
    Backbone: 'common/vendor/backbone',
    Handlebars: 'common/vendor/handlebars',
    Spinner: 'common/vendor/spin',
    Underscore: 'common/vendor/underscore',
    hbs: 'common/vendor/rjs-plugins/hbs',
    text: 'common/vendor/rjs-plugins/text',
    jquery: 'common/vendor/jquery',
    'bootstrap.dropdown': 'common/vendor/bootstrap.dropdown',
    'jquery.eventMagic': 'common/vendor/frankfoerster/jquery.eventMagic',
    'jquery.livequery': 'common/vendor/frankfoerster/jquery.livequery',
    'jquery.scrollParent': 'common/vendor/frankfoerster/jquery.scrollParent',
    'jquery.toggleSelect': 'common/vendor/frankfoerster/jquery.toggleSelect',
    'jquery.nSortable': 'common/vendor/frankfoerster/jquery.nSortable',
    'jquery.tSortable': 'common/vendor/frankfoerster/jquery.tSortable',
    'jquery.multiselect': 'common/vendor/frankfoerster/jquery.multiselect',
    'jquery.color': 'common/vendor/jquery.color.min',
    'jquery.cookie': 'common/vendor/jquery.cookie.min'
  },
  shim: {
    Backbone: {
      deps: ['Underscore', 'jquery'],
      exports: 'Backbone'
    },
    Handlebars: {
      exports: 'Handlebars'
    },
    Underscore: {
      exports: '_'
    },
    jquery: {
      exports: '$'
    },
    'bootstrap.dropdown': ['jquery'],
    'jquery.livequery': ['jquery'],
    'jquery.multiselect': ['jquery', 'jquery.eventMagic'],
    'jquery.scrollParent': ['jquery'],
    'jquery.toggleSelect': ['jquery'],
    'jquery.nSortable': ['jquery', 'jquery.eventMagic', 'jquery.scrollParent'],
    'jquery.tSortable': ['jquery', 'jquery.eventMagic', 'jquery.scrollParent'],
    'jquery.color': ['jquery'],
    'jquery.cookie': ['jquery']
  }
});

// removed in production by uglify
if (typeof DEBUG === 'undefined') {
  DEBUG = true;
}
