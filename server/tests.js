const assert = require('node:assert/strict');

// AlgorithmRepository
// should return the latest algorithm
const AlgorithmRepository = require('./AlgorithmRepository');
const algorithmRepository = new AlgorithmRepository();
const latestAlgorithm = algorithmRepository.getLatestAlgorithm();
assert.ok(latestAlgorithm, 'AlgorithmRepository: No latest algorithm found.');

// RSSFeed
// should return a valid RSS feed object
const RSSFeed = require('./RSSFeed');
const rssFeed = new RSSFeed();
const feedXML = rssFeed.render();
assert.ok(feedXML.indexOf('<rss') !== -1, 'RSSFeed: No valid rss xml.');
// console.log(feedXML);

console.log('All tests completed!');