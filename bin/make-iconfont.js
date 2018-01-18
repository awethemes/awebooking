const path = require('path');
const generator = require('icon.font');

const options = {
  fontName:        'awebooking',
  configFile:      './assets/svg/config.json',
  src:             './assets/svg',
  dest:            './assets/fonts',
  image:           false,
  htmlTemplate:    'bin/webfonts/html.hbs',
  cssTemplate:     'bin/webfonts/css.hbs',
  codepointRanges: [ [0xF101, Infinity] ],
  templateOptions: {
    classPrefix:   'awebooking-',
    baseSelector:  '._icon',
    baseClassname: '_icon',
  },
};

generator(options).then(function() {
  console.info('Generator iconfont successfully!');
});
