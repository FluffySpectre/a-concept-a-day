const express = require('express');
const path = require('node:path');
const { renderTemplateFile } = require('./template');
const RSSFeed = require('./RSSFeed');
const AlgorithmRepository = require('./AlgorithmRepository');

const app = express();
const port = 3000;

const algorithmRepository = new AlgorithmRepository();

app.use('/public', express.static(path.resolve(__dirname, 'public')));

app.get('/', async (req, res) => {
  const json = await algorithmRepository.getLatestAlgorithm();
  if (!json) {
    res.status(404).send('No daily algorithm found!');
    return;
  }
  res.send(await renderTemplateFile(path.join(__dirname, 'public', 'index.html'), json));
});

app.get('/json', async (req, res) => {
  const json = await algorithmRepository.getLatestAlgorithm();
  if (!json) {
    res.status(404).send('No daily algorithm found!');
    return;
  }
  res.json(json);
});

app.get('/prev/:date', async (req, res) => {
  const date = req.params.date;
  const json = await algorithmRepository.getAlgorithmOfDate(date);
  if (!json) {
    res.status(301).redirect('/');
    return;
  }
  res.send(await renderTemplateFile(path.join(__dirname, 'public', 'index.html'), json));
});

app.get('/rss', async (req, res) => {
  const rssFeed = new RSSFeed();
  const feedXML = await rssFeed.render();
  res.set('Content-Type', 'application/rss+xml');
  res.send(feedXML);
});

app.listen(port, () => {
  const datetime = new Date().toISOString();
  console.log(`[${datetime}] Daily Algorithm Server listening on port ${port}`);
});
