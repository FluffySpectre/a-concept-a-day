const fs = require('node:fs/promises');
const path = require('path');

module.exports = class AlgorithmRepository {
  async getLatestAlgorithm() {
    let newestDate = await fs.readdir(__dirname + '/public/previous');
    newestDate = newestDate
      .filter(file => file.endsWith('.json')) // Filter json files
      .map(file => file.split('.')[0]) // Remove the extension
      .sort((a, b) => { // Sort by date
          if (a < b) return 1;
          if (a > b) return -1;
          return 0;
      })[0]
    return this.getAlgorithmOfDate(newestDate);
  }

  async getAlgorithmOfDate(date) {
    const filename = __dirname + `/public/previous/${date}.json`;
    const exists = await this.existsAlgorithmForDate(date);
    if (!exists) {
        return null;
    }
    const data = await fs.readFile(filename, 'utf8');
    return JSON.parse(data);
  }

  async getAlgorithms() {
    const previousFolder = path.join(__dirname, 'public/previous');
    const files = await fs.readdir(previousFolder);
    const jsonFiles = files.filter(file => file.endsWith('.json'));
    
    const algorithms = await Promise.all(jsonFiles.map(async file => {
        const algorithm = await fs.readFile(path.join(previousFolder, file), 'utf8');
        return JSON.parse(algorithm);
    }));
    
    return algorithms;
}

  async existsAlgorithmForDate(date) {
    const filename = __dirname + `/public/previous/${date}.json`;

    try {
      await fs.access(filename, fs.constants.R_OK);
      return true;
    } catch {
      return false;
    }
  }
}
