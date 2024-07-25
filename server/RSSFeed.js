const { Builder } = require('xml2js');
const AlgorithmRepository = require('./AlgorithmRepository');

module.exports = class RSSFeed {
  constructor() {
    this.algorithmRepository = new AlgorithmRepository();
  }

  get_feed_object() {
    const current = this.algorithmRepository.getAlgorithmOfToday();
    return {
      rss: {
        $: {
          version: '2.0',
          'xmlns:content': 'http://purl.org/rss/1.0/modules/content/',
        },
        channel: {
          title: 'Daily Algorithm',
          link: 'https://daily-algorithm.com',
          description: 'Daily Algorithm RSS Feed',
          pubDate: new Date(current.date * 1000).toISOString(),
          item: this.get_algorithms()
        }
      }
    };
  }

  get_algorithms() {
    // Get the previous algorithms from the /public/previous folder
    // and return them as an array of objects
    const algorithms = this.algorithmRepository.getAlgorithms();
    const objs = algorithms.map(algorithm => {
      // Convert the unix timestamp to ISO format. Only date no time
      const isoString = new Date(algorithm.date * 1000).toISOString();
      const dateISO = isoString.split('T')[0];

      const bodyHTML = algorithm.summary + '<br><br>' + algorithm.example + '<br><br><code>' + algorithm.coding_example + '</code>';

      return {
        title: algorithm.name,
        link: `https://daily-algorithm.com/prev/${dateISO}`,
        description: algorithm.summary,
        'content:encoded': bodyHTML,
        pubDate: isoString
      };
    });

    return objs;
  }

  render() {
    const feed = this.get_feed_object();

    // console.log(`RSS Feed: ${JSON.stringify(feed, null, 2)}`);

    const builder = new Builder({
      cdata: true
    });
    return builder.buildObject(feed);
  }
}
