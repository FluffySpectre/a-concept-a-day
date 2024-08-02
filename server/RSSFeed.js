const { Builder } = require('xml2js');
const AlgorithmRepository = require('./AlgorithmRepository');
const { renderTemplate } = require('./template');

module.exports = class RSSFeed {
  constructor() {
    this.algorithmRepository = new AlgorithmRepository();
  }

  async get_feed_object() {
    const { date } = await this.algorithmRepository.getLatestAlgorithm();
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
          pubDate: new Date(date * 1000).toISOString(),
          item: await this.get_algorithms()
        }
      }
    };
  }

  async get_algorithms() {
    // Get the algorithms from the repository
    const algorithms = await this.algorithmRepository.getAlgorithms();
    return algorithms.map(({ name, date, content }) => {
      // Convert the unix timestamp to ISO format. Only date no time
      const isoString = new Date(date * 1000).toISOString();
      const dateISO = isoString.split('T')[0];
  
      const rssTemplate = `
        {{#content}}
        <h4>{{item.title}}</h4>
  
        {{#if item.type = code}}
        <p><pre><code>{{item.content}}</code></pre></p>
        {{/if}}
  
        {{#if item.type = text}}
        <p>{{item.content}}</p>
        {{/if}}
        {{/content}}
      `;
  
      return {
        title: name,
        link: `https://daily-algorithm.com/prev/${dateISO}`,
        description: content[0].content,
        'content:encoded': renderTemplate(rssTemplate, { content }),
        pubDate: isoString
      };
    });
  }

  async render() {
    const feed = await this.get_feed_object();
    return new Builder({ cdata: true }).buildObject(feed);
  }
}
