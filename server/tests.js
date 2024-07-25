const assert = require('node:assert/strict');

// AlgorithmRepository
// should return the latest algorithm
const AlgorithmRepository = require('./AlgorithmRepository');
const algorithmRepository = new AlgorithmRepository();
const testLatestAlgorithm = async () => {
    const latestAlgorithm = await algorithmRepository.getLatestAlgorithm();
    assert.ok(latestAlgorithm, 'AlgorithmRepository: No latest algorithm found.');
};
testLatestAlgorithm();

// RSSFeed
// should return valid RSS feed xml
const RSSFeed = require('./RSSFeed');
const rssFeed = new RSSFeed();
const testValidRSSXML = async () => {
    const feedXML = await rssFeed.render();
    assert.ok(feedXML.indexOf('<rss') !== -1, 'RSSFeed: No valid rss xml.');
    // console.log(feedXML);
};
testValidRSSXML();

console.log('All tests completed!');