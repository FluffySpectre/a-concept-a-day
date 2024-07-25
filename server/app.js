const express = require('express')
const fs = require('fs');
const app = express()
const port = 3000
const TemplateRenderer = require('./TemplateRenderer');

function getDailyAlgorithmData() {
  const filename = __dirname + '/public/daily_algorithm.json';
  if (!fs.existsSync(filename)) {
    return null;
  }
  const data = fs.readFileSync(filename);
  return JSON.parse(data);
}

// app.use(express.static('public'))

app.get('/', (req, res) => {
  // Read daily_algorith.json file in the public folder
  const json = getDailyAlgorithmData();
  if (!json) {
    res.status(404).send('No daily algorithm found!');
    return;
  }

  // Render the index.html file with the json data
  // and send the rendered html to the client
  const templateHTML = fs.readFileSync(__dirname + '/public/index.html', 'utf8');
  const templateRenderer = new TemplateRenderer(json);
  res.send(templateRenderer.render(templateHTML));
})

app.get('/json', (req, res) => {
  const json = getDailyAlgorithmData();
  if (!json) {
    res.status(404).send('No daily algorithm found!');
    return;
  }
  res.json(json);
})

app.listen(port, () => {
  console.log(`Daily Algorithm Server listening on port ${port}`)
})
