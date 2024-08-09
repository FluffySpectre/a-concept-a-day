import abc
import subprocess
import logging
import os
import ollama
from groq import Groq

class BaseAIClient(abc.ABC):
    @abc.abstractmethod
    def prompt_json(self, prompt) -> str:
        pass

class AIClientFactory():
    @staticmethod
    def get_client() -> BaseAIClient:
        name = os.environ.get("AI_PROVIDER", "ollama")
        if name == "ollama":
            return OllamaClient()
        elif name == "groq":
            return GroqClient(api_key=os.environ.get("AI_API_KEY", ""))
        raise Exception(f"{name} is not a supported AI provider!")

class OllamaClient(BaseAIClient):
    def __init__(self, autostart_ollama = True):
        self.__handle_ollama_autostart(autostart_ollama)

    def prompt_json(self, prompt, system_prompt = "") -> str:
        response = ollama.chat(
            model="llama3.1:8b-instruct-q6_K",
            messages=[
                {"role": "system", "content": system_prompt},
                {"role": "user", "content": prompt}
            ],
            format="json",
            options={"temperature": 0.1}
        )
        return response["message"]["content"]
    
    def __handle_ollama_autostart(self, autostart):
        if autostart and not self.__is_ollama_running():
            logging.info("Ollama is getting started...")
            self.__start_ollama()

    def __is_ollama_running(self):
        result = subprocess.run(["pgrep", "ollama"], capture_output=True, text=True)
        return result.stdout.strip().isnumeric()

    def __start_ollama(self):
        subprocess.run(["ollama", "list"], capture_output=False, stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)

class GroqClient(BaseAIClient):
    def __init__(self, api_key):
        self.client = Groq(api_key=api_key)

    def prompt_json(self, prompt, system_prompt = "") -> str:
        response = self.client.chat.completions.create(
            model="llama-3.1-70b-versatile",
            messages=[
                {"role": "system", "content": system_prompt},
                {"role": "user", "content": prompt}
            ],
            temperature=0.1,
            # max_tokens=1024,
            stream=False,
            stop=None,
            response_format={"type": "json_object"}
        )
        return response.choices[0].message.content
