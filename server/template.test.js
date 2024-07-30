const { renderTemplate } = require('./template');

test('should render a template with all supported features', () => {
  const template = `
    <div>
      <h1>{{title}}</h1>
      <h2>{{nested.val}}</h2>
      <h3>{{nested.level2.val}}</h3>
      {{#items}}
        <p>{{item}}</p>
      {{/items}}
      {{#examples}}
        <p>{{item.language}}: {{item.code}}</p>
      {{/examples}}
      {{#if code_example}}
        <p>Show me if condition is true</p>
      {{/if}}
      {{#if not code_example}}
        <p>Show me if condition is false</p>
      {{/if}}
    </div>
  `;

  const data = {
    title: 'Example',
    items: ['Item 1', 'Item 2'],
    nested: {
      val: 'Single nested value',
      level2: {
        val: 'Double nested value'
      }
    },
    code_example: 'Python code example',
    examples: [
      { language: 'Python', code: 'Python code' },
      { language: 'C#', code: 'CSharp Code' },
    ],
  };

  const renderedTemplate = renderTemplate(template, data);

  expect(renderedTemplate).toContain('<h1>Example</h1>');
  expect(renderedTemplate).toContain('<h2>Single nested value</h2>');
  expect(renderedTemplate).toContain('<h3>Double nested value</h3>');
  expect(renderedTemplate).toContain('<p>Item 1</p>');
  expect(renderedTemplate).toContain('<p>Python: Python code</p>');
  expect(renderedTemplate).toContain('<p>C#: CSharp Code</p>');
  expect(renderedTemplate).toContain('<p>Show me if condition is true</p>');
  expect(renderedTemplate).not.toContain('<p>Show me if condition is false</p>');
});