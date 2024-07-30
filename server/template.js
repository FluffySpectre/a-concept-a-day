const fs = require('node:fs/promises');

const renderTemplateFile = async (filePath, data) => {
  const template = await fs.readFile(filePath, 'utf8');
  return renderTemplate(template, data);
};

const renderTemplate = (template, data) => {
  // Function to process conditionals
  const processConditionals = (template, data) => {
    return template.replace(/{{\s*#if\s+(not\s+)?([\w.]+)(?:\s*=\s*([\w.]+))?\s*}}([\s\S]*?){{\s*\/if\s*}}/g, (match, not, condition, valueToCheckAgainst, innerTemplate) => {
      const value = condition.split('.').reduce((acc, key) => acc && acc[key], data);
      const isTruthy = valueToCheckAgainst !== undefined ? value == valueToCheckAgainst : Boolean(value);
      return not ? !isTruthy ? innerTemplate : '' : isTruthy ? innerTemplate : '';
    });
  };

  // Function to process loops
  const processLoops = (template, data) => {
    return template.replace(/{{\s*#(\w+)\s*}}([\s\S]*?){{\s*\/\1\s*}}/g, (match, key, innerTemplate) => {
      const items = data[key];
      if (!Array.isArray(items)) {
        return match;
      }
      return items.map(item => processConditionals(replaceVariables(innerTemplate, { item }), { item })).join('');
    });
  };

  // Function to replace variables
  const replaceVariables = (template, data) => {
    return template.replace(/{{\s*([\w.]+)\s*}}/g, (match, key) => {
      const value = key.split('.').reduce((acc, k) => acc && acc[k], data);
      return value !== undefined ? value : match;
    });
  };

  // Run all the processing functions
  let rendered = processLoops(template, data);
  rendered = processConditionals(rendered, data);
  rendered = replaceVariables(rendered, data);

  return rendered;
};

// Examples:
// const template = `
//   <div>
//     <h1>{{title}}</h1>
//     <h2>{{nested.val}}</h2>
//     <h3>{{nested.level2.val}}</h3>
//     {{#items}}
//       <p>{{item}}</p>
//     {{/items}}
//     {{#examples}}
//       <p>{{item.language}}: {{item.code}}</p>
//     {{/examples}}
//     {{#if code_example}}
//       <p>Show me if condition is true</p>
//     {{/if}}
//     {{#if not code_example}}
//       <p>Show me if condition is false</p>
//     {{/if}}
//     {{ #if int_val = 42 }}
//     <p>Show me if condition is equal to 42</p>
//     {{ /if }}
//   </div>
// `;

// const data = {
//   title: 'Example',
//   items: ['Item 1', 'Item 2'],
//   code_example: 'Python code example',
//   nested: {
//     val: 'Single nested value',
//     level2: {
//       val: 'Double nested value'
//     }
//   },
//   examples: [
//     { language: 'Python', code: 'Python code' },
//     { language: 'C#', code: 'CSharp Code' },
//   ],
//   int_val: 42,
// };

module.exports = {
  renderTemplateFile,
  renderTemplate,
};
