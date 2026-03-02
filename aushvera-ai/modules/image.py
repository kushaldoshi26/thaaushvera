import requests, os, base64
from dotenv import load_dotenv

load_dotenv()

def image_ai(message):
    api_key = os.getenv("OPENAI_API_KEY")

    response = requests.post(
        "https://api.openai.com/v1/images/generations",
        headers={
            "Authorization": f"Bearer {api_key}",
            "Content-Type": "application/json"
        },
        json={
            "model": "gpt-image-1",
            "prompt": message,
            "size": "1024x1024"
        }
    )

    result = response.json()

    try:
        b64 = result["data"][0]["b64_json"]
    except Exception:
        return {"error": "Image generation failed"}

    return {
        "response": "Image generated successfully",
        "image_base64": b64
    }
