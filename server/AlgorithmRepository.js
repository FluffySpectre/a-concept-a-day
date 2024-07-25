const fs = require('fs');
const path = require('path');

module.exports = class AlgorithmRepository {
  getLatestAlgorithm() {
    const newestDate = fs.readdirSync( __dirname + '/public/previous')
        .filter(file => file.endsWith('.json')) // Filter json files
        .sort((a, b) => { // Sort by date
            if (a < b) return 1;
            if (a > b) return -1;
            return 0;
        })[0]
        .match(/\d{4}-\d{2}-\d{2}/)[0]; // Filter the date from the filename
    return this.getAlgorithmOfDate(newestDate);
  }

  getAlgorithmOfDate(date) {
    const filename = __dirname + `/public/previous/${date}.json`;
    if (!this.existsAlgorithmForDate(date)) {
        return null;
    }
    const data = fs.readFileSync(filename, 'utf8');
    return JSON.parse(data);
  }

  getAlgorithms() {
    const previousFolder = path.join(__dirname, 'public/previous');
    const files = fs.readdirSync(previousFolder);
    const jsonFiles = files.filter(file => file.endsWith('.json'));
    return jsonFiles.map(file => {
        const algorithm = fs.readFileSync(path.join(previousFolder, file), 'utf8');
        return JSON.parse(algorithm);
    });
  }

  existsAlgorithmForDate(date) {
    const filename = __dirname + `/public/previous/${date}.json`;
    return fs.existsSync(filename);
  }
}
