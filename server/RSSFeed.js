const { Builder } = require('xml2js');

module.exports = class RSSFeed {
    constructor() {
    }

    get_feed_object() {
        return {
            rss: {
                $: {
                    version: '2.0'
                },
                channel: {
                    title: 'Daily Algorithm',
                    link: 'https://daily-algorithm.com',
                    description: 'Daily algorithm problems and solutions.',
                    pubDate: new Date().toISOString(),
                    item: this.get_algorithms()
                }
            }
        };
    }

    get_algorithms() {
        return [
            {
                title: 'Algorithm 1',
                link: 'https://daily-algorithm.com/prev/2024-07-25',
                description: 'This is the first algorithm.',
                pubDate: new Date().toISOString()
            },
            {
                title: 'Algorithm 2',
                link: 'https://daily-algorithm.com/prev/2024-07-24',
                description: 'This is the second algorithm.',
                pubDate: new Date().toISOString()
            }
        ];
    }

    render() {
        const feed = this.get_feed_object();
    
        // console.log(`RSS Feed: ${JSON.stringify(feed, null, 2)}`);

        const builder = new Builder();
        return builder.buildObject(feed);
    }
}
