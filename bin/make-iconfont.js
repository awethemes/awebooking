const path = require('path');
const generator = require('icon.font');

const options = {
  src:             './assets/img/raw-svg',
  dest:            './assets/fonts',
  image:           false,
  fontName:        'awebooking-webfont',
  configFile:      'bin/webfonts/config.json',
  htmlTemplate:    'bin/webfonts/html.hbs',
  cssTemplate:     'bin/webfonts/css.hbs',
  types:           ['woff2', 'woff', 'ttf', 'eot', 'svg'],
  codepointRanges: [ [0xF101, Infinity] ],
  templateOptions: {
    baseSelector:  'afc', // awebooking-webfont
    classPrefix:   'afc-',
  },
};

generator(options).then(function() {
  console.info('Generator iconfont successfully!');
});
