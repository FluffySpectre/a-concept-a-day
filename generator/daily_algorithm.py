import sys
import subprocess
import json
from datetime import datetime
import ollama

sys.dont_write_bytecode = True

# Config
answer_language = "English"
target_dir = "/home/daily-algorithm/da-backend/server/public"
previous_algorithms_dir = f"{target_dir}/previous"
previous_algorithms_file = "previous_algorithms.txt"

# Try to read the list of previous_algorithms
previous_algorithms = []
try:
    with open(previous_algorithms_file, "r") as file:
        previous_algorithms = file.read().splitlines()
except FileNotFoundError:
    pass

def is_ollama_running():
    result = subprocess.run(["pgrep", "ollama"], capture_output=True, text=True)
    return result.stdout.strip().isnumeric()

def start_ollama():
    subprocess.run(["ollama", "list"], capture_output=False, stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)

def prompt_ollama(prompt):
    response = ollama.chat(
        model="llama3.1:8b-instruct-q6_K",
        messages=[
            {
                "role": "user",
                "content": prompt,
            },
        ]
    )
    return response["message"]["content"]

def save_previous_algorithms(algorithms):
    with open(previous_algorithms_file, "w") as file:
        file.write("\n".join(algorithms))

def save_new_algorithm(algorithm):
    dt = datetime.now()
    today = dt.strftime("%Y-%m-%d")
    algorithm["date"] = datetime.timestamp(dt)
    with open(f"{previous_algorithms_dir}/{today}.json", "w") as file:
        json.dump(algorithm, file, indent=2)

def generate_prompt():
    previous_algorithms_str = "\n".join(previous_algorithms)
    return (
        f"Please suggest an interesting and useful algorithm that a software developer and game developer should know.\n"
        f"Your answer should be in {answer_language} and contain the following information about this algorithm:\n"
        "- Name of the algorithm\n"
        "- A brief, concise summary of the algorithm in simple terms\n"
        "- The single steps of the algorithm formatted as HTML ordered list\n"
        "- An interesting practical example of the application of the algorithm\n"
        "- The time and space complexity of the algorithm\n"
        "- An Python code example for the algorithm\n\n"
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
    contents = []
    contents.append({"title": "Summary", "content": algorithm["summary"], "type": "text"})
    contents.append({"title": "Use Case", "content": algorithm["example"], "type": "text"})
    contents.append({"title": "Steps", "content": algorithm["step_description"], "type": "text"})
    contents.append({"title": "Complexity", "content": algorithm["complexity"], "type": "text"})
    contents.append({"title": "Code Example", "content": algorithm["example_code"], "type": "code"})
    return contents

def generate_new_algorithm():
    new_algorithm = None
    retry_count = 10
    while new_algorithm is None and retry_count > 0:
        prompt = generate_prompt()
        response = prompt_ollama(prompt)
        try:
            new_algorithm = json.loads(response)
        except:
            print("Failed to parse response. Trying again...")
            print(response)
            retry_count -= 1
    return new_algorithm

# Make sure ollama is running
if not is_ollama_running():
    print("Ollama is getting started...")
    start_ollama()

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

    # Save the new_algorithm to the previous folder
    save_new_algorithm(final_new_algorithm)

    print("Daily algorithm was updated!")
