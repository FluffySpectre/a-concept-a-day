const assert = require('node:assert/strict');

// RSSFeed
// should return a valid RSS feed object
const RSSFeed = require('./RSSFeed');
const rssFeed = new RSSFeed();
const feedXML = rssFeed.render();
assert.ok(feedXML.indexOf('<rss') !== -1, 'RSSFeed: No valid rss xml.');

console.log('All tests completed!');