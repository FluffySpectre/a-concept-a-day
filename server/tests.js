const assert = require('node:assert/strict');

// AlgorithmRepository
// should return the latest algorithm
const AlgorithmRepository = require('./AlgorithmRepository');
const algorithmRepository = new AlgorithmRepository();
const testLatestAlgorithm = async () => {
  const latestAlgorithm = await algorithmRepository.getLatestAlgorithm();
  assert.ok(latestAlgorithm, 'AlgorithmRepository: No latest algorithm found.');
};
testLatestAlgorithm();

// RSSFeed
// should return valid RSS feed xml
const RSSFeed = require('./RSSFeed');
const rssFeed = new RSSFeed();
const testValidRSSXML = async () => {
  const feedXML = await rssFeed.render();
  assert.ok(feedXML.indexOf('<rss') !== -1, 'RSSFeed: No valid rss xml.');
  // console.log(feedXML);
};
testValidRSSXML();

// template
// should render a template with all supported features
const { renderTemplate } = require('./template');
const template = `
  <div>
    <h1>{{title}}</h1>
    {{#items}}
      <p>{{item}}</p>
    {{/items}}
    {{#if not code_example}}
      {{#examples}}
        <p>{{item.language}}: {{item.code}}</p>
      {{/examples}}
    {{/if}}
  </div>
`;

const data = {
  title: 'Example',
  items: ['Item 1', 'Item 2'],
  // code_example: 'Python code example',
  examples: [
    { language: 'Python', code: 'Python code' },
    { language: 'C#', code: 'CSharp Code' },
  ],
};

const renderedTemplate = renderTemplate(template, data);
assert.ok(renderedTemplate.indexOf('<h1>Example</h1>') !== -1, 'template: Property not rendered correctly.');
assert.ok(renderedTemplate.indexOf('<p>Item 1</p>') !== -1, 'template: Simple array not rendered correctly.');
assert.ok(renderedTemplate.indexOf('<p>Python: Python code</p>') !== -1, 'template: Object array not rendered correctly.');
// console.log(renderedTemplate);

console.log('All tests completed!');
