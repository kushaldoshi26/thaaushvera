import requests, os
from dotenv import load_dotenv

load_dotenv()

def debug_ai(message, data):
    logs = data.get("logs", "")

    api_key = os.getenv("GEMINI_API_KEY")

    response = requests.post(
        f"https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key={api_key}",
        json={
            "contents": [
                {"parts": [{"text": f"Analyze Laravel error logs:\n{logs}"}]}
            ]
        }
    )

    return response.json()
