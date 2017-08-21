var i18n = require('node-wp-i18n');

i18n.makepot({
  type: 'wp-plugin',
  domainPath: '/languages',
  mainFile: 'awebooking.php',
  potFilename: 'awebooking.pot',
  updateTimestamp: true,
  exclude: [
    'tests/.*',
    'vendor/.*',
    'skeleton/.*'
  ],
  potHeaders: {
    poedit: true,
    'x-poedit-keywordslist': true
  }
});

console.log('Successfully written: languages/awebooking.pot');
