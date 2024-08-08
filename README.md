# daily-algorithm-website

Link: **[https://daily-algorithm.com](https://daily-algorithm.com)**

## What it does
Every day, an LLM selects and features an interesting algorithm on the website that developers should know about.

## Prerequisites
- Local ollama installation or Groq API key
- Python >=3.12
- NodeJS >= 20

## Setup

### Algorithm generator

1. Open a terminal and switch into the "generator" directory.
2. Run:
    ```sh
    pip install -r ./requirements.txt
    ```
3. Duplicate the **.env.example**-file and rename it to **.env**.
4. Open the **.env**-file and adjust the settings.
5. Complete! Now you can run the algorithm generator:
    ```sh
    python daily_algorithm.py
    ```

### Website server

1. Open a terminal and switch into the "server" directory.
2. Run:
    ```sh
    npm install
    ```
3. Run:
    ```sh
    node app.js
    ```
4. Open a webbrowser and enter:
    ```
    http://localhost:3000
    ```