const fs = require('node:fs/promises');
const path = require('path');

module.exports = class AlgorithmRepository {
  async getLatestAlgorithm() {
    const dirPath = path.join(__dirname, 'public', 'previous');
    const files = await fs.readdir(dirPath);
    
    const newestDate = files
      .filter(file => file.endsWith('.json'))
      .reduce((latest, file) => {
        const date = file.slice(0, -5); // Remove the extension
        return !latest || new Date(date) > new Date(latest) ? date : latest;
      }, null);
  
    return this.getAlgorithmOfDate(newestDate);
  }

  async getAlgorithmOfDate(date) {
    const filename = path.join(__dirname, 'public', 'previous', `${date}.json`);
    const exists = await this.existsAlgorithmForDate(date);
    if (!exists) return null;
    const data = await fs.readFile(filename, 'utf8');
    return JSON.parse(data);
  }

  async getAlgorithms() {
    const previousFolder = path.join(__dirname, 'public', 'previous');
    const files = await fs.readdir(previousFolder);
    const jsonFiles = files.filter(file => file.endsWith('.json'));

    return Promise.all(jsonFiles.map(async file => {
      const algorithm = await fs.readFile(path.join(previousFolder, file), 'utf8');
      return JSON.parse(algorithm);
    }));
  }

  async existsAlgorithmForDate(date) {
    try {
      await fs.access(path.join(__dirname, 'public', 'previous', `${date}.json`), fs.constants.R_OK);
      return true;
    } catch {
      return false;
    }
  }
}
