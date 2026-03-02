import requests, os
from dotenv import load_dotenv

load_dotenv()

def design_ai(message):
    api_key = os.getenv("GEMINI_API_KEY")

    response = requests.post(
        f"https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key={api_key}",
        json={
            "contents": [
                {"parts": [{"text": f"You are a luxury Ayurvedic brand creative director. {message}"}]}
            ]
        }
    )

    result = response.json()
    try:
        text = result["candidates"][0]["content"]["parts"][0]["text"]
    except Exception:
        text = "AI failed to generate response."
    return {"response": text}
