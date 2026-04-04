from fastapi import FastAPI
from pydantic import BaseModel
from router import route_task

app = FastAPI()

class Prompt(BaseModel):
    message: str
    data: dict = {}

@app.post("/ai-agent")
async def ai_agent(prompt: Prompt):
    try:
        return route_task(prompt.message, prompt.data)
    except Exception as e:
        return {"error": str(e)}
