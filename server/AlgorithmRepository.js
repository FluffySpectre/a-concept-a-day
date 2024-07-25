const fs = require('fs');
const path = require('path');

module.exports = class AlgorithmRepository {
  getAlgorithmOfToday() {
    const todayISODate = new Date().toISOString().split('T')[0];
    return this.getAlgorithmOfDate(todayISODate);
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
