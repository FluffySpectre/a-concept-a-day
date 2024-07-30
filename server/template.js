const fs = require('node:fs/promises');

const renderTemplateFile = async (filePath, data) => {
  const template = await fs.readFile(filePath, 'utf8');
  return renderTemplate(template, data);
};

const renderTemplate = (template, data) => {
  // Function to process conditionals
  const processConditionals = (template, data) => {
    return template.replace(/{{\s*#if\s+(not\s+)?([\w.]+)\s*}}([\s\S]*?){{\s*\/if\s*}}/g, (match, not, condition, innerTemplate) => {
      const keys = condition.split('.');
      let value = data;
      for (let k of keys) {
        value = value[k];
        if (value === undefined) {
          value = false;
          break;
        }
      }
      return not ? !value ? innerTemplate : '' : value ? innerTemplate : '';
    });
  };

  // Function to process loops
  const processLoops = (template, data) => {
    return template.replace(/{{\s*#(\w+)\s*}}([\s\S]*?){{\s*\/\1\s*}}/g, (match, key, innerTemplate) => {
      if (!Array.isArray(data[key])) {
        return match;
      }
      return data[key].map(item => replaceVariables(innerTemplate, { item })).join('');
    });
  };

  // Function to replace variables
  const replaceVariables = (template, data) => {
    return template.replace(/{{\s*([\w.]+)\s*}}/g, (match, key) => {
      const keys = key.split('.');
      let value = data;
      for (let k of keys) {
        value = value[k];
        if (value === undefined) {
          return match;
        }
      }
      return value;
    });
  };

  // Run all the processing functions
  let rendered = processConditionals(template, data);
  rendered = processLoops(rendered, data);
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
// };

module.exports = {
  renderTemplateFile,
  renderTemplate,
};
