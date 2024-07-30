import sys
sys.dont_write_bytecode = True

import ollama
import json
import subprocess
from datetime import datetime

# Config
answer_language = "English"
target_dir = "/home/daily-algorithm/da-backend/server/public"
previous_algorithms_dir = target_dir + "/previous"

# Try to read the list of previous_algorithms
previous_algorithms = []
try:
    with open(target_dir + "/previous_algorithms.txt", "r") as file:
        previous_algorithms = file.read().splitlines()
except FileNotFoundError:
    pass
previous_algorithms_str = ", ".join(previous_algorithms)

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
    ])
    return response["message"]["content"]

# Make sure ollama is running
if not is_ollama_running():
    print("Ollama is getting started...")
    start_ollama()

prompt = (
    f"Please suggest an interesting and useful algorithm that a software developer and game developer should know.\n"
    f"Your answer should be in {answer_language} and contain the following information about this algorithm:\n"
    "- Name of the algorithm\n"
    "- A brief, concise summary of the algorithm in simple terms\n"
    "- The single steps of the algorithm formatted as HTML ordered list\n"
    "- An interesting practical example of the application of the algorithm\n\n"
    "Ignore the following algorithms:\n"
    f"{previous_algorithms_str}\n\n"
    "Give your answer as a JSON object in the following format:\n"
    "{"
    "\"name\": \"Name of the algorithm\","
    "\"summary\": \"A brief, concise summary of the algorithm in simple terms\","
    "\"step_description\": \"The single steps of the algorithm formatted as HTML ordered list\","
    "\"example\": \"An interesting practical example of the application of the algorithm\""
    "}\n\n"
    "Respond only with the JSON object and no further explanation!"
)

new_algorithm = None
retry_count = 3
while new_algorithm is None and retry_count > 0:
    # Generate a new algorithm
    response = prompt_ollama(prompt)

    try:
        new_algorithm = json.loads(response)
    except:
        print("Failed to parse response. Trying again...")
        print(response)
        retry_count -= 1

contents = []
contents.append({ "title": "Summary", "content": new_algorithm["summary"], "type": "text"  })
contents.append({ "title": "Use Case", "content": new_algorithm["example"], "type": "text"  })

# Generate a fun fact for the new algorithm
response = prompt_ollama(
    f"Tell me a Fun-Fact about the algorithm {new_algorithm['name']}. If there is no fun-fact answer with '0'. Don't make something up! Reply only with the fun-fact or '0' but no further explanation!"
)
response = response.strip()
if response != "0":
    contents.append({ "title": "Fun Fact", "content": response.strip(), "type": "text" })

contents.append({ "title": "Steps", "content": new_algorithm["step_description"], "type": "text"  })

# Generate a coding example for the new algorithm
response = prompt_ollama(
    f"Generate a Python example for the algorithm {new_algorithm['name']}. Reply only with the code example and code comments but no further explanation!"
)
response = response.strip()
# If the response contains ``` then remove it
if response.startswith("```"):
    response = response.replace("```python", "", 1)
    if response.startswith("```"):
        response = response[3:]
if response.endswith("```"):
    response = response[:-3]
contents.append({ "title": "Code Example", "content": response.strip(), "type": "code" })

# new_algorithm["coding_examples"] = [{"language": "python", "language_display": "Python", "code": response}]

# Add the new algorithm to the list of previous_algorithms
new_algorithm_name = new_algorithm["name"]
previous_algorithms.append(new_algorithm_name)

# Save the list of previous_algorithms
with open(target_dir + "/previous_algorithms.txt", "w") as file:
    file.write("\n".join(previous_algorithms))

# Format the new algorithm object
final_new_algorithm = { 
    "name": new_algorithm["name"],
    "content": contents
}

# Save the new_algorithm to the previous folder
dt = datetime.now()
today = dt.strftime("%Y-%m-%d")
with open(previous_algorithms_dir + f"/{today}.json", "w") as file:
    final_new_algorithm["date"] = datetime.timestamp(dt)
    json.dump(final_new_algorithm, file, indent=2)

print("Daily algorithm was updated!")
