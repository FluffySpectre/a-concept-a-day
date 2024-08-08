import sys
sys.dont_write_bytecode = True

import os
from dotenv import load_dotenv
import json
import logging
from datetime import datetime
from pathlib import Path

# from classes.ai_clients import OllamaClient
from classes.ai_clients import GroqClient

# Load env
load_dotenv()

# Config
ANSWER_LANGUAGE = os.environ.get("ANSWER_LANGUAGE", "English")
TARGET_DIR = Path(os.environ.get("TARGET_DIR", "../server"))
ALGORITHMS_DIR = TARGET_DIR / "algorithms"
PREVIOUS_ALGORITHMS_FILE = Path("previous_algorithms.txt")

# Logging setup
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s', datefmt='%Y-%m-%d %H:%M:%S')

# Try to read the list of previous_algorithms
previous_algorithms = PREVIOUS_ALGORITHMS_FILE.read_text().splitlines() if PREVIOUS_ALGORITHMS_FILE.exists() else []

# Setup ai client
# ai_client = OllamaClient()
ai_client = GroqClient(api_key=os.environ.get("AI_API_KEY", ""))

def prompt_ai(prompt):
    system_prompt = "You are a brilliant software developer who knows many different algorithms and their use cases."
    return ai_client.prompt_json(prompt, system_prompt)

def save_previous_algorithms(algorithms):
    PREVIOUS_ALGORITHMS_FILE.write_text("\n".join(algorithms))

def save_new_algorithm(algorithm):
    today = datetime.now().strftime("%Y-%m-%d")
    algorithm["date"] = datetime.now().timestamp()
    with open(ALGORITHMS_DIR / f"{today}.json", "w") as file:
        json.dump(algorithm, file, indent=2)

def generate_prompt():
    previous_algorithms_str = "\n".join(previous_algorithms)
    return (
        f"Please suggest an interesting and useful algorithm that a software developer and game developer should know.\n"
        f"Your answer should be in {ANSWER_LANGUAGE} and contain the following information about this algorithm:\n"
        "- Name of the algorithm\n"
        "- A brief, concise summary of the algorithm in simple terms\n"
        "- The single steps of the algorithm formatted as HTML ordered list\n"
        "- An interesting practical example of the application of the algorithm\n"
        "- The time and space complexity of the algorithm\n"
        "- A Python code example for the algorithm\n\n"
        "Ignore the following algorithms:\n"
        f"{previous_algorithms_str}\n\n"
        "Give your answer as a properly escaped JSON object in the following format:\n"
        "{"
        "\"name\": \"Name of the algorithm\","
        "\"summary\": \"A brief, concise summary of the algorithm in simple terms\","
        "\"step_description\": \"The single steps of the algorithm formatted as HTML ordered list\","
        "\"example\": \"An interesting practical example of the application of the algorithm\","
        "\"complexity\": \"The time and space complexity of the algorithm\","
        "\"example_code\": \"An Python code example for the algorithm\""
        "}\n\n"
        "Respond only with the JSON object and no further explanation!"
    )

def generate_contents(algorithm):
    return [
        {"title": "Summary", "content": algorithm["summary"], "type": "text"},
        {"title": "Use Case", "content": algorithm["example"], "type": "text"},
        {"title": "Steps", "content": algorithm["step_description"], "type": "text"},
        {"title": "Complexity", "content": algorithm["complexity"], "type": "text"},
        {"title": "Code Example", "content": algorithm["example_code"], "type": "code"},
    ]

def generate_new_algorithm():
    new_algorithm = None
    retry_count = 10
    while new_algorithm is None and retry_count > 0:
        prompt = generate_prompt()
        try:
            response = prompt_ai(prompt)
            response = filter_response(response)
            new_algorithm = json.loads(response)
        except:
            logging.warning("Failed to parse response. Trying again...")
            logging.debug(response)
            retry_count -= 1
    return new_algorithm

def filter_response(response):
    return '\\n'.join(response.splitlines()).replace("\\*", "*")

new_algorithm = generate_new_algorithm()

if new_algorithm:
    contents = generate_contents(new_algorithm)

    # Add the new algorithm to the list of previous_algorithms
    new_algorithm_name = new_algorithm["name"]
    previous_algorithms.append(new_algorithm_name)

    # Save the list of previous_algorithms
    save_previous_algorithms(previous_algorithms)

    # Format the new algorithm object
    final_new_algorithm = {"name": new_algorithm["name"], "content": contents}

    # Save the new_algorithm to the algorithms folder
    save_new_algorithm(final_new_algorithm)

    logging.info("Daily algorithm was updated!")
