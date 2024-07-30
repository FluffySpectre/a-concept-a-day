const RSSFeed = require('./RSSFeed');

test('should return valid RSS feed xml', async () => {
  const rssFeed = new RSSFeed();
  const feedXML = await rssFeed.render();
  expect(feedXML.indexOf('<rss')).not.toBe(-1);
});
