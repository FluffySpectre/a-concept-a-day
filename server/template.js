export class TemplateRenderer {
    constructor(data) {
        this.data = data;
    }

    getValue(obj, path) {
        return path.split('.').reduce((acc, part) => acc && acc[part], obj);
    }

    renderTemplate(template) {
        return template.replace(/\{\{(\w+(\.\w+)*|\w+\[\d+\](\.\w+)*)\}\}/g, (match, p1) => {
        let path = p1.replace(/\[(\d+)\]/g, '.$1');
        let value = this.getValue(this.data, path);
        return value !== undefined ? value : match;
        });
    }

    renderTemplateWithArraySupport(template) {
        // Render arrays
        template = template.replace(/\{\{#each (\w+)\}\}([\s\S]*?)\{\{\/each\}\}/g, (match, p1, p2) => {
        let array = this.data[p1];
        if (Array.isArray(array)) {
            return array.map(item => new TemplateRenderer({ ...this.data, this: item }).renderTemplate(p2)).join('');
        }
        return match;
        });

        // Render other placeholders
        return this.renderTemplate(template);
    }

    render(template) {
        return this.renderTemplateWithArraySupport(template);
    }
}

// Examples:
// const data = {
//   title: "Hello, World!",
//   user: {
//     name: "John Doe",
//     age: 30
//   },
//   items: [
//     { name: "Item 1", value: 10 },
//     { name: "Item 2", value: 20 }
//   ]
// };

// const template = `
//   <h1>{{title}}</h1>
//   <p>Name: {{user.name}}</p>
//   <p>Age: {{user.age}}</p>
//   <ul>
//     {{#each items}}
//       <li>{{this.name}}: {{this.value}}</li>
//     {{/each}}
//   </ul>
// `;