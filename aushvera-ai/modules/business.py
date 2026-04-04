import pandas as pd
import requests, os
from dotenv import load_dotenv

load_dotenv()

def business_ai(message, data):
    df = pd.DataFrame(data.get("products", []))

    if not df.empty:
        low = df[df["sales"] < 5]
        summary = f"Low performing products: {low.to_dict()}"
    else:
        summary = "No product data provided."

    api_key = os.getenv("GEMINI_API_KEY")

    response = requests.post(
        f"https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key={api_key}",
        json={
            "contents": [
                {"parts": [{"text": summary + "\nSuggest strategy improvements."}]}
            ]
        }
    )

    return response.json()
