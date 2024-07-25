const fs = require('fs');
const path = require('path');
const { Builder } = require('xml2js');

module.exports = class RSSFeed {
    get_feed_object() {
        const current = JSON.parse(fs.readFileSync(path.join(__dirname, 'public/daily_algorithm.json'), 'utf8'));
        return {
            rss: {
                $: {
                    version: '2.0'
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
        const previousFolder = path.join(__dirname, 'public/previous');
        const files = fs.readdirSync(previousFolder);
        const jsonFiles = files.filter(file => file.endsWith('.json'));
        const algorithms = jsonFiles.map(file => {
            const algorithm = fs.readFileSync(path.join(previousFolder, file), 'utf8');
            return JSON.parse(algorithm);
        });
        const objs = algorithms.map(algorithm => {
            // Convert the unix timestamp to ISO format. Only date no time
            const isoString = new Date(algorithm.date * 1000).toISOString();
            const dateISO = isoString.split('T')[0];

            return {
                title: algorithm.name,
                link: `https://daily-algorithm.com/prev/${dateISO}`,
                description: algorithm.summary,
                pubDate: isoString
            };
        });

        // Add the current algorithm to the list
        const current = JSON.parse(fs.readFileSync(path.join(__dirname, 'public/daily_algorithm.json'), 'utf8'));
        const isoString = new Date(current.date * 1000).toISOString();
        const dateISO = isoString.split('T')[0];
        objs.unshift({
            title: current.name,
            link: `https://daily-algorithm.com/prev/${dateISO}`,
            description: current.summary,
            pubDate: isoString
        });

        return objs;
    }

    render() {
        const feed = this.get_feed_object();
    
        // console.log(`RSS Feed: ${JSON.stringify(feed, null, 2)}`);

        const builder = new Builder();
        return builder.buildObject(feed);
    }
}
