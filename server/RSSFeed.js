const { Builder } = require('xml2js');
const AlgorithmRepository = require('./AlgorithmRepository');

module.exports = class RSSFeed {
  constructor() {
    this.algorithmRepository = new AlgorithmRepository();
  }

  async get_feed_object() {
    const current = await this.algorithmRepository.getLatestAlgorithm();
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
          item: await this.get_algorithms()
        }
      }
    };
  }

  async get_algorithms() {
    // Get the previous algorithms from the /public/previous folder
    // and return them as an array of objects
    const algorithms = await this.algorithmRepository.getAlgorithms();
    const objs = algorithms.map(algorithm => {
      // Convert the unix timestamp to ISO format. Only date no time
      const isoString = new Date(algorithm.date * 1000).toISOString();
      const dateISO = isoString.split('T')[0];

      const bodyHTML = '<h4>Summary</h4>' + 
        algorithm.summary + '<br>' +
        '<h4>Usage</h4>' +
        algorithm.example + '<br>' +
        '<h4>Steps</h4>' +
        algorithm.step_description + '<br>' +
        '<h4>Coding example</h4>' +
        '<pre><code>' + algorithm.coding_example + '</code></pre>';

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

  async render() {
    const feed = await this.get_feed_object();
    const builder = new Builder({
      cdata: true
    });
    return builder.buildObject(feed);
  }
}
