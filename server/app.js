const express = require('express');
const fs = require('fs');
const TemplateRenderer = require('./TemplateRenderer');
const RSSFeed = require('./RSSFeed');
const AlgorithmRepository = require('./AlgorithmRepository');

const app = express();
const port = 3000;

const algorithmRepository = new AlgorithmRepository();

// app.use(express.static('public'))

app.get('/', (req, res) => {
  const json = algorithmRepository.getLatestAlgorithm();
  if (!json) {
    res.status(404).send('No daily algorithm found!');
    return;
  }

  // Render the index.html file with the json data
  // and send the rendered html to the client
  const templateHTML = fs.readFileSync(__dirname + '/public/index.html', 'utf8');
  const templateRenderer = new TemplateRenderer(json);
  res.send(templateRenderer.render(templateHTML));
});

app.get('/json', (req, res) => {
  const json = algorithmRepository.getLatestAlgorithm();
  if (!json) {
    res.status(404).send('No daily algorithm found!');
    return;
  }
  res.json(json);
});

app.get('/prev/:date', (req, res) => {
  const date = req.params.date;

  const json = algorithmRepository.getAlgorithmOfDate(date);
  if (!json) {
    res.status(301).redirect('/');
    return;
  }
  
  // Render the index.html file with the json data
  // and send the rendered html to the client
  const templateHTML = fs.readFileSync(__dirname + '/public/index.html', 'utf8');
  const templateRenderer = new TemplateRenderer(json);
  res.send(templateRenderer.render(templateHTML));
});

app.get('/rss', (req, res) => {
  const rssFeed = new RSSFeed();
  res.set('Content-Type', 'application/rss+xml');
  res.send(rssFeed.render());
});

app.listen(port, () => {
  const datetime = new Date().toISOString();
  console.log(`[${datetime}] Daily Algorithm Server listening on port ${port}`)
});
